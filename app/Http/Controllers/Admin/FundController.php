<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FundController extends Controller
{
    /**
     * Display a listing of fund transactions.
     */
    public function index(Request $request)
    {
        $query = FundTransaction::with('user')->latest();

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Search by user email or name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15);
        
        // Statistics
        $totalPending = FundTransaction::where('status', 'pending')->count();
        $totalApproved = FundTransaction::where('status', 'approved')->count();
        $totalRejected = FundTransaction::where('status', 'rejected')->count();
        $pendingDeposits = FundTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->sum('amount');

        return view('admin.funds.index', compact(
            'transactions',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'pendingDeposits'
        ));
    }

    /**
     * Display the specified fund transaction.
     */
    public function show($fund)
    {
        $fundTransaction = FundTransaction::with('user', 'purchaseRequest')->findOrFail($fund);
        return view('admin.funds.show', compact('fundTransaction'));
    }

    /**
     * Show the form for editing the specified fund transaction.
     */
    public function edit($fund)
    {
        $fundTransaction = FundTransaction::with('user')->findOrFail($fund);
        return view('admin.funds.edit', compact('fundTransaction'));
    }

    /**
     * Update the specified fund transaction.
     */
    public function update(Request $request, $fund)
    {
        $fundTransaction = FundTransaction::findOrFail($fund);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string|max:1000',
        ]);

        // Only allow editing amount and status for deposit transactions
        if ($fundTransaction->type !== 'deposit') {
            return redirect()->back()
                ->with('error', 'Only deposit transactions can be edited.');
        }

        // If rejecting, admin_note is required
        if ($validated['status'] === 'rejected' && empty($validated['admin_note'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['admin_note' => 'Rejection note is required when rejecting a transaction.']);
        }

        $fundTransaction->update([
            'amount' => $validated['amount'],
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'],
        ]);

        $statusMessage = $validated['status'] === 'approved' 
            ? 'Fund transaction approved successfully.' 
            : ($validated['status'] === 'rejected' 
                ? 'Fund transaction rejected.' 
                : 'Fund transaction updated successfully.');

        return redirect()->route('admin.funds.show', $fundTransaction)
            ->with('success', $statusMessage);
    }

    /**
     * Approve a fund transaction.
     */
    public function approve($fundTransaction)
    {
        $transaction = FundTransaction::findOrFail($fundTransaction);
        
        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending transactions can be approved.');
        }

        $transaction->update([
            'status' => 'approved',
            'admin_note' => null,
        ]);

        return redirect()->route('admin.funds.show', $transaction)
            ->with('success', 'Fund transaction approved successfully.');
    }

    /**
     * Reject a fund transaction.
     */
    public function reject(Request $request, $fundTransaction)
    {
        $transaction = FundTransaction::findOrFail($fundTransaction);
        
        $validated = $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);

        if ($transaction->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending transactions can be rejected.');
        }

        $transaction->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
        ]);

        return redirect()->route('admin.funds.show', $transaction)
            ->with('success', 'Fund transaction rejected.');
    }

    /**
     * Remove the specified fund transaction.
     */
    public function destroy($fund)
    {
        $fundTransaction = FundTransaction::findOrFail($fund);
        
        // Delete payment screenshot if exists
        if ($fundTransaction->payment_screenshot) {
            Storage::disk('public')->delete($fundTransaction->payment_screenshot);
        }

        $fundTransaction->delete();

        return redirect()->route('admin.funds.index')
            ->with('success', 'Fund transaction deleted successfully.');
    }
}
