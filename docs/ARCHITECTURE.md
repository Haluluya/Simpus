# System Architecture - SIMPUS

## 📐 Architecture Overview

SIMPUS menggunakan arsitektur **MVC (Model-View-Controller)** berbasis Laravel dengan integrasi ke sistem eksternal (BPJS VClaim dan SATUSEHAT FHIR R4).

### Technology Stack

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                         │
│  Browser (Chrome, Firefox, Edge, Safari)                    │
│  - HTML5, CSS3 (Tailwind CSS 3)                            │
│  - JavaScript (Alpine.js 3, Vanilla JS)                    │
│  - Vite 7 (Build tool, HMR)                                │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ HTTP/HTTPS
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                        │
│  Laravel 12 (PHP 8.2+)                                      │
│  - Controllers (Business Logic)                             │
│  - Models (Eloquent ORM)                                    │
│  - Views (Blade Templates)                                  │
│  - Services (BPJS, SATUSEHAT)                              │
│  - Middleware (Auth, CORS, etc)                            │
│  - Queue Jobs (SATUSEHAT Sync)                             │
└─────────────────────────────────────────────────────────────┘
                              │
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      DATA LAYER                             │
│  MySQL 8.0+ / MariaDB 10.6+                                │
│  - Relational Database                                      │
│  - Foreign Keys & Constraints                               │
│  - Indexes for Performance                                  │
│  - JSON columns for flexibility                             │
└─────────────────────────────────────────────────────────────┘
                              │
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
        ▼                     ▼                     ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  BPJS VClaim │    │  SATUSEHAT   │    │    Cache     │
│   REST API   │    │   FHIR R4    │    │ Database/    │
│              │    │              │    │   Redis      │
└──────────────┘    └──────────────┘    └──────────────┘
```

---

## 📊 Data Flow Diagram (DFD)

### DFD Level 0 - Context Diagram

Diagram ini menunjukkan sistem SIMPUS secara keseluruhan dan interaksinya dengan entitas eksternal.

```mermaid
graph TB
    subgraph External Entities
        P[Pasien]
        D[Dokter]
        L[Petugas Lab]
        A[Apoteker]
        R[Petugas Rekam Medis]
        PD[Petugas Pendaftaran]
        AD[Administrator]
        BPJS[BPJS VClaim API]
        SS[SATUSEHAT FHIR]
    end

    subgraph SIMPUS System
        SYS[SIMPUS<br/>Sistem Informasi<br/>Manajemen Puskesmas]
    end

    P -->|Data Pasien, Keluhan| SYS
    PD -->|Registrasi, Antrian| SYS
    D -->|Diagnosis, Resep, Rujukan| SYS
    L -->|Hasil Lab| SYS
    A -->|Dispensing Obat| SYS
    R -->|Kelola EMR| SYS
    AD -->|Konfigurasi, User Management| SYS

    SYS -->|Nomor Antrian, EMR| P
    SYS -->|Data Pasien, Riwayat| D
    SYS -->|Order Lab| L
    SYS -->|Resep| A
    SYS -->|Laporan, Statistik| AD

    SYS <-->|Validasi Peserta, SEP| BPJS
    SYS <-->|Sync Patient, Encounter, Observation| SS
