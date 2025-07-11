<?php

namespace App\Filament\Cam\Resources\ChauffeurResource\Pages;

use App\Filament\Cam\Resources\ChauffeurResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewChauffeur extends ViewRecord
{
    protected static string $resource = ChauffeurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
