<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $registrationRole = Role::firstOrCreate(['name' => 'petugas-pendaftaran', 'guard_name' => 'web']);
        $medicalRecordRole = Role::firstOrCreate(['name' => 'petugas-rekam-medis', 'guard_name' => 'web']);
        $pharmacyRole = Role::firstOrCreate(['name' => 'petugas-apotek', 'guard_name' => 'web']);
        $labRole = Role::firstOrCreate(['name' => 'lab', 'guard_name' => 'web']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@simpus.test'],
            [
                'name' => 'Administrator SIMPUS',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'designation' => 'Administrator',
            ]
        );
        $admin->syncRoles($adminRole->name);

        $doctor = User::firstOrCreate(
            ['email' => 'dokter@simpus.test'],
            [
                'name' => 'Dokter Umum',
                'password' => Hash::make('password123'),
                'phone' => '081122112233',
                'designation' => 'Dokter',
                'department' => 'Poli Umum',
                'license_number' => 'STR12345678',
                'professional_identifier' => 'DRSIMPUS01',
            ]
        );
        $doctor->syncRoles($doctorRole->name);

        $medicalRecordOfficer = User::firstOrCreate(
            ['email' => 'rekammedis@simpus.test'],
            [
                'name' => 'Petugas Rekam Medis',
                'password' => Hash::make('password123'),
                'phone' => '081266778899',
                'designation' => 'Petugas Rekam Medis',
                'department' => 'Unit Rekam Medis',
            ]
        );
        $medicalRecordOfficer->syncRoles($medicalRecordRole->name);

        $registrar = User::firstOrCreate(
            ['email' => 'pendaftaran@simpus.test'],
            [
                'name' => 'Petugas Pendaftaran',
                'password' => Hash::make('password123'),
                'phone' => '081122334455',
                'designation' => 'Petugas Pendaftaran',
                'department' => 'Loket Pendaftaran',
            ]
        );
        $registrar->syncRoles($registrationRole->name);

        $pharmacist = User::firstOrCreate(
            ['email' => 'apotik@simpus.test'],
            [
                'name' => 'Petugas Apotek',
                'password' => Hash::make('password123'),
                'phone' => '081355667788',
                'designation' => 'Petugas Apotek',
                'department' => 'Apotek',
            ]
        );
        $pharmacist->syncRoles($pharmacyRole->name);

        $labOfficer = User::firstOrCreate(
            ['email' => 'lab@simpus.test'],
            [
                'name' => 'Petugas Laboratorium',
                'password' => Hash::make('password123'),
                'phone' => '081155667788',
                'designation' => 'Analis Laboratorium',
                'department' => 'Laboratorium',
            ]
        );
        $labOfficer->syncRoles($labRole->name);
    }
}

