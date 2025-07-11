<?php

namespace App\Filament\Resources\VersementResource\Pages;

use App\Filament\Resources\VersementResource;
use App\Models\Versement;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVersement extends CreateRecord
{
    protected static string $resource = VersementResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $date = DateTime::createFromFormat('d/m/Y', $data['date_versement']);
        $vraidate=$date->format('Y-m-d');
        $ref1 = str_replace(array('/'), '',$data['date_versement']);
        $ref2= "CAG-".$ref1;
        $nombre=Versement::where("date_versement","like",$vraidate)->count();
        $data['date_versement']=date("Y/m/d",strtotime($vraidate));

        if ($nombre<10){
            $reference=$ref2."0".$nombre+1;
        }else{
            $reference=$ref2.$nombre+1;
        }
        $data['reference']=$reference;

        return $data;
    }
}