```

**External Entities:**
1. **Pasien** - Menerima layanan kesehatan
2. **Dokter** - Melakukan pemeriksaan dan diagnosis
3. **Petugas Lab** - Melakukan pemeriksaan laboratorium
4. **Apoteker** - Melayani resep obat
5. **Petugas Rekam Medis** - Mengelola data rekam medis
6. **Petugas Pendaftaran** - Registrasi dan antrian pasien
7. **Administrator** - Mengelola sistem dan user
8. **BPJS VClaim API** - Validasi kepesertaan dan SEP
9. **SATUSEHAT FHIR** - Integrasi data ke sistem nasional

---

### DFD Level 1 - Main Processes

Diagram ini memecah sistem SIMPUS menjadi proses-proses utama.

```mermaid
graph TB
    subgraph External
        PASIEN[Pasien]
        DOKTER[Dokter]
        LAB[Petugas Lab]
        APOTEKER[Apoteker]
        ADMIN[Administrator]
        BPJS[BPJS API]
        SS[SATUSEHAT API]
    end

    subgraph Data Stores
        D1[(D1: Patients)]
        D2[(D2: Visits)]
        D3[(D3: EMR Notes)]
        D4[(D4: Lab Orders)]
        D5[(D5: Prescriptions)]
        D6[(D6: Queue Tickets)]
        D7[(D7: BPJS Claims)]
        D8[(D8: Sync Queue)]
    end

    P1[1.0<br/>Pendaftaran<br/>Pasien]
    P2[2.0<br/>Sistem<br/>Antrian]
    P3[3.0<br/>Pemeriksaan<br/>& EMR]
    P4[4.0<br/>Laboratorium]
    P5[5.0<br/>Farmasi]
    P6[6.0<br/>Integrasi<br/>BPJS]
    P7[7.0<br/>Integrasi<br/>SATUSEHAT]
    P8[8.0<br/>Pelaporan &<br/>Monitoring]

    PASIEN -->|Data Registrasi| P1
    P1 -->|Data Pasien Baru| D1
    P1 -->|Trigger Antrian| P2
    D1 -->|Data Pasien| P2

    P2 -->|Nomor Antrian| D6
    D6 -->|Info Antrian| PASIEN

    DOKTER -->|Mulai Pemeriksaan| P3
    D6 -->|Data Antrian| P3
    D1 -->|Riwayat Pasien| P3
    P3 -->|Data Kunjungan| D2
    P3 -->|Catatan Medis| D3
    P3 -->|Order Lab| P4
    P3 -->|Resep| P5

    P4 -->|Data Order Lab| D4
    LAB -->|Hasil Lab| P4
    D4 -->|Hasil Lab| D3

    P5 -->|Data Resep| D5
    APOTEKER -->|Dispensing| P5
    D5 -->|Obat| PASIEN

    P1 -->|Validasi BPJS| P6
    P3 -->|Buat SEP| P6
    P6 <-->|API Request/Response| BPJS
    P6 -->|Data Klaim| D7

    P1 -->|Sync Patient| P7
    P3 -->|Sync Encounter| P7
    P4 -->|Sync Observation| P7
    P7 <-->|FHIR API| SS
    P7 -->|Queue Jobs| D8

    D1 & D2 & D3 & D4 & D5 -->|Data| P8
    P8 -->|Laporan| ADMIN
```

**Main Processes:**

1. **1.0 Pendaftaran Pasien**
   - Input: Data pasien dari petugas pendaftaran
   - Output: Data pasien tersimpan, trigger sistem antrian
   - Data Store: D1 (Patients)

2. **2.0 Sistem Antrian**
   - Input: Data pasien, poliklinik, metode pembayaran
   - Output: Nomor antrian, status antrian
   - Data Store: D6 (Queue Tickets)

3. **3.0 Pemeriksaan & EMR**
   - Input: Data pasien, keluhan, pemeriksaan dokter
   - Output: Diagnosis, catatan medis, order lab, resep
   - Data Store: D2 (Visits), D3 (EMR Notes)

4. **4.0 Laboratorium**
   - Input: Order laboratorium dari dokter
   - Output: Hasil pemeriksaan lab
   - Data Store: D4 (Lab Orders)

5. **5.0 Farmasi**
   - Input: Resep dari dokter
   - Output: Obat yang didispensing
   - Data Store: D5 (Prescriptions)

6. **6.0 Integrasi BPJS**
   - Input: Data pasien BPJS, data kunjungan
   - Output: Validasi peserta, SEP, data klaim
   - Data Store: D7 (BPJS Claims)
   - External: BPJS VClaim API

7. **7.0 Integrasi SATUSEHAT**
   - Input: Data pasien, kunjungan, hasil lab
   - Output: Data terkirim ke SATUSEHAT
   - Data Store: D8 (Sync Queue)
   - External: SATUSEHAT FHIR API

8. **8.0 Pelaporan & Monitoring**
   - Input: Semua data dari data stores
   - Output: Dashboard, laporan Excel/PDF, statistik
   - Data Store: D1-D5 (Read-only)

---

### DFD Level 2 - Detailed Process Flows

#### 2.1 DFD Level 2: Process 1.0 - Pendaftaran Pasien

```mermaid
graph TB
    subgraph External
        PETUGAS[Petugas Pendaftaran]
        BPJS_API[BPJS API]
    end

    subgraph Data Stores
        D1[(D1: Patients)]
    end

    P11[1.1<br/>Cek Data<br/>Pasien]
    P12[1.2<br/>Validasi<br/>NIK/BPJS]
    P13[1.3<br/>Input Data<br/>Pasien]
    P14[1.4<br/>Generate<br/>No. RM]
    P15[1.5<br/>Simpan Data<br/>Pasien]

    PETUGAS -->|NIK/No.BPJS| P11
    P11 -->|Cari Pasien| D1
    D1 -->|Data Pasien Existing| P11

    P11 -->|Pasien Baru| P12
    P12 <-->|Validasi Kepesertaan| BPJS_API
    P12 -->|Data Valid| P13

    P13 -->|Data Lengkap| P14
    P14 -->|No. RM Auto| P15
    P15 -->|Simpan| D1
    D1 -->|Data Tersimpan| PETUGAS
