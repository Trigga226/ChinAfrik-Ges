<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Versement;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
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
        User::observe(UserObserver::class);
        Versement::observe(\App\Observers\Versement::class);
        Schema::defaultStringLength(191);
    }
}
