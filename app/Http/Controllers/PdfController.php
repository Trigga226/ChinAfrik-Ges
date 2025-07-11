<?php

namespace App\Http\Controllers;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\Versement;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    protected WhatsAppService $whatsAppService;
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    public function genererPdf($id)
    {
        $solde=0;
        $soldet=0;
        $soldep=0;

        $paiement=Versement::find($id);
        $dossier=DossierPostulant::find($paiement->dossier_id);
        $postulant=Postulant::find($dossier->postulant_id);
        $bourse=Bourse::where('titre',$dossier->bourse)->first();

        if(!is_null($dossier->bourse) && is_null($dossier->type)){
            $bourse=Bourse::where('titre',$dossier->bourse)->first();

            $frais=$bourse->frais;
            $coutt=$bourse->coutt;
            $totalt=$frais+$coutt;
            $coutp=$bourse->coutp;
            $totalp=$frais+$coutp;

            $versement=Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
            $soldet=$totalt-$versement;
            $soldep=$totalp-$versement;



        }else{
            if ($bourse->type=='totale'){
                $bourse=Bourse::where('titre',$dossier->bourse)->first();

                $frais=$bourse->frais;
                $coutt=$bourse->coutt;
                $totalt=$frais+$coutt;

                $versement=Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
                $soldet=$totalt-$versement;
            }else{
                $bourse=Bourse::where('titre',$dossier->bourse)->first();

                $frais=$bourse->frais;
                $coutp=$bourse->coutp;
                $totalp=$frais+$coutp;

                $versement=Versement::where('dossier_id',$dossier->id)->first()->sum('montant');
                $soldet=$totalp-$versement;
            }
        }

        $filename=$paiement->reference.".pdf";

        $pdf = PDF::loadView('pdf.recu', compact(['paiement','postulant','dossier','bourse','soldep','soldet',]));
        $pdf->save(storage_path('app/public/recu/'.$filename));
        $phone=str_replace("+",'',$postulant->phone);






        //$whatsapp->sendWelcome($phone);
      $response1= $this->whatsAppService->sendFile($postulant->phone,storage_path('app/public/recu/'.$filename),$paiement->reference,'document',);
      //  $whatsapp->sendFile($postulant->phone,storage_path('app/logo.png'),'toto','document',);
       $response2= $this->whatsAppService->sendMessage($phone,"Nouveau versement de ".$dossier->nom_complet." pour".$paiement->motif." d'un montant de ".$paiement->montant." FCFA");
       $response3= $this->whatsAppService->sendVersementNotification($phone, $dossier->nom_complet, $paiement->motif, $paiement->montant, storage_path('app/public/recu/'.$filename),$paiement->reference, 'facture');


        Log::info('Résultat de l\'envoi WhatsApp', [

            'response1' => $response1
        ]);

        Log::info('Résultat de l\'envoi WhatsApp', [

            'response2' => $response2
        ]);

        Log::info('Résultat de l\'envoi WhatsApp', [

            'response3' => $response3
        ]);

        return $pdf->download($filename);
    }
}
