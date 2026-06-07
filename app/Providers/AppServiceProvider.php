<?php

namespace App\Providers;

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
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $count = \App\Models\KeranjangDetail::whereHas('keranjang', function($query) {
                    $query->where('id_user', auth()->user()->id_user);
                })->sum('qty');
                $view->with('cartCount', $count);
            } else {
                $view->with('cartCount', 0);
            }
        });
    }
}
