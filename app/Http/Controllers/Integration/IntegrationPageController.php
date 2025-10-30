<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\SyncQueue;

class IntegrationPageController extends Controller
{
    public function index()
    {
        $recentPatients = Patient::query()
            ->orderBy('name')
            ->limit(25)
            ->get(['id', 'name', 'medical_record_number', 'nik']);

        $bpjsLastSync = $this->formattedLastSync('BPJS');
        $satusehatLastSync = $this->formattedLastSync('SATUSEHAT');

        return view('integration.index', [
            'recentPatients' => $recentPatients,
            'isBpjsMock' => (bool) config('bpjs.use_mock'),
            'isSatuSehatMock' => (bool) config('satusehat.use_mock'),
            'bpjsLastSync' => $bpjsLastSync,
            'satusehatLastSync' => $satusehatLastSync,
        ]);
    }

    public function bpjsVclaim()
    {
        return view('integration.bpjs-vclaim');
    }

    private function formattedLastSync(string $target): ?string
    {
        $record = SyncQueue::query()
            ->where('target', strtoupper($target))
            ->where('status', 'SUCCESS')
            ->orderByDesc('last_synced_at')
            ->orderByDesc('updated_at')
            ->first(['last_synced_at', 'updated_at']);

        $timestamp = $record?->last_synced_at ?? $record?->updated_at;

        return $timestamp
            ? $timestamp->copy()->timezone(config('app.timezone'))->diffForHumans()
            : null;
    }
}
