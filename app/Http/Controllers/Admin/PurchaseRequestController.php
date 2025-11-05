<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Vendor;
use App\Models\User;
use App\Models\FundTransaction;
use App\Models\ShippingCharge;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of purchase requests.
     */
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['vendor', 'user'])->latest();

        // Filter by client
        if ($request->has('client_id') && $request->client_id !== '') {
            $query->where('user_id', $request->client_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $purchaseRequests = $query->paginate(15);
        $clients = User::where('role', 'customer')->orderBy('name')->get();

        return view('admin.purchase-requests.index', compact('purchaseRequests', 'clients'));
    }

    /**
     * Show the form for creating a new purchase request.
     */
    public function create(Request $request)
    {
        $clients = User::where('role', 'customer')->orderBy('name')->get();
        
        $selectedClientId = $request->get('client_id');
        $vendors = collect();
        $availableBalance = 0;
        $exchangeRate = 1.0;
        
        if ($selectedClientId) {
            $client = User::find($selectedClientId);
            if ($client && $client->role === 'customer') {
                // Show vendors assigned to this client OR general vendors (no user_id)
                $vendors = Vendor::where(function($query) use ($client) {
                    $query->where('user_id', $client->id)
                          ->orWhereNull('user_id');
                })
                    ->where('is_active', true)
                    ->get();
                $availableBalance = FundTransaction::getAvailableBalance($client->id);
                $exchangeRate = $client->exchange_rate ?? 1.0;
            }
        }
        
        return view('admin.purchase-requests.create', compact('clients', 'vendors', 'availableBalance', 'exchangeRate', 'selectedClientId'));
    }

    /**
     * Store newly created purchase requests.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'requests' => 'required|array|min:1',
            'requests.*.vendor_id' => 'required|exists:vendors,id',
            'requests.*.amount_inr' => 'required|numeric|min:0.01',
            'requests.*.is_gst_payment' => 'nullable|boolean',
            'requests.*.description' => 'nullable|string|max:1000',
            'fund_transaction_id' => 'required',
        ]);

        $client = User::findOrFail($validated['client_id']);
        
        // Verify client is a customer
        if ($client->role !== 'customer') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Selected user is not a client.']);
        }

        $exchangeRate = $client->exchange_rate ?? 1.0;
        
        if (!$exchangeRate || $exchangeRate <= 0) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Client exchange rate is not set. Please set it in client profile.']);
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
                'amount' => $amountBDT,
                'description' => $reqData['description'] ?? null,
            ];
        }

        // Check available balance
        $availableBalance = FundTransaction::getAvailableBalance($client->id);
        if ($availableBalance < $totalAmountBDT) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Insufficient funds for client. Total required: ৳' . number_format($totalAmountBDT, 2) . ' | Available: ৳' . number_format($availableBalance, 2)]);
        }

        // Verify all vendors belong to the client or are general vendors
        $vendorIds = collect($calculatedRequests)->pluck('vendor_id')->unique();
        $vendorsCount = Vendor::where(function($query) use ($client) {
            $query->where('user_id', $client->id)
                  ->orWhereNull('user_id');
        })
            ->whereIn('id', $vendorIds)
            ->count();
        
        if ($vendorsCount !== $vendorIds->count()) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'One or more selected vendors are invalid.']);
        }

        // Validate total fund amount is sufficient against total calculated BDT
        if ($availableBalance < $totalAmountBDT) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Low Fund! Please Add Fund or contact admin for a manual payment. Total available funds are insufficient.']);
        }

        // Create purchase requests in transaction
        DB::beginTransaction();
        try {
            $createdRequests = [];
            
            foreach ($calculatedRequests as $reqData) {
                // Create purchase request (PO number will be auto-generated)
                $purchaseRequest = PurchaseRequest::create([
                    'user_id' => $client->id,
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
                    'user_id' => $client->id,
                    'amount' => $reqData['amount_bdt'],
                    'type' => 'purchase',
                    'status' => 'approved',
                    'purchase_request_id' => $purchaseRequest->id,
                    'notes' => 'Purchase Order: ' . $purchaseRequest->po_number . ' (Created by Admin)',
                ]);

                // Link transaction to purchase request
                $purchaseRequest->update(['fund_transaction_id' => $fundTransaction->id]);
                
                $createdRequests[] = $purchaseRequest;
            }

            DB::commit();

            $poNumbers = collect($createdRequests)->pluck('po_number')->join(', ');
            
            return redirect()->route('admin.purchase-requests.index')
                ->with('success', count($createdRequests) . ' Purchase Order(s) created successfully for ' . $client->business_name . '. PO Numbers: ' . $poNumbers);
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
    public function show($id)
    {
        $purchaseRequest = PurchaseRequest::with(['vendor', 'user', 'fundTransaction', 'items.shippingCharge'])->findOrFail($id);
        
        return view('admin.purchase-requests.show', compact('purchaseRequest'));
    }

    /**
     * Show form to add received items for cross-checking.
     */
    public function showReceivedItemsForm($id)
    {
        $purchaseRequest = PurchaseRequest::with(['user', 'items'])->findOrFail($id);
        
        // Get shipping charges for this client
        $shippingCharges = ShippingCharge::where('user_id', $purchaseRequest->user_id)
            ->where('is_active', true)
            ->orderBy('item_name')
            ->get();
        
        return view('admin.purchase-requests.received-items', compact('purchaseRequest', 'shippingCharges'));
    }

    /**
     * Store received items and generate provisional invoice.
     */
    public function storeReceivedItems(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequest::with('user')->findOrFail($id);
        
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.shipping_charge_id' => 'required|exists:shipping_charges,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.weight' => 'required|numeric|min:0',
            'transportation_charge' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing items if any
            PurchaseOrderItem::where('purchase_request_id', $purchaseRequest->id)->delete();

            $totalCost = 0;
            $totalWeight = 0;
            
            foreach ($validated['items'] as $itemData) {
                $shippingCharge = ShippingCharge::findOrFail($itemData['shipping_charge_id']);
                
                // Verify shipping charge belongs to the client
                if ($shippingCharge->user_id !== $purchaseRequest->user_id) {
                    throw new \Exception('Shipping charge does not belong to this client.');
                }

                // Calculate costs
                $itemCost = $itemData['quantity'] * $shippingCharge->rate_per_unit;
                $weightShippingCost = $itemData['weight'] * $shippingCharge->shipping_charge_per_kg;
                
                // For quantity-based items (unit_type = 'qty'), show item cost in shipping cost column as well
                // For display purposes: shipping_cost will include item_cost for qty items
                if ($shippingCharge->unit_type === 'qty') {
                    $shippingCost = $itemCost + $weightShippingCost; // Include item cost in shipping cost column for display
                    $totalItemCost = $itemCost + $weightShippingCost; // But total is item_cost + weight_shipping_cost (not double counting)
                } else {
                    $shippingCost = $weightShippingCost;
                    $totalItemCost = $itemCost + $weightShippingCost;
                }
                
                $totalCost += $totalItemCost;
                
                // Only include weight in net weight calculation if unit_type is 'weight'
                // Exclude weights from quantity-based items (unit_type = 'qty')
                if ($shippingCharge->unit_type === 'weight') {
                    $totalWeight += $itemData['weight'];
                }

                PurchaseOrderItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'shipping_charge_id' => $shippingCharge->id,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'weight' => $itemData['weight'],
                    'rate_per_unit' => $shippingCharge->rate_per_unit,
                    'unit_type' => $shippingCharge->unit_type,
                    'item_cost' => $itemCost,
                    'shipping_cost' => $shippingCost,
                    'total_cost' => $totalItemCost,
                ]);
            }

            // Calculate net weight and packaging weight (only from weight-based items)
            $netWeight = $totalWeight;
            if ($netWeight < 20) {
                $packagingWeight = $netWeight * 0.10;
            } else {
                $packagingWeight = $netWeight * 0.08;
            }

            // Get lowest shipping charge per kg from client configuration
            $lowestShippingCharge = $purchaseRequest->user->lowest_shipping_charge_per_kg ?? 0;

            // Calculate packaging cost
            $packagingCost = $packagingWeight * $lowestShippingCharge;
            
            // Add packaging cost to total
            $totalCost += $packagingCost;
            
            // Add transportation charge if provided
            $transportationCharge = $validated['transportation_charge'] ?? 0;
            $totalCost += $transportationCharge;

            // Update purchase request with transportation charge
            $purchaseRequest->update([
                'transportation_charge' => $transportationCharge,
            ]);

            DB::commit();

            return redirect()->route('admin.purchase-requests.provisional-invoice', $purchaseRequest->id)
                ->with('success', 'Received items recorded and provisional invoice generated.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to save received items. ' . $e->getMessage()]);
        }
    }

    /**
     * Display provisional invoice for customer.
     */
    public function showProvisionalInvoice($id)
    {
        $purchaseRequest = PurchaseRequest::with(['user', 'vendor', 'items.shippingCharge'])->findOrFail($id);
        
        // Check if user is admin or the customer owner
        if (Auth::user()->role === 'customer' && $purchaseRequest->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($purchaseRequest->items->isEmpty()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.purchase-requests.show', $purchaseRequest->id)
                    ->with('error', 'No items recorded yet. Please add received items first.');
            } else {
                return redirect()->route('customer.purchase-requests.show', $purchaseRequest->id)
                    ->with('error', 'No items recorded yet.');
            }
        }

        $itemsTotalCost = $purchaseRequest->items->sum('total_cost');
        
        // Calculate net weight (only from weight-based items, exclude quantity-based items)
        $netWeight = $purchaseRequest->items()
            ->whereHas('shippingCharge', function($query) {
                $query->where('unit_type', 'weight');
            })
            ->sum('weight');
        
        // Calculate packaging weight
        // If net weight < 20 kgs: packaging weight = 10% of net weight
        // If net weight >= 20 kgs: packaging weight = 8% of net weight
        if ($netWeight < 20) {
            $packagingWeight = $netWeight * 0.10;
        } else {
            $packagingWeight = $netWeight * 0.08;
        }

        // Get lowest shipping charge per kg from client configuration
        $lowestShippingCharge = $purchaseRequest->user->lowest_shipping_charge_per_kg ?? 0;

        // Calculate packaging cost
        $packagingCost = $packagingWeight * $lowestShippingCharge;
        
        // Get transportation charge
        $transportationCharge = $purchaseRequest->transportation_charge ?? 0;
        
        // Calculate total cost
        $totalCost = $itemsTotalCost + $packagingCost + $transportationCharge;
        
        // Calculate round-off to previous 50
        $roundedTotal = floor($totalCost / 50) * 50;
        $roundOffAmount = $totalCost - $roundedTotal;
        
        // Get admin profile
        $admin = User::where('role', 'admin')->first();
        
        return view('admin.purchase-requests.provisional-invoice', compact(
            'purchaseRequest', 
            'totalCost', 
            'itemsTotalCost',
            'netWeight', 
            'packagingWeight',
            'packagingCost',
            'lowestShippingCharge',
            'transportationCharge',
            'roundedTotal',
            'roundOffAmount',
            'admin'
        ));
    }

    /**
     * Mark purchase request payment as paid and upload screenshot.
     */
    public function markAsPaid(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        
        $validated = $request->validate([
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Handle payment screenshot upload
        if ($request->hasFile('payment_screenshot')) {
            // Delete old screenshot if exists
            if ($purchaseRequest->payment_screenshot) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($purchaseRequest->payment_screenshot);
            }
            $validated['payment_screenshot'] = $request->file('payment_screenshot')->store('purchase-requests/payments', 'public');
        }

        $purchaseRequest->update([
            'payment_status' => 'paid',
            'payment_screenshot' => $validated['payment_screenshot'],
            'status' => 'approved', // Automatically approve the order when marked as paid
        ]);

        return redirect()->route('admin.purchase-requests.show', $purchaseRequest->id)
            ->with('success', 'Payment marked as paid, order status updated to approved, and screenshot uploaded successfully.');
    }

    /**
     * Show the form for editing the specified purchase request.
     */
    public function edit($id)
    {
        $purchaseRequest = PurchaseRequest::with(['vendor', 'user'])->findOrFail($id);
        
        // Only allow editing if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('admin.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be edited.');
        }

        // Show vendors assigned to this client OR general vendors (no user_id)
        $vendors = Vendor::where(function($query) use ($purchaseRequest) {
            $query->where('user_id', $purchaseRequest->user_id)
                  ->orWhereNull('user_id');
        })
            ->where('is_active', true)
            ->get();
        
        $availableBalance = FundTransaction::getAvailableBalance($purchaseRequest->user_id);
        $exchangeRate = $purchaseRequest->user->exchange_rate ?? 1.0;

        return view('admin.purchase-requests.edit', compact('purchaseRequest', 'vendors', 'availableBalance', 'exchangeRate'));
    }

    /**
     * Update the specified purchase request.
     */
    public function update(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequest::with('user')->findOrFail($id);

        // Only allow editing if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('admin.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be edited.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount_inr' => 'required|numeric|min:0.01',
            'is_gst_payment' => 'nullable|boolean',
            'description' => 'nullable|string|max:1000',
        ]);

        // Verify vendor belongs to the client or is a general vendor
        $vendor = Vendor::where('id', $validated['vendor_id'])
            ->where(function($query) use ($purchaseRequest) {
                $query->where('user_id', $purchaseRequest->user_id)
                      ->orWhereNull('user_id');
            })
            ->firstOrFail();

        $exchangeRate = $purchaseRequest->user->exchange_rate ?? 1.0;
        
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
        $availableBalance = FundTransaction::getAvailableBalance($purchaseRequest->user_id);

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
                'notes' => 'Purchase Order: ' . $purchaseRequest->po_number . ' (Updated by Admin)',
            ]);
        }

        return redirect()->route('admin.purchase-requests.index')
            ->with('success', 'Purchase request updated successfully.');
    }

    /**
     * Remove the specified purchase request.
     */
    public function destroy($id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);

        // Only allow deletion if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return redirect()->route('admin.purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be deleted.');
        }

        // Refund the amount if transaction exists
        if ($purchaseRequest->fundTransaction) {
            $purchaseRequest->fundTransaction->delete();
        }

        $purchaseRequest->delete();

        return redirect()->route('admin.purchase-requests.index')
            ->with('success', 'Purchase request cancelled. Amount has been refunded to client balance.');
    }

    /**
     * Update shipping mark for purchase request.
     */
    public function updateShippingMark(Request $request, $id)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($id);
        
        $validated = $request->validate([
            'shipping_mark' => 'nullable|string|max:255',
        ]);

        $purchaseRequest->update($validated);

        return redirect()->route('admin.purchase-requests.show', $purchaseRequest->id)
            ->with('success', 'Shipping mark updated successfully.');
    }
}
