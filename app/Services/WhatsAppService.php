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
        $this->token = "EAAOZAI7dqEf0BPMig4ZCJncS3OoI5QGZAqnZBQJ6qTlVq5XtVdIxjjkOXPo3G6m9ZBZCRPs7LY1hSrFY7kI4aIlwlSQJaSICqrudIGVvJGrWw3gWDRkkJPi7SWQDhhvv6MdaLIE2Qth9h6Ca0MMkyYv54lXarzNGmNsVGB8ZCs5NgLOrqFgD3CZAbuUOKZAg4AWePXUgn9UbFP8fysYAdOZC59FGa3rO4jHKrk05TnwFj0nUMaMBndMki50lCOEQZDZD";
        //$this->token = "EAAOZAI7dqEf0BO2SBQmX9eogFHboO18kogs7DesZCBXf8EfdLbFf3SZAtBqgOyROueizQUxzEvGYsLzjEWHM1WQvdn4hOR63iF0fYZBRI74VXwgJpGj1cG8kZAZBfSI6pWbBu7wZA4nr2JU0g2UrHhTJXO4eQeQmr33KG0divKJcoQYMYNeRHZBaAB8zuoOdvMfewAZDZD";
        //$this->phoneNumberId =  "571263062741961";
        $this->phoneNumberId =  "1596973314316789";
        $this->version = "v22.0";
        $this->baseUrl = "https://graph.facebook.com";
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
            $publicDocumentUrl = $documentUrl;
            if (!filter_var($documentUrl, FILTER_VALIDATE_URL)) {
                $relativePath = str_replace(storage_path('app/public/'), '', $documentUrl);
                $publicDocumentUrl = asset('storage/' . $relativePath);
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => config('whatsapp.facturation_template_name'),
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
}
