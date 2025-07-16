<?php

namespace App\Filament\Cam\Widgets;

use App\Models\PointageCamion;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RavitaillementChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ravitaillement des 7 derniers jours';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            $data['camions'][] = PointageCamion::whereDate('date', $date)
                ->where('ravitailler', true)
                ->sum('montant_ravitailler');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Montant ravitaillement (FCFA)',
                    'data' => $data['camions'],
                    'backgroundColor' => '#3B82F6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
