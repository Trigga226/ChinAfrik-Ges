<?php

namespace App\Filament\Resources\BourseResource\Pages;

use App\Filament\Resources\BourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBourse extends EditRecord
{
    protected static string $resource = BourseResource::class;

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
