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
    | OCR Provider
    |--------------------------------------------------------------------------
    |
    | Valores soportados:
    | - ollama_service: usa el microservicio Python de Ophi con Ollama-OCR.
    | - ollama: usa un servidor Ollama local/remoto con modelo vision directo.
    | - azure: usa Azure OpenAI.
    | - openai: usa OpenAI directo.
    |
    | Si queda vacio se mantiene el comportamiento anterior: Azure cuando estan
    | configuradas las tres variables AZURE_OPENAI_*, OpenAI en caso contrario.
    */

    'ocr_provider' => env('OCR_PROVIDER'),

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

    /*
    |--------------------------------------------------------------------------
    | Ollama OCR
    |--------------------------------------------------------------------------
    |
    | Ophi puede llamar directo a la API nativa de Ollama (/api/generate), o al
    | microservicio Python que usa Ollama-OCR + OpenCV para preprocesamiento.
    */

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://host.docker.internal:11434'),
        'model'    => env('OLLAMA_OCR_MODEL', 'llama3.2-vision:11b'),
        'timeout'  => env('OLLAMA_REQUEST_TIMEOUT', 180),
        'num_predict' => env('OLLAMA_NUM_PREDICT', 1400),
    ],

    'ocr_service' => [
        'url'        => env('OCR_SERVICE_URL', 'http://ocr-service:8000'),
        'timeout'    => env('OCR_SERVICE_TIMEOUT', 240),
        'preprocess' => env('OCR_PREPROCESS', true),
        'language'   => env('OCR_LANGUAGE', 'Spanish'),
    ],
];
