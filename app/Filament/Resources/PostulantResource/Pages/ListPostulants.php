<?php

namespace App\Filament\Resources\PostulantResource\Pages;

use App\Filament\Resources\PostulantResource;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class ListPostulants extends ListRecords
{
    protected static string $resource = PostulantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->mutateFormDataUsing(function (array $data,Actions\CreateAction $action): array  {

                try {
                    $user=new User();
                    $user->name=$data['nom_complet'];
                    $user->email=$data['email'];
                    $user->phone=str_replace('+','',$data['phone']);
                    $user->avatar_url=$data['photo'];
                    $user->genre=$data['genre'];
                    $user->password=Hash::make("12345678");
                    $user->save();
                    $user->assignRole('postulant');

                    $data['phone']=$user->phone;


                }catch (\PDOException $exception){

                    Notification::make()
                        ->title('Erreur')
                        ->danger()
                        ->body("Une erreure s'est produite. Vérifiez les informations entrées")
                        ->send();
                    $action->cancel();
                }

                return $data;
            })
            ->after(function (Postulant $record,Actions\CreateAction $action) {



                try {
                    $dossier=new DossierPostulant();
                    $dossier->postulant_id=$record->id;
                    $dossier->nom_complet=$record->nom_complet;
                    $dossier->email=$record->email;
                    $dossier->phone=$record->phone;
                    $dossier->photo=$record->photo;
                    $dossier->save();
                }catch (\PDOException $exception){
                    Notification::make()
                        ->title('Erreur')
                        ->danger()
                        ->body("Une erreure s'est produite. Vérifiez les informations entrées")
                        ->send();
                    $action->cancel();
                }

            })
            ,
        ];
    }
}
