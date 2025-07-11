<?php

namespace App\Filament\Cam\Resources\LocationMachineResource\Pages;

use App\Filament\Cam\Resources\LocationMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocationMachine extends CreateRecord
{
    protected static string $resource = LocationMachineResource::class;
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
        $machines = $this->data['details'];

        $camlis=[];
        foreach ($machines as $machine){
            $camlis[]=$machine['machine'];
        }





        // Attacher les camions à la location
        foreach ($camlis as $camion) {
            $this->record->machines()->attach($camion);
        }
    }
}