```

**Sub-processes:**
- **1.1 Cek Data Pasien** - Cari pasien berdasarkan NIK/No.BPJS
- **1.2 Validasi NIK/BPJS** - Validasi ke BPJS API (jika pasien BPJS)
- **1.3 Input Data Pasien** - Form input data demografi
- **1.4 Generate No. RM** - Auto-generate medical record number
- **1.5 Simpan Data Pasien** - Simpan ke database

---

#### 2.2 DFD Level 2: Process 3.0 - Pemeriksaan & EMR

```mermaid
graph TB
    subgraph External
        DOKTER[Dokter]
    end

    subgraph Data Stores
        D1[(D1: Patients)]
        D2[(D2: Visits)]
        D3[(D3: EMR Notes)]
        D4[(D4: Lab Orders)]
        D5[(D5: Prescriptions)]
        D6[(D6: Queue Tickets)]
    end

    P31[3.1<br/>Ambil Data<br/>Pasien]
    P32[3.2<br/>Buat<br/>Kunjungan]
    P33[3.3<br/>Input<br/>Anamnesis]
    P34[3.4<br/>Input<br/>Pemeriksaan]
    P35[3.5<br/>Input<br/>Diagnosis]
    P36[3.6<br/>Buat Order<br/>Lab]
    P37[3.7<br/>Buat<br/>Resep]
    P38[3.8<br/>Simpan<br/>EMR]
    P39[3.9<br/>Update<br/>Status Antrian]

    D6 -->|Panggil Pasien| P31
    P31 -->|Get Patient Data| D1
    D1 -->|Riwayat, Alergi| P31
    P31 -->|Data Pasien| DOKTER

    DOKTER -->|Mulai Kunjungan| P32
    P32 -->|Buat Visit| D2

    DOKTER -->|Keluhan Utama| P33
    P33 -->|Subjective| P34

    DOKTER -->|Vital Signs, Pemeriksaan Fisik| P34
    P34 -->|Objective| P35

    DOKTER -->|Diagnosis ICD-10| P35
    P35 -->|Assessment| P38

    DOKTER -->|Order Tes Lab| P36
    P36 -->|Lab Order| D4

    DOKTER -->|Input Resep| P37
    P37 -->|Prescription| D5

    P35 -->|Plan| P38
    P38 -->|Save EMR| D3
    D3 -->|EMR Tersimpan| P39

    P39 -->|Update Status=SELESAI| D6
