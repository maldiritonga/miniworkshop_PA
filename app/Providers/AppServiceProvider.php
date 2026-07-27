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
        if (env('APP_ENV') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        view()->composer('*', function ($view) {
            static $cartCount = null;

            if (auth()->check()) {
                if ($cartCount === null) {
                    $cartCount = \App\Models\KeranjangDetail::whereHas('keranjang', function($query) {
                        $query->where('id_user', auth()->user()->id_user);
                    })->sum('qty');
                }
                $view->with('cartCount', $cartCount);
            } else {
                $view->with('cartCount', 0);
            }
        });
    }
}
