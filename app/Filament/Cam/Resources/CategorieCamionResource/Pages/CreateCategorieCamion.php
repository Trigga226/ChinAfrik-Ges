<?php

namespace App\Filament\Cam\Resources\CategorieCamionResource\Pages;

use App\Filament\Cam\Resources\CategorieCamionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategorieCamion extends CreateRecord
{
    protected static string $resource = CategorieCamionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
