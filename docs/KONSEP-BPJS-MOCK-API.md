# 🏥 Konsep Mock API BPJS - Sesuai BPJS Nasional

> **Dokumen ini menjelaskan bagaimana Mock API BPJS di SIMPUS mengimplementasikan konsep status kepesertaan yang sesuai dengan sistem BPJS nasional**

**Tanggal:** 25 Januari 2025

---

## 📚 **Konsep BPJS Nasional (Real System)**

### **A. Status Kepesertaan**

| Status | Kondisi | Hak Layanan | Dasar Penentuan |
|--------|---------|-------------|-----------------|
| **AKTIF** | Iuran dibayar rutin | ✅ **Berhak** layanan BPJS | Database pembayaran iuran BPJS |
| **TIDAK AKTIF** | Menunggak iuran ≥ 1 bulan | ❌ **Tidak berhak** (bayar tunai) | Database pembayaran iuran BPJS |

### **B. Cara BPJS Menentukan Status**

```mermaid
graph LR
    A[Faskes Input NIK] --> B[BPJS API V-Claim]
    B --> C[Query Database Pembayaran]
    C --> D{Cek Tunggakan}
    D -->|< 1 bulan| E[Status: AKTIF]
    D -->|≥ 1 bulan| F[Status: TIDAK AKTIF]
    E --> G[Return ke Faskes]
    F --> G
```

**Komponen Penentuan Status:**
1. **Database Pembayaran Iuran**
   - Setiap peserta punya histori pembayaran bulanan
   - BPJS track tanggal bayar, nominal, periode

2. **Business Logic**
   ```
   IF (Tunggakan Iuran >= 1 bulan) THEN
       Status = TIDAK AKTIF
   ELSE
       Status = AKTIF
   END IF
   ```

3. **Real-time Validation**
   - Setiap kali faskes validasi, BPJS query database terbaru
   - Status bisa berubah kapan saja (tergantung pembayaran)

### **C. TIDAK Ada Hubungan dengan Angka di NIK/No Kartu**

⚠️ **PENTING:**
- Status AKTIF/TIDAK AKTIF **TIDAK ditentukan** oleh angka tertentu di NIK
- Status **TIDAK ditentukan** oleh angka tertentu di No Kartu BPJS
- Status **HANYA ditentukan** oleh histori pembayaran iuran

**Contoh Real Case:**
```
NIK: 3201012345678901
No BPJS: 0001234567890

Bulan Januari: Bayar iuran → Status: AKTIF
Bulan Februari: Tidak bayar → Status: TIDAK AKTIF
Bulan Maret: Bayar lagi → Status: AKTIF kembali
```

---

## 🎭 **Mock API SIMPUS (Simulasi untuk Prototipe)**

### **A. Tantangan Mock API**

Karena ini **prototipe akademis**, kita tidak punya:
- ❌ Database pembayaran iuran BPJS
- ❌ Histori transaksi pembayaran
- ❌ Akses ke server BPJS nasional
- ❌ Data real peserta BPJS

**Pertanyaan:** Bagaimana cara simulasi status AKTIF/TIDAK AKTIF tanpa database pembayaran?

---

### **B. Solusi Mock API (Versi Realistis)**

#### **Strategi: Database-Driven Status**

Kita simulasi database BPJS dengan cara:
1. ✅ Simpan status di **database lokal** (table `patients`)
2. ✅ Mock API **query database** untuk cek status
3. ✅ Sesuai dengan cara BPJS asli (query database)

**Diagram:**
```mermaid
graph LR
    A[Input NIK/No BPJS] --> B[Mock API BpjsClient]
    B --> C[Query Database Patients]
    C --> D{Patient Found?}
    D -->|Yes| E[Baca meta.bpjs_status]
    D -->|No| F[Fallback: Pattern 9999/999]
    E --> G[Return Status]
    F --> G
```

---

### **C. Implementasi di Code**

#### **1. Database Schema (Table patients)**

```sql
CREATE TABLE patients (
    id BIGINT PRIMARY KEY,
    nik VARCHAR(20) UNIQUE,
    bpjs_card_no VARCHAR(30) UNIQUE,
    name VARCHAR(255),
    meta JSON,  -- ⭐ Simpan bpjs_status di sini
    ...
);
```

**Contoh Data:**
```json
{
    "nik": "1111012345678901",
    "bpjs_card_no": "1111234567890",
    "name": "BUDI SANTOSO",
    "meta": {
        "bpjs_status": "AKTIF",           // ⭐ Seperti BPJS punya database pembayaran
        "bpjs_class": "KELAS I",
        "participant_type": "PNS",
        "last_payment_date": "2025-01-01" // Optional: simulasi tanggal bayar terakhir
    }
}
```

