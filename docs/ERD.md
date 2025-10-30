# Entity Relationship Diagram (ERD) - SIMPUS

## Diagram ERD

```mermaid
erDiagram
    %% Core Entities
    users ||--o{ patients : "creates/updates"
    users ||--o{ visits : "provides"
    users ||--o{ emr_notes : "authors"
    users ||--o{ lab_orders : "orders"
    users ||--o{ lab_orders : "verifies"
    users ||--o{ prescriptions : "prescribes"
    users ||--o{ referrals : "creates"
    users ||--o{ bpjs_claims : "performs"
    users ||--o{ audit_logs : "performs"
    
    %% Patient -> Visit Flow
    patients ||--o{ visits : "has"
    patients ||--o{ queue_tickets : "has"
    patients ||--o{ referrals : "has"
    patients ||--o{ bpjs_claims : "has"
    
    %% Visit -> Clinical Data
    visits ||--o{ emr_notes : "has"
    visits ||--o{ lab_orders : "has"
    visits ||--o{ prescriptions : "has"
    visits ||--o{ queue_tickets : "linked"
    visits ||--o{ referrals : "has"
    visits ||--o{ bpjs_claims : "has"
    
    %% Lab System
    lab_orders ||--o{ lab_order_items : "contains"
    lab_orders ||--o{ lab_order_results : "has"
    
    %% Pharmacy System
    prescriptions ||--o{ prescription_items : "contains"
    prescription_items }o--|| master_medicines : "uses"
    
    %% Integration Queue
    sync_queue ||--o{ visits : "syncs"
    sync_queue ||--o{ patients : "syncs"
    
    %% Spatie Permission System
    roles ||--o{ role_has_permissions : "has"
    permissions ||--o{ role_has_permissions : "assigned to"
    roles ||--o{ model_has_roles : "assigned to"
    permissions ||--o{ model_has_permissions : "assigned to"
    users ||--o{ model_has_roles : "has"
    users ||--o{ model_has_permissions : "has"

    %% Table Definitions
    
    users {
        bigint id PK
        varchar name
        varchar email UK
        varchar department
        varchar phone
        enum gender
        date date_of_birth
        varchar license_number
        varchar professional_identifier
        timestamp last_login_at
        json profile_meta
        timestamp created_at
    }
    
    patients {
        bigint id PK
        varchar medical_record_number UK
        varchar nik UK
        varchar bpjs_card_no UK
        varchar name
        date date_of_birth
        enum gender
        varchar blood_type
        varchar phone
        varchar email
        text address
        varchar village
        varchar district
        varchar city
        varchar province
        text allergies
        varchar emergency_contact_name
        varchar emergency_contact_phone
        json meta
        bigint created_by FK
        timestamp created_at
    }
    
    visits {
        bigint id PK
        bigint patient_id FK
        bigint provider_id FK
        varchar visit_number UK
        datetime visit_datetime
        varchar clinic_name
        enum coverage_type
        varchar sep_no
        varchar bpjs_reference_no
        varchar queue_number
        enum status
        text chief_complaint
        text triage_notes
        json meta
        timestamp created_at
    }
    
    emr_notes {
        bigint id PK
        bigint visit_id FK
        bigint author_id FK
        text subjective
        text objective
        text assessment
        text plan
        varchar icd10_code
        varchar icd10_description
        text notes
        json meta
        timestamp created_at
    }
    
    lab_orders {
        bigint id PK
        bigint visit_id FK
        bigint ordered_by FK
        bigint verified_by FK
        varchar order_number UK
        enum status
        enum priority
        timestamp requested_at
        timestamp processed_at
        timestamp completed_at
        text clinical_notes
        varchar bpjs_order_reference
        varchar fhir_service_request_id
        json meta
        timestamp created_at
    }
    
    lab_order_items {
        bigint id PK
        bigint lab_order_id FK
        varchar test_name
        varchar loinc_code
        varchar specimen_type
        text result
        varchar unit
        varchar reference_range
        enum abnormal_flag
        enum result_status
        timestamp observed_at
        timestamp resulted_at
        json meta
        timestamp created_at
    }
    
    lab_order_results {
        bigint id PK
        bigint lab_order_id FK
        varchar nama_tes
        text hasil
        varchar nilai_rujukan
        timestamp created_at
    }
    
    prescriptions {
        bigint id PK
        bigint visit_id FK
        bigint user_id_doctor FK
        enum status
        text catatan
        timestamp created_at
    }
    
    prescription_items {
        bigint id PK
        bigint prescription_id FK
        bigint master_medicine_id FK
        int jumlah
        varchar dosis
        timestamp created_at
    }
    
    master_medicines {
        bigint id PK
        varchar nama_obat
        varchar satuan
        int stok
        timestamp created_at
    }
    
    medicines {
        bigint id PK
        varchar kode UK
        varchar nama
        varchar satuan
        int stok
        int stok_minimal
        text keterangan
        timestamp created_at
    }
    
    queue_tickets {
        bigint id PK
        bigint patient_id FK
        bigint visit_id FK
        date tanggal_antrian
        varchar nomor_antrian
        varchar department
        enum status
        json meta
        timestamp created_at
    }
    
    referrals {
        bigint id PK
        bigint patient_id FK
        bigint visit_id FK
        bigint created_by FK
        varchar referral_number UK
        varchar referred_to
        varchar referred_department
        varchar contact_person
        varchar contact_phone
        enum status
        timestamp scheduled_at
        timestamp sent_at
        timestamp responded_at
        text reason
        text notes
        json meta
        timestamp created_at
    }
    
    bpjs_claims {
        bigint id PK
        bigint patient_id FK
        bigint visit_id FK
        bigint performed_by FK
        varchar interaction_type
        varchar request_method
        varchar endpoint
        varchar external_reference
        int status_code
        varchar status_message
        int response_time_ms
        json headers
        longtext raw_request
        longtext raw_response
        timestamp performed_at
        varchar signature
        json meta
        timestamp created_at
    }
    
    sync_queue {
        bigint id PK
        varchar entity_type
        bigint entity_id
        enum target
        enum status
        int attempts
        varchar correlation_id
        json payload
        text last_error
        json meta
        timestamp available_at
        timestamp last_synced_at
        timestamp locked_at
        timestamp failed_at
        timestamp created_at
    }
    
    audit_logs {
        bigint id PK
        bigint user_id FK
        varchar action
        varchar entity_type
        bigint entity_id
        json changes
        json meta
        varchar ip_address
        varchar user_agent
        timestamp performed_at
        varchar status
        text error_message
        json old_values
        json new_values
        text description
        timestamp created_at
    }
    
    roles {
        bigint id PK
        varchar name
        varchar guard_name
        timestamp created_at
    }
    
    permissions {
        bigint id PK
        varchar name
        varchar guard_name
        timestamp created_at
    }
    
    role_has_permissions {
        bigint permission_id PK,FK
        bigint role_id PK,FK
    }
    
    model_has_roles {
        bigint role_id PK,FK
        varchar model_type PK
        bigint model_id PK
    }
    
    model_has_permissions {
        bigint permission_id PK,FK
        varchar model_type PK
        bigint model_id PK
    }
```

