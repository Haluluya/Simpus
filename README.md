
# SIMPUS - Sistem Informasi Manajemen Puskesmas

Aplikasi web untuk manajemen klinik/puskesmas berbasis Laravel 12 dengan integrasi BPJS VClaim dan SATUSEHAT FHIR R4.

## 🎯 Fitur Utama

### Manajemen Klinik
- 📝 **Pendaftaran Pasien** - Manajemen data pasien dengan No. RM otomatis
- 🏥 **Kunjungan & EMR** - Electronic Medical Record lengkap untuk setiap kunjungan
- 📋 **Sistem Antrian** - Queue management dengan monitor real-time per poli
- 🔍 **Pencarian Cerdas** - Search suggestion untuk data pasien dan obat

### Layanan Medis
- 🔬 **Laboratorium** - Permintaan dan hasil tes lab dengan print report PDF
- 💊 **Farmasi** - Manajemen resep, dispensing obat, dan master obat
- 🩺 **Rujukan** - Manajemen rujukan internal dan eksternal
- 📋 **EMR Notes** - Catatan medis terstruktur per kunjungan

### Integrasi Eksternal
- 🔗 **Integrasi BPJS VClaim** - Validasi peserta, SEP, rujukan dengan mock mode
- 🔗 **Integrasi SATUSEHAT** - Sinkronisasi FHIR R4 (Patient, Encounter, Observation) via queue
- 📊 **BPJS Claims** - Tracking klaim BPJS per kunjungan

### Pelaporan & Monitoring
- 📊 **Dashboard** - Statistik kunjungan, pasien, dan layanan real-time
- 📈 **Laporan** - Export laporan ke Excel (kunjungan, lab, farmasi)
- 📋 **Audit Log** - Tracking aktivitas pengguna sistem
- 🖥️ **Queue Monitor** - Monitor antrian per poli dengan status real-time

### Manajemen User & Keamanan
- 👥 **Role-based Access Control** - Admin, Dokter, Petugas Rekam Medis, Pendaftaran, Lab, Apotek
- 🔐 **Spatie Permission** - Granular permission management
- 🔒 **Authentication** - Laravel Breeze untuk auth

## 🛠️ Technology Stack

- **Backend**: Laravel 12, PHP 8.2
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Frontend**: Alpine.js 3, Tailwind CSS 3, Vite 7
- **Auth**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Queue**: Database driver dengan Redis support
- **Cache**: Database/Redis
- **Export**: Maatwebsite Excel, DomPDF

## 📚 Dokumentasi

- 🏗️ **[System Architecture](docs/ARCHITECTURE.md)** - Overview arsitektur sistem dan technology stack
- 📊 **[ERD (Entity Relationship Diagram)](docs/ERD.md)** - Skema database lengkap dengan relasi
- 🔄 **[DFD (Data Flow Diagram)](docs/DFD.md)** - Alur data dan proses sistem
- 📖 **[Optimasi Performa](docs/OPTIMASI-PERFORMA.md)** - Panduan caching dan optimasi
- 🤝 **[Cara Sharing Project](SHARING.md)** - Panduan berbagi project dengan tim

## 📋 Requirements

- **PHP** >= 8.2.24
- **Composer** >= 2.x
- **Node.js** >= 18.x & npm
- **MySQL** >= 8.0 atau MariaDB >= 10.6
- **Redis** (optional, untuk queue & cache)
- **Web Server**: Apache/Nginx atau Laragon (Windows)
- **Git**

## 🚀 Quick Start

### Opsi 1: Auto Setup (Recommended)

Jalankan satu perintah untuk setup otomatis:

```bash
composer run setup
```

Script ini akan:
- Copy `.env.example` ke `.env`
- Generate application key
- Install dependencies (composer & npm)
- Run migrations & seeders
- Build frontend assets

**Setelah setup**, edit `.env` untuk konfigurasi database Anda, lalu jalankan:

```bash
php artisan migrate:fresh --seed
```

### Opsi 2: Manual Setup

#### 1. Clone & Setup Environment

```bash
# Clone repository
git clone https://github.com/Haluluya/Simpus.git
cd Simpus

# Copy environment file
cp .env.example .env
```

