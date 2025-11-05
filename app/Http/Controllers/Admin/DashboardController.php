<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\FundTransaction;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // First Row Metrics
        // Available Funds (BDT) - Sum of all clients' available balances
        $availableFundsBDT = 0;
        $customers = User::where('role', 'customer')->get();
        foreach ($customers as $customer) {
            $availableFundsBDT += FundTransaction::getAvailableBalance($customer->id);
        }
        
        // PO Fund (BDT) - BDT value of pending purchase requests that are not paid to vendor
        $poFundBDT = PurchaseRequest::where('payment_status', '!=', 'paid')
            ->orWhereNull('payment_status')
            ->sum('amount_bdt');
        
        // PO Value (INR) - INR value of purchase requests that are not paid to vendor
        $poValueINR = PurchaseRequest::where(function($query) {
                $query->where('payment_status', '!=', 'paid')
                      ->orWhereNull('payment_status');
            })
            ->sum('amount_inr');
        
        // GST Earning (BDT) - 5% of amount_inr × exchange rate for non-GST purchase requests (paid and payment pending only)
        $gstEarningBDT = 0;
        $nonGstRequests = PurchaseRequest::where('is_gst_payment', false)
            ->where(function($query) {
                $query->where('payment_status', 'paid')
                      ->orWhere('payment_status', 'pending')
                      ->orWhereNull('payment_status'); // Include null as pending
            })
            ->with('user')
            ->get();
        
        foreach ($nonGstRequests as $request) {
            if ($request->user && $request->user->exchange_rate && $request->amount_inr) {
                // GST calculation: 5% of amount_inr × exchange rate
                $gstEarningBDT += ($request->amount_inr * 0.05) * $request->user->exchange_rate;
            }
        }
        
        // Second Row: Deposit Statistics
        $pendingDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');
        
        $todayDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'approved')
            ->whereDate('created_at', today())
            ->sum('amount');
        
        $thisMonthDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
        
        $lastMonthDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');
        
        // Third Row: Purchase Request Statistics
        $totalPurchaseRequests = PurchaseRequest::count();
        $pendingPurchaseRequests = PurchaseRequest::where('status', 'pending')->count();
        $approvedPurchaseRequests = PurchaseRequest::where('status', 'approved')->count();
        $completedPurchaseRequests = PurchaseRequest::where('status', 'completed')->count();
        
        // Fourth Row: Invoice Metrics
        // Due Invoices (BDT) - Value of payment pending invoices
        $dueInvoicesBDT = Invoice::where('payment_status', 'pending')
            ->sum('rounded_total');
        
        // Provisional Invoices (BDT) - Purchase requests with items but no invoice
        $provisionalInvoicesBDT = PurchaseRequest::whereHas('items')
            ->whereDoesntHave('invoices')
            ->sum('amount_bdt');
        
        // Delivery Pending - Count of invoices that are paid and need to be delivered, or have open dispute window
        $deliveryPending = Invoice::where(function($query) {
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
        
        // Completed - Count of successful deliveries or invoices with closed dispute window
        $completedDeliveries = Invoice::where(function($query) {
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
        
        // Last 30 Days Data for Graph
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $deposits = FundTransaction::where('type', 'deposit')
                ->where('status', 'approved')
                ->whereDate('created_at', $dateStr)
                ->sum('amount');
            
            $purchaseOrders = PurchaseRequest::whereDate('created_at', $dateStr)
                ->sum('amount_bdt');
            
            $invoiceValue = Invoice::whereDate('created_at', $dateStr)
                ->sum('rounded_total');
            
            $last30Days[] = [
                'date' => $date->format('M d'),
                'deposits' => $deposits,
                'purchase_orders' => $purchaseOrders,
                'invoice_value' => $invoiceValue,
            ];
        }
        
        // Top Clients with Order Trend
        $topClients = User::where('role', 'customer')
            ->withCount([
                'purchaseRequests as total_orders',
                'purchaseRequests as recent_orders' => function($query) {
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                }
            ])
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->get()
            ->map(function($client) {
                $client->total_amount = PurchaseRequest::where('user_id', $client->id)
                    ->sum('amount_bdt');
                $client->recent_amount = PurchaseRequest::where('user_id', $client->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(30))
                    ->sum('amount_bdt');
                return $client;
            });
        
        return view('admin.dashboard', compact(
            // First Row
            'availableFundsBDT',
            'poFundBDT',
            'poValueINR',
            'gstEarningBDT',
            // Second Row
            'pendingDeposits',
            'todayDeposits',
            'thisMonthDeposits',
            'lastMonthDeposits',
            // Third Row
            'totalPurchaseRequests',
            'pendingPurchaseRequests',
            'approvedPurchaseRequests',
            'completedPurchaseRequests',
            // Fourth Row
            'dueInvoicesBDT',
            'provisionalInvoicesBDT',
            'deliveryPending',
            'completedDeliveries',
            // Graph Data
            'last30Days',
            // Top Clients
            'topClients'
        ));
    }
}
