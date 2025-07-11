<?php

namespace App\Filament\Cam\Widgets;

use App\Models\Camion;
use App\Models\PointageCamion;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PointageTable extends BaseWidget
{
    protected static ?string $heading = 'Statistiques des Camions';
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Camion::query()
            )
            ->columns([
                TextColumn::make('designation')
                    ->label('Camion')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jours_travail')
                    ->label('Jours de travail')
                    ->getStateUsing(function (Camion $record) {
                        return self::calculerJoursTravail($record);
                    }),
                TextColumn::make('jours_non_travail')
                    ->label('Jours sans travail')
                    ->getStateUsing(function (Camion $record) {
                        return self::calculerJoursNonTravail($record);
                    }),
                TextColumn::make('taux_utilisation')
                    ->label('Taux d\'utilisation (%)')
                    ->getStateUsing(function (Camion $record) {
                        $joursTravail = self::calculerJoursTravail($record);
                        $joursTotal = $joursTravail + self::calculerJoursNonTravail($record);
                        return $joursTotal > 0 ? round(($joursTravail / $joursTotal) * 100, 2) : 0;
                    })
                    ->suffix('%'),
            ])
            ;
    }

    private static function calculerJoursTravail(Camion $camion): int
    {
        $total = 0;
        $pointages = PointageCamion::where('camion', $camion->designation)
            ->where('a_travailler', true)
            ->get();

        return $pointages->count();
    }

    private static function calculerJoursNonTravail(Camion $camion): int
    {
        $total = 0;
        $pointages = PointageCamion::where('camion', $camion->designation)
            ->where('a_travailler', false)
            ->get();

        return $pointages->count();
    }
}
