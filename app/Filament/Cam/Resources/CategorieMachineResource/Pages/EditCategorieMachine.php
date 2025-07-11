<?php

namespace App\Filament\Cam\Resources\CategorieMachineResource\Pages;

use App\Filament\Cam\Resources\CategorieMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategorieMachine extends EditRecord
{
    protected static string $resource = CategorieMachineResource::class;

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
