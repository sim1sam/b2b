<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    /**
     * Display a listing of the customer's vendors.
     */
    public function index()
    {
        // Show vendors assigned to this customer OR general vendors (no user_id)
        $vendors = Vendor::where(function($query) {
            $query->where('user_id', Auth::id())
                  ->orWhereNull('user_id');
        })->latest()->paginate(10);
        return view('customer.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        return view('customer.vendors.create');
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'gstin' => 'nullable|string|max:15',
            'account_details' => 'nullable|string',
            'payment_number' => 'nullable|string|max:20',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Handle QR code upload
        if ($request->hasFile('qr_code')) {
            $validated['qr_code'] = $request->file('qr_code')->store('vendors/qr-codes', 'public');
        }

        // Handle is_active checkbox
        $validated['is_active'] = $request->has('is_active') ? true : false;
        
        // Assign vendor to current user
        $validated['user_id'] = Auth::id();

        Vendor::create($validated);

        return redirect()->route('customer.vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        // Ensure customer can only view their own vendors or general vendors
        if ($vendor->user_id !== null && $vendor->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('customer.vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        // Ensure customer can only edit their own vendors (not general vendors)
        if ($vendor->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('customer.vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        // Ensure customer can only update their own vendors
        if ($vendor->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'vendor_name' => 'required|string|max:255',
            'gstin' => 'nullable|string|max:15',
            'account_details' => 'nullable|string',
            'payment_number' => 'nullable|string|max:20',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

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

        return redirect()->route('customer.vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Ensure customer can only delete their own vendors
        if ($vendor->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        // Delete QR code if exists
        if ($vendor->qr_code) {
            Storage::disk('public')->delete($vendor->qr_code);
        }

        $vendor->delete();

        return redirect()->route('customer.vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
