<?php

use App\Http\Controllers\Api\MockApiController;
use Illuminate\Support\Facades\Route;

// Mock API BPJS V-Claim
Route::prefix('mock/bpjs/vclaim')->group(function () {
    Route::get('peserta/{nik}', [MockApiController::class, 'getPeserta']);
    Route::post('rujukan', [MockApiController::class, 'postRujukan']);
});

// Mock API SatuSehat
Route::prefix('mock/satusehat')->group(function () {
    Route::post('Encounter', [MockApiController::class, 'postEncounter']);
    Route::post('DiagnosticReport', [MockApiController::class, 'postDiagnosticReport']);
    Route::post('MedicationRequest', [MockApiController::class, 'postMedicationRequest']);
});