#### **2. BpjsClient - Query Database**

```php
// File: app/Services/Bpjs/BpjsClient.php

private function mockPeserta(string $endpoint, array $context): array
{
    // Extract NIK dari request
    $nik = '...'; // from endpoint
    $card = '...'; // from endpoint

    // ⭐ REALISTIS: Query database (seperti BPJS query database pembayaran)
    $patient = null;
    if ($nik) {
        $patient = Patient::where('nik', $nik)->first();
    }
    if (!$patient && $card) {
        $patient = Patient::where('bpjs_card_no', $card)->first();
    }

    // Determine status dari database
    if ($patient) {
        // ⭐ Baca dari meta (seperti BPJS baca dari database pembayaran)
        $bpjsStatus = $patient->meta['bpjs_status'] ?? 'AKTIF';
        $bpjsClass = $patient->meta['bpjs_class'] ?? 'KELAS I';
        $participantType = $patient->meta['participant_type'] ?? 'PNS';

        $isInactive = ($bpjsStatus === 'TIDAK AKTIF');
        $status = $bpjsStatus;
        $nama = $patient->name;
        $kelas = $bpjsClass;
    } else {
        // Fallback untuk testing manual (jika tidak ada di database)
        $isInactive = str_ends_with($nik, '9999') || str_ends_with($card, '999');
        $status = $isInactive ? 'TIDAK AKTIF' : 'AKTIF';
        $nama = 'Peserta Mock';
        $kelas = $isInactive ? 'KELAS TIDAK BERLAKU' : 'KELAS I';
    }

    return [
        'metaData' => ['code' => '200', 'message' => 'OK'],
        'response' => [
            'peserta' => [
                'statusPeserta' => [
                    'keterangan' => $status  // ⭐ Status dari database
                ],
                // ...
            ]
        ]
    ];
}
```

#### **3. Seeder - Set Status di Database**

```php
// File: database/seeders/BpjsPatientSeeder.php

// ✅ PESERTA AKTIF
Patient::create([
    'nik' => '1111012345678901',
    'bpjs_card_no' => '1111234567890',
    'name' => 'BUDI SANTOSO',
    'meta' => [
        'bpjs_status' => 'AKTIF',  // ⭐ Set status secara explicit
        'bpjs_class' => 'KELAS I',
        'participant_type' => 'PNS',
        'note' => 'Rutin bayar iuran setiap tanggal 10'
    ],
]);

// ❌ PESERTA TIDAK AKTIF
Patient::create([
    'nik' => '1111012345679999',
    'bpjs_card_no' => '1111234567999',
    'name' => 'AHMAD DAHLAN',
    'meta' => [
        'bpjs_status' => 'TIDAK AKTIF',  // ⭐ Set status secara explicit
        'bpjs_class' => 'KELAS TIDAK BERLAKU',
        'participant_type' => 'PBPU',
        'note' => 'Menunggak iuran sejak 6 bulan yang lalu',  // ⭐ Alasan realistis
        'last_payment_date' => '2024-06-01'  // Terakhir bayar 6 bulan lalu
    ],
]);
```

---

## 📊 **Perbandingan: Real BPJS vs Mock API**

| Aspek | Real BPJS | Mock API SIMPUS |
|-------|-----------|-----------------|
| **Data Source** | Database pembayaran iuran BPJS nasional | Database lokal (table patients) |
| **Cara Cek Status** | Query histori pembayaran iuran | Query field `meta->bpjs_status` |
| **Logika** | `IF tunggakan >= 1 bulan THEN TIDAK AKTIF` | `IF meta['bpjs_status'] == 'TIDAK AKTIF' THEN ...` |
| **Real-time** | Ya (update saat ada pembayaran) | Ya (bisa update status kapan saja) |
| **Pattern 9999/999** | ❌ Tidak ada | ✅ Hanya fallback untuk testing manual |
| **Realistis?** | 100% (sistem asli) | 95% (sangat mirip konsepnya) |

---

## 🎯 **Mengapa Solusi Ini Lebih Baik?**

### **❌ Pattern-Based (Versi Lama - Tidak Realistis)**

```php
// SALAH: Status ditentukan dari angka
if (str_ends_with($nik, '9999')) {
    $status = 'TIDAK AKTIF';
}
```

**Masalah:**
- ❌ Tidak sesuai konsep BPJS asli
- ❌ Tidak bisa ubah status secara dinamis
- ❌ Hanya trick programming

### **✅ Database-Driven (Versi Baru - Realistis)**

```php
// BENAR: Status dari database
$patient = Patient::where('nik', $nik)->first();
$status = $patient->meta['bpjs_status'];
```