```

**Sub-processes:**
- **3.1 Ambil Data Pasien** - Load data pasien dan riwayat medis
- **3.2 Buat Kunjungan** - Create visit record baru
- **3.3 Input Anamnesis** - Subjective (SOAP): keluhan, riwayat penyakit
- **3.4 Input Pemeriksaan** - Objective (SOAP): vital signs, pemeriksaan fisik
- **3.5 Input Diagnosis** - Assessment (SOAP): diagnosis dengan ICD-10
- **3.6 Buat Order Lab** - Jika perlu pemeriksaan lab
- **3.7 Buat Resep** - Plan (SOAP): terapi farmakologi
- **3.8 Simpan EMR** - Simpan catatan medis lengkap
- **3.9 Update Status Antrian** - Ubah status antrian jadi SELESAI

---

#### 2.3 DFD Level 2: Process 6.0 - Integrasi BPJS

```mermaid
graph TB
    subgraph External
        PETUGAS[Petugas Pendaftaran]
        BPJS[BPJS VClaim API]
    end

    subgraph Data Stores
        D1[(D1: Patients)]
        D2[(D2: Visits)]
        D7[(D7: BPJS Claims)]
    end

    P61[6.1<br/>Validasi<br/>Kepesertaan]
    P62[6.2<br/>Cek<br/>Rujukan]
    P63[6.3<br/>Buat<br/>SEP]
    P64[6.4<br/>Update<br/>SEP]
    P65[6.5<br/>Delete<br/>SEP]
    P66[6.6<br/>Monitoring<br/>Klaim]

    PETUGAS -->|No.Kartu/NIK| P61
    D1 -->|Data Pasien| P61
    P61 <-->|GET /Peserta| BPJS
    P61 -->|Status Kepesertaan| PETUGAS
    P61 -->|Update meta BPJS| D1

    PETUGAS -->|Cek Rujukan| P62
    P62 <-->|GET /Rujukan| BPJS
    P62 -->|Data Rujukan| PETUGAS

    PETUGAS -->|Data SEP| P63
    D1 -->|Data Pasien| P63
    D2 -->|Data Kunjungan| P63
    P63 <-->|POST /SEP| BPJS
    P63 -->|No.SEP| D7
    P63 -->|No.SEP| PETUGAS

    PETUGAS -->|Update Data SEP| P64
    D7 -->|Data SEP Lama| P64
    P64 <-->|PUT /SEP| BPJS
    P64 -->|Update SEP| D7

    PETUGAS -->|Hapus SEP| P65
    D7 -->|Data SEP| P65
    P65 <-->|DELETE /SEP| BPJS
    P65 -->|Status Deleted| D7

    PETUGAS -->|Lihat Klaim| P66
    D7 -->|Data Klaim| P66
    P66 <-->|GET /Monitoring| BPJS
    P66 -->|Laporan Klaim| PETUGAS
```

**Sub-processes:**
- **6.1 Validasi Kepesertaan** - Validasi status BPJS pasien (AKTIF/TIDAK AKTIF)
- **6.2 Cek Rujukan** - Cek rujukan FKTP/FKTL
- **6.3 Buat SEP** - Create Surat Eligibilitas Peserta
- **6.4 Update SEP** - Update data SEP
- **6.5 Delete SEP** - Hapus SEP (jika salah input)
- **6.6 Monitoring Klaim** - Monitor status klaim BPJS

---

#### 2.4 DFD Level 2: Process 7.0 - Integrasi SATUSEHAT

```mermaid
graph TB
    subgraph External
        SS[SATUSEHAT FHIR API]
    end

    subgraph Data Stores
        D1[(D1: Patients)]
        D2[(D2: Visits)]
        D4[(D4: Lab Orders)]
        D8[(D8: Sync Queue)]
    end

    subgraph Queue Workers
        QW[Queue Worker]
    end

    P71[7.1<br/>Build FHIR<br/>Patient]
    P72[7.2<br/>Build FHIR<br/>Encounter]
    P73[7.3<br/>Build FHIR<br/>Observation]
    P74[7.4<br/>Queue<br/>Sync Job]
    P75[7.5<br/>Process<br/>Queue]
    P76[7.6<br/>Send to<br/>SATUSEHAT]
    P77[7.7<br/>Handle<br/>Response]

    D1 -->|Pasien Baru/Update| P71
    P71 -->|FHIR Patient Resource| P74

    D2 -->|Kunjungan Baru| P72
    D1 -->|Data Pasien| P72
    P72 -->|FHIR Encounter Resource| P74

    D4 -->|Hasil Lab| P73
    D2 -->|Data Kunjungan| P73
    P73 -->|FHIR Observation Resource| P74

    P74 -->|Create Job| D8
    D8 -->|Pending Jobs| QW

    QW -->|Pick Job| P75
    P75 -->|Job Data| P76
    P76 <-->|POST /Patient, /Encounter, /Observation| SS

    P76 -->|Response| P77
    P77 -->|Success: Update FHIR ID| D1
    P77 -->|Success: Update FHIR ID| D2
    P77 -->|Success: Mark Completed| D8
    P77 -->|Failed: Retry/Mark Failed| D8
