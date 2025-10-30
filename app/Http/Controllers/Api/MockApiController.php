<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MockApiController extends Controller
{
    /**
     * Mock BPJS V-Claim: GET peserta by NIK
     */
    public function getPeserta(string $nik)
    {
        Log::info('BPJS Mock: getPeserta', ['nik' => $nik]);

        if ($nik === '1111') {
            return response()->json([
                'status' => 'AKTIF',
                'nama' => 'Pasien Dummy Aktif',
                'nik' => $nik,
                'noKartu' => '0000000000001',
            ]);
        }

        if ($nik === '2222') {
            return response()->json([
                'status' => 'TIDAK AKTIF',
                'nama' => 'Pasien Dummy Nonaktif',
                'nik' => $nik,
                'noKartu' => '0000000000002',
            ]);
        }

        return response()->json([
            'message' => 'Data tidak ditemukan',
        ], 404);
    }

    /**
     * Mock BPJS V-Claim: POST rujukan
     */
    public function postRujukan(Request $request)
    {
        Log::info('BPJS Mock: postRujukan', $request->all());

        return response()->json([
            'status' => 'OK',
            'nomor_rujukan' => 'RJK-DUMMY-12345',
        ]);
    }

    /**
     * Mock SatuSehat: POST Encounter
     */
    public function postEncounter(Request $request)
    {
        Log::info('SatuSehat Mock: Encounter', $request->all());

        return response()->json([
            'status' => 'OK',
            'id_fhir' => 'dummy-encounter-id',
            'resourceType' => 'Encounter',
        ], 201);
    }

    /**
     * Mock SatuSehat: POST DiagnosticReport
     */
    public function postDiagnosticReport(Request $request)
    {
        Log::info('SatuSehat Mock: DiagnosticReport', $request->all());

        return response()->json([
            'status' => 'OK',
            'id_fhir' => 'dummy-diagnostic-report-id',
            'resourceType' => 'DiagnosticReport',
        ], 201);
    }

    /**
     * Mock SatuSehat: POST MedicationRequest
     */
    public function postMedicationRequest(Request $request)
    {
        Log::info('SatuSehat Mock: MedicationRequest', $request->all());

        return response()->json([
            'status' => 'OK',
            'id_fhir' => 'dummy-medication-request-id',
            'resourceType' => 'MedicationRequest',
        ], 201);
    }
}
