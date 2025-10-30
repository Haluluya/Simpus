# Panduan Integrasi BPJS & SATUSEHAT - Mode Mock API

## 📋 **Ringkasan untuk Mahasiswa/Prototype**

Sistem SIMPUS ini menggunakan **Mock API** sebagai default untuk keperluan:
- ✅ **Tugas Kuliah / Skripsi**
- ✅ **Prototype / Demo**
- ✅ **Development & Testing**
- ✅ **Presentasi tanpa koneksi internet**

**Mock API** = API Palsu yang mengembalikan data dummy tanpa koneksi ke server BPJS/SATUSEHAT yang sebenarnya.

---

## 🔍 **Cara Mengecek Mode yang Sedang Aktif**

### **Metode 1: Melalui File `.env`**

Buka file `.env` di root project, cari baris berikut:

```env
# BPJS Configuration
BPJS_USE_MOCK=true          # ← Jika true = Mock API (tidak koneksi ke BPJS asli)
                             # ← Jika false = Live API (butuh kredensial asli)

# SATUSEHAT Configuration  
SATUSEHAT_USE_MOCK=true     # ← Jika true = Mock API (tidak koneksi ke SATUSEHAT asli)
                             # ← Jika false = Live API (butuh kredensial asli)
```

### **Metode 2: Melalui Dashboard Web**

1. Login ke sistem SIMPUS
2. Buka menu **"Integrasi"** di sidebar
3. Lihat status card:
   ```
   BPJS VClaim
   Mode: Sandbox ← Artinya Mock API
   Mode: Live Production ← Artinya API Asli
   ```

### **Metode 3: Melalui Kode (Developer)**

Buka file `config/bpjs.php` atau `config/satusehat.php`:

```php
// File: config/bpjs.php
'use_mock' => filter_var(env('BPJS_USE_MOCK', true), FILTER_VALIDATE_BOOL),
//                                                ↑
//                                      Default = true (Mock)
```

---

## 🎯 **Rekomendasi untuk Tugas Kuliah/Prototype**

### **GUNAKAN MOCK API** ✅

**Alasan:**
1. ❌ **Tidak butuh kredensial resmi** dari BPJS/Kemenkes (susah didapat untuk mahasiswa)
2. ❌ **Tidak butuh koneksi internet** saat demo/presentasi
3. ✅ **Data konsisten** - selalu return data yang sama untuk testing
4. ✅ **Tidak ada biaya** - API BPJS live kadang ada quota/biaya
5. ✅ **Tidak ada delay** - response instant tanpa network latency
6. ✅ **Bebas error** - tidak tergantung server BPJS/SATUSEHAT down atau tidak
7. ✅ **Privacy** - tidak kirim data real ke server eksternal

---

## ⚙️ **Cara Mengaktifkan Mock API (Untuk Mahasiswa)**

### **Step 1: Edit File `.env`**

```env
# BPJS VClaim - Mock Mode
BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_CONS_ID=                    # ← Kosongkan saja
BPJS_SECRET=                     # ← Kosongkan saja
BPJS_USER_KEY=                   # ← Kosongkan saja
BPJS_USE_MOCK=true               # ← PENTING: Set true untuk Mock
BPJS_TIMEOUT=10

# SATUSEHAT - Mock Mode
SATUSEHAT_BASE_URL=https://api-satusehat.kemkes.go.id/fhir-r4/v1
SATUSEHAT_AUTH_URL=https://api-satusehat.kemkes.go.id/oauth2/v1
SATUSEHAT_CLIENT_ID=             # ← Kosongkan saja
SATUSEHAT_CLIENT_SECRET=         # ← Kosongkan saja
SATUSEHAT_ORGANIZATION_ID=100000001  # ← ID dummy
SATUSEHAT_USE_MOCK=true          # ← PENTING: Set true untuk Mock
SATUSEHAT_TIMEOUT=10
```

### **Step 2: Restart Server (Jika Perlu)**

```bash
# Jika pakai php artisan serve
Ctrl+C
php artisan serve

# Jika pakai Laragon/XAMPP
Restart Apache/Nginx
```

