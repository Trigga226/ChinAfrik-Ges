<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $version;
    protected string $baseUrl;
    public function __construct()
    {
        //$this->token = "EAAOZAI7dqEf0BPMig4ZCJncS3OoI5QGZAqnZBQJ6qTlVq5XtVdIxjjkOXPo3G6m9ZBZCRPs7LY1hSrFY7kI4aIlwlSQJaSICqrudIGVvJGrWw3gWDRkkJPi7SWQDhhvv6MdaLIE2Qth9h6Ca0MMkyYv54lXarzNGmNsVGB8ZCs5NgLOrqFgD3CZAbuUOKZAg4AWePXUgn9UbFP8fysYAdOZC59FGa3rO4jHKrk05TnwFj0nUMaMBndMki50lCOEQZDZD";
        $this->token = "EAAOZAI7dqEf0BPMAnEPOenjYurUDJ5f18HNnJIoRy4OLfTW1bsOBoLC2ZBwoes7vEeSgTj56zz9wKBAfZCf6Ucxm9a2m9HII2CjLaMq3MzmWnqZA3srff3DE7aueNH5o4rFoe8b3OYDZAS3mUMyUSrnNOoTRGZCpXLsKZChX3xbrNANL2aGrNeu1CerW9pZCfwZDZD";
        //$this->token = "EAAOZAI7dqEf0BO2SBQmX9eogFHboO18kogs7DesZCBXf8EfdLbFf3SZAtBqgOyROueizQUxzEvGYsLzjEWHM1WQvdn4hOR63iF0fYZBRI74VXwgJpGj1cG8kZAZBfSI6pWbBu7wZA4nr2JU0g2UrHhTJXO4eQeQmr33KG0divKJcoQYMYNeRHZBaAB8zuoOdvMfewAZDZD";
        //$this->phoneNumberId =  "571263062741961";
        //$this->phoneNumberId =  "1596973314316789";
        $this->phoneNumberId =  "571263062741961";
        $this->version = "v22.0";
        $this->baseUrl = "https://graph.facebook.com";
        $this->businessAccountId = env('WHATSAPP_BUSINESS_ACCOUNT_ID', '552281194323457');
    }
    public function sendMessage(string $to, string $message): array
    {
        try {
            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            $responses = [];
// 1. Envoi du template

// 2. Envoi de l'image

// 3. Envoi du message personnalisé
            $textResponse = Http::withToken($this->token)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => ['body' => $message]
                ]);
            $responses['text'] = $textResponse->json();
            return $responses;
        } catch (\Exception $e) {
            Log::error('WhatsApp API Error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }


    public function sendWelcome(string $to): array
    {
        $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
        try {
            $responses = [];
// 1. Envoi du template
            $templateResponse = Http::withToken($this->token)
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => config('whatsapp.template_name'),
                        'language' => [
                            'code' => 'fr'
                        ]
                    ]
                ]);
            $responses['template'] = $templateResponse->json();
            sleep(1);
            return $responses;
        }catch (\Exception $e){
            Log::error('WhatsApp API Error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }


    public function sendFile(string $to, string $filePath ,string $titre, string $type = 'document'): array
    {

        try {
            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";

            // Normaliser le chemin du fichier
            if (!str_starts_with($filePath, '/')) {
                $filePath = storage_path('app/' . $filePath);
            }

            Log::info('WhatsApp File Send - Starting', [
                'to' => $to,
                'filePath' => $filePath,
                'type' => $type
            ]);

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                Log::error('WhatsApp File Send - File not found', ['filePath' => $filePath]);
                throw new \Exception("Le fichier n'existe pas: {$filePath}");
            }

            // Vérifier la taille du fichier
            $fileSize = filesize($filePath);
            if ($fileSize === false) {
                throw new \Exception("Impossible de lire la taille du fichier");
            }

            // Vérifier le type MIME
            $mimeType = mime_content_type($filePath);

            Log::info('WhatsApp File Send - File details', [
                'size' => $fileSize,
                'mimeType' => $mimeType
            ]);

            // Obtenir l'URL publique du fichier
            $relativePath = str_replace(storage_path('app/public/'), '', $filePath);
            $fileUrl = asset('storage/' . $relativePath);


            // Vérifier si l'URL est accessible
            $testResponse = Http::get($fileUrl);
            if (!$testResponse->successful()) {
                Log::info('WhatsApp File Send - File URL not accessible', [
                    'url' => $fileUrl,
                    'status' => $testResponse->status()
                ]);
                throw new \Exception("L'URL du fichier n'est pas accessible publiquement: {$fileUrl}");
            }

            Log::info('WhatsApp File Send - File URL generated and tested', [
                'originalPath' => $filePath,
                'relativePath' => $relativePath,
                'fileUrl' => $fileUrl,
                'urlAccessible' => $testResponse->successful()
            ]);


            Log::info('WhatsApp File Send - Starting', [
                'to' => $to,
                'filePath' => $filePath,
                'type' => $type
            ]);

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                throw new \Exception("Le fichier n'existe pas: {$filePath}");
            }

            // Obtenir l'URL publique du fichier
            $relativePath = str_replace(storage_path('app/public/'), '', $filePath);
            $fileUrl = asset('storage/' . $relativePath);

            Log::info('WhatsApp File Send - File URL generated', [
                'originalPath' => $filePath,
                'relativePath' => $relativePath,
                'fileUrl' => $fileUrl
            ]);

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => $type,
                $type => [
                    'link' => $fileUrl,
                    'caption' => $titre
                ]
            ];

            Log::info('WhatsApp File Send - Request payload', ['payload' => $payload]);

            $response = Http::withToken($this->token)
                ->post($url, $payload);

            $result = $response->json();






            if (!$response->successful()) {
                throw new \Exception("Erreur WhatsApp: " . ($result['error']['message'] ?? 'Unknown error'));
            }

            return $result;
        } catch (\Exception $e) {
            Log::info('WhatsApp File Send Error', [
                'to' => $to,
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);

            return ['error' => $e->getMessage()];
        }
    }


    /**
     * Envoie une notification de versement via WhatsApp en utilisant un template.
     *
     * @param string $to Le numéro de téléphone du destinataire.
     * @param string $templateName Le nom du template WhatsApp.
     * @param string $nomclient Le nom du client.
     * @param string $motif Le motif du versement.
     * @param string $montant Le montant du versement.
     * @param string $documentUrl L'URL publique du document à envoyer.
     * @param string $documentName Le nom du fichier du document.
     * @return array La réponse de l'API WhatsApp.
     */
    public function sendVersementNotification(string $to, string $nomclient, string $motif, string $montant, string $documentUrl, string $documentName): array
    {
        $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";

        try {
            $templateName = config('whatsapp.facturation_template_name', 'facturation');

            $publicDocumentUrl = $documentUrl;
            if (!filter_var($documentUrl, FILTER_VALIDATE_URL)) {
                $relativePath = str_replace(storage_path('app/public/'), '', $documentUrl);
                $publicDocumentUrl = asset('storage/' . $relativePath);
            }

            // Structure corrigée du payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => 'facturation',
                    'language' => [
                        'code' => 'fr'
                    ],
                    'components' => [
                        [
                            'type' => 'header',
                            'parameters' => [
                                [
                                    'type' => 'document',
                                    'document' => [
                                        'link' => $publicDocumentUrl,
                                        'filename' => $documentName
                                    ]
                                ]
                            ]
                        ],
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $nomclient
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $motif
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $montant
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            Log::info('WhatsApp Versement Notification Payload', ['payload' => $payload]);

            $response = Http::withToken($this->token)->post($url, $payload);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('WhatsApp Versement Notification Error', [
                    'to' => $to,
                    'response' => $error
                ]);
                throw new \Exception("Erreur WhatsApp: " . ($error['error']['message'] ?? 'Unknown error'));
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WhatsApp API Error on versement notification', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public function sendVersementNotificationSimple(string $to, string $nomclient, string $motif, string $montant, string $documentUrl, string $documentName): array
    {
        try {
            $responses = [];

            // 1. Préparer l'URL publique du document
            $publicDocumentUrl = $documentUrl;
            if (!filter_var($documentUrl, FILTER_VALIDATE_URL)) {
                $relativePath = str_replace(storage_path('app/public/'), '', $documentUrl);
                $publicDocumentUrl = asset('storage/' . $relativePath);
            }

            // 2. Envoyer le document
            Log::info('Envoi du document WhatsApp', [
                'to' => $to,
                'document_url' => $publicDocumentUrl,
                'document_name' => $documentName
            ]);

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";

            $documentPayload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'document',
                'document' => [
                    'link' => $publicDocumentUrl,
                    'caption' => "Document de versement - {$documentName}",
                    'filename' => $documentName
                ]
            ];

            $documentResponse = Http::withToken($this->token)->post($url, $documentPayload);
            $responses['document'] = $documentResponse->json();

            if (!$documentResponse->successful()) {
                Log::error('Erreur envoi document WhatsApp', [
                    'response' => $documentResponse->json()
                ]);
            }

            // 3. Attendre un peu entre les messages
            sleep(2);

            // 4. Envoyer le message de notification
            $message = "🎉 *Notification de versement*\n\n";
            $message .= "Bonjour *{$nomclient}*,\n\n";
            $message .= "✅ Votre versement a été effectué avec succès !\n\n";
            $message .= "📋 *Détails :*\n";
            $message .= "• Motif : {$motif}\n";
            $message .= "• Montant : {$montant} FCFA\n";
            $message .= "• Document : Voir ci-dessus\n\n";
            $message .= "Merci pour votre confiance !\n\n";
            $message .= "_L'équipe de gestion_";

            $textPayload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ];

            $textResponse = Http::withToken($this->token)->post($url, $textPayload);
            $responses['text'] = $textResponse->json();

            if (!$textResponse->successful()) {
                Log::error('Erreur envoi message WhatsApp', [
                    'response' => $textResponse->json()
                ]);
            }

            Log::info('Notification WhatsApp envoyée', [
                'to' => $to,
                'responses' => $responses
            ]);

            return $responses;

        } catch (\Exception $e) {
            Log::error('Erreur notification WhatsApp', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => $e->getMessage()];
        }
    }
    public function getTemplates(): array
    {
        try {
            // Utiliser le Business Account ID au lieu du Phone Number ID
            $url = "{$this->baseUrl}/{$this->version}/{$this->businessAccountId}/message_templates";

            $response = Http::withToken($this->token)->get($url);

            if (!$response->successful()) {
                Log::error('WhatsApp Get Templates Error', [
                    'url' => $url,
                    'response' => $response->json()
                ]);
                return ['error' => 'Erreur lors de la récupération des templates'];
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WhatsApp Get Templates Exception', [
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    public function templateExists(string $templateName): bool
    {
        $templates = $this->getTemplates();

        if (isset($templates['error'])) {
            return false;
        }


        if (isset($templates['data'])) {
            foreach ($templates['data'] as $template) {
                if ($template['name'] === $templateName) {
                    return true;
                }
            }
        }

        return false;
    }
}
