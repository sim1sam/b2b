<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Vendor;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of purchase requests.
     */
    public function index(Request $request)
    {
        $purchaseRequests = PurchaseRequest::where('user_id', Auth::id())
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->with('vendor')
            ->latest()
            ->paginate(10);
        
        return view('customer.purchase-requests.index', compact('purchaseRequests'));
    }

    /**
     * Show the form for creating a new purchase request.
     */
    public function create()
    {
        // Show vendors assigned to this customer OR general vendors (no user_id)
        $vendors = Vendor::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
            ->where('is_active', true)
            ->get();
        
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        $exchangeRate = Auth::user()->exchange_rate ?? 1.0;

        return view('customer.purchase-requests.create', compact('vendors', 'availableBalance', 'exchangeRate'));
    }

    /**
     * Store newly created purchase requests (can handle multiple).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requests' => 'required|array|min:1',
            'requests.*.vendor_id' => 'required|exists:vendors,id',
            'requests.*.amount_inr' => 'required|numeric|min:0.01',
            'requests.*.is_gst_payment' => 'nullable|boolean',
            'requests.*.description' => 'nullable|string|max:1000',
            'fund_transaction_id' => 'required',
        ]);

        $user = Auth::user();
        $exchangeRate = $user->exchange_rate ?? 1.0;
        
        if (!$exchangeRate || $exchangeRate <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Exchange rate is not set. Please contact admin.']);
        }

        // Calculate total BDT amount needed
        $totalAmountBDT = 0;
        $calculatedRequests = [];
        
        foreach ($validated['requests'] as $index => $reqData) {
            $amountINR = $reqData['amount_inr'];
            $isGstPayment = isset($reqData['is_gst_payment']) && $reqData['is_gst_payment'];
            
            // Calculate BDT amount
            if ($isGstPayment) {
                $amountBDT = $amountINR * $exchangeRate;
            } else {
                // [Input Amount + (Input Amount x 5%) + 200] x Exchange Rate
                $amountWithGst = $amountINR + ($amountINR * 0.05) + 200;
                $amountBDT = $amountWithGst * $exchangeRate;
            }
            
            $totalAmountBDT += $amountBDT;
            $calculatedRequests[$index] = [
                'vendor_id' => $reqData['vendor_id'],
                'amount_inr' => $amountINR,
                'is_gst_payment' => $isGstPayment,
                'amount_bdt' => $amountBDT,
                'amount' => $amountBDT, // Store BDT as main amount
                'description' => $reqData['description'] ?? null,
                'fund_transaction_id' => $reqData['fund_transaction_id'] ?? null,
            ];
        }

        // Check available balance against total amount
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        if ($availableBalance < $totalAmountBDT) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Low Fund! Please Add Fund or contact admin for a manual payment. Total required: ৳' . number_format($totalAmountBDT, 2) . ' | Available: ৳' . number_format($availableBalance, 2)]);
        }

        // Verify all vendors belong to user or are general vendors
        $vendorIds = collect($calculatedRequests)->pluck('vendor_id')->unique();
        $vendorsCount = Vendor::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
            ->whereIn('id', $vendorIds)
            ->count();
        
        if ($vendorsCount !== $vendorIds->count()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'One or more selected vendors are invalid.']);
        }

        // Validate each request's fund amount is sufficient against total available balance
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        foreach ($calculatedRequests as $index => $reqData) {
            if ($availableBalance < $reqData['amount_bdt']) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => 'Low Fund! Please Add Fund or contact admin for a manual payment. Total available funds are insufficient for request #' . ($index + 1)]);
            }
        }

        // Create purchase requests in transaction
        DB::beginTransaction();
        try {
            $createdRequests = [];
            
            foreach ($calculatedRequests as $reqData) {
                // Create purchase request (PO number will be auto-generated)
                $purchaseRequest = PurchaseRequest::create([
                    'user_id' => Auth::id(),
                    'vendor_id' => $reqData['vendor_id'],
                    'amount' => $reqData['amount_bdt'],
                    'amount_inr' => $reqData['amount_inr'],
                    'is_gst_payment' => $reqData['is_gst_payment'],
                    'amount_bdt' => $reqData['amount_bdt'],
                    'description' => $reqData['description'],
                    'status' => 'pending',
                ]);

                // Create fund transaction (deduct from balance)
                $fundTransaction = FundTransaction::create([
                    'user_id' => Auth::id(),
                    'amount' => $reqData['amount_bdt'],
                    'type' => 'purchase',
                    'status' => 'approved',
                    'purchase_request_id' => $purchaseRequest->id,
                    'notes' => 'Purchase Order: ' . $purchaseRequest->po_number,
                ]);

                // Link transaction to purchase request
                $purchaseRequest->update(['fund_transaction_id' => $fundTransaction->id]);
                
                $createdRequests[] = $purchaseRequest;
            }

            DB::commit();

            $poNumbers = collect($createdRequests)->pluck('po_number')->join(', ');
            
            return redirect()->route('customer.purchase-requests.index')
                ->with('success', count($createdRequests) . ' Purchase Order(s) created successfully. PO Numbers: ' . $poNumbers . '. Amount deducted from your balance.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create purchase orders. Please try again.']);
        }
    }

    /**
     * Display the specified purchase request.
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only view their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $purchaseRequest->load('vendor', 'fundTransaction', 'items.shippingCharge');

        return view('customer.purchase-requests.show', compact('purchaseRequest'));
    }

    /**
     * Show the form for editing the specified purchase request.
     */
    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only edit their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow editing if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('customer.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be edited.');
        }

        // Show vendors assigned to this customer OR general vendors (no user_id)
        $vendors = Vendor::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })
            ->where('is_active', true)
            ->get();
        
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        $exchangeRate = Auth::user()->exchange_rate ?? 1.0;
        
        // Get available fund transactions for dropdown
        $availableFunds = FundTransaction::where('user_id', Auth::id())
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.purchase-requests.edit', compact('purchaseRequest', 'vendors', 'availableBalance', 'exchangeRate', 'availableFunds'));
    }

    /**
     * Upload invoice file for purchase request.
     */
    public function uploadInvoice(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only upload invoice for their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'invoice' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Handle invoice upload
        if ($request->hasFile('invoice')) {
            // Delete old invoice if exists
            if ($purchaseRequest->invoice) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($purchaseRequest->invoice);
            }
            $validated['invoice'] = $request->file('invoice')->store('purchase-requests/invoices', 'public');
        }

        $purchaseRequest->update([
            'invoice' => $validated['invoice'],
            'status' => 'completed', // Mark as completed when invoice is uploaded
        ]);

        return redirect()->route('customer.purchase-requests.show', $purchaseRequest->id)
            ->with('success', 'Invoice uploaded successfully. Purchase request marked as completed.');
    }

    /**
     * Upload tracking ID file for purchase request.
     */
    public function uploadTrackingId(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only upload tracking ID for their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'tracking_id_file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Handle tracking ID file upload
        if ($request->hasFile('tracking_id_file')) {
            // Delete old tracking ID file if exists
            if ($purchaseRequest->tracking_id_file) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($purchaseRequest->tracking_id_file);
            }
            $validated['tracking_id_file'] = $request->file('tracking_id_file')->store('purchase-requests/tracking-ids', 'public');
        }

        $purchaseRequest->update([
            'tracking_id_file' => $validated['tracking_id_file'],
        ]);

        return redirect()->route('customer.purchase-requests.show', $purchaseRequest->id)
            ->with('success', 'Tracking ID file uploaded successfully.');
    }

    /**
     * Update the specified purchase request.
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only update their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow editing if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('customer.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be edited.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount_inr' => 'required|numeric|min:0.01',
            'is_gst_payment' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        // Verify vendor belongs to user or is a general vendor
        $vendor = Vendor::where('id', $validated['vendor_id'])
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $user = Auth::user();
        $exchangeRate = $user->exchange_rate ?? 1.0;
        
        // Calculate BDT amount
        $amountINR = $validated['amount_inr'];
        $isGstPayment = isset($validated['is_gst_payment']) && $validated['is_gst_payment'];
        
        if ($isGstPayment) {
            $amountBDT = $amountINR * $exchangeRate;
        } else {
            $amountWithGst = $amountINR + ($amountINR * 0.05) + 200;
            $amountBDT = $amountWithGst * $exchangeRate;
        }

        // Calculate difference if amount changed
        $amountDifference = $amountBDT - $purchaseRequest->amount;
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());

        if ($amountDifference > 0 && $availableBalance < $amountDifference) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount_inr' => 'Insufficient funds for amount increase. Available balance: ৳' . number_format($availableBalance, 2)]);
        }

        // Update purchase request
        $purchaseRequest->update([
            'vendor_id' => $validated['vendor_id'],
            'amount' => $amountBDT,
            'amount_inr' => $amountINR,
            'is_gst_payment' => $isGstPayment,
            'amount_bdt' => $amountBDT,
            'description' => $validated['description'],
        ]);

        // Update fund transaction if amount changed
        if ($amountDifference != 0 && $purchaseRequest->fundTransaction) {
            $purchaseRequest->fundTransaction->update([
                'amount' => $amountBDT,
                'notes' => 'Purchase Order: ' . $purchaseRequest->po_number,
            ]);
        }

        return redirect()->route('customer.purchase-requests.index')
            ->with('success', 'Purchase request updated successfully.');
    }

    /**
     * Remove the specified purchase request.
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Ensure customer can only delete their own purchase requests
        if ($purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Only allow deletion if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('customer.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be deleted.');
        }

        // Refund the amount if transaction exists
        if ($purchaseRequest->fundTransaction) {
            $purchaseRequest->fundTransaction->delete();
        }

        $purchaseRequest->delete();

        return redirect()->route('customer.purchase-requests.index')
            ->with('success', 'Purchase request cancelled. Amount has been refunded to your balance.');
    }
}
