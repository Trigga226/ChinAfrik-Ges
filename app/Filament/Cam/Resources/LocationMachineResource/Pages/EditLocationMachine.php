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
        $data['total_a_percevoir'] = $data['cout_horaire'] * $data['duree']-$data['remise'];
        return $data;
    }
}
