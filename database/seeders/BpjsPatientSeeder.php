<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Seeder;

/**
 * Seeder untuk data pasien dengan integrasi BPJS
 *
 * Konsep BPJS:
 * - NIK: 16 digit (Nomor Induk Kependudukan)
 * - No BPJS: 13 digit (Nomor Kartu BPJS)
 * - Status AKTIF: Peserta yang rutin bayar iuran, berhak layanan
 * - Status TIDAK AKTIF: Peserta yang menunggak iuran, tidak berhak layanan
 */
class BpjsPatientSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table to avoid duplicates
        Patient::where('nik', 'like', '1111%')->delete();

        $patients = [
            // ========================================
            // PESERTA AKTIF (Normal Case)
            // ========================================
            // Note: Using NIK prefix 1111 to avoid collision with random factory data
            [
                'medical_record_number' => 'RM00001',
                'nik' => '1111012345678901',      // 16 digit NIK (prefix 1111 = reserved for seeder)
                'bpjs_card_no' => '1111234567890', // 13 digit BPJS (linked to NIK)
                'name' => 'BUDI SANTOSO',
                'date_of_birth' => '1990-01-15',
                'gender' => 'male',
                'blood_type' => 'O',
                'phone' => '08123456789',
                'email' => 'budi.santoso@example.com',
                'address' => 'Jl. Merdeka No. 123',
                'village' => 'Kenanga',
                'district' => 'Sukamaju',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40123',
                'occupation' => 'PNS',
                'allergies' => null,
                'emergency_contact_name' => 'Siti Santoso',
                'emergency_contact_phone' => '08123456780',
                'emergency_contact_relation' => 'Istri',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS I',
                    'participant_type' => 'PNS',
                ],
            ],
            [
                'medical_record_number' => 'RM00002',
                'nik' => '1111012345678902',
                'bpjs_card_no' => '1111234567891',
                'name' => 'SITI NURHALIZA',
                'date_of_birth' => '1985-05-20',
                'gender' => 'female',
                'blood_type' => 'A',
                'phone' => '08234567890',
                'email' => 'siti.nurhaliza@example.com',
                'address' => 'Jl. Sudirman No. 45',
                'village' => 'Melati',
                'district' => 'Sukamulya',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40124',
                'occupation' => 'PNS',
                'allergies' => 'Penisilin',
                'emergency_contact_name' => 'Ahmad Nurhaliza',
                'emergency_contact_phone' => '08234567891',
                'emergency_contact_relation' => 'Suami',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS I',
                    'participant_type' => 'PNS',
                ],
            ],
            [
                'medical_record_number' => 'RM00003',
                'nik' => '1111012345678903',
                'bpjs_card_no' => '1111234567892',
                'name' => 'ANDI WIJAYA',
                'date_of_birth' => '1995-08-10',
                'gender' => 'male',
                'blood_type' => 'B',
                'phone' => '08345678901',
                'email' => 'andi.wijaya@example.com',
                'address' => 'Jl. Asia Afrika No. 78',
                'village' => 'Anggrek',
                'district' => 'Sukasari',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40125',
                'occupation' => 'Karyawan Swasta',
                'allergies' => null,
                'emergency_contact_name' => 'Dewi Wijaya',
                'emergency_contact_phone' => '08345678902',
                'emergency_contact_relation' => 'Ibu',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS II',
                    'participant_type' => 'PPU',
                ],
            ],
            [
                'medical_record_number' => 'RM00004',
                'nik' => '1111012345678904',
                'bpjs_card_no' => '1111234567893',
                'name' => 'DEWI LESTARI',
                'date_of_birth' => '1988-12-25',
                'gender' => 'female',
                'blood_type' => 'AB',
                'phone' => '08456789012',
                'email' => 'dewi.lestari@example.com',
                'address' => 'Jl. Gatot Subroto No. 90',
                'village' => 'Mawar',
                'district' => 'Cibeunying',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40126',
                'occupation' => 'Guru',
                'allergies' => 'Seafood',
                'emergency_contact_name' => 'Hendra Lestari',
                'emergency_contact_phone' => '08456789013',
                'emergency_contact_relation' => 'Suami',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS I',
                    'participant_type' => 'PNS',
                ],
            ],
            [
                'medical_record_number' => 'RM00005',
                'nik' => '1111012345678905',
                'bpjs_card_no' => '1111234567894',
                'name' => 'HENDRA GUNAWAN',
                'date_of_birth' => '1992-03-30',
                'gender' => 'male',
                'blood_type' => 'O',
                'phone' => '08567890123',
                'email' => 'hendra.gunawan@example.com',
                'address' => 'Jl. Dipatiukur No. 12',
                'village' => 'Flamboyan',
                'district' => 'Coblong',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40127',
                'occupation' => 'Wiraswasta',
                'allergies' => null,
                'emergency_contact_name' => 'Rina Gunawan',
                'emergency_contact_phone' => '08567890124',
                'emergency_contact_relation' => 'Istri',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS III',
                    'participant_type' => 'PBPU',
                ],
            ],

            // ========================================
            // PESERTA TIDAK AKTIF (Menunggak Iuran)
            // ========================================
            [
                'medical_record_number' => 'RM00006',
                'nik' => '1111012345678909',
                'bpjs_card_no' => '1111234567898',
                'name' => 'AHMAD DAHLAN',
                'date_of_birth' => '1980-06-15',
                'gender' => 'male',
                'blood_type' => 'A',
                'phone' => '08678901234',
                'email' => 'ahmad.dahlan@example.com',
                'address' => 'Jl. Cihampelas No. 56',
                'village' => 'Dahlia',
                'district' => 'Sukajadi',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40128',
                'occupation' => 'Buruh',
                'allergies' => null,
                'emergency_contact_name' => 'Fatimah Dahlan',
                'emergency_contact_phone' => '08678901235',
                'emergency_contact_relation' => 'Istri',
                'meta' => [
                    'bpjs_status' => 'TIDAK AKTIF',
                    'bpjs_class' => 'KELAS TIDAK BERLAKU',
                    'participant_type' => 'PBPU',
                    'note' => 'Menunggak iuran sejak 6 bulan yang lalu',
                ],
            ],
            [
                'medical_record_number' => 'RM00007',
                'nik' => '1111012345678910',
                'bpjs_card_no' => '1111234567899',
                'name' => 'RINA MARLINA',
                'date_of_birth' => '1987-09-08',
                'gender' => 'female',
                'blood_type' => 'B',
                'phone' => '08789012345',
                'email' => 'rina.marlina@example.com',
                'address' => 'Jl. Buah Batu No. 234',
                'village' => 'Kamboja',
                'district' => 'Bandung Kidul',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40129',
                'occupation' => 'Pedagang',
                'allergies' => null,
                'emergency_contact_name' => 'Agus Marlina',
                'emergency_contact_phone' => '08789012346',
                'emergency_contact_relation' => 'Suami',
                'meta' => [
                    'bpjs_status' => 'TIDAK AKTIF',
                    'bpjs_class' => 'KELAS TIDAK BERLAKU',
                    'participant_type' => 'PBPU',
                    'note' => 'Menunggak iuran sejak 3 bulan yang lalu',
                ],
            ],

            // ========================================
            // PESERTA AKTIF - Variasi Lain
            // ========================================
            [
                'medical_record_number' => 'RM00008',
                'nik' => '1111012345678906',
                'bpjs_card_no' => '1111234567895',
                'name' => 'RUDI HARTONO',
                'date_of_birth' => '1993-11-11',
                'gender' => 'male',
                'blood_type' => 'O',
                'phone' => '08890123456',
                'email' => 'rudi.hartono@example.com',
                'address' => 'Jl. Soekarno Hatta No. 567',
                'village' => 'Tulip',
                'district' => 'Batununggal',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40130',
                'occupation' => 'Dokter',
                'allergies' => null,
                'emergency_contact_name' => 'Maya Hartono',
                'emergency_contact_phone' => '08890123457',
                'emergency_contact_relation' => 'Istri',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS I',
                    'participant_type' => 'PNS',
                ],
            ],
            [
                'medical_record_number' => 'RM00009',
                'nik' => '1111012345678907',
                'bpjs_card_no' => '1111234567896',
                'name' => 'MAYA KUSUMA',
                'date_of_birth' => '1991-04-22',
                'gender' => 'female',
                'blood_type' => 'A',
                'phone' => '08901234567',
                'email' => 'maya.kusuma@example.com',
                'address' => 'Jl. Pasteur No. 89',
                'village' => 'Sakura',
                'district' => 'Sukagalih',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40131',
                'occupation' => 'Perawat',
                'allergies' => 'Kacang',
                'emergency_contact_name' => 'Lisa Kusuma',
                'emergency_contact_phone' => '08901234568',
                'emergency_contact_relation' => 'Adik',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS II',
                    'participant_type' => 'PPU',
                ],
            ],
            [
                'medical_record_number' => 'RM00010',
                'nik' => '1111012345678908',
                'bpjs_card_no' => '1111234567897',
                'name' => 'LISA PERMATA',
                'date_of_birth' => '1989-07-07',
                'gender' => 'female',
                'blood_type' => 'AB',
                'phone' => '08012345678',
                'email' => 'lisa.permata@example.com',
                'address' => 'Jl. Ir. H. Juanda No. 101',
                'village' => 'Orchid',
                'district' => 'Sumur Bandung',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40132',
                'occupation' => 'Bidan',
                'allergies' => null,
                'emergency_contact_name' => 'Agus Permata',
                'emergency_contact_phone' => '08012345679',
                'emergency_contact_relation' => 'Suami',
                'meta' => [
                    'bpjs_status' => 'AKTIF',
                    'bpjs_class' => 'KELAS I',
                    'participant_type' => 'PNS',
                ],
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        $this->command->info('✅ Berhasil membuat ' . count($patients) . ' data pasien BPJS');
        $this->command->info('📋 Breakdown:');
        $this->command->info('   - 8 Peserta AKTIF');
        $this->command->info('   - 2 Peserta TIDAK AKTIF (menunggak iuran)');
    }
}
