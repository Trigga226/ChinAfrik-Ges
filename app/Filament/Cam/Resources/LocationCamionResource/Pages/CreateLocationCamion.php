<?php

namespace App\Filament\Cam\Resources\LocationCamionResource\Pages;

use App\Filament\Cam\Resources\LocationCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocationCamion extends CreateRecord
{
    protected static string $resource = LocationCamionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculer le total à percevoir
       // $data['total_a_percevoir'] = ($data['duree'] * $data['cout_jounalier']) - $data['remise'];

        $tot=0;
        foreach ($data['details'] as $key => $value) {
            $tot+=$value['montant'];
        }

        $tot=$tot-$data['remise'];

        $data['total_a_percevoir']=$tot;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Récupérer les camions sélectionnés
        $camions = $this->data['details'];

        $camlis=[];
        foreach ($camions as $camion){
            $camlis[]=$camion['camion'];
        }





        // Attacher les camions à la location
        foreach ($camlis as $camion) {
            $this->record->camions()->attach($camion);
        }
    }
}
