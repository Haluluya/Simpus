<?php

namespace App\Services\Ai;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiClient
{
    private string $baseUrl;
    private ?string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.ai.base_url', config('app.url').'/ai'), '/');
        $this->apiKey = config('services.ai.api_key');
    }

    public function predictRisk(array $payload): array
    {
        if ($this->shouldMock()) {
            return $this->mockPrediction($payload);
        }

        $response = Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(5)
            ->withHeaders($this->apiKey ? ['X-API-Key' => $this->apiKey] : [])
            ->post('/predict', $payload)
            ->throw();

        return $response->json();
    }

    private function shouldMock(): bool
    {
        return blank($this->baseUrl) || str_contains($this->baseUrl, 'localhost:0');
    }

    private function mockPrediction(array $payload): array
    {
        Log::info('AI prediction mocked', ['payload' => $payload]);

        return [
            'risk_score' => rand(10, 90) / 100,
            'category' => 'low',
            'explanation' => 'Mocked AI response. Configure services.ai.base_url for real predictions.',
        ];
    }
}
