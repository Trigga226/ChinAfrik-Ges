<?php

namespace App\Filament\Cam\Resources\CamionResource\Pages;

use App\Filament\Cam\Resources\CamionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCamion extends EditRecord
{
    protected static string $resource = CamionResource::class;

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
