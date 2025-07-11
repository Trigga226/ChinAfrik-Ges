<?php

namespace App\Filament\Resources\BourseResource\Widgets;

use App\Models\Bourse;
use Filament\Widgets\ChartWidget;

class BourseCatChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {

        return [
                'datasets' => [
                    [
                        'label' => 'Blog posts created',
                        'data' => Bourse::all()->groupBy('titre')->count(),
                    ],
                ],
                'labels' => Bourse::all()->pluck('titre')->toArray(),

        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