## Penjelasan Relasi

### 1. Core Entities

#### Users (Pengguna Sistem)
- **Relasi ke Patients**: One-to-Many (1:N)
  - Satu user dapat membuat/mengupdate banyak pasien
  - FK: `patients.created_by`, `patients.updated_by`

- **Relasi ke Visits**: One-to-Many (1:N)
  - Satu provider (dokter) dapat menangani banyak kunjungan
  - FK: `visits.provider_id`

- **Relasi ke EMR Notes**: One-to-Many (1:N)
  - Satu dokter dapat menulis banyak catatan EMR
  - FK: `emr_notes.author_id`

- **Relasi ke Lab Orders**: One-to-Many (1:N)
  - Satu dokter dapat membuat banyak order lab
  - Satu laboran dapat memverifikasi banyak order lab
  - FK: `lab_orders.ordered_by`, `lab_orders.verified_by`

- **Relasi ke Prescriptions**: One-to-Many (1:N)
  - Satu dokter dapat membuat banyak resep
  - FK: `prescriptions.user_id_doctor`

### 2. Patient Journey Flow

#### Patients → Visits
- **Relasi**: One-to-Many (1:N)
- Satu pasien dapat memiliki banyak kunjungan
- FK: `visits.patient_id`
- ON DELETE: CASCADE (jika pasien dihapus, semua kunjungan ikut terhapus)

