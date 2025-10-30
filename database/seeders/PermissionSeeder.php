<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'patient.view',
            'patient.create',
            'patient.update',
            'patient.delete',
            'visit.view',
            'visit.create',
            'visit.update',
            'queue.view',
            'queue.create',
            'queue.update',
            'emr.view',
            'emr.create',
            'emr.update',
            'lab.view',
            'lab.create',
            'lab.update',
            'lab.result',
            'prescription.view',
            'prescription.create',
            'prescription.update',
            'referral.view',
            'referral.create',
            'referral.update',
            'medicine.view',
            'medicine.create',
            'medicine.update',
            'medicine.manage',
            'integration.bpjs',
            'integration.satusehat',
            'bpjs.verify',
            'bpjs.sep',
            'satusehat.sync',
            'report.view',
            'report.export',
            'queue.manage',
            'audit.view',
            'user.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'administrator' => $permissions,
            'admin' => $permissions,
            'doctor' => [
                'dashboard.view',
                'patient.view',
                'queue.view',
                'queue.update',
                'visit.view',
                'visit.create',
                'visit.update',
                'emr.create',
                'emr.update',
                'lab.create',
                'prescription.create',
                'referral.create',
                'integration.bpjs',
                'integration.satusehat',
                'bpjs.verify',
                'satusehat.sync',
            ],
            'petugas-rekam-medis' => [
                'dashboard.view',
                'patient.view',
                'visit.view',
                'visit.update',
                'emr.view',
                'emr.create',
                'emr.update',
                'referral.view',
                'referral.create',
                'referral.update',
                'satusehat.sync',
                'report.view',
            ],
            'petugas-pendaftaran' => [
                'dashboard.view',
                'patient.view',
                'patient.create',
                'patient.update',
                'visit.create',
                'queue.view',
                'queue.update',
                'integration.bpjs',
                'bpjs.verify',
            ],
            'petugas-apotek' => [
                'dashboard.view',
                'queue.view',
                'prescription.view',
                'prescription.update',
                'medicine.view',
                'integration.satusehat',
                'satusehat.sync',
            ],
            'lab' => [
                'dashboard.view',
                'queue.view',
                'lab.view',
                'lab.update',
                'lab.result',
                'integration.satusehat',
                'satusehat.sync',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web']
            );
            $role->syncPermissions($rolePermissions);
        }
    }
}

