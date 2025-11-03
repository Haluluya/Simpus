<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Queue Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix yang digunakan ketika poli tidak memiliki pengaturan khusus.
    |
    */
    'default_prefix' => 'A',

    /*
    |--------------------------------------------------------------------------
    | Default Department
    |--------------------------------------------------------------------------
    |
    | Poli fallback ketika department tidak dikirim dari form pendaftaran.
    |
    */
    'default_department' => 'Poli Umum',

    /*
    |--------------------------------------------------------------------------
    | Panjang Digit Nomor
    |--------------------------------------------------------------------------
    |
    | Banyak digit numerik dalam nomor antrean yang akan diisi leading zero.
    |
    */
    'number_length' => 2,

    /*
    |--------------------------------------------------------------------------
    | Mapping Prefix Poli
    |--------------------------------------------------------------------------
    |
    | Prefix khusus per poli. Jika tidak ditemukan, sistem akan membuat prefix
    | berdasarkan inisial nama poli.
    |
    */
    'prefixes' => [
        'Poli Umum' => 'Q',
        'Poli Gigi' => 'G',
        'Poli KIA (Kesehatan Ibu dan Anak)' => 'KIA',
        'Poli Anak' => 'AN',
        'Poli Penyakit Dalam' => 'PD',
        'Poli Bedah' => 'BD',
        'Poli Mata' => 'MT',
        'Poli THT' => 'THT',
        'Poli Kulit dan Kelamin' => 'KK',
        'Poli Saraf' => 'SF',
        'Poli Jantung' => 'JT',
        'Poli Paru' => 'PR',
        'Poli Jiwa' => 'JW',
        'Poli Rehabilitasi Medik' => 'RM',
        'Loket Pendaftaran' => 'L',
        'Laboratorium' => 'LAB',
        'Apotek' => 'AP',
        'Unit Rekam Medis' => 'URM',
    ],

    /*
    |--------------------------------------------------------------------------
    | Daftar Poli Aktif
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk menampilkan pilihan poli pada form pendaftaran dan
    | antrian. Sesuaikan sesuai kebutuhan fasilitas kesehatan.
    |
    */
    'departments' => [
        'Poli Umum',
        'Poli Gigi',
        'Poli KIA (Kesehatan Ibu dan Anak)',
        'Poli Anak',
        'Poli Penyakit Dalam',
        'Poli Bedah',
        'Poli Mata',
        'Poli THT',
        'Poli Kulit dan Kelamin',
        'Poli Saraf',
        'Poli Jantung',
        'Poli Paru',
        'Poli Jiwa',
        'Poli Rehabilitasi Medik',
    ],
];
