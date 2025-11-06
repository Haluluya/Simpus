# SIMPUS Documentation Index

Selamat datang di dokumentasi lengkap SIMPUS (Sistem Informasi Manajemen Puskesmas).

## 📖 Dokumentasi Utama

### 🏗️ Arsitektur & Desain

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **[ARCHITECTURE.md](ARCHITECTURE.md)** | System architecture, DFD (Level 0, 1, 2), Component diagram, Sequence diagrams, Deployment architecture | System Architect, Tech Lead, Developer |
| **[DATABASE-SCHEMA.md](DATABASE-SCHEMA.md)** | ERD lengkap, Struktur tabel, Relasi database, Indexes, Performance tuning | Backend Developer, DBA |

### 🔗 Integrasi Eksternal

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **[BPJS-INTEGRATION.md](BPJS-INTEGRATION.md)** | Integrasi BPJS VClaim REST API, Mock API setup, Validasi peserta, SEP management | Backend Developer, Integration Specialist |
| **[SATUSEHAT-INTEGRATION.md](SATUSEHAT-INTEGRATION.md)** | Integrasi SATUSEHAT FHIR R4, Mock API setup, Sync Patient/Encounter/Observation | Backend Developer, Healthcare IT |

### 👨‍💻 Development

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **[DEVELOPMENT-GUIDE.md](DEVELOPMENT-GUIDE.md)** | Panduan development, Testing, Debugging, Code style, Git workflow | All Developers |
| **[API-DOCUMENTATION.md](API-DOCUMENTATION.md)** | REST API endpoints, Request/Response examples, Authentication | Backend Developer, Frontend Developer, API Consumer |

### 🚀 Deployment & Operations

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **[DEPLOYMENT.md](DEPLOYMENT.md)** | Deployment production, Server setup, Optimization, Backup strategy | DevOps, System Admin |

### 📚 Guides & Tutorials

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **[BPJS-MOCK-API-GUIDE.md](BPJS-MOCK-API-GUIDE.md)** | Panduan setup dan testing BPJS Mock API | Developer (Testing) |
| **[KONSEP-BPJS-MOCK-API.md](KONSEP-BPJS-MOCK-API.md)** | Konsep dan design BPJS Mock API | Technical Team |

---

## 🚀 Quick Start

### Untuk Developer Baru

1. **Mulai di sini**: [../README.md](../README.md) - Overview project dan quick start
2. **Pahami arsitektur**: [ARCHITECTURE.md](ARCHITECTURE.md) - Lihat DFD dan component diagram
3. **Pelajari database**: [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md) - Pahami ERD dan struktur data
4. **Setup development**: [DEVELOPMENT-GUIDE.md](DEVELOPMENT-GUIDE.md) - Panduan coding dan testing

### Untuk System Architect / Tech Lead

1. **Architecture Overview**: [ARCHITECTURE.md](ARCHITECTURE.md)
   - DFD Level 0, 1, 2
   - Component & Deployment diagram
   - Security architecture
   - Scalability considerations

2. **Database Design**: [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md)
   - ERD diagram
   - Table relationships
   - Index strategy
   - Performance optimization

3. **Integration Architecture**:
   - [BPJS-INTEGRATION.md](BPJS-INTEGRATION.md)
   - [SATUSEHAT-INTEGRATION.md](SATUSEHAT-INTEGRATION.md)

### Untuk DevOps / System Admin

