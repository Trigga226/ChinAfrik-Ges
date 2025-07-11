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
/*    public function sendVersementNotificationWithTemplate(string $to, string $nomclient, string $motif, string $montant, string $documentUrl, string $documentName): array
    {
        try {
            Log::info('=== ENVOI NOTIFICATION AVEC TEMPLATE ===', [
                'to' => $to,
                'nomclient' => $nomclient,
                'motif' => $motif,
                'montant' => $montant
            ]);

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            $templateName = 'facturation'; // Le nom du template

            // Format du numéro et préparation de l'URL du document
            $formattedPhone = $this->formatPhoneNumber($to);
            $publicDocumentUrl = $this->prepareDocumentUrl($documentUrl);

            // Vérifier si l'URL du document est accessible
            if (!$this->isUrlAccessible($publicDocumentUrl)) {
                throw new \Exception("Document non accessible à l'URL : {$publicDocumentUrl}");
            }

            Log::info('Préparation envoi', [
                'template_name' => $templateName,
                'formatted_phone' => $formattedPhone,
                'document_url' => $publicDocumentUrl,
            ]);

            // Construire les composants du template
            $components = [];

            // 1. Composant Header (Document)
            $components[] = [
                'type' => 'header',
                'parameters' => [
                    [
                        'type' => 'document',
                        'document' => [
                            'link' => $publicDocumentUrl
                        ]
                    ]
                ]
            ];

            // 2. Composant Body
            $components[] = [
                'type' => 'body',
                'parameters' => [
                    ['type' => 'text', 'text' => $nomclient],
                    ['type' => 'text', 'text' => $motif],
                    ['type' => 'text', 'text' => $montant]
                ]
            ];

            // Préparer le payload final
            $templatePayload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'fr'],
                    'components' => $components
                ]
            ];

            Log::info('Envoi template - Payload', $templatePayload);

            // Envoyer la requête
            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post($url, $templatePayload);

            $result = $response->json();

            Log::info('Envoi template - Réponse', [
                'status' => $response->status(),
                'response' => $result
            ]);

            // Gérer les erreurs
            if (!$response->successful()) {
                $error = $result['error'] ?? [];
                Log::error('Erreur envoi template', [
                    'status' => $response->status(),
                    'error' => $error
                ]);
                throw new \Exception(
                    "Erreur template WhatsApp: " . ($error['message'] ?? 'Erreur inconnue') .
                    " (Code: " . ($error['code'] ?? 'N/A') . ")"
                );
            }

            Log::info('=== NOTIFICATION TEMPLATE ENVOYÉE AVEC SUCCÈS ===', [
                'to' => $formattedPhone,
                'template_name' => $templateName,
                'message_id' => $result['messages'][0]['id'] ?? null
            ]);

            return [
                'success' => true,
                'response' => $result,
                'message' => 'Notification envoyée avec succès'
            ];

        } catch (\Exception $e) {
            Log::error('=== ERREUR NOTIFICATION TEMPLATE ===', [
                'to' => $to,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['error' => $e->getMessage()];
        }
    }*/
// Méthodes d'aide


