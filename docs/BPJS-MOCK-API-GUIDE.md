# 📘 Panduan Mock API BPJS & SatuSehat untuk Prototipe SIMPUS

> **Dokumen ini menjelaskan implementasi Mock API untuk integrasi BPJS Web Service (V-Claim) dan SatuSehat API dalam prototipe aplikasi SIMPUS.**

---

## 📑 Daftar Isi

1. [Latar Belakang](#latar-belakang)
2. [Konsep BPJS & SatuSehat](#konsep-bpjs--satusehat)
3. [Arsitektur Mock API](#arsitektur-mock-api)
4. [Konfigurasi](#konfigurasi)
5. [Dataset Testing](#dataset-testing)
6. [Skenario Testing](#skenario-testing)
7. [Endpoint API](#endpoint-api)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Latar Belakang

### Mengapa Menggunakan Mock API?

Dalam pengembangan **prototipe akademis** SIMPUS, kami menggunakan **Mock API** untuk mensimulasikan integrasi dengan sistem nasional karena:

#### ✅ **Alasan Teknis**
- API BPJS official memerlukan **kredensial resmi** (Consumer ID, Secret Key, User Key) yang hanya diberikan ke fasilitas kesehatan teregistrasi
- SatuSehat memerlukan **proses registrasi organisasi** ke Kementerian Kesehatan dan approval
- Tidak memungkinkan testing dengan **data pasien riil** (masalah privasi dan legalitas)

#### ✅ **Alasan Akademis**
- Fokus pada **pemahaman alur bisnis** bukan infrastruktur
- Demonstrasi **kemampuan integrasi** sistem
- **Repeatability** - hasil konsisten untuk presentasi dan demo
- **Kecepatan development** - tidak ada dependency pada API eksternal

#### ✅ **Production-Ready Design**
- Implementasi sudah mengikuti **best practices** integrasi BPJS dan SatuSehat
- **Mudah switch** ke API production dengan hanya mengganti kredensial di `.env`
- Mock logic mengikuti **response format official** dari dokumentasi BPJS dan SatuSehat

---

## 🏥 Konsep BPJS & SatuSehat

### A. BPJS Kesehatan (V-Claim API)

**BPJS Kesehatan** adalah program jaminan kesehatan nasional Indonesia. Untuk klaim layanan kesehatan, faskes harus terintegrasi dengan **BPJS Web Service V-Claim**.

#### **1. Komponen Identitas Peserta**

| Field | Deskripsi | Format | Contoh |
|-------|-----------|--------|--------|
| **NIK** | Nomor Induk Kependudukan | 16 digit | `3201012345678901` |
| **No Kartu BPJS** | Nomor kartu peserta BPJS | 13 digit | `0001234567890` |
| **Nama** | Nama lengkap peserta | String | `BUDI SANTOSO` |
| **Tanggal Lahir** | Tanggal lahir peserta | YYYY-MM-DD | `1990-01-15` |

> **📌 Catatan Penting:**
> - NIK dan No BPJS adalah **identitas berbeda** namun **terintegrasi** (1 NIK = 1 No BPJS)
> - NIK digunakan untuk **validasi kependudukan**
> - No BPJS digunakan untuk **klaim layanan kesehatan**

#### **2. Status Kepesertaan**

| Status | Keterangan | Hak Layanan |
|--------|------------|-------------|
| **AKTIF** | Peserta membayar iuran rutin | ✅ Berhak mendapat layanan BPJS |
| **TIDAK AKTIF** | Peserta menunggak iuran ≥ 1 bulan | ❌ Tidak berhak layanan (harus bayar tunai) |

**Penyebab Status TIDAK AKTIF:**
- Menunggak pembayaran iuran
- Peserta mengundurkan diri
- Data peserta bermasalah (ganda, tidak valid)

#### **3. Kelas Rawat**

| Kelas | Iuran/Bulan (2025) | Fasilitas |
|-------|-------------------|-----------|
| **KELAS I** | Rp 150.000 | Kamar 2-4 orang, AC |
| **KELAS II** | Rp 100.000 | Kamar 4-6 orang |
| **KELAS III** | Rp 42.000 (disubsidi) | Kamar >6 orang |

#### **4. SEP (Surat Eligibilitas Peserta)**

SEP adalah **dokumen wajib** yang harus dibuat untuk setiap kunjungan peserta BPJS.

**Fungsi SEP:**
- Bukti bahwa peserta **eligible** mendapat layanan BPJS
- Dasar untuk **klaim biaya** layanan ke BPJS
- Mencatat **diagnosa**, **poli tujuan**, **rujukan**, dll

**Komponen SEP:**
- No SEP (unique identifier)
- Data peserta (NIK, No Kartu, Nama)
- Data pelayanan (Poli, Diagnosa, Tgl Pelayanan)
- Data rujukan (jika dari FKTP ke FKTL)
- DPJP (Dokter Penanggung Jawab Pelayanan)

#### **5. Rujukan**

**Rujukan** adalah surat dari faskes untuk merujuk pasien ke faskes lain.

**Jenis Rujukan:**
- **Rujukan Vertikal:** FKTP (Puskesmas) → FKTL (Rumah Sakit)
- **Rujukan Horizontal:** FKTL → FKTL (antar RS spesialistik)
- **Rujukan Balik:** FKTL → FKTP (setelah sembuh)

---

### B. SatuSehat (Kemenkes FHIR API)

**SatuSehat** adalah platform **Pertukaran Data Kesehatan** (Health Information Exchange) milik Kementerian Kesehatan.

#### **1. Format FHIR (Fast Healthcare Interoperability Resources)**

SatuSehat menggunakan **FHIR R4** sebagai standar format data.

**Resource Utama:**
- **Patient:** Data demografi pasien
- **Encounter:** Data kunjungan/pertemuan pasien dengan tenaga kesehatan
- **Observation:** Hasil pemeriksaan (vital sign, lab)
- **Condition:** Diagnosa/kondisi pasien
- **Medication:** Data obat dan pemberian obat

#### **2. OAuth 2.0 Authentication**

SatuSehat menggunakan **OAuth 2.0 Client Credentials Flow**.

**Flow:**
1. Request token ke `/oauth2/v1/token` dengan client_id & client_secret
2. Dapat access_token (valid ~1 jam)
3. Gunakan token untuk akses FHIR API (`Authorization: Bearer {token}`)

---

## 🏗️ Arsitektur Mock API

### Diagram Alur

```
┌─────────────────┐
│  User Interface │
│  (Web Browser)  │
└────────┬────────┘
         │
         │ HTTP Request
         ▼
┌─────────────────────────────┐
│  Controller Layer           │
│  - BpjsController           │
│  - SatuSehatController      │
└────────┬────────────────────┘
         │
         │ Method Call
         ▼
┌─────────────────────────────┐
│  Service Layer              │
│  - BpjsClient               │
│  - SatuSehatClient          │
│  - FhirMapper               │
└────────┬────────────────────┘
         │
         ├──────────┬──────────────────┐
         │          │                  │
         ▼          ▼                  ▼
┌────────────┐ ┌──────────────┐ ┌─────────────┐
│ Credentials│ │  Mock Files  │ │  Database   │
│   Check    │ │  (JSON)      │ │  (Logging)  │
└────────────┘ └──────────────┘ └─────────────┘
         │
         ▼
  ┌─── Mock Mode? ───┐
  │                  │
 YES                NO
  │                  │
  ▼                  ▼
┌──────────┐   ┌──────────────┐
│  Mock    │   │  BPJS/SS API │
│ Response │   │  (Production)│
└──────────┘   └──────────────┘
```

### File Structure

```
storage/app/mocks/bpjs/
├── referensi_diagnosa.json      # Data ICD-10
├── referensi_poli.json          # Daftar poliklinik
├── referensi_faskes.json        # Daftar fasilitas kesehatan
├── referensi_prosedur.json      # Kode prosedur medis
├── referensi_kelas.json         # Kelas rawat
├── referensi_dokter.json        # Daftar dokter DPJP
├── rujukan_template.json        # Template rujukan
├── sep_create_success.json      # Response SEP create
├── sep_update_success.json      # Response SEP update
├── sep_delete_success.json      # Response SEP delete
└── monitoring_sep.json          # Data monitoring SEP

storage/app/mocks/satusehat/
└── [auto-generated files]       # FHIR payload yang di-queue
```

---

## ⚙️ Konfigurasi

### 1. Environment Variables

Edit file `.env`:

```env
# ========================================
# BPJS V-Claim Configuration
# ========================================
BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_CONS_ID=                    # Kosongkan untuk mock mode
BPJS_SECRET=                     # Kosongkan untuk mock mode
BPJS_USER_KEY=                   # Kosongkan untuk mock mode
BPJS_SERVICE_NAME=vclaim
BPJS_USE_MOCK=true              # Set true untuk mock
BPJS_TIMEOUT=10
BPJS_TIMESTAMP_OFFSET=0

# ========================================
# SatuSehat Configuration
# ========================================
SATUSEHAT_BASE_URL=https://api-satusehat.kemkes.go.id/fhir-r4
SATUSEHAT_AUTH_URL=https://api-satusehat.kemkes.go.id/oauth2/v1
SATUSEHAT_CLIENT_ID=             # Kosongkan untuk mock mode
SATUSEHAT_CLIENT_SECRET=         # Kosongkan untuk mock mode
SATUSEHAT_ORGANIZATION_ID=
SATUSEHAT_FACILITY_ID=
SATUSEHAT_USE_MOCK=true         # Set true untuk mock
SATUSEHAT_TIMEOUT=10
```

### 2. Database Seeder

Jalankan seeder untuk membuat data pasien testing:

```bash
php artisan db:seed --class=BpjsPatientSeeder
```

**Output:**
- ✅ 8 Peserta AKTIF (NIK/BPJS normal)
- ❌ 2 Peserta TIDAK AKTIF (NIK ending 9999, BPJS ending 999)

---

## 📊 Dataset Testing

### Pattern Testing

| Pattern | Status | Contoh NIK | Contoh BPJS |
|---------|--------|-----------|-------------|
| **Normal** | ✅ AKTIF | `3201012345678901` | `0001234567890` |
| **Ending 9999** | ❌ TIDAK AKTIF | `3201012345679999` | `0001234567890` |
| **Ending 999** | ❌ TIDAK AKTIF | `3201012345678901` | `0001234567999` |

### Data Pasien Testing

#### ✅ **Peserta AKTIF**

| No | RM | NIK | BPJS | Nama | Kelas |
|----|-----|-----|------|------|-------|
| 1 | RM00001 | 3201012345678901 | 0001234567890 | BUDI SANTOSO | KELAS I |
| 2 | RM00002 | 3201012345678902 | 0001234567891 | SITI NURHALIZA | KELAS I |
| 3 | RM00003 | 3201012345678903 | 0001234567892 | ANDI WIJAYA | KELAS II |
| 4 | RM00004 | 3201012345678904 | 0001234567893 | DEWI LESTARI | KELAS I |
| 5 | RM00005 | 3201012345678905 | 0001234567894 | HENDRA GUNAWAN | KELAS III |
| 6 | RM00008 | 3201012345678906 | 0001234567895 | RUDI HARTONO | KELAS I |
| 7 | RM00009 | 3201012345678907 | 0001234567896 | MAYA KUSUMA | KELAS II |
| 8 | RM00010 | 3201012345678908 | 0001234567897 | LISA PERMATA | KELAS I |

#### ❌ **Peserta TIDAK AKTIF**

| No | RM | NIK | BPJS | Nama | Alasan |
|----|-----|-----|------|------|--------|
| 1 | RM00006 | 3201012345679999 | 0001234567999 | AHMAD DAHLAN | Menunggak 6 bulan |
| 2 | RM00007 | 3201012345689999 | 0001234568999 | RINA MARLINA | Menunggak 3 bulan |

---

## 🧪 Skenario Testing

### A. Testing BPJS V-Claim

#### **Test Case 1: Validasi Peserta AKTIF (by NIK)**

**Endpoint:** `POST /api/bpjs/cek-peserta`

**Request:**
```json
{
  "nik": "3201012345678901",
  "service_date": "2025-01-25"
}
```

**Expected Response:**
```json
{
  "metaData": {
    "code": "200",
    "message": "OK"
  },
  "response": {
    "peserta": {
      "noKartu": "0001234567890",
      "nik": "3201012345678901",
      "nama": "Peserta Aktif (Mock)",
      "statusPeserta": {
        "kode": "1",
        "keterangan": "AKTIF"
      },
      "hakKelas": {
        "kode": "1",
        "keterangan": "KELAS I"
      },
      "jenisPeserta": {
        "kode": "11",
        "keterangan": "PNS"
      }
    }
  }
}
```

**Validasi:**
- ✅ HTTP Status: 200
- ✅ Status peserta: AKTIF
- ✅ Kelas: KELAS I
- ✅ Data tersimpan di tabel `bpjs_claims`

---

#### **Test Case 2: Validasi Peserta TIDAK AKTIF (by NIK)**

**Endpoint:** `POST /api/bpjs/cek-peserta`

**Request:**
```json
{
  "nik": "3201012345679999",
  "service_date": "2025-01-25"
}
```

**Expected Response:**
```json
{
  "metaData": {
    "code": "200",
    "message": "OK"
  },
  "response": {
    "peserta": {
      "noKartu": "0001234567999",
      "nik": "3201012345679999",
      "nama": "Peserta Non-Aktif (Mock)",
      "statusPeserta": {
        "kode": "0",
        "keterangan": "TIDAK AKTIF"
      },
      "hakKelas": {
        "kode": "0",
        "keterangan": "KELAS TIDAK BERLAKU"
      }
    }
  }
}
```

**Validasi:**
- ✅ HTTP Status: 200
- ✅ Status peserta: TIDAK AKTIF
- ✅ Kelas: KELAS TIDAK BERLAKU
- ❌ Tidak berhak layanan BPJS (harus bayar tunai)

---

#### **Test Case 3: Validasi Peserta by Kartu BPJS**

**Endpoint:** `POST /api/bpjs/cek-peserta-kartu`

**Request:**
```json
{
  "card_number": "0001234567890",
  "service_date": "2025-01-25"
}
```

**Expected Response:** (sama dengan Test Case 1)

---

#### **Test Case 4: Create SEP**

**Endpoint:** `POST /api/bpjs/sep/create`

**Request:**
```json
{
  "noKartu": "0001234567890",
  "tglSep": "2025-01-25",
  "ppkPelayanan": "1101P001",
  "jnsPelayanan": "2",
  "klsRawat": "1",
  "noMR": "RM00001",
  "rujukan": {
    "asalRujukan": "1",
    "tglRujukan": "2025-01-20",
    "noRujukan": "0301R0011701010001",
    "ppkRujukan": "1101P001"
  },
  "catatan": "Kontrol rutin diabetes",
  "diagAwal": "E11.9",
  "poli": {
    "tujuan": "INT",
    "eksekutif": "0"
  },
  "cob": {
    "cob": "0"
  },
  "katarak": {
    "katarak": "0"
  },
  "jaminan": {
    "lakaLantas": "0",
    "noLP": "",
    "penjamin": {
      "tglKejadian": "",
      "keterangan": "",
      "suplesi": {
        "suplesi": "0",
        "noSepSuplesi": "",
        "lokasiLaka": {
          "kdPropinsi": "",
          "kdKabupaten": "",
          "kdKecamatan": ""
        }
      }
    }
  },
  "tujuanKunj": "0",
  "flagProcedure": "",
  "kdPenunjang": "",
  "assesmentPel": "",
  "skdp": {
    "noSurat": "",
    "kodeDPJP": "999999"
  },
  "dpjpLayan": "999999",
  "noTelp": "08123456789",
  "user": "admin_puskesmas"
}
```

**Expected Response:**
```json
{
  "metaData": {
    "code": "200",
    "message": "Sukses!"
  },
  "response": {
    "sep": {
      "noSep": "0301R0012501250001",
      "tglSep": "2025-01-25",
      "noKartu": "0001234567890",
      "nama": "Peserta Mock",
      "diagnosa": "E11.9",
      "poliTujuan": "INT"
    }
  }
}
```

**Validasi:**
- ✅ HTTP Status: 200
- ✅ No SEP ter-generate (format: `{kode_faskes}{yymmdd}{sequence}`)
- ✅ Data SEP tersimpan di log

---

#### **Test Case 5: Cari Diagnosa (ICD-10)**

**Endpoint:** `GET /api/bpjs/referensi/diagnosa?keyword=diabet`

**Expected Response:**
```json
{
  "metaData": {
    "code": "200",
    "message": "OK"
  },
  "response": {
    "diagnosa": [
      {
        "kode": "E11",
        "nama": "Diabetes mellitus tipe 2"
      },
      {
        "kode": "E11.0",
        "nama": "Diabetes mellitus tipe 2 dengan koma"
      },
      {
        "kode": "E11.9",
        "nama": "Diabetes mellitus tipe 2 tanpa komplikasi"
      }
    ]
  }
}
```

---

#### **Test Case 6: Daftar Poliklinik**

**Endpoint:** `GET /api/bpjs/referensi/poli`

**Expected Response:**
```json
{
  "metaData": {
    "code": "200",
    "message": "OK"
  },
  "response": {
    "poli": [
      {
        "kode": "001",
        "nama": "POLI UMUM"
      },
      {
        "kode": "INT",
        "nama": "PENYAKIT DALAM"
      }
    ]
  }
}
```

---

### B. Testing SatuSehat

#### **Test Case 7: Sync Patient ke SatuSehat**

**Endpoint:** `POST /api/satusehat/patient/{patient_id}/sync`

**Request:** (via UI atau API)

**Expected Behavior:**
1. ✅ Record dibuat di tabel `sync_queue` dengan status `PENDING`
2. ✅ Job `SyncToSatuSehat` di-dispatch ke queue
3. ✅ FHIR Patient resource di-generate oleh `FhirMapper`
4. ✅ Payload FHIR disimpan ke `storage/app/mocks/satusehat/Patient_*.json` (mock mode)
5. ✅ Status queue diupdate menjadi `SENT`

**Validasi FHIR Payload:**
```json
{
  "resourceType": "Patient",
  "identifier": [
    {
      "system": "https://fhir.kemkes.go.id/id/nik",
      "value": "3201012345678901"
    }
  ],
  "name": [
    {
      "use": "official",
      "text": "BUDI SANTOSO"
    }
  ],
  "gender": "male",
  "birthDate": "1990-01-15"
}
```

---

## 📡 Endpoint API

### BPJS Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/bpjs/cek-peserta` | Validasi peserta by NIK |
| POST | `/api/bpjs/cek-peserta-kartu` | Validasi peserta by No Kartu |
| POST | `/api/bpjs/sep/create` | Buat SEP baru |
| PUT | `/api/bpjs/sep/update` | Update SEP |
| DELETE | `/api/bpjs/sep/delete` | Hapus SEP |
| POST | `/api/bpjs/rujukan/cek` | Cek rujukan |
| GET | `/api/bpjs/referensi/diagnosa` | Cari diagnosa ICD-10 |
| GET | `/api/bpjs/referensi/poli` | Daftar poliklinik |
| GET | `/api/bpjs/monitoring/sep` | Monitoring klaim SEP |

### SatuSehat Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/satusehat/patient/{id}/sync` | Queue sync patient |
| POST | `/api/satusehat/visit/{id}/sync` | Queue sync encounter |

---

## 🔧 Troubleshooting

### Issue 1: Mock tidak berjalan

**Gejala:** API tetap mencoba koneksi ke BPJS official

**Solusi:**
```bash
# Pastikan kredensial dikosongkan di .env
BPJS_CONS_ID=
BPJS_SECRET=
BPJS_USER_KEY=

# Atau set explicit mock mode
BPJS_USE_MOCK=true

# Clear config cache
php artisan config:clear
```

---

### Issue 2: Mock file tidak ditemukan

**Gejala:** Error "mock file not found"

**Solusi:**
```bash
# Cek apakah folder dan file ada
ls -la storage/app/mocks/bpjs/

# Jika tidak ada, buat manual atau run migration ulang
mkdir -p storage/app/mocks/bpjs
```

---

### Issue 3: Data seeder gagal

**Gejala:** Error duplicate entry atau constraint violation

**Solusi:**
```bash
# Reset database
php artisan migrate:fresh

# Run seeder lagi
php artisan db:seed --class=BpjsPatientSeeder
```

---

### Issue 4: SatuSehat queue tidak jalan

**Gejala:** Status queue tetap PENDING

**Solusi:**
```bash
# Jalankan queue worker
php artisan queue:work

# Atau untuk development (auto-reload)
php artisan queue:listen
```

---

## 🎓 Kesimpulan untuk Prototipe Akademis

### ✅ **Kelebihan Mock API untuk Prototipe**

1. **Tidak perlu kredensial resmi** - cocok untuk mahasiswa/peneliti
2. **Kontrol penuh** atas response dan skenario testing
3. **Repeatability** - hasil konsisten untuk presentasi
4. **Cepat dan offline** - tidak bergantung koneksi internet
5. **Production-ready** - tinggal ganti credentials untuk go-live

### 📋 **Checklist untuk Demonstrasi**

- [ ] Seeder database sudah dijalankan
- [ ] Environment variables sudah dikonfigurasi (mock mode)
- [ ] Test validasi peserta AKTIF berhasil
- [ ] Test validasi peserta TIDAK AKTIF berhasil
- [ ] Test create SEP berhasil
- [ ] Test referensi (diagnosa, poli) berhasil
- [ ] Test sync ke SatuSehat berhasil
- [ ] Logging di tabel `bpjs_claims` dan `sync_queue` berjalan

### 🎯 **Penjelasan untuk Dosen/Penguji**

> "Dalam prototipe ini, kami menggunakan **Mock API** untuk mensimulasikan integrasi BPJS V-Claim dan SatuSehat. Implementasi sudah mengikuti **dokumentasi official** dan **best practices**, sehingga **production-ready**. Mock API memungkinkan kami mendemonstrasikan **alur bisnis lengkap** validasi kepesertaan, pembuatan SEP, dan sinkronisasi data ke sistem nasional tanpa memerlukan kredensial resmi. Untuk go-live, cukup mengganti kredensial di file `.env` dan sistem akan otomatis beralih ke API production."

---

**📅 Terakhir diupdate:** 25 Januari 2025
**👨‍💻 Maintainer:** Tim Pengembang SIMPUS
