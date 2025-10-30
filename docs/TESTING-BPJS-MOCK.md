# Testing Guide: BPJS VClaim Mock API

## 🎯 Skenario Testing

Mock API BPJS dirancang untuk mensimulasikan berbagai kondisi real yang mungkin terjadi di production.

---

## 📋 Test Case: Validasi Peserta

### ✅ Test Case 1: Peserta AKTIF

**Input:**
```
NIK          : 3201234567890001
No. Kartu    : 0001234567001
Tanggal Layanan: 2025-10-30
```

**Expected Output:**
```json
{
  "metaData": {
    "code": "200",
    "message": "Mocked BPJS response"
  },
  "response": {
    "peserta": {
      "noKartu": "0001234567001",
      "nik": "3201234567890001",
      "nama": "Peserta Aktif (Mock)",
      "hakKelas": {
        "keterangan": "KELAS I"
      },
      "statusPeserta": {
        "keterangan": "AKTIF"
      },
      "tglLahir": "1990-01-01",
      "pisa": "Puskesmas Mock",
      "jenisKelamin": "L"
    }
  }
}
```

**Validasi:**
- ✅ Status peserta: **AKTIF**
- ✅ Kelas hak: **KELAS I**
- ✅ Response code: **200**
- ✅ Bisa lanjut create SEP

---

### ❌ Test Case 2: Peserta TIDAK AKTIF

**Input:**
```
NIK          : 3201234569999999  ← Berakhiran 9999
No. Kartu    : 0001234567999     ← Berakhiran 999
Tanggal Layanan: 2025-10-30
```

**Expected Output:**
```json
{
  "metaData": {
    "code": "200",
    "message": "Mocked BPJS response"
  },
  "response": {
    "peserta": {
      "noKartu": "0001234567999",
      "nik": "3201234569999999",
      "nama": "Peserta Non-Aktif (Mock)",
      "hakKelas": {
        "keterangan": "KELAS TIDAK BERLAKU"
      },
      "statusPeserta": {
        "keterangan": "TIDAK AKTIF"
      },
      "tglLahir": "1990-01-01",
      "pisa": "Puskesmas Mock",
      "jenisKelamin": "L"
    }
  }
}
```

**Validasi:**
- ⚠️ Status peserta: **TIDAK AKTIF**
- ⚠️ Kelas hak: **KELAS TIDAK BERLAKU**
- ✅ Response code: **200** (API sukses, tapi peserta non-aktif)
- ❌ **TIDAK BISA** create SEP (harus ditolak di aplikasi)

---

### 🧪 Test Case 3: Variasi NIK/Kartu

| NIK/Kartu | Pattern | Status | Keterangan |
|-----------|---------|--------|------------|
| `3201001234567890` | Normal | ✅ AKTIF | NIK valid normal |
| `3201001239999999` | Ends `9999` | ❌ TIDAK AKTIF | Simulasi non-aktif |
| `0001234567890` | Normal | ✅ AKTIF | No. Kartu valid |
| `0001234567999` | Ends `999` | ❌ TIDAK AKTIF | Simulasi non-aktif |
| `3201111119999999` | Ends `9999` | ❌ TIDAK AKTIF | Kombinasi angka |

---

## 📝 Test Procedure (Step-by-step)

### A. Testing via Web UI

1. **Login ke SIMPUS**
   ```
   Username: admin/dokter/petugas
   Password: sesuai database
   ```

2. **Masuk ke Menu Integrasi**
   ```
   Sidebar → Integrasi → BPJS VClaim
   ```

3. **Tab: Cek Peserta**
   - Pilih method: "NIK" atau "No. Kartu"
   - Input data test case
   - Klik "Cek Peserta"

4. **Verifikasi Response**
   - Lihat bagian "Hasil Verifikasi"
   - Cek status: AKTIF / TIDAK AKTIF
   - Screenshot untuk dokumentasi

### B. Testing via Postman/API

**Endpoint:**
```
POST http://localhost/bpjs/cek-peserta-kartu
```

**Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {csrf_token}
Cookie: simpus_session={session_id}
```

**Body (JSON):**
```json
{
  "no_kartu": "0001234567001",
  "service_date": "2025-10-30"
}
```

**Expected Response (AKTIF):**
```json
{
  "success": true,
  "data": {
    "metaData": {
      "code": "200",
      "message": "Mocked BPJS response"
    },
    "response": {
      "peserta": {
        "statusPeserta": {
          "keterangan": "AKTIF"
        }
      }
    }
  }
}
```

---

## 🔍 Validation Checklist

Untuk setiap test case, validasi hal berikut:

### Frontend (UI)
- [ ] Form validation berfungsi (required fields)
- [ ] Loading indicator muncul saat request
- [ ] Response ditampilkan dengan benar
- [ ] Status badge warna sesuai (hijau=AKTIF, merah=TIDAK AKTIF)
- [ ] Error message jelas jika ada masalah

### Backend (Database)
- [ ] Record tersimpan di tabel `bpjs_claims`
- [ ] `patient_id` terisi jika pasien ada
- [ ] `raw_request` berisi JSON valid
- [ ] `raw_response` berisi JSON valid
- [ ] `response_time_ms` terisi
- [ ] `status_code` = 200
- [ ] `meta.source` = "mock"

### Logging
- [ ] Log muncul di `storage/logs/laravel.log`
- [ ] Log level: INFO
- [ ] Audit log tercatat di `audit_logs` table

---

## 🎓 Untuk Presentasi/Demo

### Skenario Demo yang Baik:

**1. Happy Path (Peserta AKTIF)**
```
"Pak/Bu, saya akan demo cek peserta BPJS yang AKTIF..."
→ Input NIK: 3201234567890001
→ Hasil: AKTIF ✅
→ "Peserta ini bisa lanjut create SEP"
```

**2. Error Handling (Peserta TIDAK AKTIF)**
```
"Sekarang saya coba peserta yang TIDAK AKTIF..."
→ Input NIK: 3201234569999999
→ Hasil: TIDAK AKTIF ❌
→ "Sistem mendeteksi peserta non-aktif, 
   tidak bisa lanjut pelayanan"
```

**3. Jelaskan Mock Logic**
```
"Ini menggunakan Mock API untuk demo.
Pattern: NIK berakhiran 9999 = simulasi TIDAK AKTIF
Real production: akan hit API BPJS real"
```

---

## 📊 Test Data Examples

### Valid Test Data (Copy-Paste Ready)

**Set 1: Peserta AKTIF**
```
NIK: 3201234567890001
No. Kartu: 0001234567001
Tanggal: 2025-10-30
Expected: AKTIF, KELAS I
```

**Set 2: Peserta AKTIF (Alternatif)**
```
NIK: 3503010101900001
No. Kartu: 0001122334455
Tanggal: 2025-10-30
Expected: AKTIF, KELAS I
```

**Set 3: Peserta TIDAK AKTIF**
```
NIK: 3201234569999999
No. Kartu: 0001234567999
Tanggal: 2025-10-30
Expected: TIDAK AKTIF, KELAS TIDAK BERLAKU
```

**Set 4: Peserta TIDAK AKTIF (Alternatif)**
```
NIK: 3503010109999999
No. Kartu: 0001122334999
Tanggal: 2025-10-30
Expected: TIDAK AKTIF, KELAS TIDAK BERLAKU
```

---

## 🐛 Troubleshooting

### Problem: Response selalu sama

**Solusi:**
1. Cek file `.env`: `BPJS_USE_MOCK=true`
2. Clear cache: `php artisan config:clear`
3. Restart queue: `php artisan queue:restart`

### Problem: NIK berakhiran 9999 tapi tetap AKTIF

**Solusi:**
1. Cek kode di `BpjsClient.php` line ~338
2. Pastikan fungsi `str_ends_with()` berfungsi
3. Debug: tambahkan `dd($nik, $isInactive)` di mock function

### Problem: Database error saat save

**Solusi:**
1. Cek migrasi `bpjs_claims` sudah run
2. Pastikan `raw_request` dan `raw_response` di-json_encode()
3. Run: `php artisan migrate:status`

---

## 📈 Success Metrics

Testing dianggap sukses jika:

1. ✅ **2 dari 2 test case passed** (AKTIF + TIDAK AKTIF)
2. ✅ **Database logs tercatat** di `bpjs_claims`
3. ✅ **UI menampilkan** response dengan benar
4. ✅ **Response time** < 200ms (mock API)
5. ✅ **No errors** di log file

---

## 🔄 Next Steps

Setelah Mock API testing berhasil:

1. **Documentation**: Screenshot hasil test → laporan
2. **Live API**: Siapkan kredensial real untuk production
3. **Error Handling**: Test dengan input invalid
4. **Performance**: Test dengan load testing (optional)

---

**Happy Testing!** 🎉

Dibuat: 2025-10-30  
Update terakhir: 2025-10-30
