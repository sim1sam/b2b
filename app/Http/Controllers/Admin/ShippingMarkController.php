<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ShippingMarkController extends Controller
{
    /**
     * Display a listing of shipping marks.
     */
    public function index()
    {
        // Get all unique shipping marks with their purchase requests
        $shippingMarks = PurchaseRequest::whereNotNull('shipping_mark')
            ->where('shipping_mark', '!=', '')
            ->with('user')
            ->get()
            ->groupBy('shipping_mark')
            ->map(function ($purchaseRequests, $mark) {
                return [
                    'mark' => $mark,
                    'client' => $purchaseRequests->first()->user,
                    'count' => $purchaseRequests->count(),
                    'purchase_requests' => $purchaseRequests,
                    'has_invoice' => $purchaseRequests->first()->invoices()->count() > 0,
                ];
            })
            ->sortBy('mark')
            ->values();

        return view('admin.shipping-marks.index', compact('shippingMarks'));
    }

    /**
     * Show purchase requests for a specific shipping mark.
     */
    public function show($mark)
    {
        $decodedMark = urldecode($mark);
        
        $purchaseRequests = PurchaseRequest::where('shipping_mark', $decodedMark)
            ->with(['user', 'vendor', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($purchaseRequests->isEmpty()) {
            return redirect()->route('admin.shipping-marks.index')
                ->with('error', 'Shipping mark not found.');
        }

        $client = $purchaseRequests->first()->user;
        
        // Check if invoice already exists
        $hasInvoice = $purchaseRequests->first()->invoices()->exists();
        
        // Calculate totals
        $totalAmount = 0;
        $allItems = collect();
        
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
                
                foreach ($pr->items as $item) {
                    $allItems->push($item);
                }
            }
        }

        $roundedTotal = floor($totalAmount / 50) * 50;
        $roundOffAmount = $totalAmount - $roundedTotal;

        return view('admin.shipping-marks.show', compact(
            'decodedMark',
            'purchaseRequests',
            'client',
            'hasInvoice',
            'totalAmount',
            'roundedTotal',
            'roundOffAmount',
            'allItems'
        ));
    }
}
