<?php

namespace App\Filament\Cam\Pages;

use App\Filament\Cam\Widgets\ChiffreAffaireWidget;
use App\Filament\Cam\Widgets\PointagesStatsWidget;
use App\Filament\Cam\Widgets\PointagesChartWidget;
use App\Filament\Cam\Widgets\RavitaillementChartWidget;
use App\Filament\Cam\Widgets\RentabiliteStatsWidget;
use App\Filament\Cam\Widgets\ProductiviteStatsWidget;
use App\Filament\Cam\Widgets\LocationCamionStatsWidget;
use Filament\Pages\Dashboard;

class MonDashboardCamion extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static ?string $title = 'Tableau de bord';

    protected static string $view = 'filament.cam.pages.mon-dashboard-camion';

    protected int | string | array $columnSpan = [
        'md' => 3,
        'xl' => 4,
    ];

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('switchPanel')
                ->label('Aller a ChinAfrik')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->url(function () {
                    // Remplacez ceci par l'URL de votre autre panel
                    return '/admin';
                })->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ChiffreAffaireWidget::class,
            PointagesStatsWidget::class,
            LocationCamionStatsWidget::class,
            RentabiliteStatsWidget::class,
            ProductiviteStatsWidget::class,
            PointagesChartWidget::class,
            RavitaillementChartWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'md' => 4,
            'xl' => 5,
        ];
    }
}
