<?php

namespace App\Http\Controllers;

use App\Models\Bourse;
use App\Models\DossierPostulant;
use App\Models\Postulant;
use App\Models\Versement;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
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
        //$whatsapp->sendFile($postulant->phone,storage_path('app/public/recu/'.$filename),$paiement->reference,'document',);
        //  $whatsapp->sendFile($postulant->phone,storage_path('app/logo.png'),'toto','document',);
       // $whatsapp->sendMessage('22671301755','Nous paiement de '.$paiement->montant . "de ".$postulant->nom_complet." pour ".$paiement->motif);
        //$this->whatsappService->sendVersementNotificationSimple('22671301755',$postulant->nom_complet,$paiement->motif,$paiement->montant,storage_path('app/public/recu/'.$filename),'Facture');
        //$this->whatsappService->sendVersementNotificationWithTemplate('22671301755',$postulant->nom_complet,$paiement->motif,$paiement->montant,storage_path('app/public/recu/'.$filename),'Facture');

        //$admin=["22671301755","22670692165","8615527905630"];
        $admin=["22671301755","22664575750"];
        foreach($admin as $a){
           // $this->whatsappService->sendVersementNotificationSimple($a,$postulant->nom_complet,$paiement->motif,$paiement->montant,storage_path('app/public/recu/'.$filename),'Facture');
            $this->whatsappService->sendVersementNotificationWithTemplate($a,$postulant->nom_complet,$paiement->motif,$paiement->montant,storage_path('app/public/recu/'.$filename),'Facture',);
        //    $whatsapp->sendWelcome($a);
        //    $whatsapp->sendFile($a,storage_path('app/public/recu/'.$filename),"Nouveau paiement de ".$paiement->montant . "de ".$postulant->nom_complet." pour ".$paiement->motif,'document',);
        }

        $this->listTemplates();

        return $pdf->download($filename);
    }



    public function listTemplates()
    {
        $templates = $this->whatsappService->getTemplates();

        if (isset($templates['error'])) {
            return response()->json(['error' => $templates['error']], 500);
        }

        $templateNames = [];
        if (isset($templates['data'])) {
            foreach ($templates['data'] as $template) {
                $templateNames[] = [
                    'name' => $template['name'],
                    'status' => $template['status'] ?? 'unknown',
                    'language' => $template['language'] ?? 'unknown'
                ];
            }
        }

        return response()->json([
            'templates' => $templateNames,
            'facturation_exists' => $this->whatsappService->templateExists('facturation')
        ]);
    }
}
