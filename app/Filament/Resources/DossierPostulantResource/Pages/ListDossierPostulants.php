<?php

namespace App\Filament\Resources\DossierPostulantResource\Pages;

use App\Filament\Resources\DossierPostulantResource;
use App\Models\Bourse;
use App\Models\Versement;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDossierPostulants extends ListRecords
{
    protected static string $resource = DossierPostulantResource::class;

    protected function getHeaderActions(): array
    {
     
        return [
            Actions\CreateAction::make()
            ->modal()
            ->before(function (array $data,Actions\CreateAction $action): array {

                try {

                    $jour=date('d');
                }catch (\PDOException $exception){
                    Notification::make()
                        ->title('Erreur')
                        ->danger()
                        ->body("Une erreure s'est produite. Vérifiez les informations entrées")
                        ->send();
                    $action->cancel();
                }


                return $data;
            }),
        ];
    }
}
