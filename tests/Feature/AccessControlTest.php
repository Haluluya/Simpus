<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => PermissionSeeder::class]);
    }

    public function test_audit_log_page_requires_proper_permission(): void
    {
        $doctor = User::factory()->create();
        $doctor->assignRole('doctor');

        $this->actingAs($doctor)
            ->get(route('audit.logs'))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('audit.logs'))
            ->assertOk()
            ->assertSeeText('Audit Trail');
    }
}