```

**Sub-processes:**
- **7.1 Build FHIR Patient** - Convert data pasien ke FHIR Patient resource
- **7.2 Build FHIR Encounter** - Convert data kunjungan ke FHIR Encounter resource
- **7.3 Build FHIR Observation** - Convert hasil lab ke FHIR Observation resource
- **7.4 Queue Sync Job** - Tambahkan job ke sync queue
- **7.5 Process Queue** - Queue worker ambil pending jobs
- **7.6 Send to SATUSEHAT** - Kirim HTTP request ke SATUSEHAT API
- **7.7 Handle Response** - Proses response (success/failed), retry mechanism

---

## 🏗️ Component Diagram

```mermaid
graph TB
    subgraph Presentation Layer
        V[Views<br/>Blade Templates]
        JS[Alpine.js<br/>Components]
    end

    subgraph Application Layer
        C[Controllers]
        MW[Middleware]
        R[Routes]
    end

    subgraph Business Logic Layer
        S[Services<br/>BpjsClient, SatusehatClient]
        J[Queue Jobs]
        EV[Events & Listeners]
    end

    subgraph Data Access Layer
        M[Models<br/>Eloquent ORM]
        MG[Migrations]
    end

    subgraph External Services
        BPJS[BPJS VClaim API]
        SS[SATUSEHAT FHIR API]
    end

    subgraph Database
        DB[(MySQL/MariaDB)]
    end

    V <-->|Data| C
    JS <-->|AJAX| C
    C <-->|Use| S
    C <-->|Dispatch| J
    C <-->|Fire| EV
    C <-->|ORM| M
    M <-->|Query| DB
    MG -->|Schema| DB
    S <-->|HTTP| BPJS
    S <-->|HTTP| SS
    J <-->|Async| S
    R -->|Route| C
    MW -->|Filter| C
```

**Components:**

1. **Presentation Layer**
   - **Views**: Blade templates untuk rendering HTML
   - **Alpine.js Components**: Interactive UI components

2. **Application Layer**
   - **Controllers**: Handle HTTP requests & responses
   - **Middleware**: Authentication, authorization, logging
   - **Routes**: URL routing configuration

3. **Business Logic Layer**
   - **Services**: BPJS client, SATUSEHAT client, business rules
   - **Queue Jobs**: Async processing untuk SATUSEHAT sync
   - **Events & Listeners**: Event-driven architecture

4. **Data Access Layer**
   - **Models**: Eloquent ORM models
   - **Migrations**: Database schema version control

5. **External Services**
   - **BPJS VClaim API**: REST API untuk BPJS integration
   - **SATUSEHAT FHIR API**: FHIR R4 API untuk SATUSEHAT integration

6. **Database**
   - **MySQL/MariaDB**: Relational database

---

## 🔄 Sequence Diagrams

### Sequence Diagram: Pendaftaran Pasien BPJS

```mermaid
sequenceDiagram
    actor Petugas as Petugas Pendaftaran
    participant UI as Web Interface
    participant Ctrl as PatientController
    participant BPJS as BpjsClient
    participant API as BPJS API
    participant DB as Database

    Petugas->>UI: Input NIK/No.Kartu BPJS
    UI->>Ctrl: POST /patients/validate-bpjs
    Ctrl->>BPJS: validatePeserta(nik)
    BPJS->>API: GET /Peserta/{nik}

    alt Peserta AKTIF
        API-->>BPJS: Response: Status AKTIF, Data Peserta
        BPJS-->>Ctrl: Return peserta data
        Ctrl->>UI: Show peserta data (auto-fill form)
        UI->>Petugas: Display form dengan data BPJS

        Petugas->>UI: Submit form registrasi
        UI->>Ctrl: POST /patients
        Ctrl->>DB: Insert patient record
        DB-->>Ctrl: Patient ID
        Ctrl->>DB: Update meta['bpjs_status'] = 'AKTIF'
        Ctrl->>UI: Success response
        UI->>Petugas: Show success message + No.RM
    else Peserta TIDAK AKTIF
        API-->>BPJS: Response: Status TIDAK AKTIF (Tunggakan)
        BPJS-->>Ctrl: Return status tidak aktif
        Ctrl->>UI: Warning: Pasien tidak bisa pakai BPJS
        UI->>Petugas: Show warning, ubah ke UMUM
    else Error (NIK tidak ditemukan)
        API-->>BPJS: Error response
        BPJS-->>Ctrl: Throw exception
        Ctrl->>UI: Error: NIK tidak terdaftar di BPJS
        UI->>Petugas: Show error message
    end