### **Step 3: Verifikasi**

1. Buka browser: `http://localhost:8000/integration`
2. Cek status card:
   - **BPJS VClaim** → Mode: **Sandbox** ✅
   - **SATUSEHAT** → Mode: **Sandbox** ✅

---

## 🔬 **Cara Kerja Mock API**

### **BPJS Mock API**

**Location:** `app/Services/Bpjs/BpjsClient.php`

```php
public function get(string $endpoint, array $query = []): array
{
    // Cek jika kredensial kosong atau USE_MOCK=true
    if (! $this->credentialsAvailable()) {
        return $this->mockResponse($endpoint, $query);  // ← Return data dummy
    }

    // Jika kredensial lengkap, baru hit API asli
    $response = $this->httpClient()
        ->get($this->buildUrl($endpoint), $query)
        ->throw();

    return $response->json();
}
```

**Mock Response Example:**

```php
private function mockResponse(string $endpoint, array $context = []): array
{
    return [
        'metaData' => [
            'code' => '200',
            'message' => 'Mocked BPJS response (Data Dummy)',
        ],
        'response' => [
            'peserta' => [
                'noKartu' => '0001234567890',
                'nik' => '3201234567890123',
                'nama' => 'John Doe (Data Dummy)',
                'hakKelas' => ['keterangan' => 'Kelas 3'],
                'statusPeserta' => ['keterangan' => 'AKTIF'],
            ],
        ],
    ];
}
```

### **SATUSEHAT Mock API**

**Location:** `app/Services/SatuSehat/SatuSehatClient.php`

```php
public function postResource(string $resourceType, array $payload): array
{
    // Cek jika USE_MOCK=true
    if ($this->shouldUseMock()) {
        return $this->storeMockPayload($resourceType, $payload);  // ← Simpan ke file
    }

    // Jika live, hit API SATUSEHAT asli
    $response = Http::baseUrl($this->baseUrl)
        ->acceptJson()
        ->timeout($this->timeout)
        ->withToken($this->accessToken())
        ->post($resourceType, $payload)
        ->throw();

    return $response->json();
}
```

**Mock Storage:**
- Data disimpan ke file JSON di `storage/app/mocks/satusehat/`
- Setiap request menghasilkan file baru dengan ID unik
- Tidak ada data yang dikirim ke server eksternal

---

## 📊 **Perbandingan Mode**

| Aspek | Mock API (Tugas Kuliah) ✅ | Live API (Production) |
|-------|---------------------------|----------------------|
| **Kredensial** | Tidak perlu | Harus punya CONS_ID, SECRET, CLIENT_ID dari BPJS/Kemenkes |
| **Internet** | Tidak perlu | Wajib ada koneksi stabil |
| **Data** | Dummy (konsisten) | Real (dari database BPJS/SATUSEHAT) |
| **Response Time** | Instant (~1ms) | Tergantung network (~200-1000ms) |
| **Biaya** | Gratis | Mungkin ada quota/biaya |
| **Error Handling** | Selalu sukses | Bisa gagal (timeout, server down) |
| **Security** | Aman (tidak ada data keluar) | Butuh enkripsi dan signature |
| **Cocok untuk** | Development, Demo, Tugas | Production, Real Hospital |

---

## 🎓 **Penjelasan untuk Laporan/Presentasi Tugas Kuliah**

### **Skenario Penjelasan ke Dosen:**

