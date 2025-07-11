<?php

namespace App\Observers;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\DocBlock\Tags\Version;

class Versement
{
    protected WhatsAppService $whatsAppService;
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function created(\App\Models\Versement $versement): void
    {
        $dossier=DossierPostulant::find($versement->dossier_id);

        if(!is_null($dossier->bourse) && is_null($dossier->type)){
            $bourse=Bourse::where('titre',$dossier->bourse)->first();
            $frais=$bourse->frais;
            $coutt=$bourse->coutt;
            $totalt=$frais+$coutt;


            $coutp=$bourse->coutp;
            $totalp=$frais+$coutp;

            $versements= \App\Models\Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
            $soldet=$totalt-$versements;
            $soldep=$totalp-$versements;

            if ($dossier->phone) {
// Format du numéro de téléphone
                $phone = $dossier->phone;
                if (!str_starts_with($phone, '+')) {
                    $phone = '+' . $phone;
                }
                $message = "Bonjour {$dossier->nom_complet} , votre versement pour {$versement->motif} d'un montant de {$versement->montant} FCFA a bien été enregistré. Votre solde général est de {$soldet} FCFA en cas de bourse totale ou de {$soldep} FCFA en cas de bourse partielle .\nChinAfrik Group vous remercie.";
                Log::info('Tentative d\'envoi de message WhatsApp', [
                    'user' => $versement->id,
                    'phone' => $phone
                ]);

                $this->whatsAppService->sendMessage($phone,$message);
               /* $response = $this->whatsAppService->sendWelcome($phone);
                Log::info('Résultat de l\'envoi WhatsApp', [
                    'user' => $versement->id,
                    'response' => $response
                ]);*/
            }

        }
        if(!is_null($dossier->bourse) && !is_null($dossier->type)){
            $bourse=Bourse::where('titre',$dossier->bourse)->first();
            if ($bourse->type=='totale'){
                $frais=$bourse->frais;
                $coutt=$bourse->coutt;
                $totalt=$frais+$coutt;


                $versements= \App\Models\Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
                $soldet=$totalt-$versements;

                if ($dossier->phone) {
// Format du numéro de téléphone
                    $phone = $dossier->phone;
                    if (!str_starts_with($phone, '+')) {
                        $phone = '+' . $phone;
                    }
                    $message = "Bonjour {$dossier->nom_complet} , votre versement pour {$versement->motif} d'un montant de {$versement->montant} FCFA a bien été enregistré. Votre solde général est de {$soldet} FCFA.\nChinAfrik Group vous remercie.";
                    Log::info('Tentative d\'envoi de message WhatsApp', [
                        'user' => $versement->id,
                        'phone' => $phone
                    ]);

                    $this->whatsAppService->sendMessage($phone,$message);
                    /* $response = $this->whatsAppService->sendWelcome($phone);
                     Log::info('Résultat de l\'envoi WhatsApp', [
                         'user' => $versement->id,
                         'response' => $response
                     ]);*/
                }
            }else{
                $frais=$bourse->frais;
                $coutp=$bourse->coutp;
                $totalp=$frais+$coutp;


                $versements= \App\Models\Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
                $soldep=$totalp-$versements;

                if ($dossier->phone) {
// Format du numéro de téléphone
                    $phone = $dossier->phone;
                    if (!str_starts_with($phone, '+')) {
                        $phone = '+' . $phone;
                    }
                    $message = "Bonjour {$dossier->nom_complet} , votre versement pour {$versement->motif} d'un montant de {$versement->montant} FCFA a bien été enregistré. Votre solde général est de {$soldep} FCFA.\nChinAfrik Group vous remercie.";
                    Log::info('Tentative d\'envoi de message WhatsApp', [
                        'user' => $versement->id,
                        'phone' => $phone
                    ]);

                    $this->whatsAppService->sendMessage($phone,$message);
                    /* $response = $this->whatsAppService->sendWelcome($phone);
                     Log::info('Résultat de l\'envoi WhatsApp', [
                         'user' => $versement->id,
                         'response' => $response
                     ]);*/
                }
            }


        }

    }


}
