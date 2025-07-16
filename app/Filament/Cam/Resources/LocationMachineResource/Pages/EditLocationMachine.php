<?php

namespace App\Filament\Cam\Resources\LocationMachineResource\Pages;

use App\Filament\Cam\Resources\LocationMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLocationMachine extends EditRecord
{
    protected static string $resource = LocationMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $tot=0;
        foreach ($data['details'] as $key => $value) {
            $tot+=$value['montant'];
        }

        $tot=$tot-$data['remise'];

        $data['total_a_percevoir']=$tot;

        return $data;
    }
}
