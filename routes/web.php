<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/telecharger-recu/{record}', [\App\Http\Controllers\PdfController::class, 'genererPdf'])->name('recu')->middleware('auth');

Route::get('/telecharger-pointage/{resource}', [\App\Http\Controllers\CamionsController::class, 'generatePDFCamion'])->name('pointage')->middleware('auth');

Route::get('login',function (){
    return redirect('/admin/login');
})->name('login');

// Dans routes/web.php
Route::get('/whatsapp/webhook', function () {
    $mode = request('hub_mode');
    $token = request('hub_verify_token');
    $challenge = request('hub_challenge');

    if ($mode === 'subscribe' && $token === config('whatsapp.webhook_verify_token')) {
        return response($challenge, 200);
    }

    return response('Forbidden', 403);
});

Route::post('/whatsapp/webhook', function () {
    $payload = request()->all();

    Log::info('WhatsApp Webhook Received', $payload);

    // Traiter les status des messages
    if (isset($payload['entry'])) {
        foreach ($payload['entry'] as $entry) {
            if (isset($entry['changes'])) {
                foreach ($entry['changes'] as $change) {
                    if (isset($change['value']['statuses'])) {
                        foreach ($change['value']['statuses'] as $status) {
                            Log::info('Message Status', [
                                'message_id' => $status['id'],
                                'status' => $status['status'],
                                'timestamp' => $status['timestamp']
                            ]);
                        }
                    }
                }
            }
        }
    }

    return response('OK', 200);
});
