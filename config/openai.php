<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Project
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API project. This is used optionally in
    | situations where you are using a legacy user API key and need association
    | with a project. This is not required for the newer API keys.
    */
    'project' => env('OPENAI_PROJECT'),

    /*
    |--------------------------------------------------------------------------
    | OpenAI Base URL
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API base URL used to make requests. This
    | is needed if using a custom API endpoint. Defaults to: api.openai.com/v1
    */
    'base_uri' => env('OPENAI_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | OCR Model
    |--------------------------------------------------------------------------
    |
    | Modelo a usar para el OCR de ingredientes en el admin scanner.
    | gpt-4o-mini es el default: buen balance precio/calidad para imágenes.
    */

    'ocr_model' => env('OPENAI_OCR_MODEL', 'gpt-4o-mini'),

    /*
    |--------------------------------------------------------------------------
    | Azure OpenAI
    |--------------------------------------------------------------------------
    |
    | Si AZURE_OPENAI_ENDPOINT está seteado, OcrService usa Azure en vez de
    | la API de OpenAI directa. En Azure el "modelo" es el deployment name.
    */

    'azure' => [
        'endpoint'   => env('AZURE_OPENAI_ENDPOINT'),
        'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
        'api_key'    => env('AZURE_OPENAI_API_KEY'),
    ],
];