> "Sistem SIMPUS ini mengintegrasikan dengan BPJS VClaim dan SATUSEHAT menggunakan **Mock API** untuk keperluan prototype. 
> 
> Mock API adalah simulasi API yang mengembalikan data dummy tanpa koneksi ke server eksternal. Pendekatan ini dipilih karena:
>
> 1. **Akses Kredensial Terbatas**: Kredensial resmi BPJS dan SATUSEHAT hanya diberikan kepada fasilitas kesehatan yang terdaftar resmi, bukan untuk keperluan akademik.
>
> 2. **Development Best Practice**: Dalam software development, Mock API adalah standar industri untuk testing dan development sebelum production.
>
> 3. **Demonstrasi Stabil**: Mock API memastikan demo berjalan konsisten tanpa bergantung pada koneksi internet atau status server eksternal.
>
> 4. **Keamanan Data**: Tidak ada data pasien real yang dikirim ke server eksternal selama development.
>
> Sistem ini sudah **production-ready** dengan implementasi lengkap HTTP client, authentication (HMAC SHA-256 untuk BPJS, OAuth 2.0 untuk SATUSEHAT), dan error handling. Untuk deployment ke rumah sakit/puskesmas real, tinggal mengganti `USE_MOCK=false` dan mengisi kredensial resmi."

### **Poin Penting untuk Slide Presentasi:**

**Slide: Integrasi Eksternal**
```
✓ BPJS VClaim REST API
  - Validasi kepesertaan
  - Manajemen SEP (Surat Eligibilitas Peserta)
  - Monitoring klaim
  - Status: Mock API (Prototype)

✓ SATUSEHAT FHIR API
  - Sync data pasien (Patient resource)
  - Sync kunjungan (Encounter resource)
  - Standard: FHIR R4
  - Status: Mock API (Prototype)

✓ Mode Deployment
  - Development: Mock API (untuk demo)
  - Production: Live API (dengan kredensial resmi)
```

---

## 🧪 **Testing Mock API**

### **Test 1: BPJS - Cek Peserta**

1. Buka: `/integration`
2. Pilih tab "BPJS VClaim"
3. Isi form:
   - NIK: `3201234567890123` (16 digit apapun)
   - Tanggal: `2025-10-30`
4. Klik "Verifikasi Peserta"
5. **Expected Result:**
   ```
   ✓ Peserta Ditemukan
   Nama: Mocked Peserta (atau nama dari file mock)
   No. Kartu: 0001234567890
   NIK: 3201234567890123
   Hak Kelas: Kelas Mock
   Status: AKTIF
   ```

### **Test 2: SATUSEHAT - Sync Patient**

1. Buka: `/patients`
2. Pilih salah satu pasien
3. Klik tombol "Sync to SATUSEHAT"
4. **Expected Result:**
   ```
   ✓ Patient synced successfully (Mock Mode)
   ```
5. Data tidak benar-benar terkirim, hanya disimpan di `storage/app/mocks/satusehat/`

### **Test 3: Cek File Mock**

```bash
# Lihat file mock yang dihasilkan
ls storage/app/mocks/bpjs/
ls storage/app/mocks/satusehat/

# Baca content file mock
cat storage/app/mocks/satusehat/patient_123_timestamp.json
```

---

## 🚀 **Cara Migrasi ke Live API (Untuk Production)**

### **Step 1: Dapatkan Kredensial Resmi**

**BPJS:**
- Daftar di BPJS Kesehatan sebagai faskes
- Dapatkan: CONS_ID, SECRET_KEY, USER_KEY
- Website: https://bpjs-kesehatan.go.id

**SATUSEHAT:**
- Daftar di SATUSEHAT Kemenkes
- Dapatkan: CLIENT_ID, CLIENT_SECRET, ORGANIZATION_ID
- Website: https://satusehat.kemkes.go.id

### **Step 2: Update `.env`**

```env
# BPJS - Live Mode
BPJS_CONS_ID=your_real_cons_id
BPJS_SECRET=your_real_secret_key
BPJS_USER_KEY=your_real_user_key
BPJS_USE_MOCK=false          # ← Set false untuk Live

# SATUSEHAT - Live Mode
SATUSEHAT_CLIENT_ID=your_real_client_id
SATUSEHAT_CLIENT_SECRET=your_real_client_secret
SATUSEHAT_ORGANIZATION_ID=your_real_org_id
SATUSEHAT_USE_MOCK=false     # ← Set false untuk Live
```

### **Step 3: Test Connection**

