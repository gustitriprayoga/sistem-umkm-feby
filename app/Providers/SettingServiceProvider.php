<?php

namespace App\Providers;

use App\Models\Backend\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class SettingServiceProvider extends ServiceProvider
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
        View::composer(['layouts.backend.sidebar'], function ($view) {
            $settings = Setting::where('id', 1)->first(); // Query data settings
            $view->with('settings', $settings); // Kirim data ke view
        });

        View::composer(['layouts.backend.master'], function ($view) {
            $settings = Setting::where('id', 1)->first(); // Query data settings
            $view->with('settings', $settings); // Kirim data ke view
        });



    }
}
