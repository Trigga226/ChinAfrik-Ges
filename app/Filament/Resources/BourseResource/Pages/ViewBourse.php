<?php

namespace App\Filament\Resources\BourseResource\Pages;

use App\Filament\Resources\BourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBourse extends ViewRecord
{
    protected static string $resource = BourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