#### Visits → Clinical Data
- **EMR Notes** (1:N): Satu kunjungan dapat memiliki banyak catatan EMR
- **Lab Orders** (1:N): Satu kunjungan dapat memiliki banyak order lab
- **Prescriptions** (1:N): Satu kunjungan dapat memiliki banyak resep
- **Queue Tickets** (1:1): Satu kunjungan terhubung dengan satu nomor antrian
- **Referrals** (1:N): Satu kunjungan dapat menghasilkan banyak rujukan

### 3. Lab System

#### Lab Orders → Lab Order Items
- **Relasi**: One-to-Many (1:N)
- Satu order lab dapat berisi banyak tes/item
- FK: `lab_order_items.lab_order_id`
- ON DELETE: CASCADE

#### Lab Orders → Lab Order Results
- **Relasi**: One-to-Many (1:N)
- Satu order lab dapat memiliki banyak hasil
- FK: `lab_order_results.lab_order_id`
- ON DELETE: CASCADE

### 4. Pharmacy System

#### Prescriptions → Prescription Items
- **Relasi**: One-to-Many (1:N)
- Satu resep dapat berisi banyak item obat
- FK: `prescription_items.prescription_id`
- ON DELETE: CASCADE

#### Prescription Items → Master Medicines
- **Relasi**: Many-to-One (N:1)
- Banyak prescription item menggunakan satu master obat
- FK: `prescription_items.master_medicine_id`
- ON DELETE: CASCADE

### 5. Integration System

#### Sync Queue
- **Relasi ke berbagai entitas**:
  - Entity type: `Visit`, `Patient`, `Encounter`, dll
  - Entity ID: Foreign key polymorphic ke berbagai tabel
  - Target: `satusehat`, `bpjs`
  - Status: `pending`, `processing`, `completed`, `failed`

#### BPJS Claims
- **Relasi ke Patients**: Many-to-One (N:1)
- **Relasi ke Visits**: Many-to-One (N:1)
- **Relasi ke Users**: Many-to-One (N:1)
- Mencatat semua interaksi dengan BPJS VClaim API

### 6. Spatie Permission System (RBAC)

#### Role-Based Access Control
- **Roles** → **Permissions**: Many-to-Many (N:M)
  - Pivot table: `role_has_permissions`
  
- **Users** → **Roles**: Many-to-Many (N:M)
  - Pivot table: `model_has_roles` (polymorphic)
  
- **Users** → **Permissions**: Many-to-Many (N:M)
  - Pivot table: `model_has_permissions` (polymorphic, untuk direct permission)

## Status Values

### Visit Status
- `WAITING` - Menunggu di antrian
- `IN_PROGRESS` - Sedang diperiksa
- `COMPLETED` - Selesai diperiksa
- `CANCELLED` - Dibatalkan

### Lab Order Status
- `PENDING` - Menunggu diproses
- `IN_PROGRESS` - Sedang diproses
- `COMPLETED` - Selesai dengan hasil
- `CANCELLED` - Dibatalkan

### Prescription Status
- `PENDING` - Menunggu diproses apoteker
- `PROCESSED` - Sudah diproses
- `DISPENSED` - Sudah diserahkan ke pasien
- `CANCELLED` - Dibatalkan