```

---

### Sequence Diagram: Pemeriksaan & EMR (SOAP)

```mermaid
sequenceDiagram
    actor Dokter
    participant UI as Web Interface
    participant Ctrl as VisitController
    participant EmrCtrl as EmrController
    participant DB as Database
    participant Queue as Queue System

    Dokter->>UI: Pilih pasien dari antrian
    UI->>Ctrl: GET /visits/{visit_id}
    Ctrl->>DB: Get visit data + patient history
    DB-->>Ctrl: Visit + Patient + Previous EMR
    Ctrl->>UI: Display patient data & history
    UI->>Dokter: Show EMR form (SOAP)

    Dokter->>UI: Input Subjective (Keluhan)
    Dokter->>UI: Input Objective (Vital signs, Pemeriksaan)
    Dokter->>UI: Input Assessment (Diagnosis ICD-10)
    Dokter->>UI: Input Plan (Terapi, Resep)

    alt Ada Order Lab
        Dokter->>UI: Tambah order laboratorium
        UI->>Ctrl: POST /lab-orders
        Ctrl->>DB: Insert lab order + items
    end

    alt Ada Resep
        Dokter->>UI: Tambah resep obat
        UI->>Ctrl: POST /prescriptions
        Ctrl->>DB: Insert prescription + items
    end

    Dokter->>UI: Save EMR
    UI->>EmrCtrl: POST /emr-notes
    EmrCtrl->>DB: Insert EMR note
    EmrCtrl->>DB: Update visit status
    EmrCtrl->>Queue: Dispatch SyncEncounter job
    Queue-->>EmrCtrl: Job queued
    EmrCtrl->>UI: Success response
    UI->>Dokter: EMR tersimpan, antrian selesai
```

---

### Sequence Diagram: SATUSEHAT Sync (Background Job)

```mermaid
sequenceDiagram
    participant Job as Queue Job
    participant Client as SatusehatClient
    participant Auth as OAuth Service
    participant API as SATUSEHAT API
    participant DB as Database

    Job->>Client: syncPatient(patient_id)
    Client->>DB: Get patient data
    DB-->>Client: Patient record

    Client->>Client: Build FHIR Patient resource

    Client->>Auth: getAccessToken()
    Auth->>API: POST /oauth2/v1/token
    API-->>Auth: Access token (expires 3600s)
    Auth-->>Client: Return token

    Client->>API: POST /fhir-r4/v1/Patient

    alt Success
        API-->>Client: 201 Created + FHIR ID
        Client->>DB: Update patient.meta['satusehat_id']
        Client->>DB: Update sync_queue status = COMPLETED
        Client-->>Job: Success
    else Patient already exists (409)
        API-->>Client: 409 Conflict
        Client->>API: GET /fhir-r4/v1/Patient?identifier={nik}
        API-->>Client: Existing Patient with FHIR ID
        Client->>DB: Update patient.meta['satusehat_id']
        Client->>DB: Update sync_queue status = COMPLETED
    else Error (400, 500, timeout)
        API-->>Client: Error response
        Client->>DB: Increment sync_queue.attempts
        Client->>DB: Update sync_queue.error_message

        alt Attempts < 3
            Client->>DB: Update sync_queue status = PENDING (retry)
            Client-->>Job: Fail (will retry)
        else Attempts >= 3
            Client->>DB: Update sync_queue status = FAILED
            Client-->>Job: Fail (max retries)
        end
    end
