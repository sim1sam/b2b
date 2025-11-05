<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index()
    {
        $clients = User::where('role', 'customer')
            ->latest()
            ->paginate(15);
        
        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create()
    {
        return view('admin.clients.create');
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'lowest_shipping_charge_per_kg' => 'required|numeric|min:0',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
        }

        // Hash password and set role
        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'customer';

        $client = User::create($validated);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client created successfully.');
    }

    /**
     * Display the specified client.
     */
    public function show($id)
    {
        $client = User::findOrFail($id);
        
        if ($client->role !== 'customer') {
            abort(404, 'Client not found.');
        }
        
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit($id)
    {
        $client = User::findOrFail($id);
        
        if ($client->role !== 'customer') {
            abort(404, 'Client not found.');
        }
        
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client.
     */
    public function update(Request $request, $id)
    {
        $client = User::findOrFail($id);
        
        if ($client->role !== 'customer') {
            abort(404, 'Client not found.');
        }
        
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'contact_person_name' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'address' => 'nullable|string',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'lowest_shipping_charge_per_kg' => 'required|numeric|min:0',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($client->logo) {
                Storage::disk('public')->delete($client->logo);
            }
            $validated['logo'] = $request->file('logo')->store('clients/logos', 'public');
        } else {
            // Keep existing logo if not uploading new one
            unset($validated['logo']);
        }

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client profile updated successfully.');
    }
}
