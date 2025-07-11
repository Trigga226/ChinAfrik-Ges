<?php

namespace App\Filament\Cam\Widgets;

use App\Models\PointageCamion;
use App\Models\PointageMachine;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PointagesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pointages des 7 derniers jours';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            $data['camions'][] = PointageCamion::whereDate('date', $date)
                ->where('a_travailler', true)
                ->count();
            $data['machines'][] = PointageMachine::whereDate('date', $date)
                ->where('a_travailler', true)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Camions',
                    'data' => $data['camions'],
                    'borderColor' => '#10B981',
                ],
                [
                    'label' => 'Machines',
                    'data' => $data['machines'],
                    'borderColor' => '#F59E0B',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