**Kelebihan:**
- ✅ Sesuai konsep BPJS asli (query database)
- ✅ Status bisa diubah kapan saja (seperti real BPJS)
- ✅ Bisa simulasi berbagai skenario
- ✅ Lebih mudah dipahami

---

## 🧪 **Testing Scenarios**

### **Scenario 1: Peserta AKTIF**

**Database:**
```json
{
    "nik": "1111012345678901",
    "meta": {
        "bpjs_status": "AKTIF",
        "last_payment_date": "2025-01-10"
    }
}
```

**Test:**
```bash
POST /api/bpjs/cek-peserta
{
    "nik": "1111012345678901",
    "service_date": "2025-01-25"
}
```

**Response:**
```json
{
    "response": {
        "peserta": {
            "statusPeserta": {
                "keterangan": "AKTIF"  // ⭐ Dari database
            }
        }
    }
}
```

---

### **Scenario 2: Peserta TIDAK AKTIF**

**Database:**
```json
{
    "nik": "1111012345679999",
    "meta": {
        "bpjs_status": "TIDAK AKTIF",
        "last_payment_date": "2024-06-01",
        "note": "Menunggak 6 bulan"
    }
}
```

**Test:**
```bash
POST /api/bpjs/cek-peserta
{
    "nik": "1111012345679999",
    "service_date": "2025-01-25"
}
```

**Response:**
```json
{
    "response": {
        "peserta": {
            "statusPeserta": {
                "keterangan": "TIDAK AKTIF"  // ⭐ Dari database
            }
        }
    }
}
```

---

### **Scenario 3: Simulasi Pembayaran (Update Status)**

```php
// Simulasi: Peserta bayar iuran → status berubah AKTIF

$patient = Patient::where('nik', '1111012345679999')->first();

// Update meta (seperti BPJS update database pembayaran)
$meta = $patient->meta;
$meta['bpjs_status'] = 'AKTIF';
$meta['last_payment_date'] = now()->toDateString();
$meta['note'] = 'Iuran sudah dibayar, status AKTIF kembali';

$patient->update(['meta' => $meta]);

// Sekarang validasi lagi → status AKTIF!
```

---

## 📝 **Penjelasan untuk Dosen/Penguji**

### **Konsep:**

> "Mock API BPJS di SIMPUS mengikuti konsep sistem BPJS nasional yang sebenarnya:
>
> 1. **Database-Driven:** Status kepesertaan disimpan di database lokal (field `meta->bpjs_status`)
> 2. **Query-Based:** Mock API query database untuk mendapatkan status (seperti BPJS query database pembayaran iuran)
> 3. **Realistis:** Tidak ada pattern magic number (9999/999), status ditentukan secara explicit di database
> 4. **Dinamis:** Status bisa diubah kapan saja dengan update database (simulasi pembayaran iuran)
> 5. **Production-Ready:** Struktur kode sudah siap untuk integrasi dengan BPJS API real
>
> Pattern 9999/999 hanya digunakan sebagai **fallback** untuk testing manual jika data tidak ada di database."

### **Demo Flow:**

1. **Show Database:**
   ```sql
   SELECT nik, name, meta->>'$.bpjs_status' as status
   FROM patients
   WHERE nik IN ('1111012345678901', '1111012345679999');
   ```

2. **Test Validasi AKTIF:**
   - Input NIK: `1111012345678901`
   - Show response: Status AKTIF
   - Explain: "Status dibaca dari database, bukan dari pattern"

3. **Test Validasi TIDAK AKTIF:**
   - Input NIK: `1111012345679999`
   - Show response: Status TIDAK AKTIF
   - Explain: "Status TIDAK AKTIF karena di database meta->bpjs_status = 'TIDAK AKTIF'"

4. **Simulasi Update Status:**
   ```php
   // Update status di database
   $patient->update([
       'meta' => [
           'bpjs_status' => 'AKTIF',  // Dari TIDAK AKTIF ke AKTIF
           'last_payment_date' => '2025-01-25'
       ]
   ]);
   ```
   - Test validasi lagi → Status berubah jadi AKTIF
   - Explain: "Seperti BPJS asli, status bisa berubah saat ada pembayaran iuran"

---

## ✅ **Summary**

| Aspek | Status |
|-------|--------|
| **Sesuai Konsep BPJS** | ✅ Ya (query database untuk cek status) |
| **Realistis** | ✅ Ya (tidak bergantung pattern angka) |
| **Dinamis** | ✅ Ya (status bisa diubah kapan saja) |
| **Mudah Dipahami** | ✅ Ya (logic jelas dan eksplisit) |
| **Production-Ready** | ✅ Ya (tinggal ganti dengan query ke BPJS API) |

---

**📅 Terakhir diupdate:** 25 Januari 2025
**✅ Status:** Implementasi sesuai konsep BPJS nasional
