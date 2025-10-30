<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PatientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);
    }

    public function test_admin_can_create_patient_and_view_history(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'nik' => '3273010101010001',
            'bpjs_card_no' => '0001112223334',
            'name' => 'Pasien Uji Coba',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'phone' => '081234000123',
            'address' => 'Jl. Testing No. 1',
        ];

        $response = $this->actingAs($admin)
            ->post(route('patients.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'nik' => $payload['nik'],
            'name' => $payload['name'],
        ]);

        $patient = Patient::where('nik', $payload['nik'])->firstOrFail();

        Visit::factory()->create([
            'patient_id' => $patient->id,
            'provider_id' => $admin->id,
            'coverage_type' => 'BPJS',
            'clinic_name' => 'Poli Umum',
        ]);

        $showResponse = $this->actingAs($admin)
            ->get(route('patients.show', $patient));

        $showResponse
            ->assertOk()
            ->assertSeeText($payload['name'])
            ->assertSeeText('BPJS');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'patient_created',
            'entity_type' => Patient::class,
            'entity_id' => $patient->id,
        ]);
    }
}
