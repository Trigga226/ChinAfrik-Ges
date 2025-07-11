<?php
return [
    'token' => env('WHATSAPP_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'version' => 'v22.0',
    'base_url' => 'https://graph.facebook.com',
    'logo_url' => 'https://example.com/logo.png',
    'template_name' => env('WHATSAPP_TEMPLATE_NAME'),
    'facturation_template_name' => env('WHATSAPP_FACTURATION_TEMPLATE_NAME', 'facturation'),
];
