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
        $mockFile = Arr::get($this->mockConfig, 'peserta_file');

        if ($mockFile && Storage::disk('local')->exists($mockFile)) {
            $content = Storage::disk('local')->get($mockFile);
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

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

        // Determine status based on NIK/Card number pattern
        // NIK ending with 9999 or Card ending with 999 = TIDAK AKTIF
        $isInactive = str_ends_with($nik, '9999') || str_ends_with($card, '999');
        
        $status = $isInactive ? 'TIDAK AKTIF' : 'AKTIF';
        $nama = $isInactive ? 'Peserta Non-Aktif (Mock)' : 'Peserta Aktif (Mock)';
        $kelas = $isInactive ? 'KELAS TIDAK BERLAKU' : 'KELAS I';

        Log::info('BPJS mock response fallback', [
            'endpoint' => $endpoint,
            'context' => $context,
            'nik' => $nik,
            'card' => $card,
            'status' => $status,
        ]);

        return [
            'metaData' => [
                'code' => '200',
                'message' => 'Mocked BPJS response',
            ],
            'response' => [
                'peserta' => [
                    'noKartu' => $card,
                    'nik' => $nik,
                    'nama' => $nama,
                    'hakKelas' => ['keterangan' => $kelas],
                    'statusPeserta' => ['keterangan' => $status],
                    'tglLahir' => '1990-01-01',
                    'pisa' => 'Puskesmas Mock',
                    'jenisKelamin' => 'L',
                ],
            ],
        ];
    }
}
