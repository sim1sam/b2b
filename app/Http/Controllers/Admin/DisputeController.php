<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    /**
     * Display a listing of disputes.
     */
    public function index()
    {
        $disputes = Invoice::whereIn('dispute_status', ['open', 'resolved', 'closed'])
            ->whereNotNull('dispute_opened_at')
            ->with('user')
            ->latest('dispute_opened_at')
            ->paginate(15);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified dispute.
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

        // Calculate remaining time if dispute is open
        $hoursRemaining = 0;
        $minutesRemaining = 0;
        if ($invoice->dispute_status === 'open' && $invoice->dispute_opened_at) {
            $minutesPassed = $invoice->dispute_opened_at->diffInMinutes(now());
            $totalMinutesRemaining = (48 * 60) - $minutesPassed;
            if ($totalMinutesRemaining > 0) {
                $hoursRemaining = floor($totalMinutesRemaining / 60);
                $minutesRemaining = $totalMinutesRemaining % 60;
            }
        }

        return view('admin.disputes.show', compact('invoice', 'hoursRemaining', 'minutesRemaining'));
    }

    /**
     * Add admin response to dispute.
     */
    public function addResponse(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->dispute_status !== 'open' && $invoice->dispute_status !== 'resolved') {
            return redirect()->route('admin.disputes.show', $invoice->id)
                ->with('error', 'This dispute is not active.');
        }

        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
        ]);

        $invoice->update([
            'admin_response' => $validated['admin_response'],
            'dispute_status' => 'resolved',
        ]);

        return redirect()->route('admin.disputes.show', $invoice->id)
            ->with('success', 'Response added successfully.');
    }

    /**
     * Mark dispute as solved.
     */
    public function markAsSolved($id)
    {
        $invoice = Invoice::findOrFail($id);

        if ($invoice->dispute_status !== 'open' && $invoice->dispute_status !== 'resolved') {
            return redirect()->route('admin.disputes.show', $invoice->id)
                ->with('error', 'This dispute is not active.');
        }

        $invoice->update([
            'dispute_status' => 'closed',
            'dispute_resolved_at' => now(),
            'order_status' => 'completed',
        ]);

        return redirect()->route('admin.disputes.show', $invoice->id)
            ->with('success', 'Dispute marked as solved and order status updated to completed.');
    }
}
