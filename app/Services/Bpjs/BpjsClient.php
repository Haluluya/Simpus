<?php

namespace App\Services\Bpjs;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * BPJS VClaim REST API Client
 * 
 * Client untuk integrasi dengan BPJS VClaim REST API (bukan PCare)
 * VClaim untuk FKTL (Fasilitas Kesehatan Tingkat Lanjutan) seperti RS dan Puskesmas
 * 
 * Endpoint: https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
 * Auth: HMAC SHA-256 signature
 * 
 * Fitur:
 * - Validasi peserta (by NIK atau No. Kartu)
 * - Manajemen SEP (Create, Update, Delete, Monitor)
 * - Cek rujukan
 * - Referensi (diagnosa, poli, faskes, dll)
 */
class BpjsClient
{
    private string $baseUrl;
    private ?string $consId;
    private ?string $secretKey;
    private ?string $userKey;
    private bool $useMock;
    private int $timeout;
    private int $timeOffset;
    private array $mockConfig;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('bpjs.base_url'), '/');
        $this->consId = config('bpjs.cons_id');
        $this->secretKey = config('bpjs.secret_key');
        $this->userKey = config('bpjs.user_key');
        $this->useMock = (bool) config('bpjs.use_mock', false);
        $this->timeout = (int) config('bpjs.timeout', 10);
        $this->timeOffset = (int) config('bpjs.time_offset', 0);
        $this->mockConfig = (array) config('bpjs.mock', []);
    }

    /**
     * Perform a GET request to BPJS endpoint.
     *
     * @throws RequestException
     */
    public function get(string $endpoint, array $query = []): array
    {
        if (! $this->credentialsAvailable()) {
            return $this->mockResponse($endpoint, $query);
        }

        $response = $this->httpClient()
            ->get($this->buildUrl($endpoint), $query)
            ->throw();

        return $response->json();
    }

    /**
     * Perform a POST request to BPJS endpoint.
     *
     * @throws RequestException
     */
    public function post(string $endpoint, array $payload = [], array $query = []): array
    {
        if (! $this->credentialsAvailable()) {
            return $this->mockResponse($endpoint, ['payload' => $payload] + $query);
        }

        $response = $this->httpClient()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withQueryParameters($query)
            ->post($this->buildUrl($endpoint), $payload)
            ->throw();

        return $response->json();
    }

    /**
     * Perform a PUT request to BPJS endpoint.
     *
     * @throws RequestException
     */
    public function put(string $endpoint, array $payload = [], array $query = []): array
    {
        if (! $this->credentialsAvailable()) {
            return $this->mockResponse($endpoint, ['payload' => $payload] + $query);
        }

        $response = $this->httpClient()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withQueryParameters($query)
            ->put($this->buildUrl($endpoint), $payload)
            ->throw();

        return $response->json();
    }

    /**
     * Perform a DELETE request to BPJS endpoint.
     *
     * @throws RequestException
     */
    public function delete(string $endpoint, array $payload = [], array $query = []): array
    {
        if (! $this->credentialsAvailable()) {
            return $this->mockResponse($endpoint, ['payload' => $payload] + $query);
        }

        $response = $this->httpClient()
            ->withHeaders(['Content-Type' => 'application/json'])
            ->withQueryParameters($query)
            ->delete($this->buildUrl($endpoint), $payload)
            ->throw();

        return $response->json();
    }

    public function validateParticipantByNik(string $nik, string $serviceDate): array
    {
        $endpoint = 'Peserta/Peserta/nik/'.$nik.'/tglPelayanan/'.$serviceDate;

        return $this->get($endpoint);
    }

    /**
     * Validate participant by BPJS card number.
     */
    public function validateParticipantByCard(string $cardNumber, string $serviceDate): array
    {
        $endpoint = 'Peserta/Peserta/nokartu/'.$cardNumber.'/tglPelayanan/'.$serviceDate;

        return $this->get($endpoint);
    }

    /**
     * Create SEP (Surat Eligibilitas Peserta).
     */
    public function createSep(array $payload): array
    {
        $endpoint = 'SEP/SEP/insert';

        return $this->post($endpoint, ['request' => $payload]);
    }

    /**
     * Update SEP data.
     */
    public function updateSep(array $payload): array
    {
        $endpoint = 'SEP/SEP/update';

        return $this->put($endpoint, ['request' => $payload]);
    }

    /**
     * Delete SEP by SEP number.
     */
    public function deleteSep(string $sepNumber, string $userId): array
    {
        $endpoint = 'SEP/SEP/delete';

        return $this->delete($endpoint, [
            'request' => [
                'noSep' => $sepNumber,
                'user' => $userId,
            ],
        ]);
    }

    /**
     * Get referral by referral number.
     */
    public function getReferral(string $referralNumber): array
    {
        $endpoint = 'Rujukan/Rujukan/'.$referralNumber;

        return $this->get($endpoint);
    }

    /**
     * Get list of referrals by participant card number.
     */
    public function getReferralsByCard(string $cardNumber): array
    {
        $endpoint = 'Rujukan/Rujukan/List/Peserta/'.$cardNumber;

        return $this->get($endpoint);
    }

    /**
     * Get referral from primary care (FKTP).
     */
    public function getPrimaryCareReferral(string $referralNumber): array
    {
        $endpoint = 'Rujukan/Rujukan/RS/'.$referralNumber;

        return $this->get($endpoint);
    }

    /**
     * Get diagnoses list.
     */
    public function getDiagnoses(string $keyword): array
    {
        $endpoint = 'referensi/Diagnosa/'.$keyword;

        return $this->get($endpoint);
    }

    /**
     * Get polyclinic list.
     */
    public function getPolyclinics(string $keyword = ''): array
    {
        $endpoint = $keyword ? 'referensi/Poli/'.$keyword : 'referensi/Poli';

        return $this->get($endpoint);
    }

    /**
     * Get provider (healthcare facility) list.
     */
    public function getProviders(string $keyword, string $serviceType = '2'): array
    {
        $endpoint = 'referensi/Faskes/'.$keyword.'/'.$serviceType;

        return $this->get($endpoint);
    }

    /**
     * Get procedure list.
     */
    public function getProcedures(string $keyword): array
    {
        $endpoint = 'referensi/Prosedur/'.$keyword;

        return $this->get($endpoint);
    }

    /**
     * Get service class list.
     */
    public function getServiceClasses(): array
    {
        $endpoint = 'referensi/KelasPelayanan';

        return $this->get($endpoint);
    }

    /**
     * Get doctor DPJP list.
     */
    public function getDoctors(string $serviceDate): array
    {
        $endpoint = 'referensi/Dokter/tglPelayanan/'.$serviceDate;

        return $this->get($endpoint);
    }

    /**
     * Monitor SEP by date and service type.
     */
    public function monitorSep(string $startDate, string $endDate, string $serviceType = '2'): array
    {
        $endpoint = 'Monitoring/Klaim/Tanggal/'.$startDate.'/Jnspelayanan/'.$serviceType.'/Status/0';

        return $this->get($endpoint);
    }

    private function credentialsAvailable(): bool
    {
        return ! $this->useMock
            && filled($this->consId)
            && filled($this->secretKey)
            && filled($this->userKey);
    }

    public function useMock(): bool
    {
        return ! $this->credentialsAvailable();
    }

    private function httpClient(): PendingRequest
    {
        $timestamp = $this->timestamp();
        $signaturePayload = $this->consId.'&'.$timestamp;
        $signature = base64_encode(hash_hmac('sha256', $signaturePayload, $this->secretKey ?? '', true));

        return Http::timeout($this->timeout)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'X-cons-id' => $this->consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'user_key' => $this->userKey,
            ]);
    }

    private function timestamp(): string
    {
        $timestamp = now('UTC')->addSeconds($this->timeOffset)->timestamp;

        return (string) $timestamp;
    }

    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');

        return "{$this->baseUrl}/{$endpoint}";
    }

    private function mockResponse(string $endpoint, array $context = []): array
    {
        Log::info('BPJS mock response', [
            'endpoint' => $endpoint,
            'context' => $context,
        ]);

        // Route mock response based on endpoint pattern
        return match (true) {
            // Referensi endpoints - load from static mock files
            str_contains($endpoint, 'referensi/Diagnosa') => $this->loadMockFile('referensi_diagnosa.json'),
            str_contains($endpoint, 'referensi/Poli') => $this->loadMockFile('referensi_poli.json'),
            str_contains($endpoint, 'referensi/Faskes') => $this->loadMockFile('referensi_faskes.json'),
            str_contains($endpoint, 'referensi/Prosedur') => $this->loadMockFile('referensi_prosedur.json'),
            str_contains($endpoint, 'referensi/KelasPelayanan') => $this->loadMockFile('referensi_kelas.json'),
            str_contains($endpoint, 'referensi/Dokter') => $this->loadMockFile('referensi_dokter.json'),

            // SEP endpoints
            str_contains($endpoint, 'SEP/SEP/insert') => $this->mockSepCreate($context),
            str_contains($endpoint, 'SEP/SEP/update') => $this->loadMockFile('sep_update_success.json'),
            str_contains($endpoint, 'SEP/SEP/delete') => $this->loadMockFile('sep_delete_success.json'),

            // Rujukan endpoints
            str_contains($endpoint, 'Rujukan/Rujukan') => $this->mockRujukan($context),

            // Monitoring
            str_contains($endpoint, 'Monitoring/Klaim') => $this->loadMockFile('monitoring_sep.json'),

            // Peserta validation (by NIK or Card)
            str_contains($endpoint, 'Peserta/Peserta') => $this->mockPeserta($endpoint, $context),

            // Default fallback
            default => $this->mockDefaultResponse(),
        };
    }

    private function loadMockFile(string $filename): array
    {
        $path = 'mocks/bpjs/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            $content = Storage::disk('local')->get($path);
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        Log::warning('BPJS mock file not found', ['path' => $path]);

        return $this->mockDefaultResponse();
    }

    private function mockPeserta(string $endpoint, array $context): array
    {
        // Extract NIK/Card from endpoint URL
        $nik = '0000000000000000';
        $card = '0000000000000';

        // Parse from endpoint: Peserta/Peserta/nik/{nik}/tglPelayanan/{date}
        if (preg_match('/\/nik\/(\d+)/', $endpoint, $matches)) {
            $nik = $matches[1];
        }

        // Parse from endpoint: Peserta/Peserta/nokartu/{card}/tglPelayanan/{date}
        if (preg_match('/\/nokartu\/(\d+)/', $endpoint, $matches)) {
            $card = $matches[1];
        }

        // Fallback: check context/payload
        $payloadContext = Arr::get($context, 'payload', []);
        $nik = $nik !== '0000000000000000' ? $nik : (Arr::get($context, 'nik') ?? Arr::get($payloadContext, 'nik', $nik));
        $card = $card !== '0000000000000' ? $card : (Arr::get($context, 'noKartu') ?? Arr::get($payloadContext, 'bpjs_card_no', $card));

        // ⭐ REALISTIS: Query database untuk cek status (seperti BPJS asli)
        // BPJS asli query database pembayaran iuran, kita query database patients
        $patient = null;
        if ($nik !== '0000000000000000') {
            $patient = \App\Models\Patient::where('nik', $nik)->first();
        }
        if (! $patient && $card !== '0000000000000') {
            $patient = \App\Models\Patient::where('bpjs_card_no', $card)->first();
        }

        \Log::info('BPJS Mock Peserta Lookup', [
            'nik' => $nik,
            'card' => $card,
            'patient_found' => $patient ? true : false,
            'patient_name' => $patient?->name ?? 'not found'
        ]);

        // Determine status dari database (REALISTIS!)
        if ($patient) {
            // Baca status dari meta field (seperti BPJS baca dari database pembayaran)
            $bpjsStatus = $patient->meta['bpjs_status'] ?? 'AKTIF';
            $bpjsClass = $patient->meta['bpjs_class'] ?? 'KELAS I';
            $participantType = $patient->meta['participant_type'] ?? 'PNS';

            \Log::info('BPJS Status from Patient Meta', [
                'patient_id' => $patient->id,
                'current_meta_status' => $bpjsStatus,
                'current_meta_class' => $bpjsClass
            ]);

            // Use status directly from database
            $status = $bpjsStatus;
            $kelas = $bpjsClass;
            $isInactive = ($status === 'TIDAK AKTIF');
            $nama = $patient->name;
            $card = $patient->bpjs_card_no ?? $card;
            $nik = $patient->nik;
            
            // Update status di database sesuai dengan hasil verifikasi
            $bpjsStatus = $status;
        } else {
            // Fallback if not in database (default to active)
            $isInactive = false;
            $status = 'AKTIF';
            $nama = 'Peserta Mock';
            $kelas = 'KELAS I';
            $participantType = 'PNS';

            \Log::info('BPJS Status using fallback (not in database)', [
                'nik' => $nik,
                'card' => $card,
                'status' => $status,
                'kelas' => $kelas
            ]);
        }
        
        // Update patient data with current BPJS status (for real verification scenarios)
        if ($patient) {
            $currentMeta = $patient->meta ?? [];
            $currentMeta['bpjs_status'] = $status;
            $currentMeta['bpjs_class'] = $kelas;
            
            $patient->update(['meta' => $currentMeta]);
            
            \Log::info('BPJS status updated for patient', [
                'patient_id' => $patient->id,
                'patient_name' => $patient->name,
                'bpjs_status' => $status,
                'bpjs_class' => $kelas,
                'nik' => $nik,
                'card' => $card
            ]);
        } else {
            \Log::info('No patient found for BPJS verification, using pattern-based status', [
                'nik' => $nik,
                'card' => $card,
                'determined_status' => $status,
                'is_inactive' => $isInactive
            ]);
        }

        $kodeKelas = $isInactive ? '0' : '1';
        $kodeJenis = $isInactive ? '99' : '11';

        return [
            'metaData' => [
                'code' => '200',
                'message' => 'OK',
            ],
            'response' => [
                'peserta' => [
                    'noKartu' => $card,
                    'nik' => $nik,
                    'nama' => $nama,
                    'pisa' => 'Puskesmas Kenanga',
                    'sex' => 'L',
                    'tglLahir' => '1990-01-15',
                    'tglCetakKartu' => '2020-01-01',
                    'tglTAT' => '2099-12-31',
                    'tglTMT' => '2020-01-01',
                    'umur' => [
                        'umurSekarang' => '35 tahun 0 bulan 0 hari',
                        'umurSaatPelayanan' => '35 tahun 0 bulan 0 hari',
                    ],
                    'hakKelas' => [
                        'kode' => $kodeKelas,
                        'keterangan' => $kelas,
                    ],
                    'jenisPeserta' => [
                        'kode' => $kodeJenis,
                        'keterangan' => $participantType,
                    ],
                    'statusPeserta' => [
                        'kode' => $isInactive ? '0' : '1',
                        'keterangan' => $status,
                    ],
                    'mr' => [
                        'noMR' => 'RM' . substr($nik, -5),
                        'noTelepon' => '08123456789',
                    ],
                    'provUmum' => [
                        'kdProvider' => '1101P001',
                        'nmProvider' => 'Puskesmas Kenanga',
                    ],
                ],
            ],
        ];
    }

    private function mockSepCreate(array $context): array
    {
        $payload = Arr::get($context, 'payload.request', []);
        $noKartu = Arr::get($payload, 'noKartu', '0000000000000');
        $nama = Arr::get($payload, 'nama', 'Peserta Mock');

        // Generate SEP number
        $sepNumber = '0301R001' . date('ymd') . rand(1000, 9999);

        return [
            'metaData' => [
                'code' => '200',
                'message' => 'Sukses!',
            ],
            'response' => [
                'sep' => [
                    'noSep' => $sepNumber,
                    'tglSep' => date('Y-m-d'),
                    'tglPelayanan' => Arr::get($payload, 'tglSep', date('Y-m-d')),
                    'jnsPelayanan' => Arr::get($payload, 'jnsPelayanan', '2'),
                    'kelasRawat' => Arr::get($payload, 'klsRawat', '1'),
                    'noKartu' => $noKartu,
                    'nama' => $nama,
                    'tglLahir' => '1990-01-15',
                    'jnsPeserta' => 'PNS',
                    'pisa' => 'Puskesmas Kenanga',
                    'poliTujuan' => Arr::get($payload, 'poliTujuan', '001'),
                    'diagnosa' => Arr::get($payload, 'diagAwal', 'J06.9'),
                    'catatan' => Arr::get($payload, 'catatan', ''),
                    'noRujukan' => Arr::get($payload, 'noRujukan', ''),
                    'noTelp' => Arr::get($payload, 'noTelp', '08123456789'),
                    'user' => Arr::get($payload, 'user', 'admin_puskesmas'),
                ],
            ],
        ];
    }

    private function mockRujukan(array $context): array
    {
        $template = $this->loadMockFile('rujukan_template.json');

        // You can customize rujukan data here based on context if needed
        return $template;
    }

    private function mockDefaultResponse(): array
    {
        return [
            'metaData' => [
                'code' => '200',
                'message' => 'OK (Mock)',
            ],
            'response' => null,
        ];
    }
}
