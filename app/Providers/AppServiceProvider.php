<?php

namespace App\Providers;

use App\Models\Barberia;
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
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $admin = auth('admin')->user();
            $view->with('barberia', $admin ? Barberia::find($admin->barberia_id) : null);
        });

        View::composer('admin.*', function ($view) {
            $admin = auth('admin')->user();
            $view->with('barberia', $admin ? Barberia::find($admin->barberia_id) : null);
        });
    }
}
