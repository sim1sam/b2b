<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;

class BrandingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Dynamically update AdminLTE config based on admin settings
        try {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                if ($admin->system_logo) {
                    config(['adminlte.logo_img' => \Illuminate\Support\Facades\Storage::url($admin->system_logo)]);
                }
                if ($admin->app_name) {
                    config(['adminlte.logo' => $admin->app_name]);
                }
                // Favicon is now handled directly in the master template override
                // No need to inject via extra_head config
            }
            
            // Set dashboard URL based on authenticated user role
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->role === 'admin') {
                    config(['adminlte.dashboard_url' => 'admin/dashboard']);
                } elseif ($user->role === 'customer') {
                    config(['adminlte.dashboard_url' => 'customer/dashboard']);
                }
            }
        } catch (\Exception $e) {
            // Ignore if database is not ready
        }

        // Share branding data with all views
        View::composer('*', function ($view) {
            try {
                $admin = User::where('role', 'admin')->first();
                
                $view->with([
                    'systemLogo' => $admin && $admin->system_logo 
                        ? \Illuminate\Support\Facades\Storage::url($admin->system_logo) 
                        : asset('vendor/adminlte/dist/img/AdminLTELogo.png'),
                    'appName' => $admin && $admin->app_name 
                        ? $admin->app_name 
                        : 'AdminLTE',
                    'favicon' => $admin && $admin->favicon 
                        ? \Illuminate\Support\Facades\Storage::url($admin->favicon) 
                        : null,
                    'footerCopyright' => $admin && $admin->footer_copyright_text 
                        ? $admin->footer_copyright_text 
                        : null,
                    'footerDeveloperName' => $admin && $admin->footer_developer_name 
                        ? $admin->footer_developer_name 
                        : null,
                    'footerDeveloperLink' => $admin && $admin->footer_developer_link 
                        ? $admin->footer_developer_link 
                        : null,
                ]);
            } catch (\Exception $e) {
                // Ignore if database is not ready
                $view->with([
                    'systemLogo' => asset('vendor/adminlte/dist/img/AdminLTELogo.png'),
                    'appName' => 'AdminLTE',
                    'favicon' => null,
                    'footerCopyright' => null,
                    'footerDeveloperName' => null,
                    'footerDeveloperLink' => null,
                ]);
            }
        });


    }
}