```

---

## 🚀 Deployment Architecture

### Production Deployment Diagram

```mermaid
graph TB
    subgraph Internet
        USER[Users/Clients]
    end

    subgraph DMZ / Reverse Proxy
        NGINX[Nginx/Apache<br/>Web Server<br/>Port 80/443]
        SSL[SSL/TLS Certificate]
    end

    subgraph Application Servers
        APP1[Laravel App Server 1<br/>PHP-FPM 8.2]
        APP2[Laravel App Server 2<br/>PHP-FPM 8.2]
        QUEUE[Queue Worker<br/>Supervisor]
    end

    subgraph Database Layer
        MASTER[(MySQL Master<br/>Read/Write)]
        SLAVE[(MySQL Slave<br/>Read Only)]
    end

    subgraph Cache Layer
        REDIS[Redis<br/>Cache & Sessions]
    end

    subgraph External APIs
        BPJS[BPJS VClaim API<br/>new-api.bpjs-kesehatan.go.id]
        SS[SATUSEHAT API<br/>api-satusehat.kemkes.go.id]
    end

    subgraph Monitoring
        LOG[Log Files<br/>storage/logs]
        MON[Monitoring<br/>Laravel Telescope/Horizon]
    end

    USER -->|HTTPS| SSL
    SSL -->|Decrypt| NGINX
    NGINX -->|Load Balance| APP1
    NGINX -->|Load Balance| APP2

    APP1 & APP2 -->|Read/Write| MASTER
    APP1 & APP2 -->|Read Only| SLAVE
    MASTER -->|Replication| SLAVE

    APP1 & APP2 <-->|Cache/Session| REDIS
    APP1 & APP2 -->|Dispatch Jobs| REDIS
    QUEUE -->|Process Jobs| REDIS

    APP1 & APP2 <-->|HTTP/REST| BPJS
    QUEUE <-->|HTTP/REST| SS

    APP1 & APP2 -->|Write Logs| LOG
    APP1 & APP2 -->|Metrics| MON
