<?php

namespace App\Filament\Cam\Widgets;

use App\Models\Camion;
use App\Models\PointageCamion;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PointageGeneralChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'pointageGeneralChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'PointageGeneralChart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $cam=new Camion();
        dd(self::calculerJoursNonTravail($cam));
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'BasicBarChart',
                    'data' => [7, 10, 13, 15, 18],
                ],
            ],
            'xaxis' => [
                'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => true,
                ],
            ],
        ];
    }

    private static function calculerJoursTravail(Camion $camion): int
    {
        $total = 0;
        $pointages = PointageCamion::query()
            ->whereHas('locations', function ($query) use ($camion) {
                $query->whereHas('camions', function ($q) use ($camion) {
                    $q->where('camions.id', $camion->id);
                });
            })
            ->get();

        foreach ($pointages as $pointage) {
            if (is_array($pointage->pointages)) {
                foreach ($pointage->pointages as $p) {
                    if (isset($p['camion']) && $p['camion'] === $camion->designation &&
                        isset($p['a_travailler']) && $p['a_travailler'] === true) {
                        $total++;
                    }
                }
            }
        }

        return $total;
    }

    private static function calculerJoursNonTravail(Camion $camion): int
    {
        $total = 0;
        $pointages = PointageCamion::query()
            ->whereHas('locations', function ($query) use ($camion) {
                $query->whereHas('camions', function ($q) use ($camion) {
                    $q->where('camions.id', $camion->id);
                });
            })
            ->get();

        foreach ($pointages as $pointage) {
            if (is_array($pointage->pointages)) {
                foreach ($pointage->pointages as $p) {
                    if (isset($p['camion']) && $p['camion'] === $camion->designation &&
                        isset($p['a_travailler']) && $p['a_travailler'] === false) {
                        $total++;
                    }
                }
            }
        }

        return $total;
    }

}