// VERSION DE DEBUG - Testez d'abord sans le document
    public function sendVersementNotificationWithTemplate(string $to, string $nomclient, string $motif, string $montant, string $documentUrl, string $documentName): array
    {
        try {
            Log::info('=== ENVOI NOTIFICATION AVEC TEMPLATE (DEBUG) ===', [
                'to' => $to,
                'nomclient' => $nomclient,
                'motif' => $motif,
                'montant' => $montant
            ]);

            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";
            $templateName = 'facturation';
            $formattedPhone = $this->formatPhoneNumber($to);

            // ÉTAPE 1: Tester d'abord SANS le document header
            $components = [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => trim($nomclient)
                        ],
                        [
                            'type' => 'text',
                            'text' => trim($motif)
                        ],
                        [
                            'type' => 'text',
                            'text' => trim($montant)
                        ]
                    ]
                ]
            ];

            $templatePayload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'fr'],
                    'components' => $components
                ]
            ];

            Log::info('DEBUG - Payload sans document', [
                'payload' => json_encode($templatePayload, JSON_PRETTY_PRINT)
            ]);

            $response = Http::withToken($this->token)
                ->timeout(30)
                ->post($url, $templatePayload);

            $result = $response->json();

            Log::info('DEBUG - Réponse sans document', [
                'status' => $response->status(),
                'response' => $result
            ]);

            if (!$response->successful()) {
                $error = $result['error'] ?? [];
                $errorDetails = $result['error']['error_data'] ?? [];

                Log::error('DEBUG - Erreur sans document', [
                    'status' => $response->status(),
                    'error' => $error,
                    'error_details' => $errorDetails
                ]);

                // Si ça échoue même sans document, le problème est dans le template lui-même
                throw new \Exception(
                    "Erreur template (sans document): " . ($error['message'] ?? 'Erreur inconnue') .
                    " - " . ($errorDetails['details'] ?? '')
                );
            }

            // Si ça marche sans document, essayons avec le document
            Log::info('DEBUG - Succès sans document, test avec document...');

            // ÉTAPE 2: Ajouter le document header
            $publicDocumentUrl = $this->prepareDocumentUrl($documentUrl);

            if (!$this->isUrlAccessible($publicDocumentUrl)) {
                throw new \Exception("Document non accessible à l'URL : {$publicDocumentUrl}");
            }

            $componentsWithDocument = [
                [
                    'type' => 'header',
                    'parameters' => [
                        [
                            'type' => 'document',
                            'document' => [
                                'link' => $publicDocumentUrl,
                                'filename' => 'Facture_' . date('YmdHis') . '.pdf'
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => trim($nomclient)
                        ],
                        [
                            'type' => 'text',
                            'text' => trim($motif)
                        ],
                        [
                            'type' => 'text',
                            'text' => trim($montant)
                        ]
                    ]
                ]
            ];

            $templatePayloadWithDoc = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedPhone,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'fr'],
                    'components' => $componentsWithDocument
                ]
            ];

            Log::info('DEBUG - Payload avec document', [
                'payload' => json_encode($templatePayloadWithDoc, JSON_PRETTY_PRINT)
            ]);

            $responseWithDoc = Http::withToken($this->token)
                ->timeout(30)
                ->post($url, $templatePayloadWithDoc);

            $resultWithDoc = $responseWithDoc->json();

            Log::info('DEBUG - Réponse avec document', [
                'status' => $responseWithDoc->status(),
                'response' => $resultWithDoc
            ]);

            if (!$responseWithDoc->successful()) {
                $error = $resultWithDoc['error'] ?? [];
                $errorDetails = $resultWithDoc['error']['error_data'] ?? [];

                Log::error('DEBUG - Erreur avec document', [
                    'status' => $responseWithDoc->status(),
                    'error' => $error,
                    'error_details' => $errorDetails
                ]);

                throw new \Exception(
                    "Erreur template (avec document): " . ($error['message'] ?? 'Erreur inconnue') .
                    " - " . ($errorDetails['details'] ?? '')
                );
            }

            Log::info('=== DEBUG - SUCCÈS AVEC DOCUMENT ===', [
                'message_id' => $resultWithDoc['messages'][0]['id'] ?? null
            ]);

            return [
                'success' => true,
                'response' => $resultWithDoc,
                'message' => 'Notification envoyée avec succès'
            ];

        } catch (\Exception $e) {
            Log::error('=== DEBUG - ERREUR ===', [
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Supprimer tous les caractères non numériques sauf le +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Ajouter le préfixe international si nécessaire
        if (!str_starts_with($phone, '+')) {
            if (str_starts_with($phone, '0')) {
                $phone = '+226' . substr($phone, 1); // Pour le Burkina Faso
            } else {
                $phone = '+' . $phone;
            }
        }

        return $phone;
    }

    private function prepareDocumentUrl(string $documentUrl): string
    {
        if (filter_var($documentUrl, FILTER_VALIDATE_URL)) {
            return $documentUrl;
        }

        $relativePath = str_replace(storage_path('app/public/'), '', $documentUrl);
        return asset('storage/' . $relativePath);
    }

    private function isUrlAccessible(string $url): bool
    {
        try {
            $response = Http::timeout(10)->head($url);
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('URL accessibility check failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    private function getErrorMessage(array $error): string
    {
        $code = $error['error']['code'] ?? 0;
        $message = $error['error']['message'] ?? 'Unknown error';

        $errorMessages = [
            100 => 'Paramètre invalide - Vérifiez la structure du template',
            131000 => 'Template non trouvé ou non approuvé',
            131005 => 'Template non approuvé',
            131021 => 'Paramètres du template invalides',
            131026 => 'Format du numéro de téléphone invalide',
            131047 => 'Réessayez dans quelques minutes',
            131051 => 'Média non supporté',
            131052 => 'Média trop volumineux',
            131053 => 'Média non accessible'
        ];

        return $errorMessages[$code] ?? "Erreur WhatsApp ({$code}): {$message}";
    }
    public function sendVersementNotificationSimple(string $to, string $nomclient, string $motif, string $montant, string $documentUrl, string $documentName): array
    {
        try {
            // Vérifier le format du numéro
            $to = $this->formatPhoneNumber($to);

            // Vérifier l'URL du document
            $publicDocumentUrl = $this->prepareDocumentUrl($documentUrl);

            if (!$this->isUrlAccessible($publicDocumentUrl)) {
                throw new \Exception("Document non accessible à l'URL : {$publicDocumentUrl}");
            }

            $responses = [];
            $url = "{$this->baseUrl}/{$this->version}/{$this->phoneNumberId}/messages";

            // 1. Envoyer le document
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

            $documentResponse = Http::withToken($this->token)
                ->timeout(30)
                ->post($url, $documentPayload);

            $responses['document'] = $documentResponse->json();

            // Vérifier la réponse
            if (!$documentResponse->successful()) {
                Log::error('Erreur envoi document WhatsApp', [
                    'status' => $documentResponse->status(),
                    'response' => $documentResponse->json(),
                    'payload' => $documentPayload
                ]);
                throw new \Exception("Erreur envoi document : " . $documentResponse->body());
            }

            // 2. Attendre entre les messages
            sleep(2);

            // 3. Envoyer le message texte
            $message = $this->buildNotificationMessage($nomclient, $motif, $montant);

            $textPayload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ];

            $textResponse = Http::withToken($this->token)
                ->timeout(30)
                ->post($url, $textPayload);

            $responses['text'] = $textResponse->json();

            if (!$textResponse->successful()) {
                Log::error('Erreur envoi message WhatsApp', [
                    'status' => $textResponse->status(),
                    'response' => $textResponse->json(),
                    'payload' => $textPayload
                ]);
                throw new \Exception("Erreur envoi message : " . $textResponse->body());
            }

            Log::info('Notification WhatsApp envoyée avec succès', [
                'to' => $to,
                'document_sent' => isset($responses['document']['messages']),
                'text_sent' => isset($responses['text']['messages'])
            ]);

            return $responses;

        } catch (\Exception $e) {
            Log::error('Erreur notification WhatsApp', [
                'to' => $to,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

// Méthodes d'aide


    private function buildNotificationMessage(string $nomclient, string $motif, string $montant): string
    {
        return "🎉 *Notification de versement*\n\n" .
            "Bonjour *{$nomclient}*,\n\n" .
            "✅ Votre versement a été effectué avec succès !\n\n" .
            "📋 *Détails :*\n" .
            "• Motif : {$motif}\n" .
            "• Montant : {$montant} FCFA\n" .
            "• Document : Voir ci-dessus\n\n" .
            "Merci pour votre confiance !\n\n" .
            "_L'équipe de gestion_";
    }    public function getTemplates(): array
    {
        try {
            // Utiliser le Business Account ID au lieu du Phone Number ID
            $url = "{$this->baseUrl}/{$this->version}/{$this->businessAccountId}/facturation";

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
