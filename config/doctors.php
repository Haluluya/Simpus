<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daftar Dokter per Poli
    |--------------------------------------------------------------------------
    |
    | Daftar dokter yang tersedia di setiap poli/departemen.
    | Format: 'Nama Poli' => ['Nama Dokter 1', 'Nama Dokter 2', ...]
    |
    | TODO: Pindahkan ke database table 'doctors' untuk manajemen yang lebih baik.
    |
    */
    'by_department' => [
        'Poli Umum' => [
            'Dr. Ahmad Hidayat, Sp.PD',
            'Dr. Sari Indah, Sp.PD',
            'Dr. Budi Santoso',
        ],
        'Poli Gigi' => [
            'Dr. Rina Melati, drg',
            'Dr. Dani Firmansyah, drg',
            'Dr. Maya Sari, drg, Sp.KG',
        ],
        'Poli KIA (Kesehatan Ibu dan Anak)' => [
            'Dr. Fitri Handayani, Sp.OG',
            'Dr. Ratna Dewi, Sp.OG',
            'Dr. Nur Aini, Sp.A',
        ],
        'Poli Anak' => [
            'Dr. Hendro Wijaya, Sp.A',
            'Dr. Lisa Permata, Sp.A',
            'Dr. Agus Setiawan, Sp.A',
        ],
        'Poli Penyakit Dalam' => [
            'Dr. Bambang Suryanto, Sp.PD',
            'Dr. Dewi Lestari, Sp.PD',
        ],
        'Poli Bedah' => [
            'Dr. Irfan Hakim, Sp.B',
            'Dr. Rudi Hartono, Sp.B',
        ],
        'Poli Mata' => [
            'Dr. Sinta Maharani, Sp.M',
            'Dr. Eko Prasetyo, Sp.M',
        ],
        'Poli THT' => [
            'Dr. Yoga Pratama, Sp.THT',
            'Dr. Dina Marlina, Sp.THT',
        ],
        'Poli Kulit dan Kelamin' => [
            'Dr. Fajar Ramadhan, Sp.KK',
            'Dr. Lia Anggraini, Sp.KK',
        ],
        'Poli Saraf' => [
            'Dr. Hendra Gunawan, Sp.S',
            'Dr. Wulan Sari, Sp.S',
        ],
        'Poli Jantung' => [
            'Dr. Rizki Firmansyah, Sp.JP',
            'Dr. Anita Kusuma, Sp.JP',
        ],
        'Poli Paru' => [
            'Dr. Yoga Aditya, Sp.P',
            'Dr. Nina Kartika, Sp.P',
        ],
        'Poli Jiwa' => [
            'Dr. Reza Pahlevi, Sp.KJ',
            'Dr. Melisa Putri, Sp.KJ',
        ],
        'Poli Rehabilitasi Medik' => [
            'Dr. Andi Kurniawan, Sp.KFR',
            'Dr. Tika Amelia, Sp.KFR',
        ],
    ],
];
