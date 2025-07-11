<?php

namespace App\Observers;

use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    protected WhatsAppService $whatsAppService;
    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }
    public function created(User $user): void
    {
        if ($user->phone) {
// Format du numéro de téléphone
            $phone = $user->phone;
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
            $message = "Bienvenue {$user->name} ! Votre compte a été créé avec succès.";
            Log::info('Tentative d\'envoi de message WhatsApp', [
                'user' => $user->id,
                'phone' => $phone
            ]);
            $response = $this->whatsAppService->sendWelcome($phone);
            Log::info('Résultat de l\'envoi WhatsApp', [
                'user' => $user->id,
                'response' => $response
            ]);
        }
    }

    public function updated(User $user): void
    {
        if ($user->phone) {
// Format du numéro de téléphone
            $phone = $user->phone;
            if (!str_starts_with($phone, '+')) {
                $phone = '+' . $phone;
            }
            $message = "Bonjour {$user->name} ! Votre compte a été modifier.";
            Log::info('Tentative d\'envoi de message WhatsApp', [
                'user' => $user->id,
                'phone' => $phone
            ]);
            $response = $this->whatsAppService->sendMessage($phone,$message);
            Log::info('Résultat de l\'envoi WhatsApp', [
                'user' => $user->id,
                'response' => $response
            ]);
        }
    }
}