1. **Setup Production**: [DEPLOYMENT.md](DEPLOYMENT.md)
2. **Performance Tuning**: [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md#indexes--performance)
3. **Architecture**: [ARCHITECTURE.md](ARCHITECTURE.md#deployment-architecture)

### Untuk Healthcare IT Specialist

1. **BPJS Integration**: [BPJS-INTEGRATION.md](BPJS-INTEGRATION.md)
   - VClaim REST API
   - SEP management
   - Referral system

2. **SATUSEHAT Integration**: [SATUSEHAT-INTEGRATION.md](SATUSEHAT-INTEGRATION.md)
   - FHIR R4 implementation
   - Patient/Encounter/Observation sync
   - Queue processing

3. **Data Flow**: [ARCHITECTURE.md](ARCHITECTURE.md#data-flow-diagram-dfd)

---

## 📊 Diagram & Visual

### Entity Relationship Diagram (ERD)
Lihat diagram lengkap di [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md#entity-relationships)

### Data Flow Diagram (DFD)
- **DFD Level 0** (Context): [ARCHITECTURE.md](ARCHITECTURE.md#dfd-level-0---context-diagram)
- **DFD Level 1** (Main Processes): [ARCHITECTURE.md](ARCHITECTURE.md#dfd-level-1---main-processes)
- **DFD Level 2** (Detailed):
  - [Pendaftaran Pasien](ARCHITECTURE.md#21-dfd-level-2-process-10---pendaftaran-pasien)
  - [Pemeriksaan & EMR](ARCHITECTURE.md#22-dfd-level-2-process-30---pemeriksaan--emr)
  - [Integrasi BPJS](ARCHITECTURE.md#23-dfd-level-2-process-60---integrasi-bpjs)
  - [Integrasi SATUSEHAT](ARCHITECTURE.md#24-dfd-level-2-process-70---integrasi-satusehat)

### Sequence Diagrams
- [Pendaftaran Pasien BPJS](ARCHITECTURE.md#sequence-diagram-pendaftaran-pasien-bpjs)
- [Pemeriksaan & EMR](ARCHITECTURE.md#sequence-diagram-pemeriksaan--emr-soap)
- [SATUSEHAT Sync](ARCHITECTURE.md#sequence-diagram-satusehat-sync-background-job)

---

## 📦 Technology Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 12, PHP 8.2 |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ |
| **Frontend** | Alpine.js 3, Tailwind CSS 3, Vite 7 |
| **Auth** | Laravel Breeze |
| **Permissions** | Spatie Laravel Permission |
| **Queue** | Database driver dengan Redis support |
| **Cache** | Database/Redis |
| **Export** | Maatwebsite Excel, DomPDF |
| **Integration** | BPJS VClaim REST API, SATUSEHAT FHIR R4 |

---

## 🎯 Fitur Utama

### 🏥 Manajemen Klinik
- ✅ Pendaftaran Pasien dengan No. RM otomatis
- ✅ Electronic Medical Record (EMR) dengan format SOAP
- ✅ Sistem Antrian real-time per poliklinik
- ✅ Pencarian Cerdas pasien dan obat

### 🔬 Layanan Medis
- ✅ Laboratorium: Order, hasil, print report PDF
- ✅ Farmasi: Resep, dispensing, master obat
- ✅ Rujukan: Internal & eksternal

### 🔗 Integrasi Eksternal
- ✅ **BPJS VClaim**: Validasi peserta, SEP, rujukan, referensi
- ✅ **SATUSEHAT FHIR R4**: Sync Patient, Encounter, Observation
- ✅ Mock API mode untuk testing tanpa kredensial

### 📊 Pelaporan & Monitoring
- ✅ Dashboard real-time
- ✅ Export laporan Excel
- ✅ Audit trail lengkap
- ✅ Queue monitoring

### 👥 Keamanan
- ✅ Role-Based Access Control (6 role user)
- ✅ Authentication dengan Laravel Breeze
- ✅ Audit logging
- ✅ CSRF & XSS protection

---

## 🆘 Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Cek konfigurasi `.env`
   - Pastikan MySQL service running
   - Lihat: [DEVELOPMENT-GUIDE.md](DEVELOPMENT-GUIDE.md)

2. **BPJS API Timeout**
   - Aktifkan mock mode: `BPJS_USE_MOCK=true`
   - Lihat: [BPJS-INTEGRATION.md](BPJS-INTEGRATION.md#mock-api-mode)

3. **SATUSEHAT Sync Failed**
   - Cek queue worker: `php artisan queue:work`
   - Cek sync_queue table untuk error
   - Lihat: [SATUSEHAT-INTEGRATION.md](SATUSEHAT-INTEGRATION.md)

4. **Performance Issues**
   - Enable cache: `php artisan config:cache`
   - Check indexes: [DATABASE-SCHEMA.md](DATABASE-SCHEMA.md#indexes--performance)
   - Lihat: [ARCHITECTURE.md](ARCHITECTURE.md#performance-optimization)

---

## 🤝 Contributing

Baca panduan kontribusi di [../CONTRIBUTING.md](../CONTRIBUTING.md)

---

## 📝 Changelog

Lihat history perubahan di [../CHANGELOG.md](../CHANGELOG.md)

---

## 📧 Support

- **Repository**: https://github.com/Haluluya/Simpus
- **Issues**: https://github.com/Haluluya/Simpus/issues

---

## 📚 External References

### Laravel
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)

### BPJS
- [BPJS VClaim API Documentation](https://dvlp.bpjs-kesehatan.go.id/)
- [Panduan Teknis VClaim](https://dvlp.bpjs-kesehatan.go.id/vclaim)

### SATUSEHAT
- [SATUSEHAT Platform](https://satusehat.kemkes.go.id)
- [SATUSEHAT FHIR Documentation](https://satusehat.kemkes.go.id/platform/docs/id/interoperabilitas/fhir)
- [HL7 FHIR R4 Specification](https://hl7.org/fhir/R4/)

### Frontend
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Alpine.js](https://alpinejs.dev/start-here)
- [Vite](https://vitejs.dev/)

---

**Last Updated**: 2025-01-06
**Maintained By**: SIMPUS Development Team
