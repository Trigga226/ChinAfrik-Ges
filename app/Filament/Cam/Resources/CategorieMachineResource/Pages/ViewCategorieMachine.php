<?php

namespace App\Filament\Cam\Resources\CategorieMachineResource\Pages;

use App\Filament\Cam\Resources\CategorieMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCategorieMachine extends ViewRecord
{
    protected static string $resource = CategorieMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
