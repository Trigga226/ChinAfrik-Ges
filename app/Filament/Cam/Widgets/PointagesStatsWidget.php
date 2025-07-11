<?php

namespace App\Filament\Cam\Widgets;

use App\Models\PointageCamion;
use App\Models\PointageMachine;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PointagesStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $aujourdhui = Carbon::today();

        $pointagesCamions = PointageCamion::whereDate('date', $aujourdhui)
            ->where('a_travailler', true)
            ->count();
        $pointagesMachines = PointageMachine::whereDate('date', $aujourdhui)
            ->where('a_travailler', true)
            ->count();

        $camionsActifs = PointageCamion::whereDate('date', $aujourdhui)
            ->where('a_travailler', true)
            ->distinct('camion')
            ->count();

        $machinesActives = PointageMachine::whereDate('date', $aujourdhui)
            ->where('a_travailler', true)
            ->distinct('machine')
            ->count();

        return [
            Stat::make('Pointages Camions', $pointagesCamions)
                ->description($camionsActifs . ' camions actifs aujourd\'hui')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),
            Stat::make('Pointages Machines', $pointagesMachines)
                ->description($machinesActives . ' machines actives aujourd\'hui')
                ->descriptionIcon('heroicon-m-cog')
                ->color('warning'),
            Stat::make('Total Pointages', $pointagesCamions + $pointagesMachines)
                ->description('Pointages du jour')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }
}
