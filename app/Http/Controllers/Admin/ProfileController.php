<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the admin profile.
     */
    public function edit()
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            // If no admin exists, use current logged in user if admin
            $admin = Auth::user();
            if ($admin->role !== 'admin') {
                abort(403, 'Unauthorized access.');
            }
        }
        
        return view('admin.profile.edit', compact('admin'));
    }

    /**
     * Update the admin profile.
     */
    public function update(Request $request)
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            // If no admin exists, use current logged in user if admin
            $admin = Auth::user();
            if ($admin->role !== 'admin') {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'admin_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'system_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:2048',
            'favicon' => 'nullable|mimes:ico,png|max:512',
            'app_name' => 'nullable|string|max:100',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'mobile_number' => 'nullable|string|max:20',
        ]);

        // Handle admin logo upload
        if ($request->hasFile('admin_logo')) {
            // Delete old logo if exists
            if ($admin->admin_logo) {
                Storage::disk('public')->delete($admin->admin_logo);
            }
            $validated['admin_logo'] = $request->file('admin_logo')->store('admin/logo', 'public');
        } else {
            // Keep existing logo if not uploading new one
            unset($validated['admin_logo']);
        }

        // Handle system logo upload
        if ($request->hasFile('system_logo')) {
            // Delete old logo if exists
            if ($admin->system_logo) {
                Storage::disk('public')->delete($admin->system_logo);
            }
            $validated['system_logo'] = $request->file('system_logo')->store('admin/system-logo', 'public');
        } else {
            // Keep existing logo if not uploading new one
            unset($validated['system_logo']);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            // Delete old favicon if exists
            if ($admin->favicon) {
                Storage::disk('public')->delete($admin->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('admin/favicon', 'public');
        } else {
            // Keep existing favicon if not uploading new one
            unset($validated['favicon']);
        }

        $admin->update($validated);

        return redirect()->route('admin.profile.edit')
            ->with('success', 'Admin profile updated successfully.');
    }
}
