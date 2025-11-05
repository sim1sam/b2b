<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::with('user')->latest()->paginate(10);
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = \App\Models\User::where('role', 'customer')->orderBy('business_name')->orderBy('name')->get();
        return view('admin.vendors.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'vendor_name' => 'required|string|max:255',
            'gstin' => 'nullable|string|max:15',
            'account_details' => 'nullable|string',
            'payment_number' => 'nullable|string|max:20',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Ensure user_id belongs to a customer if provided
        if (isset($validated['user_id']) && $validated['user_id']) {
            $user = \App\Models\User::find($validated['user_id']);
            if ($user && $user->role !== 'customer') {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_id' => 'Selected user must be a customer.']);
            }
        } else {
            // Set to null if not provided
            $validated['user_id'] = null;
        }

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            $validated['qr_code'] = $request->file('qr_code')->store('vendors/qr-codes', 'public');
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Vendor::create($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('admin.vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        $clients = \App\Models\User::where('role', 'customer')->orderBy('business_name')->orderBy('name')->get();
        return view('admin.vendors.edit', compact('vendor', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'vendor_name' => 'required|string|max:255',
            'gstin' => 'nullable|string|max:15',
            'account_details' => 'nullable|string',
            'payment_number' => 'nullable|string|max:20',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Ensure user_id belongs to a customer if provided
        if (isset($validated['user_id']) && $validated['user_id']) {
            $user = \App\Models\User::find($validated['user_id']);
            if ($user && $user->role !== 'customer') {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_id' => 'Selected user must be a customer.']);
            }
        } else {
            // Set to null if not provided
            $validated['user_id'] = null;
        }

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($vendor->qr_code) {
                Storage::disk('public')->delete($vendor->qr_code);
            }
            $validated['qr_code'] = $request->file('qr_code')->store('vendors/qr-codes', 'public');
        } else {
            // Keep existing QR code if no new file uploaded
            unset($validated['qr_code']);
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $vendor->update($validated);

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Delete QR code if exists
        if ($vendor->qr_code) {
            Storage::disk('public')->delete($vendor->qr_code);
        }

        $vendor->delete();

        return redirect()->route('admin.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
