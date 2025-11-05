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
        
        // Fund Statistics
        $availableBalance = FundTransaction::getAvailableBalance($user->id);
        $totalDeposits = FundTransaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');
        $totalSpent = FundTransaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'approved')
            ->sum('amount');
        $invoicePayments = FundTransaction::where('user_id', $user->id)
            ->where('type', 'invoice_payment')
            ->where('status', 'approved')
            ->sum('amount');
        
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
        
        // Invoice Statistics
        $totalInvoices = Invoice::where('user_id', $user->id)->count();
        $paidInvoices = Invoice::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->count();
        $pendingInvoices = Invoice::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->count();
        $deliveredInvoices = Invoice::where('user_id', $user->id)
            ->where('delivery_status', 'delivered')
            ->count();
        $completedOrders = Invoice::where('user_id', $user->id)
            ->where('order_status', 'completed')
            ->count();
        $openDisputes = Invoice::where('user_id', $user->id)
            ->where('dispute_status', 'open')
            ->count();
        
        // Vendor Statistics
        $totalVendors = Vendor::where('user_id', $user->id)->count();
        $activeVendors = Vendor::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        // Recent Purchase Requests
        $recentPurchaseRequests = PurchaseRequest::where('user_id', $user->id)
            ->with('vendor')
            ->latest()
            ->limit(5)
            ->get();
        
        // Recent Transactions
        $recentTransactions = FundTransaction::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        
        // Recent Invoices
        $recentInvoices = Invoice::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        
        return view('customer.dashboard', compact(
            'availableBalance',
            'totalDeposits',
            'totalSpent',
            'invoicePayments',
            'totalPurchaseRequests',
            'pendingPurchaseRequests',
            'approvedPurchaseRequests',
            'completedPurchaseRequests',
            'totalVendors',
            'activeVendors',
            'recentPurchaseRequests',
            'recentTransactions',
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices',
            'deliveredInvoices',
            'completedOrders',
            'openDisputes',
            'recentInvoices'
        ));
    }
}
