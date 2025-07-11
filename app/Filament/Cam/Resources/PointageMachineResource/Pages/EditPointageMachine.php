<?php

namespace App\Filament\Cam\Resources\PointageMachineResource\Pages;

use App\Filament\Cam\Resources\PointageMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointageMachine extends EditRecord
{
    protected static string $resource = PointageMachineResource::class;

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
