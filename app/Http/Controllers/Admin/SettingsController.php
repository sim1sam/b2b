<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Show the form for editing settings.
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
        
        return view('admin.settings.edit', compact('admin'));
    }

    /**
     * Update the settings.
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
            'apps_home_url' => 'nullable|url|max:500',
            'footer_copyright_text' => 'nullable|string|max:500',
            'footer_developer_name' => 'nullable|string|max:255',
            'footer_developer_link' => 'nullable|url|max:500',
        ]);

        $admin->update($validated);

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings updated successfully.');
    }
}
