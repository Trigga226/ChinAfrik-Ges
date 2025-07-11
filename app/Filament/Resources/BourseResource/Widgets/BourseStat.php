<?php

namespace App\Filament\Resources\BourseResource\Widgets;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BourseStat extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected ?string $heading = 'Analytics';

    protected ?string $description = 'An overview of some analytics.';
    protected function getStats(): array
    {

        return [
            Stat::make('Nombre de bourse', Bourse::all()->count())
                ->description('Le nombre de type de bourse')
                ->color('success'),
            Stat::make('Nombre de dossiers', DossierPostulant::all()->count())
                ->description("dont:".DossierPostulant::where('etat','En cours')->orwhere('etat','Suspendu')->orwhere('etat',null)->orwhere('etat','En attente')->get()->count()." dossiers en cours")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Unique views', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
        ];
    }
}
