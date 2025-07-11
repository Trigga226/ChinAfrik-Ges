<?php

namespace App\Filament\Cam\Widgets;

use App\Models\LocationCamion;
use App\Models\LocationMachine;
use App\Models\DepenseCamion;
use App\Models\DepenseMachine;
use App\Models\PointageCamion;
use App\Models\PointageMachine;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentabiliteStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Statistiques des camions
        $camions = \App\Models\Camion::all();
        $statsCamions = [];

        foreach ($camions as $camion) {
            $chiffreAffaire = LocationCamion::whereHas('camions', function ($query) use ($camion) {
                $query->where('designation', $camion->designation);
            })->where('statut', 'Terminer')->sum('total_a_percevoir');

            $depenses = DepenseCamion::where('camion', $camion->designation)->sum('montant');

            $statsCamions[] = [
                'designation' => $camion->designation,
                'chiffre_affaire' => $chiffreAffaire,
                'depenses' => $depenses,
                'benefice' => $chiffreAffaire - $depenses
            ];
        }

        // Trier par bénéfice décroissant
        usort($statsCamions, function($a, $b) {
            return $b['benefice'] <=> $a['benefice'];
        });

        // Prendre les 3 meilleurs camions
        $meilleursCamions = array_slice($statsCamions, 0, 3);

        // Statistiques des machines
        $machines = \App\Models\Machine::all();
        $statsMachines = [];

        foreach ($machines as $machine) {
            $chiffreAffaire = LocationMachine::whereHas('machines', function ($query) use ($machine) {
                $query->where('designation', $machine->designation);
            })->where('statut', 'Terminer')->sum('total_a_percevoir');

            $depenses = DepenseMachine::where('machine', $machine->designation)->sum('montant');

            $statsMachines[] = [
                'designation' => $machine->designation,
                'chiffre_affaire' => $chiffreAffaire,
                'depenses' => $depenses,
                'benefice' => $chiffreAffaire - $depenses
            ];
        }

        // Trier par bénéfice décroissant
        usort($statsMachines, function($a, $b) {
            return $b['benefice'] <=> $a['benefice'];
        });

        // Prendre les 3 meilleures machines
        $meilleuresMachines = array_slice($statsMachines, 0, 3);

        $stats = [];

        // Ajouter les statistiques des meilleurs camions
        foreach ($meilleursCamions as $camion) {
            $stats[] = Stat::make(
                "Meilleur Camion: {$camion['designation']}",
                number_format($camion['benefice'], 0, ',', ' ') . ' XOF'
            )
            ->description(
                "CA: " . number_format($camion['chiffre_affaire'], 0, ',', ' ') . ' XOF' .
                " | Dépenses: " . number_format($camion['depenses'], 0, ',', ' ') . ' XOF'
            )
            ->descriptionIcon('heroicon-m-truck')
            ->color($camion['benefice'] > 0 ? 'success' : 'danger');
        }

        // Ajouter les statistiques des meilleures machines
        foreach ($meilleuresMachines as $machine) {
            $stats[] = Stat::make(
                "Meilleure Machine: {$machine['designation']}",
                number_format($machine['benefice'], 0, ',', ' ') . ' XOF'
            )
            ->description(
                "CA: " . number_format($machine['chiffre_affaire'], 0, ',', ' ') . ' XOF' .
                " | Dépenses: " . number_format($machine['depenses'], 0, ',', ' ') . ' XOF'
            )
            ->descriptionIcon('heroicon-m-cog')
            ->color($machine['benefice'] > 0 ? 'success' : 'danger');
        }

        return $stats;
    }
}
