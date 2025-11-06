<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\BpjsClaim;
use App\Models\Patient;
use App\Services\Bpjs\BpjsClient;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BpjsController extends Controller
{
    public function __construct(private readonly BpjsClient $client)
    {
    }

    public function cekPesertaByNik(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'string', 'size:16'],
            'service_date' => ['required', 'date'],
        ]);

        $patient = null;

        try {
            $start = microtime(true);
            $response = $this->client->validateParticipantByNik($validated['nik'], $validated['service_date']);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $patient = Patient::where('nik', $validated['nik'])->first();

            // Update patient's BPJS status if patient exists and response contains participant data
            if ($patient && isset($response['response']['peserta'])) {
                $peserta = $response['response']['peserta'];
                $statusKeterangan = $peserta['statusPeserta']['keterangan'] ?? null;
                $kelasKeterangan = $peserta['hakKelas']['keterangan'] ?? 'KELAS I';
                
                if ($statusKeterangan) {
                    $patient->updateBpjsStatus($statusKeterangan, $kelasKeterangan);
                    
                    \Log::info('BPJS status updated via integration menu', [
                        'patient_id' => $patient->id,
                        'patient_name' => $patient->name,
                        'bpjs_status' => $statusKeterangan,
                        'bpjs_class' => $kelasKeterangan,
                        'method' => 'by-nik'
                    ]);
                }
            }

            $claim = BpjsClaim::create([
                'patient_id' => $patient?->id,
                'performed_by' => Auth::id(),
                'interaction_type' => 'CHECK_PARTICIPANT',
                'request_method' => 'GET',
                'endpoint' => 'Peserta/Peserta/nik',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'headers' => [],
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => [
                    'source' => $this->client->useMock() ? 'mock' : 'live',
                ],
            ]);

            Audit::log(
                'bpjs_check_participant',
                BpjsClaim::class,
                $claim->id,
                null,
                [
                    'nik' => $validated['nik'],
                    'patient_id' => $patient?->id,
                    'status_message' => $claim->status_message,
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Throwable $exception) {
            Log::error('BPJS cek peserta gagal', [
                'nik' => $validated['nik'],
                'error' => $exception->getMessage(),
            ]);

            Audit::log(
                'bpjs_check_participant_failed',
                Patient::class,
                $patient?->id ?? null,
                null,
                [
                    'nik' => $validated['nik'],
                    'error' => $exception->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }
    }

    public function cekPesertaByKartu(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_kartu' => ['required', 'string', 'min:13'],
            'service_date' => ['required', 'date'],
        ]);

        try {
            $start = microtime(true);
            $response = $this->client->validateParticipantByCard($validated['no_kartu'], $validated['service_date']);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $patient = Patient::where('bpjs_card_no', $validated['no_kartu'])->first();

            // Update patient's BPJS status if patient exists and response contains participant data
            if ($patient && isset($response['response']['peserta'])) {
                $peserta = $response['response']['peserta'];
                $statusKeterangan = $peserta['statusPeserta']['keterangan'] ?? null;
                $kelasKeterangan = $peserta['hakKelas']['keterangan'] ?? 'KELAS I';
                
                if ($statusKeterangan) {
                    $patient->updateBpjsStatus($statusKeterangan, $kelasKeterangan);
                    
                    \Log::info('BPJS status updated via integration menu', [
                        'patient_id' => $patient->id,
                        'patient_name' => $patient->name,
                        'bpjs_status' => $statusKeterangan,
                        'bpjs_class' => $kelasKeterangan,
                        'method' => 'by-kartu'
                    ]);
                }
            }

            BpjsClaim::create([
                'patient_id' => $patient?->id,
                'performed_by' => Auth::id(),
                'interaction_type' => 'CHECK_PARTICIPANT',
                'request_method' => 'GET',
                'endpoint' => 'Peserta/Peserta/nokartu',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => ['source' => $this->client->useMock() ? 'mock' : 'live'],
            ]);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            Log::error('BPJS cek peserta by kartu gagal', [
                'no_kartu' => $validated['no_kartu'],
                'error' => $exception->getMessage(),
            ]);

            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function createSep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'noKartu' => ['required', 'string'],
            'tglSep' => ['required', 'date_format:Y-m-d'],
            'ppkPelayanan' => ['required', 'string'],
            'jnsPelayanan' => ['required', 'string'],
            'klsRawat' => ['required', 'string'],
            'noMR' => ['required', 'string'],
            'asalRujukan' => ['required', 'string'],
            'tglRujukan' => ['required', 'date_format:Y-m-d'],
            'noRujukan' => ['required', 'string'],
            'ppkRujukan' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
            'diagAwal' => ['required', 'string'],
            'poli' => ['required', 'string'],
            'tujuan' => ['required', 'string'],
            'eksekutif' => ['required', 'string'],
            'cob' => ['required', 'string'],
            'katarak' => ['required', 'string'],
            'lakaLantas' => ['required', 'string'],
            'user' => ['required', 'string'],
        ]);

        try {
            $start = microtime(true);
            $response = $this->client->createSep($validated);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            $patient = Patient::where('bpjs_card_no', $validated['noKartu'])->first();

            BpjsClaim::create([
                'patient_id' => $patient?->id,
                'performed_by' => Auth::id(),
                'interaction_type' => 'CREATE_SEP',
                'request_method' => 'POST',
                'endpoint' => 'SEP/SEP/insert',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => [
                    'source' => $this->client->useMock() ? 'mock' : 'live',
                    'sep_no' => data_get($response, 'response.sep.noSep'),
                ],
            ]);

            Audit::log('bpjs_create_sep', BpjsClaim::class, $patient?->id, null, [
                'no_kartu' => $validated['noKartu'],
                'sep_no' => data_get($response, 'response.sep.noSep'),
            ]);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            Log::error('BPJS create SEP gagal', ['error' => $exception->getMessage(), 'payload' => $validated]);

            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function updateSep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'noSep' => ['required', 'string'],
            'klsRawat' => ['nullable', 'string'],
            'noMR' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
            'diagAwal' => ['nullable', 'string'],
            'poli' => ['nullable', 'string'],
            'user' => ['required', 'string'],
        ]);

        try {
            $start = microtime(true);
            $response = $this->client->updateSep($validated);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            BpjsClaim::create([
                'performed_by' => Auth::id(),
                'interaction_type' => 'UPDATE_SEP',
                'request_method' => 'PUT',
                'endpoint' => 'SEP/SEP/update',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => ['source' => $this->client->useMock() ? 'mock' : 'live'],
            ]);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            Log::error('BPJS update SEP gagal', ['error' => $exception->getMessage(), 'payload' => $validated]);

            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deleteSep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'noSep' => ['required', 'string'],
            'user' => ['required', 'string'],
        ]);

        try {
            $start = microtime(true);
            $response = $this->client->deleteSep($validated['noSep'], $validated['user']);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            BpjsClaim::create([
                'performed_by' => Auth::id(),
                'interaction_type' => 'DELETE_SEP',
                'request_method' => 'DELETE',
                'endpoint' => 'SEP/SEP/delete',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => ['source' => $this->client->useMock() ? 'mock' : 'live'],
            ]);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            Log::error('BPJS delete SEP gagal', ['error' => $exception->getMessage(), 'sep' => $validated['noSep']]);

            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function cekRujukan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'no_rujukan' => ['required', 'string'],
        ]);

        try {
            $start = microtime(true);
            $response = $this->client->getReferral($validated['no_rujukan']);
            $durationMs = (int) ((microtime(true) - $start) * 1000);

            BpjsClaim::create([
                'performed_by' => Auth::id(),
                'interaction_type' => 'CHECK_REFERRAL',
                'request_method' => 'GET',
                'endpoint' => 'Rujukan/Rujukan',
                'status_code' => 200,
                'status_message' => data_get($response, 'metaData.message'),
                'response_time_ms' => $durationMs,
                'raw_request' => json_encode($validated),
                'raw_response' => json_encode($response),
                'performed_at' => now(),
                'meta' => ['source' => $this->client->useMock() ? 'mock' : 'live'],
            ]);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            Log::error('BPJS cek rujukan gagal', ['error' => $exception->getMessage()]);

            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function getDiagnoses(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'min:3'],
        ]);

        try {
            $response = $this->client->getDiagnoses($validated['keyword']);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function getPolyclinics(Request $request): JsonResponse
    {
        try {
            $keyword = $request->input('keyword', '');
            $response = $this->client->getPolyclinics($keyword);

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function monitorSep(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d'],
            'service_type' => ['nullable', 'string'],
        ]);

        try {
            $response = $this->client->monitorSep(
                $validated['start_date'],
                $validated['end_date'],
                $validated['service_type'] ?? '2'
            );

            return response()->json(['success' => true, 'data' => $response]);
        } catch (\Throwable $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
