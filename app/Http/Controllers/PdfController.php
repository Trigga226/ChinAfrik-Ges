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

            $versement=Versement::where('dossier_id',$dossier->id)->sum('montant');
          
            $soldet=$totalt-$versement;
            $soldep=$totalp-$versement;


      



        }else{
            if ($bourse->type=='totale'){
                $bourse=Bourse::where('titre',$dossier->bourse)->first();

                $frais=$bourse->frais;
                $coutt=$bourse->coutt;
                $totalt=$frais+$coutt;

                $versement=Versement::where('dossier_id',$dossier->id)->sum('montant');
                $soldet=$totalt-$versement;
            }else{
                $bourse=Bourse::where('titre',$dossier->bourse)->first();

                $frais=$bourse->frais;
                $coutp=$bourse->coutp;
                $totalp=$frais+$coutp;

                $versement=Versement::where('dossier_id',$dossier->id)->sum('montant');
                $soldet=$totalp-$versement;
            }
        }

        $filename=$paiement->reference.".pdf";

        $pdf = PDF::loadView('pdf.recu', compact(['paiement','postulant','dossier','bourse','soldep','soldet',]));
        $pdf->save(storage_path('app/public/recu/'.$filename));
        $phone=str_replace("+",'',$postulant->phone);






        $this->whatsappService->sendVersementConfirmationNotificationWithTemplate($phone,$postulant->nom_complet,$paiement->motif,$paiement->montant,storage_path('app/public/recu/'.$filename),'Facture',);

        $admin=["22664166061","22670692165","8615527905630","22671301755"];
        //$admin=["22671301755"];
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

    public function genererExportPdf()
    {
        // Vous devrez récupérer les données nécessaires pour votre vue ici
        $data = [
            'client' => (object)['designation' => 'Client Test', 'phone' => '123456789', 'email' => 'test@client.com'],
            'location' => (object)['date_debut' => '2025-07-15', 'duree' => 10, 'camions' => [(object)['designation' => 'Camion 1'], (object)['designation' => 'Camion 2']]],
            'statistiques' => [
                'Camion 1' => ['jours_travailles' => 5, 'jours_restants' => 5, 'total_ravitailler' => 150],
                'Camion 2' => ['jours_travailles' => 7, 'jours_restants' => 3, 'total_ravitailler' => 200]
            ],
            'records' => [
                (object)['date' => '2025-07-15', 'camion' => 'Camion 1', 'chauffeurs' => (object)['nom' => 'Dupont', 'prenom' => 'Jean'], 'heure_sortie' => '08:00', 'heure_retour' => '17:00', 'a_travailler' => true, 'ravitailler' => true, 'qte_ravitailler' => 50],
                (object)['date' => '2025-07-15', 'camion' => 'Camion 2', 'chauffeurs' => (object)['nom' => 'Martin', 'prenom' => 'Pierre'], 'heure_sortie' => '08:00', 'heure_retour' => '17:00', 'a_travailler' => true, 'ravitailler' => false, 'qte_ravitailler' => 0]
            ],
            'date' => date('d/m/Y')
        ];

        $pdf = PDF::loadView('pdf.export', $data);
        $pdf->save(public_path('kosbora.pdf'));

        return $pdf->download('kosbora.pdf');
    }
}