```bash
# Test BPJS connection
php artisan tinker
>>> $client = app(\App\Services\Bpjs\BpjsClient::class);
>>> $client->validateParticipantByNik('3201234567890123', '2025-10-30');

# Test SATUSEHAT connection
>>> $client = app(\App\Services\SatuSehat\SatuSehatClient::class);
>>> $client->postResource('Patient', ['resourceType' => 'Patient', ...]);
```

---

## 📝 **FAQ untuk Mahasiswa**

### **Q: Apakah Mock API ini curang/tidak valid untuk tugas?**
**A:** Tidak! Mock API adalah best practice dalam software development. Bahkan perusahaan besar seperti Google, Facebook menggunakan mock untuk testing. Yang penting adalah:
- Implementasi HTTP client sudah benar ✅
- Authentication logic sudah ada ✅
- Error handling sudah proper ✅
- Bisa switch ke Live API dengan mudah ✅

### **Q: Bagaimana cara menjelaskan ke dosen bahwa ini bukan API asli?**
**A:** Jelaskan dengan transparan:
1. Tampilkan screenshot status "Mode: Sandbox"
2. Jelaskan alasan teknis (tidak punya akses kredensial)
3. Tunjukkan kode implementation yang proper
4. Tekankan bahwa ini production-ready, tinggal ganti config

### **Q: Apa bedanya Mock dengan tidak ada integrasi sama sekali?**
**A:** 
- **Tanpa Integrasi**: Tidak ada kode, tidak ada HTTP client, tidak ada API call
- **Dengan Mock**: Semua kode lengkap, HTTP client ada, logic benar, hanya data-nya dummy
- Mock lebih profesional dan menunjukkan pemahaman arsitektur yang baik

### **Q: Apakah data di Mock bisa diubah?**
**A:** Ya! Edit file:
- BPJS: `app/Services/Bpjs/BpjsClient.php` → method `mockResponse()`
- SATUSEHAT: Data disimpan di `storage/app/mocks/satusehat/*.json`

### **Q: Bagaimana cara demo agar terlihat profesional?**
**A:**
1. Tunjukkan status card "Online" dan "Mode: Sandbox"
2. Lakukan beberapa transaksi (cek peserta, sync data)
3. Tunjukkan audit log di database (bukti semua tercatat)
4. Jelaskan bahwa ini siap production dengan switch config
5. Tunjukkan dokumentasi API endpoint yang lengkap

---

## 📌 **Checklist Sebelum Presentasi**

- [ ] `.env` sudah set `BPJS_USE_MOCK=true` dan `SATUSEHAT_USE_MOCK=true`
- [ ] Browser cache sudah di-clear
- [ ] Database sudah di-seed dengan data dummy yang bagus
- [ ] Status card di dashboard menunjukkan "Mode: Sandbox"
- [ ] Test semua fitur integrasi minimal 1x (cek peserta, sync data)
- [ ] Prepare penjelasan tentang Mock API untuk dosen
- [ ] Screenshot status "Online" dan "Sandbox" untuk laporan
- [ ] Siapkan file `.env.example` untuk dokumentasi

---

## 🎯 **Kesimpulan**

**Untuk Mahasiswa/Tugas Kuliah:**
✅ Gunakan **Mock API** (USE_MOCK=true)
✅ Tidak perlu kredensial asli
✅ Sistem tetap menunjukkan status "Online" karena Mock API selalu available
✅ Cukup transparan saat presentasi bahwa ini prototype dengan Mock
✅ Tunjukkan bahwa implementasi sudah production-ready

**Untuk Production/Real Hospital:**
🏥 Ganti ke **Live API** (USE_MOCK=false)
🏥 Dapatkan kredensial resmi dari BPJS & SATUSEHAT
🏥 Test connection dengan data real
🏥 Monitor error log dan performance
🏥 Setup backup dan failover mechanism

---

**Sistem SIMPUS ini sudah mengimplementasikan best practice untuk integrasi eksternal dengan proper abstraction, sehingga mudah di-switch antara Mock dan Live API!** 🎉

Dibuat: {{ date('Y-m-d') }}
