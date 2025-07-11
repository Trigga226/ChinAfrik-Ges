<?php

namespace App\Providers;

use App\Http\Responses\CustomLoginResponse;
use App\Models\User;
use App\Models\Versement;
use App\Observers\UserObserver;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
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
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }
}


namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        $user = auth()->user();

        // Redirection selon le rôle (avec Shield)
        if ($user->hasRole(['secretaire kosboura','camion','chauffeur'])) {
            return redirect()->to('/camion');
        }

        // Redirection par défaut
        return redirect('/');
    }
}
