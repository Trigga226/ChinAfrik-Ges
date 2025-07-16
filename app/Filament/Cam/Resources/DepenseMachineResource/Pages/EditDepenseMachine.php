<?php

namespace App\Filament\Cam\Resources\DepenseMachineResource\Pages;

use App\Filament\Cam\Resources\DepenseMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepenseMachine extends EditRecord
{
    protected static string $resource = DepenseMachineResource::class;

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
