<?php

namespace App\Menu;

use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleMenuFilter implements FilterInterface
{
    /**
     * Transforms a menu item. Filters out items that don't match the user's role.
     *
     * @param  array  $item  A menu item
     * @return array|false
     */
    public function transform($item)
    {
        // If item has 'role' key, check if user has required role
        if (isset($item['role'])) {
            $user = Auth::user();
            if (!$user || $user->role !== $item['role']) {
                // Set restricted to filter out this item
                $item['restricted'] = true;
            }
        }

        // Handle dynamic URL (for Apps Home)
        if (isset($item['data']['dynamic_url']) && $item['data']['dynamic_url']) {
            try {
                $admin = User::where('role', 'admin')->first();
                if ($admin && $admin->apps_home_url) {
                    $item['url'] = $admin->apps_home_url;
                } else {
                    // Hide menu item if URL is not set
                    $item['restricted'] = true;
                }
            } catch (\Exception $e) {
                // Hide menu item on error
                $item['restricted'] = true;
            }
        }

        return $item;
    }
}
