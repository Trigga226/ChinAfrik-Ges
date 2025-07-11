<?php

namespace App\Filament\Cam\Resources\ChauffeurResource\Pages;

use App\Filament\Cam\Resources\ChauffeurResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChauffeur extends CreateRecord
{
    protected static string $resource = ChauffeurResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
