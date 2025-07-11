<?php

namespace App\Filament\Cam\Widgets;

use App\Models\LocationCamion;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LocationCamionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Statistiques des locations en cours
        $locationsEnCours = LocationCamion::where('statut', 'En cours')->get();
        $totalCAEnCours = $locationsEnCours->sum('total_a_percevoir');
        $nombreLocationsEnCours = $locationsEnCours->count();

        // Statistiques des locations terminées
        $locationsTerminees = LocationCamion::where('statut', 'Terminer')->get();
        $totalCATerminees = $locationsTerminees->sum('total_a_percevoir');
        $nombreLocationsTerminees = $locationsTerminees->count();

        // Statistiques des locations en attente
        $locationsEnAttente = LocationCamion::where('statut', 'En attente')->get();
        $totalCAEnAttente = $locationsEnAttente->sum('total_a_percevoir');
        $nombreLocationsEnAttente = $locationsEnAttente->count();

        // Statistiques du mois en cours
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $locationsMois = LocationCamion::whereBetween('date', [$debutMois, $finMois])->get();
        $totalCAMois = $locationsMois->sum('total_a_percevoir');
        $nombreLocationsMois = $locationsMois->count();

        return [
            Stat::make('Locations en cours', $nombreLocationsEnCours)
                ->description(number_format($totalCAEnCours, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Locations terminées', $nombreLocationsTerminees)
                ->description(number_format($totalCATerminees, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('Locations en attente', $nombreLocationsEnAttente)
                ->description(number_format($totalCAEnAttente, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Locations du mois', $nombreLocationsMois)
                ->description(number_format($totalCAMois, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
