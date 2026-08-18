<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Share all settings globally to every Blade view.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            try {
                $view->with('appSettings', all_settings());
            } catch (\Exception $e) {
                // Ignore if DB not ready (e.g. fresh install before migrations)
                $view->with('appSettings', []);
            }
        });
    }
}
