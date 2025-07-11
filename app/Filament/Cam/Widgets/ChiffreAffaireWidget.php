<?php

namespace App\Filament\Cam\Widgets;

use App\Models\LocationCamion;
use App\Models\LocationMachine;
use App\Models\DepenseCamion;
use App\Models\DepenseMachine;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChiffreAffaireWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Chiffre d'affaire des camions (uniquement les locations terminées)
        $chiffreAffaireCamions = LocationCamion::where('statut', 'Terminer')
            ->sum('total_a_percevoir');

        // Dépenses des camions
        $depensesCamions = DepenseCamion::sum('montant');

        // Bénéfice net camions
        $beneficeNetCamions = $chiffreAffaireCamions - $depensesCamions;

        // Chiffre d'affaire des machines (uniquement les locations terminées)
        $chiffreAffaireMachines = LocationMachine::where('statut', 'Terminer')
            ->sum('total_a_percevoir');

        // Dépenses des machines
        $depensesMachines = DepenseMachine::sum('montant');

        // Bénéfice net machines
        $beneficeNetMachines = $chiffreAffaireMachines - $depensesMachines;

        // Statistiques du mois en cours (uniquement les locations terminées)
        $debutMois = Carbon::now()->startOfMonth();
        $finMois = Carbon::now()->endOfMonth();

        $chiffreAffaireMoisCamions = LocationCamion::where('statut', 'Terminer')
            ->whereBetween('date', [$debutMois, $finMois])
            ->sum('total_a_percevoir');

        $depensesMoisCamions = DepenseCamion::whereBetween('date', [$debutMois, $finMois])
            ->sum('montant');

        $chiffreAffaireMoisMachines = LocationMachine::where('statut', 'Terminer')
            ->whereBetween('date', [$debutMois, $finMois])
            ->sum('total_a_percevoir');

        $depensesMoisMachines = DepenseMachine::whereBetween('date', [$debutMois, $finMois])
            ->sum('montant');

        return [
            Stat::make('Chiffre d\'affaire Camions', number_format($chiffreAffaireCamions, 0, ',', ' ') . ' XOF')
                ->description('CA du mois: ' . number_format($chiffreAffaireMoisCamions, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Dépenses Camions', number_format($depensesCamions, 0, ',', ' ') . ' XOF')
                ->description('Dépenses du mois: ' . number_format($depensesMoisCamions, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Bénéfice Net Camions', number_format($beneficeNetCamions, 0, ',', ' ') . ' XOF')
                ->description('Bénéfice du mois: ' . number_format($chiffreAffaireMoisCamions - $depensesMoisCamions, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($beneficeNetCamions > 0 ? 'success' : 'danger'),

            Stat::make('Chiffre d\'affaire Machines', number_format($chiffreAffaireMachines, 0, ',', ' ') . ' XOF')
                ->description('CA du mois: ' . number_format($chiffreAffaireMoisMachines, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-cog')
                ->color('warning'),

            Stat::make('Dépenses Machines', number_format($depensesMachines, 0, ',', ' ') . ' XOF')
                ->description('Dépenses du mois: ' . number_format($depensesMoisMachines, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger'),

            Stat::make('Bénéfice Net Machines', number_format($beneficeNetMachines, 0, ',', ' ') . ' XOF')
                ->description('Bénéfice du mois: ' . number_format($chiffreAffaireMoisMachines - $depensesMoisMachines, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($beneficeNetMachines > 0 ? 'success' : 'danger'),

            Stat::make('Total Chiffre d\'affaire', number_format($chiffreAffaireCamions + $chiffreAffaireMachines, 0, ',', ' ') . ' XOF')
                ->description('Bénéfice total: ' . number_format($beneficeNetCamions + $beneficeNetMachines, 0, ',', ' ') . ' XOF')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }
}
