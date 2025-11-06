# BPJS VClaim Integration

Panduan lengkap integrasi BPJS VClaim REST API di SIMPUS, termasuk **Mock API Mode** untuk testing tanpa kredensial.

## 📋 Daftar Isi

- [Overview](#overview)
- [Fitur](#fitur)
- [Konfigurasi](#konfigurasi)
- [Mock API Mode](#mock-api-mode)
- [Production Mode](#production-mode)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

---

## Overview

SIMPUS terintegrasi dengan **BPJS VClaim REST API** untuk:
- Validasi kepesertaan BPJS
- Manajemen SEP (Surat Eligibilitas Peserta)
- Cek rujukan FKTP/FKTL
- Akses referensi (Diagnosa, Poli, Faskes, Prosedur)

### Konsep BPJS di SIMPUS

**Status Peserta BPJS** disimpan di database (`patients.meta['bpjs_status']`), bukan menggunakan magic number. Ini membuat sistem lebih realistis dan mirip dengan BPJS asli yang query database pembayaran iuran.

**Status yang tersedia:**
- `AKTIF` - Peserta rutin bayar iuran, berhak layanan
- `TIDAK AKTIF` - Peserta menunggak iuran, tidak berhak layanan

---

## Fitur

### ✅ Validasi Peserta
- Cek status kepesertaan by NIK atau No. Kartu BPJS
- Mendapatkan data peserta (Nama, Kelas, Status, Jenis Peserta)
- Validasi hak kelas rawat

### ✅ Manajemen SEP
- Create SEP untuk kunjungan pasien
- Update SEP (jika ada perubahan data)
- Delete SEP (jika batal)
- Monitor SEP berdasarkan tanggal dan status

### ✅ Rujukan
- Get rujukan by nomor rujukan
- Get list rujukan by no. kartu BPJS
- Validasi rujukan dari FKTP

### ✅ Referensi
- Diagnosa ICD-10
- Daftar Poliklinik
- Daftar Faskes (PPK1/PPK2)
- Prosedur/Tindakan
- Kelas Pelayanan
- Daftar Dokter DPJP

---

## Konfigurasi

### Environment Variables

Edit file `.env`:

```env
# BPJS VClaim Configuration
BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_CONS_ID=your_cons_id
BPJS_SECRET=your_secret_key
BPJS_USER_KEY=your_user_key
BPJS_USE_MOCK=true  # true = Mock Mode, false = Production
BPJS_TIMEOUT=10
BPJS_TIME_OFFSET=0  # UTC time offset in seconds
```

### Konfigurasi File

Buka `config/bpjs.php`:

```php
return [
    'base_url' => env('BPJS_BASE_URL', 'https://new-api.bpjs-kesehatan.go.id/vclaim-rest/'),
    'cons_id' => env('BPJS_CONS_ID'),
    'secret_key' => env('BPJS_SECRET'),
    'user_key' => env('BPJS_USER_KEY'),
    'use_mock' => env('BPJS_USE_MOCK', false),
    'timeout' => env('BPJS_TIMEOUT', 10),
    'time_offset' => env('BPJS_TIME_OFFSET', 0),
];
```

---

## Mock API Mode

### Apa itu Mock API Mode?

Mock API Mode memungkinkan Anda **testing tanpa kredensial BPJS asli**. Semua request ke BPJS akan diintercept dan dikembalikan dengan data mock yang realistis.

### Kapan Menggunakan Mock Mode?

✅ **Development & Testing**
- Testing fitur BPJS tanpa credentials
- Unit testing & integration testing
- Demo aplikasi untuk klien

❌ **Production**
- Saat aplikasi sudah live
- Saat sudah memiliki kredensial BPJS asli

### Cara Mengaktifkan Mock Mode

Set environment variable:

```env
BPJS_USE_MOCK=true
```

### Data Mock yang Tersedia

Mock mode menyediakan **10 data pasien BPJS** yang realistis:

#### 8 Pasien AKTIF
| No | Nama | NIK | No. BPJS | Status | Kelas |
|----|------|-----|----------|--------|-------|
| 1 | BUDI SANTOSO | 1111012345678901 | 1111234567890 | AKTIF | KELAS I |
| 2 | SITI NURHALIZA | 1111012345678902 | 1111234567891 | AKTIF | KELAS I |
| 3 | ANDI WIJAYA | 1111012345678903 | 1111234567892 | AKTIF | KELAS II |
| 4 | DEWI LESTARI | 1111012345678904 | 1111234567893 | AKTIF | KELAS I |
| 5 | HENDRA GUNAWAN | 1111012345678905 | 1111234567894 | AKTIF | KELAS III |
| 6 | RUDI HARTONO | 1111012345678906 | 1111234567895 | AKTIF | KELAS I |
| 7 | MAYA KUSUMA | 1111012345678907 | 1111234567896 | AKTIF | KELAS II |
| 8 | LISA PERMATA | 1111012345678908 | 1111234567897 | AKTIF | KELAS I |

#### 2 Pasien TIDAK AKTIF (Menunggak Iuran)
| No | Nama | NIK | No. BPJS | Status | Kelas | Keterangan |
|----|------|-----|----------|--------|-------|------------|
| 9 | AHMAD DAHLAN | 1111012345678909 | 1111234567898 | TIDAK AKTIF | - | Menunggak 6 bulan |
| 10 | RINA MARLINA | 1111012345678910 | 1111234567899 | TIDAK AKTIF | - | Menunggak 3 bulan |

### Seed Data Mock

Jalankan seeder untuk populate data mock:

```bash
php artisan db:seed --class=BpjsPatientSeeder
```

Atau reset database dan seed ulang:

```bash
php artisan migrate:fresh --seed
```

### Lokasi File Mock

Mock responses disimpan di `storage/app/mocks/bpjs/`:

```
storage/app/mocks/bpjs/
├── referensi_diagnosa.json      # Daftar diagnosa ICD-10
├── referensi_poli.json           # Daftar poliklinik
├── referensi_faskes.json         # Daftar faskes PPK
├── referensi_prosedur.json       # Daftar prosedur
├── referensi_kelas.json          # Kelas pelayanan
├── referensi_dokter.json         # Daftar dokter DPJP
├── rujukan_template.json         # Template rujukan
├── sep_create_success.json       # Response create SEP
├── sep_update_success.json       # Response update SEP
├── sep_delete_success.json       # Response delete SEP
└── monitoring_sep.json           # Response monitoring SEP
```

### Cara Kerja Mock Mode

1. **Request ke BpjsClient**
   ```php
   $bpjsClient = new BpjsClient();
   $result = $bpjsClient->validateParticipantByNik($nik, $serviceDate);
   ```

2. **BpjsClient cek kredensial**
   ```php
   if (!$this->credentialsAvailable()) {
       return $this->mockResponse($endpoint, $context);
   }
   ```

3. **Mock response query database**
   ```php
   // Query patient dari database
   $patient = Patient::where('nik', $nik)->first();

   // Baca status dari meta field
   $bpjsStatus = $patient->meta['bpjs_status'] ?? 'AKTIF';

   // Return response sesuai format BPJS
   return [
       'metaData' => [
           'code' => '200',
           'message' => 'OK',
       ],
       'response' => [
           'peserta' => [
               'noKartu' => $patient->bpjs_card_no,
               'nik' => $patient->nik,
               'nama' => $patient->name,
               'statusPeserta' => [
                   'kode' => $bpjsStatus === 'AKTIF' ? '1' : '0',
                   'keterangan' => $bpjsStatus,
               ],
               // ... data lainnya
           ],
       ],
   ];
   ```

### Keuntungan Mock Mode

✅ **Database-driven** - Status BPJS dari database, bukan hardcoded
✅ **Realistis** - Mirip dengan cara BPJS asli (query database pembayaran)
✅ **Flexible** - Bisa ubah status dengan update database
✅ **Testing-friendly** - Data mock yang konsisten
✅ **No credentials required** - Testing tanpa credentials BPJS

---

## Production Mode

### Persiapan

1. **Dapatkan Kredensial BPJS**
   - Daftar di [https://bpjs-kesehatan.go.id](https://bpjs-kesehatan.go.id)
   - Hubungi BPJS untuk mendapatkan:
     - `CONS_ID` (Consumer ID)
     - `SECRET_KEY` (Secret Key)
     - `USER_KEY` (User Key)

2. **Update `.env`**
   ```env
   BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
   BPJS_CONS_ID=your_actual_cons_id
   BPJS_SECRET=your_actual_secret_key
   BPJS_USER_KEY=your_actual_user_key
   BPJS_USE_MOCK=false  # ⚠️ Disable mock mode
   BPJS_TIMEOUT=10
   ```

3. **Test Koneksi**
   ```bash
   # Test validasi peserta
   php artisan tinker

   >>> $client = new \App\Services\Bpjs\BpjsClient();
   >>> $result = $client->validateParticipantByNik('1234567890123456', '2025-01-15');
   >>> dd($result);
   ```

### HMAC Signature

BPJS VClaim menggunakan **HMAC SHA-256 signature** untuk autentikasi.

```php
// Generate signature
$timestamp = time();
$signaturePayload = $consId . '&' . $timestamp;
$signature = base64_encode(hash_hmac('sha256', $signaturePayload, $secretKey, true));

// Headers
$headers = [
    'X-cons-id' => $consId,
    'X-timestamp' => $timestamp,
    'X-signature' => $signature,
    'user_key' => $userKey,
];
```

### Time Offset

Jika server Anda memiliki time offset dengan server BPJS, set `BPJS_TIME_OFFSET`:

```env
# Tambah 2 jam (7200 detik)
BPJS_TIME_OFFSET=7200

# Kurangi 1 jam (-3600 detik)
BPJS_TIME_OFFSET=-3600
```

---

## API Endpoints

### Validasi Peserta

#### By NIK
```php
POST /bpjs/cek-peserta
Content-Type: application/json

{
    "nik": "1111012345678901",
    "service_date": "2025-01-15"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "Peserta ditemukan",
    "data": {
        "metaData": {
            "code": "200",
            "message": "OK"
        },
        "response": {
            "peserta": {
                "noKartu": "1111234567890",
                "nik": "1111012345678901",
                "nama": "BUDI SANTOSO",
                "pisa": "Puskesmas Kenanga",
                "sex": "L",
                "tglLahir": "1990-01-15",
                "hakKelas": {
                    "kode": "1",
                    "keterangan": "KELAS I"
                },
                "statusPeserta": {
                    "kode": "1",
                    "keterangan": "AKTIF"
                }
            }
        }
    }
}
```

#### By No. Kartu BPJS
```php
POST /bpjs/cek-peserta-kartu
Content-Type: application/json

{
    "no_kartu": "1111234567890",
    "service_date": "2025-01-15"
}
```

### Manajemen SEP

#### Create SEP
```php
POST /bpjs/sep/create
Content-Type: application/json

{
    "noKartu": "1111234567890",
    "tglSep": "2025-01-15",
    "noRujukan": "001/Rujuk/I/2025",
    "tglRujukan": "2025-01-10",
    "ppkPelayanan": "1101P001",
    "jnsPelayanan": "2",
    "klsRawat": "1",
    "diagAwal": "A00.0",
    "poli": "INT",
    "noMR": "RM00001",
    "catatan": "Pasien rujukan dari Puskesmas",
    "user": "admin_puskesmas"
}
```

**Response Success:**
```json
{
    "success": true,
    "message": "SEP berhasil dibuat",
    "data": {
        "metaData": {
            "code": "200",
            "message": "Sukses!"
        },
        "response": {
            "sep": {
                "noSep": "0301R001250115001",
                "tglSep": "2025-01-15",
                "tglPelayanan": "2025-01-15",
                "jnsPelayanan": "2",
                "kelasRawat": "1",
                "noKartu": "1111234567890",
                "nama": "BUDI SANTOSO",
                "diagnosa": "A00.0"
            }
        }
    }
}
```

#### Delete SEP
```php
DELETE /bpjs/sep/delete
Content-Type: application/json

{
    "noSep": "0301R001250115001",
    "user": "admin_puskesmas"
}
```

### Rujukan

#### Get Rujukan by Nomor
```php
POST /bpjs/rujukan/cek
Content-Type: application/json

{
    "no_rujukan": "001/Rujuk/I/2025"
}
```

### Referensi

#### Diagnosa ICD-10
```php
GET /bpjs/referensi/diagnosa/{keyword}
```

#### Poliklinik
```php
GET /bpjs/referensi/poli
GET /bpjs/referensi/poli/{keyword}
```

#### Faskes
```php
GET /bpjs/referensi/faskes/{keyword}
```

---

## Testing

### Unit Testing

```bash
# Run all BPJS tests
php artisan test --filter=BpjsTest

# Run specific test
php artisan test --filter=test_can_validate_participant_by_nik
```

### Manual Testing via Browser

1. Login ke aplikasi
2. Buka menu **Integrasi → BPJS VClaim**
3. Test fitur:
   - ✅ Validasi Peserta (by NIK atau No. Kartu)
   - ✅ Buat SEP
   - ✅ Cek Rujukan
   - ✅ Referensi (Diagnosa, Poli, dll)

### Testing dengan Postman

Import collection: `postman/SIMPUS.postman_collection.json`

Folder **BPJS** berisi:
- Cek Peserta (by NIK)
- Cek Peserta (by Kartu)
- Create SEP
- Delete SEP
- Get Rujukan
- Referensi

---

## Troubleshooting

### Error: Connection timeout

**Penyebab:** Kredensial salah atau server BPJS down

**Solusi:**
1. Aktifkan mock mode:
   ```env
   BPJS_USE_MOCK=true
   ```

2. Atau tingkatkan timeout:
   ```env
   BPJS_TIMEOUT=30
   ```

### Error: Invalid signature

**Penyebab:** Time offset antara server Anda dan BPJS

**Solusi:** Set time offset di `.env`:
```env
BPJS_TIME_OFFSET=7200  # Tambah 2 jam
```

### Error: Peserta tidak ditemukan (Mock Mode)

**Penyebab:** Data belum di-seed

**Solusi:** Run seeder:
```bash
php artisan db:seed --class=BpjsPatientSeeder
```

### Error: Status selalu AKTIF (Mock Mode)

**Penyebab:** Status di database masih AKTIF

**Solusi:** Update status di database:
```sql
UPDATE patients
SET meta = JSON_SET(meta, '$.bpjs_status', 'TIDAK AKTIF')
WHERE nik = '1111012345678909';
```

### Mock Mode tidak bekerja

**Penyebab:** `BPJS_USE_MOCK` belum di-set atau credentials terisi

**Solusi:**
1. Set `BPJS_USE_MOCK=true` di `.env`
2. Clear config cache:
   ```bash
   php artisan config:clear
   ```

---

## Best Practices

### 1. Selalu Update Status di Database

Jangan hardcode status BPJS. Selalu simpan di `patients.meta`:

```php
// ✅ Good
$patient->updateBpjsStatus('TIDAK AKTIF', 'KELAS TIDAK BERLAKU');

// ❌ Bad - jangan hardcode di code
if (str_ends_with($nik, '9999')) {
    $status = 'TIDAK AKTIF';
}
```

### 2. Gunakan Queue untuk Operasi Berat

Untuk operasi yang melibatkan banyak request ke BPJS, gunakan queue:

```php
// Dispatch job
UpdateBpjsStatusJob::dispatch($patient);
```

### 3. Handle Error Gracefully

Selalu handle error dari BPJS API:

```php
try {
    $result = $bpjsClient->validateParticipantByNik($nik, $date);

    if ($result['metaData']['code'] !== '200') {
        // Handle BPJS error
        return response()->json([
            'success' => false,
            'message' => $result['metaData']['message']
        ]);
    }

    // Process success
} catch (\Exception $e) {
    // Handle connection error
    Log::error('BPJS Error: ' . $e->getMessage());
    return response()->json([
        'success' => false,
        'message' => 'Gagal menghubungi server BPJS'
    ]);
}
```

### 4. Cache Referensi

Data referensi jarang berubah, sebaiknya di-cache:

```php
// Cache diagnosa untuk 1 hari
$diagnoses = Cache::remember('bpjs_diagnoses_' . $keyword, 86400, function () use ($keyword) {
    return $this->bpjsClient->getDiagnoses($keyword);
});
```

---

## Referensi

- [BPJS VClaim API Documentation](https://bpjs-kesehatan.go.id/bpjs/dmdocuments/3c49f07053029e8e3664e2b8bcec69c7.pdf)
- [Kementerian Kesehatan RI](https://www.kemkes.go.id/)
- [Spesifikasi Teknis Integrasi BPJS](https://pcare.bpjs-kesehatan.go.id/)

---

**[⬅️ Kembali ke README](../README.md)** | **[Dokumentasi SATUSEHAT →](SATUSEHAT-INTEGRATION.md)**
