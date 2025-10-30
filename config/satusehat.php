<?php

return [
    'base_url' => rtrim(env('SATUSEHAT_BASE_URL', 'https://api-satusehat.kemkes.go.id/fhir-r4'), '/'),
    'auth_url' => rtrim(env('SATUSEHAT_AUTH_URL', 'https://api-satusehat.kemkes.go.id/oauth2/v1'), '/'),
    'client_id' => env('SATUSEHAT_CLIENT_ID'),
    'client_secret' => env('SATUSEHAT_CLIENT_SECRET'),
    'organization_id' => env('SATUSEHAT_ORGANIZATION_ID'),
    'facility_id' => env('SATUSEHAT_FACILITY_ID'),
    'timeout' => (int) env('SATUSEHAT_TIMEOUT', 10),
    'use_mock' => filter_var(env('SATUSEHAT_USE_MOCK', true), FILTER_VALIDATE_BOOL),

    'mock' => [
        'path' => env('SATUSEHAT_MOCK_PATH', storage_path('app/mocks/satusehat')),
    ],

    'oauth' => [
        'scope' => env('SATUSEHAT_SCOPE', 'patient/Patient.write patient/Patient.read diagnostic/DiagnosticResult.write'),
    ],
];
