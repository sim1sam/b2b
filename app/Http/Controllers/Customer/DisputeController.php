<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    /**
     * Display a listing of disputes.
     */
    public function index()
    {
        $disputes = Invoice::where('user_id', Auth::id())
            ->whereIn('dispute_status', ['open', 'resolved', 'closed'])
            ->whereNotNull('dispute_opened_at')
            ->with('user')
            ->latest('dispute_opened_at')
            ->paginate(15);

        return view('customer.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified dispute.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['user', 'purchaseRequests.items.shippingCharge', 'purchaseRequests.vendor'])
            ->findOrFail($id);

        // Ensure customer can only view their own disputes
        if ($invoice->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Check if this invoice has a dispute
        if (!$invoice->dispute_opened_at) {
            return redirect()->route('customer.disputes.index')
                ->with('error', 'This invoice does not have a dispute.');
        }

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

        // Get admin profile
        $admin = \App\Models\User::where('role', 'admin')->first();

        return view('customer.disputes.show', compact('invoice', 'hoursRemaining', 'minutesRemaining', 'admin'));
    }
}
