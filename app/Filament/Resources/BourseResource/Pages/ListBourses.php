<?php

namespace App\Filament\Resources\BourseResource\Pages;

use App\Filament\Resources\BourseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBourses extends ListRecords
{
    protected static string $resource = BourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->modal(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BourseResource\Widgets\BourseStat::class
        ];
    }
}