### Queue Ticket Status
- `WAITING` - Menunggu dipanggil
- `CALLED` - Sedang dipanggil
- `COMPLETED` - Selesai dilayani
- `SKIPPED` - Dilewati
- `CANCELLED` - Dibatalkan

### Referral Status
- `PENDING` - Menunggu dikirim
- `SENT` - Sudah dikirim
- `RECEIVED` - Sudah diterima faskes tujuan
- `REJECTED` - Ditolak
- `COMPLETED` - Selesai ditangani

### Sync Queue Status
- `PENDING` - Menunggu disync
- `PROCESSING` - Sedang disync
- `COMPLETED` - Berhasil disync
- `FAILED` - Gagal disync

### Sync Queue Target
- `satusehat` - Sync ke SATUSEHAT FHIR
- `bpjs` - Sync ke BPJS VClaim

## Coverage Type (Jenis Pembayaran)
- `BPJS` - Pasien BPJS/JKN
- `UMUM` - Pasien umum/mandiri

## Gender
- `MALE` - Laki-laki
- `FEMALE` - Perempuan

## Lab Priority
- `ROUTINE` - Pemeriksaan rutin
- `URGENT` - Mendesak
- `STAT` - Segera (emergency)

## Lab Abnormal Flag
- `NORMAL` - Hasil normal
- `HIGH` - Di atas nilai normal
- `LOW` - Di bawah nilai normal
- `CRITICAL` - Kritis (memerlukan tindakan segera)

## Key Indexes

### Performance Indexes
- `patients.medical_record_number` (UNIQUE)
- `patients.nik` (UNIQUE)
- `patients.bpjs_card_no` (UNIQUE)
- `visits.visit_number` (UNIQUE)
- `visits.visit_datetime` + `coverage_type` (COMPOSITE)
- `lab_orders.order_number` (UNIQUE)
- `lab_orders.visit_id` + `status` (COMPOSITE)
- `queue_tickets.tanggal_antrian` + `nomor_antrian` (UNIQUE COMPOSITE)
- `sync_queue.entity_type` + `entity_id` (COMPOSITE)
- `sync_queue.target` + `status` (COMPOSITE)
- `bpjs_claims.interaction_type` + `endpoint` (COMPOSITE)

### Foreign Key Indexes
- Semua foreign key memiliki index otomatis untuk query join yang cepat

## Soft Deletes
Tabel-tabel berikut menggunakan soft delete (`deleted_at` column):
- `patients`
- `visits`
- `lab_orders`

## JSON Meta Fields
Banyak tabel memiliki kolom `meta` (JSON) untuk menyimpan data tambahan yang fleksibel:
- `users.profile_meta`
- `patients.meta`
- `visits.meta`
- `emr_notes.meta`
- `lab_orders.meta`
- `lab_order_items.meta`
- `queue_tickets.meta`
- `referrals.meta`
- `bpjs_claims.meta`
- `sync_queue.meta`
- `audit_logs.meta`

## Audit Trail
Sistem menggunakan `audit_logs` untuk mencatat semua perubahan data penting:
- User yang melakukan aksi
- Jenis aksi (create, update, delete, dll)
- Entity yang diubah
- Nilai lama dan baru
- IP address dan user agent
- Timestamp

## Integration References
- **BPJS**: `visits.sep_no`, `visits.bpjs_reference_no`, `lab_orders.bpjs_order_reference`
- **FHIR/SATUSEHAT**: `lab_orders.fhir_service_request_id`
- **Correlation ID**: `sync_queue.correlation_id` untuk tracing

## Notes
- Semua tabel memiliki `created_at` dan `updated_at` (timestamps)
- Foreign key dengan ON DELETE SET NULL: untuk data reference yang perlu dipertahankan
- Foreign key dengan ON DELETE CASCADE: untuk data dependent yang harus ikut terhapus
- Unique constraints untuk mencegah duplikasi data kritis
- Composite indexes untuk query yang sering digunakan bersama
