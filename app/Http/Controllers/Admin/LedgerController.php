<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use App\Models\PurchaseRequest;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
    /**
     * Display customer selection for ledger report.
     */
    public function index(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get();
        
        $selectedCustomerId = $request->get('customer_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $ledgerData = null;
        $selectedCustomer = null;
        
        if ($selectedCustomerId) {
            $selectedCustomer = User::findOrFail($selectedCustomerId);
            
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
            $totalPurchases = PurchaseRequest::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->count();
            
            $totalPurchaseAmount = PurchaseRequest::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('amount');
            
            $totalInvoices = Invoice::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->count();
            
            $totalInvoiceAmount = Invoice::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('rounded_total');
            
            $totalTransactions = FundTransaction::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->count();
            
            // Financial Summary
            $totalDeposits = FundTransaction::where('user_id', $selectedCustomerId)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('amount');
            
            $totalWithdrawals = FundTransaction::where('user_id', $selectedCustomerId)
                ->where('type', 'withdrawal')
                ->where('status', 'approved')
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('amount');
            
            $totalPurchasePayments = FundTransaction::where('user_id', $selectedCustomerId)
                ->where('type', 'purchase')
                ->where('status', 'approved')
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('amount');
            
            $totalInvoicePayments = FundTransaction::where('user_id', $selectedCustomerId)
                ->where('type', 'invoice_payment')
                ->where('status', 'approved')
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->sum('amount');
            
            $currentBalance = FundTransaction::getAvailableBalance($selectedCustomerId);
            
            // Detailed Records
            $purchases = PurchaseRequest::where('user_id', $selectedCustomerId)
                ->with('vendor')
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->latest()
                ->get();
            
            $invoices = Invoice::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->latest()
                ->get();
            
            $transactions = FundTransaction::where('user_id', $selectedCustomerId)
                ->when($startDate || $endDate, function($query) use ($dateCondition) {
                    $dateCondition($query);
                })
                ->latest()
                ->get();
            
            $ledgerData = [
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
            ];
        }
        
        return view('admin.ledger.index', compact(
            'customers',
            'selectedCustomer',
            'selectedCustomerId',
            'ledgerData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display the ledger report view with print/download options.
     */
    public function view(Request $request, $customerId)
    {
        $customer = User::findOrFail($customerId);
        
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
        $purchases = PurchaseRequest::where('user_id', $customerId)
            ->with('vendor')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $invoices = Invoice::where('user_id', $customerId)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $transactions = FundTransaction::where('user_id', $customerId)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        // Get admin profile
        $admin = User::where('role', 'admin')->first();
        
        return view('admin.ledger.view', compact(
            'customer',
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
    public function download(Request $request, $customerId)
    {
        $customer = User::findOrFail($customerId);
        
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
        $purchases = PurchaseRequest::where('user_id', $customerId)
            ->with('vendor')
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $invoices = Invoice::where('user_id', $customerId)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        $transactions = FundTransaction::where('user_id', $customerId)
            ->when($startDate || $endDate, function($query) use ($dateCondition) {
                $dateCondition($query);
            })
            ->latest()
            ->get();
        
        // Get admin profile
        $admin = User::where('role', 'admin')->first();
        
        $data = [
            'user' => $customer,
            'admin' => $admin,
            'purchases' => $purchases,
            'invoices' => $invoices,
            'transactions' => $transactions,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
        
        try {
            $pdf = Pdf::loadView('admin.ledger.pdf', $data);
            
            $filename = 'ledger_' . $customer->name . '_' . ($startDate ? $startDate : 'all') . '_' . ($endDate ? $endDate : 'all') . '_' . date('YmdHis') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return redirect()->route('admin.ledger.index')
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}

