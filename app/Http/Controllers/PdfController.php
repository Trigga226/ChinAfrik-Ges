<?php

namespace App\Http\Controllers;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\Versement;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{

    public function genererPdf($id)
    {
        $whatsapp=new WhatsAppService();
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
       // $whatsapp->sendFile($postulant->phone,storage_path('app/public/recu/'.$filename),$paiement->reference,'document',);
      //  $whatsapp->sendFile($postulant->phone,storage_path('app/logo.png'),'toto','document',);
        $whatsapp->sendVersementNotification($phone, $dossier->nom_complet, $paiement->motif, $paiement->montant, storage_path('app/public/recu/'.$filename),$paiement->reference, 'facture');




        return $pdf->download($filename);
    }
}
