<?php

namespace App\Filament\Cam\Resources\SuiviMachineResource\Pages;

use App\Filament\Cam\Resources\SuiviMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuiviMachine extends EditRecord
{
    protected static string $resource = SuiviMachineResource::class;

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
}
