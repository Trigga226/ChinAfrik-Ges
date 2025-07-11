<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/telecharger-recu/{record}', [\App\Http\Controllers\PdfController::class, 'genererPdf'])->name('recu')->middleware('auth');

Route::get('/telecharger-pointage/{resource}', [\App\Http\Controllers\CamionsController::class, 'generatePDFCamion'])->name('pointage')->middleware('auth');
