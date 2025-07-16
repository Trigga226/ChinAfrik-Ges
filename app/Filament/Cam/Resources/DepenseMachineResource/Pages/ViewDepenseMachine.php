<?php

namespace App\Filament\Cam\Resources\DepenseMachineResource\Pages;

use App\Filament\Cam\Resources\DepenseMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDepenseMachine extends ViewRecord
{
    protected static string $resource = DepenseMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
