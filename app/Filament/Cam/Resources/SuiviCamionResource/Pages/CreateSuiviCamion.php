<?php

namespace App\Filament\Cam\Resources\SuiviCamionResource\Pages;

use App\Filament\Cam\Resources\SuiviCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSuiviCamion extends CreateRecord
{
    protected static string $resource = SuiviCamionResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
