<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class CamionsController extends Controller
{
    public function generatePDFCamion(Request $request)
    {

        dd($request);


        $pdf = PDF::loadView('pdf.export', compact([]));
        $pdf->save(storage_path('app/public/pointage/'.'toto.pdf'));
        return $pdf->download($filename);

    }
}
