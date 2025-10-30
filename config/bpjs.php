<?php

/**
 * BPJS VClaim REST API Configuration
 * 
 * Menggunakan BPJS VClaim REST API untuk Fasilitas Kesehatan Tingkat Lanjutan (FKTL)
 * Base URL: https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
 * 
 * Bukan PCare! PCare untuk FKTP (klinik kecil)
 */

return [
    'base_url' => env('BPJS_BASE_URL', 'https://new-api.bpjs-kesehatan.go.id/vclaim-rest/'),
    'cons_id' => env('BPJS_CONS_ID'),
    'secret_key' => env('BPJS_SECRET'),
    'user_key' => env('BPJS_USER_KEY'),
    'service_name' => env('BPJS_SERVICE_NAME', 'vclaim'),
    'timeout' => (int) env('BPJS_TIMEOUT', 10),
    'time_offset' => (int) env('BPJS_TIMESTAMP_OFFSET', 0), // in seconds
    'use_mock' => filter_var(env('BPJS_USE_MOCK', true), FILTER_VALIDATE_BOOL),

    'mock' => [
        'peserta_file' => env('BPJS_MOCK_FILE', storage_path('app/mocks/bpjs/peserta.json')),
        'sep_file' => env('BPJS_MOCK_SEP_FILE', storage_path('app/mocks/bpjs/sep.json')),
    ],

    'headers' => [
        'user_key' => env('BPJS_USER_KEY'),
    ],
];
