<?php

namespace App\Filament\Cam\Resources\PointageMachineResource\Pages;

use App\Filament\Cam\Resources\PointageMachineResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPointageMachine extends ViewRecord
{
    protected static string $resource = PointageMachineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
