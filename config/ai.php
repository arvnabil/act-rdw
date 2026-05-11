<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    | Supported: "google" (AI Studio), "vertex" (Google Cloud Vertex AI)
    | Switch provider via .env: AI_PROVIDER=google
    */
    'provider' => env('AI_PROVIDER', 'google'),

    /*
    |--------------------------------------------------------------------------
    | Google AI Studio (Default)
    |--------------------------------------------------------------------------
    | Uses GEMINI_API_KEY from .env (already configured via gemini-php/laravel)
    */
    'google' => [
        'model'           => env('GOOGLE_AI_MODEL', 'models/gemini-2.0-flash'),
        'embedding_model' => env('GOOGLE_AI_EMBEDDING_MODEL', 'models/gemini-embedding-001'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Vertex AI (Google Cloud)
    |--------------------------------------------------------------------------
    | Requires: composer require google/cloud-aiplatform
    | Requires service account JSON: GOOGLE_APPLICATION_CREDENTIALS=/path/to/key.json
    */
    'vertex' => [
        'project_id'      => env('VERTEX_PROJECT_ID', ''),
        'location'        => env('VERTEX_LOCATION', 'us-central1'),
        'model'           => env('VERTEX_MODEL', 'gemini-2.0-flash-001'),
        'embedding_model' => env('VERTEX_EMBEDDING_MODEL', 'text-embedding-005'),
    ],

];
