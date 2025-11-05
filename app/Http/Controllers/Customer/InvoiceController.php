<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\FundTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices for the customer.
     */
    public function index(Request $request)
    {
        $query = Invoice::where('user_id', Auth::id())
            ->with(['purchaseRequests']);

        // Filter by payment_status
        $paymentStatusFilter = $request->has('payment_status') && $request->payment_status !== '';
        if ($paymentStatusFilter) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by delivery_status
        if ($request->has('delivery_status') && $request->delivery_status !== '') {
            if ($request->delivery_status === 'pending') {
                // Include paid invoices that need delivery OR invoices with open dispute window
                $query->where(function($q) use ($paymentStatusFilter) {
                    $q->where(function($subQ) use ($paymentStatusFilter) {
                        // If payment_status filter is already applied, only check delivery status
                        if ($paymentStatusFilter) {
                            $subQ->where('delivery_status', '!=', 'delivered');
                        } else {
                            $subQ->where('payment_status', 'paid')
                                 ->where('delivery_status', '!=', 'delivered');
                        }
                    })->orWhere('dispute_status', 'open');
                });
            } else {
                $query->where('delivery_status', $request->delivery_status);
            }
        }

        // Filter by order_status
        if ($request->has('order_status') && $request->order_status !== '') {
            if ($request->order_status === 'completed') {
                // Include completed orders OR invoices with closed dispute window
                $query->where(function($q) {
                    $q->where('order_status', 'completed')
                      ->orWhere('dispute_status', 'closed');
                });
            } else {
                $query->where('order_status', $request->order_status);
            }
        }

        $invoices = $query->latest()->paginate(15);

        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());

        // Calculate dispute window status for each invoice
        foreach ($invoices as $invoice) {
            // Auto-close dispute window if 48 hours have passed
            if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
                $hoursPassed = $invoice->dispute_opened_at->diffInHours(now());
                if ($hoursPassed >= 48) {
                    $invoice->update([
                        'dispute_status' => 'closed',
                        'order_status' => 'completed',
                    ]);
                    $invoice->refresh();
                }
            }

            // Check if dispute window is open
            $invoice->dispute_window_open = false;
            $invoice->hours_remaining = 0;
            $invoice->minutes_remaining = 0;
            
            if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
                $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
                $totalMinutesRemaining = (48 * 60) - $minutesPassed;
                if ($totalMinutesRemaining > 0) {
                    $invoice->dispute_window_open = true;
                    $invoice->hours_remaining = floor($totalMinutesRemaining / 60);
                    $invoice->minutes_remaining = $totalMinutesRemaining % 60;
                }
            }
        }

        return view('customer.invoices.index', compact('invoices', 'availableBalance'));
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['purchaseRequests.items.shippingCharge', 'purchaseRequests.vendor', 'user'])
            ->findOrFail($id);

        // Ensure customer can only view their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Auto-close dispute window if 48 hours have passed
        if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
            $hoursPassed = $invoice->dispute_opened_at->diffInHours(now());
            if ($hoursPassed >= 48) {
                $invoice->update([
                    'dispute_status' => 'closed',
                    'order_status' => 'completed',
                ]);
                $invoice->refresh();
            }
        }

        // Get admin profile
        $admin = User::where('role', 'admin')->first();

        // Calculate all items and totals
        $allItems = collect();
        $itemsTotalCost = 0;
        $totalNetWeight = 0;
        $totalTransportationCharge = 0;

        foreach ($invoice->purchaseRequests as $pr) {
            foreach ($pr->items as $item) {
                $allItems->push($item);
            }
            
            $itemsTotalCost += $pr->items->sum('total_cost');
            
            $netWeight = $pr->items()
                ->whereHas('shippingCharge', function($query) {
                    $query->where('unit_type', 'weight');
                })
                ->sum('weight');
            $totalNetWeight += $netWeight;
            
            $totalTransportationCharge += $pr->transportation_charge ?? 0;
        }

        $packagingWeight = $totalNetWeight < 20 ? $totalNetWeight * 0.10 : $totalNetWeight * 0.08;
        $lowestShippingCharge = $invoice->user->lowest_shipping_charge_per_kg ?? 0;
        $packagingCost = $packagingWeight * $lowestShippingCharge;

        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());

        // Check if dispute window is open and calculate remaining time
        $disputeWindowOpen = false;
        $hoursRemaining = 0;
        $minutesRemaining = 0;
        if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
            $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
            $totalMinutesRemaining = (48 * 60) - $minutesPassed;
            if ($totalMinutesRemaining > 0) {
                $disputeWindowOpen = true;
                $hoursRemaining = floor($totalMinutesRemaining / 60);
                $minutesRemaining = $totalMinutesRemaining % 60;
            } else {
                // Auto-close dispute window if 48 hours passed
                $invoice->update(['dispute_status' => 'closed']);
            }
        }

        return view('customer.invoices.show', compact(
            'invoice',
            'admin',
            'allItems',
            'itemsTotalCost',
            'totalNetWeight',
            'packagingWeight',
            'packagingCost',
            'lowestShippingCharge',
            'totalTransportationCharge',
            'availableBalance',
            'disputeWindowOpen',
            'hoursRemaining',
            'minutesRemaining'
        ));
    }

    /**
     * Process invoice payment.
     */
    public function pay(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Ensure customer can only pay their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if already paid
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Invoice is already paid.');
        }

        // Check available balance
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        
        if ($availableBalance < $invoice->rounded_total) {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Insufficient funds. Available balance: ৳' . number_format($availableBalance, 2) . '. Please add funds first.');
        }

        DB::beginTransaction();
        try {
            // Create fund transaction for invoice payment
            $transaction = FundTransaction::create([
                'user_id' => Auth::id(),
                'invoice_id' => $invoice->id,
                'amount' => $invoice->rounded_total,
                'type' => 'invoice_payment',
                'status' => 'approved', // Auto-approved since deducted from available funds
                'notes' => 'Payment for Invoice: ' . $invoice->invoice_number,
            ]);

            // Update invoice payment status
            $invoice->update([
                'payment_status' => 'paid',
            ]);

            DB::commit();

            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('success', 'Invoice payment successful. Amount deducted from your available funds.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Request delivery for paid invoice.
     */
    public function requestDelivery(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Ensure customer can only request delivery for their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if invoice is paid
        if ($invoice->payment_status !== 'paid') {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Please pay the invoice first before requesting delivery.');
        }

        // Check if delivery already requested
        if ($invoice->delivery_request_status === 'requested') {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('info', 'Delivery request already submitted.');
        }

        $invoice->update([
            'delivery_request_status' => 'requested',
        ]);

        return redirect()->route('customer.invoices.show', $invoice->id)
            ->with('success', 'Delivery request submitted successfully.');
    }

    /**
     * Submit dispute note.
     */
    public function submitDispute(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Ensure customer can only dispute their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if dispute window is open
        if ($invoice->dispute_status !== 'open') {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Dispute window is not open.');
        }

        // Check if 48 hours have passed
        if ($invoice->dispute_opened_at && $invoice->dispute_opened_at->diffInHours(now()) >= 48) {
            // Auto-close dispute window
            $invoice->update([
                'dispute_status' => 'closed',
            ]);
            
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Dispute window has closed. 48 hours have passed since delivery.');
        }

        $validated = $request->validate([
            'dispute_note' => 'required|string|max:1000',
        ]);

        $invoice->update([
            'dispute_note' => $validated['dispute_note'],
            'dispute_status' => 'resolved', // Mark as resolved when dispute is submitted
        ]);

        return redirect()->route('customer.invoices.show', $invoice->id)
            ->with('success', 'Dispute note submitted successfully. Admin will review your concern.');
    }

    /**
     * Mark invoice as received in good condition.
     */
    public function markAsReceived($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Ensure customer can only mark their own invoices
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if dispute window is open
        if ($invoice->dispute_status !== 'open') {
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Dispute window is not open.');
        }

        // Check if 48 hours have passed
        if ($invoice->dispute_opened_at && $invoice->dispute_opened_at->diffInHours(now()) >= 48) {
            // Auto-close dispute window
            $invoice->update([
                'dispute_status' => 'closed',
            ]);
            
            return redirect()->route('customer.invoices.show', $invoice->id)
                ->with('error', 'Dispute window has closed. 48 hours have passed since delivery.');
        }

        $invoice->update([
            'dispute_status' => 'resolved',
        ]);

        return redirect()->route('customer.invoices.show', $invoice->id)
            ->with('success', 'Order marked as received in good condition.');
    }
}
