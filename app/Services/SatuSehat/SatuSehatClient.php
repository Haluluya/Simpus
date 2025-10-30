<?php

namespace App\Services\SatuSehat;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SatuSehatClient
{
    private string $baseUrl;
    private string $authUrl;
    private ?string $clientId;
    private ?string $clientSecret;
    private bool $useMock;
    private int $timeout;
    private array $mockConfig;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('satusehat.base_url'), '/');
        $this->authUrl = rtrim(config('satusehat.auth_url'), '/');
        $this->clientId = config('satusehat.client_id');
        $this->clientSecret = config('satusehat.client_secret');
        $this->useMock = (bool) config('satusehat.use_mock', false);
        $this->timeout = (int) config('satusehat.timeout', 10);
        $this->mockConfig = (array) config('satusehat.mock', []);
    }

    /**
     * Send resource payload to SATUSEHAT FHIR endpoint.
     *
     * @throws RequestException
     */
    public function postResource(string $resourceType, array $payload): array
    {
        if ($this->shouldUseMock()) {
            return $this->storeMockPayload($resourceType, $payload);
        }

        $response = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout)
            ->withToken($this->accessToken())
            ->post($resourceType, $payload)
            ->throw();

        return $response->json();
    }

    public function accessToken(): string
    {
        $cacheKey = $this->cacheKey();

        if ($token = Cache::get($cacheKey)) {
            return $token;
        }

        if ($this->shouldUseMock()) {
            $token = Str::uuid()->toString();
            Cache::put($cacheKey, $token, now()->addMinutes(5));

            return $token;
        }

        $response = Http::asForm()
            ->timeout($this->timeout)
            ->post($this->authUrl.'/oauth2/v1/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ])
            ->throw()
            ->json();

        $token = $response['access_token'] ?? '';
        $expiresIn = (int) ($response['expires_in'] ?? 3600);
        Cache::put($cacheKey, $token, now()->addSeconds(max(60, $expiresIn - 60)));

        return $token;
    }

    private function cacheKey(): string
    {
        return 'satusehat:access_token';
    }

    private function shouldUseMock(): bool
    {
        return $this->useMock || blank($this->clientId) || blank($this->clientSecret);
    }

    private function storeMockPayload(string $resourceType, array $payload): array
    {
        $basePath = $this->mockConfig['path'] ?? 'mocks/satusehat';
        $timestamp = now()->format('Ymd_His');
        $fileName = "{$resourceType}_{$timestamp}_".Str::random(6).'.json';
        $path = trim($basePath, '/').'/'.$fileName;

        Storage::put($path, json_encode([
            'resourceType' => $resourceType,
            'payload' => $payload,
            'saved_at' => now()->toISOString(),
        ], JSON_PRETTY_PRINT));

        Log::info('SATUSEHAT mock payload stored', [
            'path' => $path,
            'resourceType' => $resourceType,
        ]);

        return [
            'resourceType' => $resourceType,
            'mock_path' => $path,
            'status' => 'mocked',
        ];
    }
}