```

**Infrastructure Components:**

1. **Web Server (Nginx/Apache)**
   - SSL/TLS termination
   - Static file serving
   - Load balancing (multiple app servers)
   - Rate limiting
   - GZIP compression

2. **Application Servers (PHP-FPM)**
   - Multiple instances untuk high availability
   - Session storage di Redis (shared)
   - Horizontal scaling ready

3. **Queue Workers (Supervisor)**
   - Background job processing
   - SATUSEHAT sync jobs
   - Email notifications
   - Report generation
   - Auto-restart on failure

4. **Database (MySQL)**
   - Master-slave replication
   - Automated backups (daily)
   - Point-in-time recovery
   - Connection pooling

5. **Cache (Redis)**
   - Session storage
   - Application cache
   - Queue driver
   - Rate limiting

6. **Monitoring & Logging**
   - Laravel Telescope (development)
   - Laravel Horizon (queue monitoring)
   - Log rotation (daily)
   - Error tracking

---

## 🔐 Security Architecture

### Security Layers

```
┌─────────────────────────────────────────────────────────────┐
│  Layer 1: Network Security                                  │
│  - Firewall (Allow only 80/443)                            │
│  - DDoS protection                                          │
│  - Rate limiting                                            │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│  Layer 2: Transport Security                                │
│  - SSL/TLS encryption (HTTPS)                               │
│  - Certificate validation                                   │
│  - Secure headers (HSTS, CSP, X-Frame-Options)             │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│  Layer 3: Authentication & Authorization                    │
│  - Laravel Breeze (Session-based auth)                      │
│  - Password hashing (bcrypt)                                │
│  - CSRF protection                                          │
│  - Spatie Permission (RBAC)                                 │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│  Layer 4: Application Security                              │
│  - Input validation & sanitization                          │
│  - SQL injection prevention (Eloquent ORM)                  │
│  - XSS prevention (Blade escaping)                          │
│  - File upload validation                                   │
│  - API authentication (BPJS signature, OAuth2)              │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│  Layer 5: Data Security                                     │
│  - Database encryption at rest (optional)                   │
│  - Sensitive data masking                                   │
│  - Audit logging                                            │
│  - Regular backups                                          │
│  - Soft deletes (data retention)                            │
└─────────────────────────────────────────────────────────────┘
```

### Authentication Flow

```mermaid
sequenceDiagram
    actor User
    participant UI as Login Page
    participant Ctrl as AuthController
    participant MW as AuthMiddleware
    participant Sess as Session Store
    participant DB as Database

    User->>UI: Input email & password
    UI->>Ctrl: POST /login
    Ctrl->>DB: Find user by email
    DB-->>Ctrl: User record

    alt Valid Credentials
        Ctrl->>Ctrl: Verify password (bcrypt)
        Ctrl->>Sess: Create session
        Sess-->>Ctrl: Session ID
        Ctrl->>DB: Log audit (LOGIN action)
        Ctrl->>UI: Redirect to dashboard
        UI->>User: Show dashboard
    else Invalid Credentials
        Ctrl->>UI: Error: Invalid credentials
        UI->>User: Show error message
    end

    Note over User,DB: Subsequent Requests

    User->>UI: Access protected page
    UI->>MW: Check authentication
    MW->>Sess: Validate session

    alt Session Valid
        Sess-->>MW: User authenticated
        MW->>DB: Check permissions (Spatie)
        DB-->>MW: User has permission
        MW->>Ctrl: Allow access
        Ctrl->>UI: Render page
        UI->>User: Show page
    else Session Invalid
        MW->>UI: Redirect to login
        UI->>User: Login required
    end
```

---

## 📈 Scalability & Performance

### Horizontal Scaling

**Application Servers:**
```
Load Balancer (Nginx)
       │
       ├─── App Server 1 (PHP-FPM)
       ├─── App Server 2 (PHP-FPM)
       ├─── App Server 3 (PHP-FPM)
       └─── App Server N (PHP-FPM)
```

**Queue Workers:**
```
Redis Queue
       │
       ├─── Worker 1 (Supervisor)
       ├─── Worker 2 (Supervisor)
       └─── Worker N (Supervisor)
```

**Database:**
```
Master (Write)
  │
  ├─── Slave 1 (Read)
  ├─── Slave 2 (Read)
  └─── Slave N (Read)
```

### Performance Optimization

1. **Query Optimization**
   - Proper indexing (see DATABASE-SCHEMA.md)
   - Eager loading (prevent N+1 queries)
   - Query caching
   - Database connection pooling

2. **Caching Strategy**
   - Route caching: `php artisan route:cache`
   - Config caching: `php artisan config:cache`
   - View caching: `php artisan view:cache`
   - Application caching: Redis cache driver
   - HTTP caching: ETags, Last-Modified headers

3. **Asset Optimization**
   - Vite build optimization
   - CSS/JS minification
   - Image optimization (lazy loading, WebP)
   - CDN for static assets (optional)

4. **Queue Optimization**
   - Separate queues by priority
   - Multiple queue workers
   - Failed job handling
   - Queue monitoring (Horizon)

---

## 📚 See Also

- [Database Schema & ERD](DATABASE-SCHEMA.md)
- [BPJS Integration](BPJS-INTEGRATION.md)
- [SATUSEHAT Integration](SATUSEHAT-INTEGRATION.md)
- [Development Guide](DEVELOPMENT-GUIDE.md)
- [Deployment Guide](DEPLOYMENT.md)
