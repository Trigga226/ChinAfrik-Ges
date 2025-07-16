<?php

namespace App\Filament\Cam\Resources\SuiviCamionResource\Pages;

use App\Filament\Cam\Resources\SuiviCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuiviCamion extends EditRecord
{
    protected static string $resource = SuiviCamionResource::class;

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
