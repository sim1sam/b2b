<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FundController extends Controller
{
    /**
     * Display fund management dashboard.
     */
    public function index()
    {
        $transactions = FundTransaction::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        $totalDeposits = FundTransaction::where('user_id', Auth::id())
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');
        $totalSpent = FundTransaction::where('user_id', Auth::id())
            ->where('type', 'purchase')
            ->where('status', 'approved')
            ->sum('amount');
        $pendingDeposits = FundTransaction::where('user_id', Auth::id())
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');

        return view('customer.funds.index', compact(
            'transactions',
            'availableBalance',
            'totalDeposits',
            'totalSpent',
            'pendingDeposits'
        ));
    }

    /**
     * Show the form for adding funds.
     */
    public function create()
    {
        $availableBalance = FundTransaction::getAvailableBalance(Auth::id());
        return view('customer.funds.create', compact('availableBalance'));
    }

    /**
     * Store a newly created fund deposit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        // Handle payment screenshot upload
        if ($request->hasFile('payment_screenshot')) {
            $validated['payment_screenshot'] = $request->file('payment_screenshot')
                ->store('funds/payment-screenshots', 'public');
        }

        $validated['user_id'] = Auth::id();
        $validated['type'] = 'deposit';
        $validated['status'] = 'pending';

        FundTransaction::create($validated);

        return redirect()->route('customer.funds.index')
            ->with('success', 'Fund deposit request submitted successfully. It will be reviewed by admin.');
    }

    /**
     * Display the specified transaction.
     */
    public function show($fund)
    {
        $fundTransaction = FundTransaction::findOrFail($fund);
        
        // Ensure customer can only view their own transactions
        if ($fundTransaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('customer.funds.show', compact('fundTransaction'));
    }
}
