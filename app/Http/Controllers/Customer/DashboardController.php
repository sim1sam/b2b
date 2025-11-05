<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\PurchaseRequest;
use App\Models\Vendor;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Available Funds
        $availableBalance = FundTransaction::getAvailableBalance($user->id);
        
        // Purchase Statistics
        $totalPurchaseINR = PurchaseRequest::where('user_id', $user->id)
            ->sum('amount_inr');
        $totalPurchaseBDT = PurchaseRequest::where('user_id', $user->id)
            ->sum('amount_bdt');
        
        // Invoice Statistics
        $totalInvoicesBDT = Invoice::where('user_id', $user->id)
            ->sum('rounded_total');
        
        // Purchase Request Statistics
        $totalPurchaseRequests = PurchaseRequest::where('user_id', $user->id)->count();
        $pendingPurchaseRequests = PurchaseRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $approvedPurchaseRequests = PurchaseRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        $completedPurchaseRequests = PurchaseRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Third Row: Invoice Metrics
        // Due Invoices (BDT) - Value of payment pending invoices
        $dueInvoicesBDT = Invoice::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->sum('rounded_total');
        
        // Provisional Invoices (BDT) - Purchase requests with items but no invoice
        $provisionalInvoicesBDT = PurchaseRequest::where('user_id', $user->id)
            ->whereHas('items')
            ->whereDoesntHave('invoices')
            ->sum('amount_bdt');
        
        // Delivery Pending - Count of paid invoices needing delivery, or have open dispute window
        $deliveryPending = Invoice::where('user_id', $user->id)
            ->where(function($query) {
                $query->where(function($q) {
                    // Paid invoices that need to be delivered
                    $q->where('payment_status', 'paid')
                      ->where('delivery_status', '!=', 'delivered');
                })->orWhere(function($q) {
                    // Invoices with open dispute window
                    $q->where('dispute_status', 'open');
                });
            })
            ->count();
        
        // Shipment Completed - Count of successful deliveries or invoices with closed dispute window
        $shipmentCompleted = Invoice::where('user_id', $user->id)
            ->where(function($query) {
                $query->where(function($q) {
                    // Successful deliveries
                    $q->where('delivery_status', 'delivered')
                      ->where('order_status', 'completed');
                })->orWhere(function($q) {
                    // Invoices with closed dispute window
                    $q->where('dispute_status', 'closed');
                });
            })
            ->count();
        
        return view('customer.dashboard', compact(
            'availableBalance',
            'totalPurchaseINR',
            'totalPurchaseBDT',
            'totalInvoicesBDT',
            'totalPurchaseRequests',
            'pendingPurchaseRequests',
            'approvedPurchaseRequests',
            'completedPurchaseRequests',
            'dueInvoicesBDT',
            'provisionalInvoicesBDT',
            'deliveryPending',
            'shipmentCompleted'
        ));
    }
}
