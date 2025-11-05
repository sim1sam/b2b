<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        $invoices = Invoice::with(['user', 'purchaseRequests'])
            ->latest()
            ->paginate(15);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Generate invoice from shipping mark.
     */
    public function generate(Request $request, $mark)
    {
        $decodedMark = urldecode($mark);
        
        $purchaseRequests = PurchaseRequest::where('shipping_mark', $decodedMark)
            ->with(['user', 'items.shippingCharge'])
            ->get();

        if ($purchaseRequests->isEmpty()) {
            return redirect()->route('admin.shipping-marks.index')
                ->with('error', 'No purchase requests found for this shipping mark.');
        }

        // Check if invoice already exists
        $existingInvoice = $purchaseRequests->first()->invoices()->first();
        if ($existingInvoice) {
            return redirect()->route('admin.invoices.show', $existingInvoice->id)
                ->with('info', 'Invoice already exists for this shipping mark.');
        }

        $client = $purchaseRequests->first()->user;

        DB::beginTransaction();
        try {
            // Calculate total amount
            $totalAmount = 0;
            
            foreach ($purchaseRequests as $pr) {
                if ($pr->items->isNotEmpty()) {
                    $itemsTotal = $pr->items->sum('total_cost');
                    $netWeight = $pr->items()
                        ->whereHas('shippingCharge', function($query) {
                            $query->where('unit_type', 'weight');
                        })
                        ->sum('weight');
                    $packagingWeight = $netWeight < 20 ? $netWeight * 0.10 : $netWeight * 0.08;
                    $lowestShippingCharge = $pr->user->lowest_shipping_charge_per_kg ?? 0;
                    $packagingCost = $packagingWeight * $lowestShippingCharge;
                    $transportationCharge = $pr->transportation_charge ?? 0;
                    $totalCost = $itemsTotal + $packagingCost + $transportationCharge;
                    $totalAmount += $totalCost;
                }
            }

            $roundedTotal = floor($totalAmount / 50) * 50;

            // Create invoice
            $invoice = Invoice::create([
                'user_id' => $client->id,
                'shipping_mark' => $decodedMark,
                'total_amount' => $totalAmount,
                'rounded_total' => $roundedTotal,
                'invoice_date' => now(),
            ]);

            // Attach purchase requests
            $invoice->purchaseRequests()->attach($purchaseRequests->pluck('id')->toArray());

            DB::commit();

            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('success', 'Invoice generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['user', 'purchaseRequests.items.shippingCharge', 'purchaseRequests.vendor'])
            ->findOrFail($id);

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

        return view('admin.invoices.show', compact(
            'invoice',
            'admin',
            'allItems',
            'itemsTotalCost',
            'totalNetWeight',
            'packagingWeight',
            'packagingCost',
            'lowestShippingCharge',
            'totalTransportationCharge'
        ));
    }

    /**
     * Mark invoice as delivered.
     */
    public function markAsDelivered($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Check if delivery was requested
        if ($invoice->delivery_request_status !== 'requested') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Delivery request not submitted by client yet.');
        }

        // Check if already delivered
        if ($invoice->delivery_status === 'delivered') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('info', 'Invoice is already marked as delivered.');
        }

        $invoice->update([
            'delivery_status' => 'delivered',
            'dispute_status' => 'open',
            'dispute_opened_at' => now(),
        ]);

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Invoice marked as delivered. Dispute window opened for 48 hours.');
    }

    /**
     * Complete order.
     */
    public function completeOrder($id)
    {
        $invoice = Invoice::findOrFail($id);

        // Check if delivered
        if ($invoice->delivery_status !== 'delivered') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Invoice must be delivered first.');
        }

        // Check if dispute window is still open
        if ($invoice->dispute_status === 'open') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('error', 'Cannot complete order while dispute window is open. Wait for dispute window to close or resolve dispute first.');
        }

        // Check if already completed
        if ($invoice->order_status === 'completed') {
            return redirect()->route('admin.invoices.show', $invoice->id)
                ->with('info', 'Order is already completed.');
        }

        $invoice->update([
            'order_status' => 'completed',
        ]);

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Order marked as completed.');
    }
}
