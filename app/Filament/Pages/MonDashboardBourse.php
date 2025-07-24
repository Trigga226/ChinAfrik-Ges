<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;

class MonDashboardBourse extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.mon-dashboard-bourse';
    use HasPageShield;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('switchPanel')
                ->label('Aller a Camion')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->url(function () {
                    // Remplacez ceci par l'URL de votre autre panel
                    return '/camion';
                })->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('secretaire_kosboura')),
        ];
    }


}
