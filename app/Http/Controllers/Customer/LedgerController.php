<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\PurchaseRequest;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
    /**
     * Display the customer ledger report.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query conditions
        $dateCondition = function($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        };
        
        // Summary Statistics
        $totalPurchases = PurchaseRequest::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        $totalPurchaseAmount = PurchaseRequest::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalInvoices = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        $totalInvoiceAmount = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('rounded_total');
        
        $totalTransactions = FundTransaction::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        // Financial Summary
        $totalDeposits = FundTransaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalWithdrawals = FundTransaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalPurchasePayments = FundTransaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalInvoicePayments = FundTransaction::where('user_id', $user->id)
            ->where('type', 'invoice_payment')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $currentBalance = FundTransaction::getAvailableBalance($user->id);
        
        // Detailed Records
        $purchases = PurchaseRequest::where('user_id', $user->id)
            ->with('vendor')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $invoices = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $transactions = FundTransaction::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        return view('customer.ledger.index', compact(
            'totalPurchases',
            'totalPurchaseAmount',
            'totalInvoices',
            'totalInvoiceAmount',
            'totalTransactions',
            'totalDeposits',
            'totalWithdrawals',
            'totalPurchasePayments',
            'totalInvoicePayments',
            'currentBalance',
            'purchases',
            'invoices',
            'transactions',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display the ledger report view with print/download options.
     */
    public function view(Request $request)
    {
        $user = Auth::user();
        
        // Get date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query conditions
        $dateCondition = function($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        };
        
        // Detailed Records
        $purchases = PurchaseRequest::where('user_id', $user->id)
            ->with('vendor')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $invoices = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $transactions = FundTransaction::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        // Get admin profile
        $admin = User::where('role', 'admin')->first();
        
        return view('customer.ledger.view', compact(
            'user',
            'purchases',
            'invoices',
            'transactions',
            'startDate',
            'endDate',
            'admin'
        ));
    }

    /**
     * Download ledger report as PDF.
     */
    public function download(Request $request)
    {
        $user = Auth::user();
        
        // Get date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Build query conditions
        $dateCondition = function($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        };
        
        // Summary Statistics
        $totalPurchases = PurchaseRequest::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        $totalPurchaseAmount = PurchaseRequest::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalInvoices = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        $totalInvoiceAmount = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('rounded_total');
        
        $totalTransactions = FundTransaction::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->count();
        
        // Financial Summary
        $totalDeposits = FundTransaction::where('user_id', $user->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalWithdrawals = FundTransaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalPurchasePayments = FundTransaction::where('user_id', $user->id)
            ->where('type', 'purchase')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $totalInvoicePayments = FundTransaction::where('user_id', $user->id)
            ->where('type', 'invoice_payment')
            ->where('status', 'approved')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->sum('amount');
        
        $currentBalance = FundTransaction::getAvailableBalance($user->id);
        
        // Detailed Records
        $purchases = PurchaseRequest::where('user_id', $user->id)
            ->with('vendor')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $invoices = Invoice::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $transactions = FundTransaction::where('user_id', $user->id)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        // Get admin profile
        $admin = User::where('role', 'admin')->first();
        
        $data = [
            'user' => $user,
            'admin' => $admin,
            'totalPurchases' => $totalPurchases,
            'totalPurchaseAmount' => $totalPurchaseAmount,
            'totalInvoices' => $totalInvoices,
            'totalInvoiceAmount' => $totalInvoiceAmount,
            'totalTransactions' => $totalTransactions,
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'totalPurchasePayments' => $totalPurchasePayments,
            'totalInvoicePayments' => $totalInvoicePayments,
            'currentBalance' => $currentBalance,
            'purchases' => $purchases,
            'invoices' => $invoices,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
        
        try {
            $pdf = Pdf::loadView('customer.ledger.pdf', $data);
            
            $filename = 'ledger_report_' . ($startDate ? $startDate : 'all') . '_' . ($endDate ? $endDate : 'all') . '_' . date('YmdHis') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->route('customer.ledger.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}