#### 2. Konfigurasi Database

Edit `.env` dan sesuaikan:

```env
DB_DATABASE=simpus
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```bash
# MySQL CLI
mysql -u root -p -e "CREATE DATABASE simpus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### 3. Install & Build

```bash
# Install dependencies
composer install
npm install

# Generate app key
php artisan key:generate

# Run migrations & seeders
php artisan migrate --seed

# Storage link
php artisan storage:link

# Build frontend assets
npm run build
```

#### 4. Jalankan Aplikasi

**Development Mode** (dengan queue worker, logs, dan hot reload):

```bash
composer run dev
```

**Manual Mode**:

```bash
# Terminal 1: Web server
php artisan serve

# Terminal 2: Queue worker (optional untuk SATUSEHAT sync)
php artisan queue:work --queue=default,satusehat

# Terminal 3: Frontend dev server
npm run dev
```

Akses aplikasi di `http://localhost:8000`

### Setup dengan Laragon (Windows)

1. Install [Laragon](https://laragon.org/) dengan PHP 8.2+, Composer, Node.js, MySQL
2. Clone project ke `D:\laragon\www\Simpus`
3. Buat virtual host: Klik kanan Laragon → Apache → Add Virtual Host
   - Nama: `simpus.test`
   - Folder: `D:\laragon\www\Simpus\public`
4. Ikuti langkah manual setup di atas
5. Akses di `http://simpus.test`

## 👤 Akun Default (Seeder)

Setelah `php artisan migrate --seed`, gunakan akun berikut untuk login:

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@simpus.test | password123 |
| Dokter | dokter@simpus.test | password123 |
| Petugas Rekam Medis | rekammedis@simpus.test | password123 |
| Petugas Pendaftaran | pendaftaran@simpus.test | password123 |
| Petugas Apotek | apotik@simpus.test | password123 |
| Petugas Lab | lab@simpus.test | password123 |

## 🔧 Konfigurasi

### Environment Variables

Edit `.env` untuk konfigurasi dasar:

```env
APP_NAME=SIMPUS
APP_URL=http://localhost
APP_LOCALE=id

DB_DATABASE=simpus
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_STORE=database
```

### Integrasi BPJS VClaim

Edit `.env`:

```env
BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_CONS_ID=your_cons_id
BPJS_SECRET=your_secret_key
BPJS_USER_KEY=your_user_key
BPJS_USE_MOCK=true  # false untuk production
BPJS_TIMEOUT=10
```

**Mock Mode**: Set `BPJS_USE_MOCK=true` untuk testing tanpa kredensial. Data mock tersedia di `storage/app/mocks/bpjs/`.

### Integrasi SATUSEHAT FHIR

Edit `.env`:

```env
SATUSEHAT_BASE_URL=https://api-satusehat.kemkes.go.id/fhir-r4
SATUSEHAT_AUTH_URL=https://api-satusehat.kemkes.go.id/oauth2/v1
SATUSEHAT_CLIENT_ID=your_client_id
SATUSEHAT_CLIENT_SECRET=your_client_secret
SATUSEHAT_ORGANIZATION_ID=your_org_id
SATUSEHAT_FACILITY_ID=your_facility_id
SATUSEHAT_USE_MOCK=true  # false untuk production
SATUSEHAT_TIMEOUT=10
```

**Mock Mode**: Set `SATUSEHAT_USE_MOCK=true` untuk testing. Data mock tersedia di `storage/app/mocks/satusehat/`.

### Queue Worker

Queue diperlukan untuk sinkronisasi SATUSEHAT. Jalankan:

```bash
# Development
php artisan queue:work --queue=default,satusehat

# Atau gunakan composer run dev (sudah include queue worker)
composer run dev
```

Production setup dengan Supervisor (lihat `deployment/supervisor/simpus-worker.conf`).

## 📦 Useful Commands

### Development

```bash
# Development server dengan queue & hot reload
composer run dev

# Run specific artisan commands
php artisan serve              # Start dev server
php artisan queue:work         # Start queue worker
npm run dev                    # Vite dev server dengan HMR

# Run tests
composer run test
php artisan test --filter=NamaTest
```

### Cache Management

```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Production optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Code Quality

```bash
# Laravel Pint (code formatter)
vendor/bin/pint              # Format all files
vendor/bin/pint --dirty      # Format only changed files
vendor/bin/pint --test       # Test without formatting
```

### Database

```bash
# Fresh migration dengan seeder
php artisan migrate:fresh --seed

# Rollback
php artisan migrate:rollback

# Reset database
php artisan migrate:reset
```

## 🧪 Testing

### PHPUnit Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/PatientTest.php
php artisan test --filter=test_can_create_patient

# With coverage
php artisan test --coverage
```

### API Testing dengan Postman

Import collection dari `postman/SIMPUS.postman_collection.json`:

- **Auth**: Login & Logout
- **Patients**: CRUD pasien
- **Visits**: Kunjungan & EMR
- **Lab**: Permintaan dan hasil lab
- **Pharmacy**: Resep dan dispensing
- **Queue**: Antrian pasien
- **BPJS**: Validasi peserta, SEP, rujukan
- **SATUSEHAT**: Sync Patient/Encounter/Observation
- **Reports**: Export Excel

## 🐛 Troubleshooting

### No application encryption key

```bash
php artisan key:generate
```

### Unknown database

Buat database terlebih dahulu:

```bash
mysql -u root -p -e "CREATE DATABASE simpus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Permission denied (Linux/Mac)

```bash
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

### Frontend tidak muncul / 404 assets

```bash
npm run build
php artisan view:clear
php artisan optimize:clear
```

### Queue tidak berjalan

Pastikan konfigurasi queue di `.env`:

```env
QUEUE_CONNECTION=database
```

Jalankan migration table jobs jika belum:

```bash
php artisan queue:table
php artisan migrate
```

Start queue worker:

```bash
php artisan queue:work --queue=default,satusehat
```

### BPJS/SATUSEHAT connection timeout

Aktifkan mock mode untuk testing:

```env
BPJS_USE_MOCK=true
SATUSEHAT_USE_MOCK=true
```

## 🚢 Production Deployment

### 1. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm ci
npm run build
```

### 2. Environment Setup

```bash
cp .env.example .env
```

Edit `.env` untuk production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Set production database
DB_DATABASE=simpus_prod
DB_USERNAME=prod_user
DB_PASSWORD=secure_password

# Use Redis untuk performa lebih baik
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

# BPJS & SATUSEHAT credentials
BPJS_USE_MOCK=false
SATUSEHAT_USE_MOCK=false
```

Generate application key:

```bash
php artisan key:generate --force
```

### 3. Database Migration

```bash
php artisan migrate --force --seed
php artisan storage:link
```

### 4. Optimization

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 5. Queue Worker

Setup Supervisor untuk menjalankan queue worker secara persistent. Lihat contoh config di `deployment/supervisor/simpus-worker.conf`.

### 6. Web Server

Konfigurasi web server:
- **Apache**: `deployment/apache/simpus.conf`
- **Nginx**: `deployment/nginx/simpus.conf`

Pastikan document root mengarah ke folder `public/`.

## 🤝 Contributing

Kontribusi sangat diterima! Untuk berkontribusi:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add: amazing feature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

Pastikan code sudah diformat dengan Laravel Pint dan lolos semua tests:

```bash
vendor/bin/pint --dirty
php artisan test
```

## 📝 License

Project ini dilisensikan di bawah [MIT License](LICENSE).

## 📧 Support

- **Repository**: [https://github.com/Haluluya/Simpus](https://github.com/Haluluya/Simpus)
- **Issues**: [https://github.com/Haluluya/Simpus/issues](https://github.com/Haluluya/Simpus/issues)
- **Documentation**: [docs/](docs/)

---

**Untuk Developer Baru**:
- 📖 Baca [System Architecture](docs/ARCHITECTURE.md) untuk memahami struktur sistem
- 📊 Lihat [ERD](docs/ERD.md) dan [DFD](docs/DFD.md) untuk memahami database dan flow
