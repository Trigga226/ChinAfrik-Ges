<?php

namespace App\Filament\Cam\Widgets;

use App\Models\PointageCamion;
use App\Models\PointageMachine;
use App\Models\LocationMachine;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductiviteStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Statistiques des camions
        $camions = \App\Models\Camion::all();
        $statsCamions = [];

        foreach ($camions as $camion) {
            $totalPointages = PointageCamion::where('camion', $camion->designation)->count();
            $joursTravailles = PointageCamion::where('camion', $camion->designation)
                ->where('a_travailler', true)
                ->count();

            $tauxProductivite = $totalPointages > 0 ? ($joursTravailles / $totalPointages) * 100 : 0;

            $statsCamions[] = [
                'designation' => $camion->designation,
                'total_pointages' => $totalPointages,
                'jours_travailles' => $joursTravailles,
                'taux_productivite' => $tauxProductivite
            ];
        }

        // Trier par taux de productivité décroissant
        usort($statsCamions, function($a, $b) {
            return $b['taux_productivite'] <=> $a['taux_productivite'];
        });

        // Prendre les 3 meilleurs camions
        $meilleursCamions = array_slice($statsCamions, 0, 3);

        // Statistiques des machines
        $machines = \App\Models\Machine::all();
        $statsMachines = [];

        foreach ($machines as $machine) {
            $totalPointages = PointageMachine::where('machine', $machine->designation)->count();
            $heuresTravailles = 0;
            $heuresNonTravailles = 0;
            $heuresTotalesLocation = 0;

            // Récupérer toutes les locations de cette machine
            $locations = LocationMachine::whereHas('machines', function ($query) use ($machine) {
                $query->where('designation', $machine->designation);
            })->get();

            // Calculer les heures totales de location
            foreach ($locations as $location) {
                foreach ($location->details as $detail) {
                    if ($detail['machine'] == $machine->id) {
                        $heuresTotalesLocation += $detail['duree'];
                    }
                }
            }

            $pointages = PointageMachine::where('machine', $machine->designation)->get();

            foreach ($pointages as $pointage) {
                if ($pointage->heure_sortie && $pointage->heure_retour) {
                    $sortie = Carbon::parse($pointage->heure_sortie);
                    $retour = Carbon::parse($pointage->heure_retour);

                    if ($retour->lt($sortie)) {
                        $retour->addDay();
                    }

                    $heures = $sortie->diffInHours($retour);
                    $minutes = $sortie->diffInMinutes($retour) % 60;
                    $duree = $heures + ($minutes / 60);

                    if ($pointage->a_travailler) {
                        $heuresTravailles += $duree;
                    } else {
                        $heuresNonTravailles += $duree;
                    }
                }
            }

            $tauxProductivite = $heuresTotalesLocation > 0 ? ($heuresTravailles / $heuresTotalesLocation) * 100 : 0;

            $statsMachines[] = [
                'designation' => $machine->designation,
                'total_pointages' => $totalPointages,
                'heures_travailles' => $heuresTravailles,
                'heures_non_travailles' => $heuresNonTravailles,
                'heures_totales_location' => $heuresTotalesLocation,
                'taux_productivite' => $tauxProductivite
            ];
        }

        // Trier par taux de productivité décroissant
        usort($statsMachines, function($a, $b) {
            return $b['taux_productivite'] <=> $a['taux_productivite'];
        });

        // Prendre les 3 meilleures machines
        $meilleuresMachines = array_slice($statsMachines, 0, 3);

        $stats = [];

        // Ajouter les statistiques des meilleurs camions
        foreach ($meilleursCamions as $camion) {
            $stats[] = Stat::make(
                "Productivité Camion: {$camion['designation']}",
                number_format($camion['taux_productivite'], 1) . '%'
            )
            ->description(
                "Jours travaillés: {$camion['jours_travailles']} sur {$camion['total_pointages']}"
            )
            ->descriptionIcon('heroicon-m-truck')
            ->color($this->getColorForProductivity($camion['taux_productivite']));
        }

        // Ajouter les statistiques des meilleures machines
        foreach ($meilleuresMachines as $machine) {
            $stats[] = Stat::make(
                "Productivité Machine: {$machine['designation']}",
                number_format($machine['taux_productivite'], 1) . '%'
            )
            ->description(
                "Heures travaillées: " . round($machine['heures_travailles'], 1) . "h sur " .
                round($machine['heures_totales_location'], 1) . "h de location"
            )
            ->descriptionIcon('heroicon-m-cog')
            ->color($this->getColorForProductivity($machine['taux_productivite']));
        }

        return $stats;
    }

    protected function getColorForProductivity(float $productivity): string
    {
        return match (true) {
            $productivity >= 80 => 'success',
            $productivity >= 60 => 'warning',
            default => 'danger',
        };
    }
}
