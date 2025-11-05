<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use App\Models\User;
use Illuminate\Http\Request;

class ShippingChargeController extends Controller
{
    /**
     * Display a listing of shipping charges.
     */
    public function index(Request $request)
    {
        $query = ShippingCharge::with('user');

        // Filter by client
        if ($request->has('client_id') && $request->client_id !== '') {
            $query->where('user_id', $request->client_id);
        }

        $shippingCharges = $query->orderBy('user_id')->orderBy('item_name')->paginate(15);
        $clients = User::where('role', 'customer')->orderBy('name')->get();

        return view('admin.shipping-charges.index', compact('shippingCharges', 'clients'));
    }

    /**
     * Show the form for creating a new shipping charge.
     */
    public function create()
    {
        $clients = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.shipping-charges.create', compact('clients'));
    }

    /**
     * Store a newly created shipping charge in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_name' => 'required|string|max:255',
            'rate_per_unit' => 'required|numeric|min:0',
            'unit_type' => 'required|in:qty,weight',
            'shipping_charge_per_kg' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Verify client is a customer
        $client = User::findOrFail($validated['user_id']);
        if ($client->role !== 'customer') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Selected user is not a client.']);
        }

        ShippingCharge::create($validated);

        return redirect()->route('admin.shipping-charges.index')
            ->with('success', 'Shipping charge created successfully.');
    }

    /**
     * Display the specified shipping charge.
     */
    public function show($id)
    {
        $shippingCharge = ShippingCharge::with('user')->findOrFail($id);
        return view('admin.shipping-charges.show', compact('shippingCharge'));
    }

    /**
     * Show the form for editing the specified shipping charge.
     */
    public function edit($id)
    {
        $shippingCharge = ShippingCharge::with('user')->findOrFail($id);
        $clients = User::where('role', 'customer')->orderBy('name')->get();
        return view('admin.shipping-charges.edit', compact('shippingCharge', 'clients'));
    }

    /**
     * Update the specified shipping charge in storage.
     */
    public function update(Request $request, $id)
    {
        $shippingCharge = ShippingCharge::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_name' => 'required|string|max:255',
            'rate_per_unit' => 'required|numeric|min:0',
            'unit_type' => 'required|in:qty,weight',
            'shipping_charge_per_kg' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Verify client is a customer
        $client = User::findOrFail($validated['user_id']);
        if ($client->role !== 'customer') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Selected user is not a client.']);
        }

        $shippingCharge->update($validated);

        return redirect()->route('admin.shipping-charges.index')
            ->with('success', 'Shipping charge updated successfully.');
    }

    /**
     * Remove the specified shipping charge from storage.
     */
    public function destroy($id)
    {
        $shippingCharge = ShippingCharge::findOrFail($id);
        $shippingCharge->delete();

        return redirect()->route('admin.shipping-charges.index')
            ->with('success', 'Shipping charge deleted successfully.');
    }
}
