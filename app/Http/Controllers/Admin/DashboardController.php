<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\FundTransaction;
use App\Models\Vendor;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Customers
        $totalCustomers = User::where('role', 'customer')->count();
        
        // Purchase Request Statistics
        $totalPurchaseRequests = PurchaseRequest::count();
        $pendingPurchaseRequests = PurchaseRequest::where('status', 'pending')->count();
        $approvedPurchaseRequests = PurchaseRequest::where('status', 'approved')->count();
        $completedPurchaseRequests = PurchaseRequest::where('status', 'completed')->count();
        
        // Fund Statistics
        $totalPendingFunds = FundTransaction::where('status', 'pending')->count();
        $totalApprovedFunds = FundTransaction::where('status', 'approved')->count();
        $pendingDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');
        
        // Invoice Statistics
        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('payment_status', 'paid')->count();
        $pendingInvoices = Invoice::where('payment_status', 'pending')->count();
        $deliveredInvoices = Invoice::where('delivery_status', 'delivered')->count();
        $completedOrders = Invoice::where('order_status', 'completed')->count();
        $openDisputes = Invoice::where('dispute_status', 'open')->count();
        
        // Vendor Statistics
        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('is_active', true)->count();
        
        // Recent Purchase Requests
        $recentPurchaseRequests = PurchaseRequest::with(['vendor', 'user'])
            ->latest()
            ->limit(10)
            ->get();
        
        // Recent Fund Transactions
        $recentTransactions = FundTransaction::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        // Recent Invoices
        $recentInvoices = Invoice::with('user')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalPurchaseRequests',
            'pendingPurchaseRequests',
            'approvedPurchaseRequests',
            'completedPurchaseRequests',
            'totalPendingFunds',
            'totalApprovedFunds',
            'pendingDeposits',
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
