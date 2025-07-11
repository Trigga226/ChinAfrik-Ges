<?php

namespace App\Filament\Cam\Widgets;

use App\Models\Camion;
use App\Models\PointageCamion;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PointageChart2 extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'pointageChart2';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Taux de non travail';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $grouped = [];

        $pointages=PointageCamion::all();
        $camions=Camion::all();
        $travailler=0;
        $nontravailler=0;
        $total=0;


        $listecamion=[];

        foreach ($pointages as $pointage) {
            foreach ($pointage->pointages as $item){
                $listecamion[]=$item['camion'];
                if ($item['a_travailler']){
                    $travailler++;
                }else{
                    $nontravailler++;
                }
            }
        }

        $listecamion=array_unique($listecamion);

        $taf=0;
        $nontaf=0;
        $listtaf=[];
        foreach ($pointages as $pointage) {
            foreach ($pointage->pointages as $item){
                $taf=0;
                $nontaf=0;
                foreach ($listecamion as $camion){
                    if ($item['a_travailler']&& $item['camion']==$camion){
                        $taf++;
                        array_push($listtaf,$camion,['tafer'=>$taf,'nontaf'=>$nontaf]);
                    }if (!$item['a_travailler']&& $item['camion']==$camion){
                        $nontaf++;
                        array_push($listtaf,$camion,['tafer'=>$taf,'nontaf'=>$nontaf]);
                    }
                }
            }
        }



        for ($i = 0; $i < count($listtaf); $i += 2) {
            $camion = $listtaf[$i];
            $values = $listtaf[$i + 1];

            if (!isset($grouped[$camion])) {
                $grouped[$camion] = [
                    'camion' => $camion,
                    'tafer' => 0,
                    'nontaf' => 0
                ];
            }

            $grouped[$camion]['tafer'] += $values['tafer'];
            $grouped[$camion]['nontaf'] += $values['nontaf'];
        }

        $tafli=[];
        foreach ($grouped as $camion => $values) {
            array_push($tafli,$values['nontaf']);
        }



        return [
            'chart' => [
                'type' => 'donut',
                'height' => 200,
            ],
            'series' =>$tafli,
            'labels' => $listecamion,
            'legend' => [
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
        ];
    }
}
