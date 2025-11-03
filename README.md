<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

# SIMPUS - Sistem Informasi Manajemen Puskesmas

Aplikasi web untuk manajemen klinik/puskesmas berbasis Laravel 12 dengan integrasi BPJS VClaim dan SATUSEHAT FHIR R4.

## 🎯 Fitur Utama

- 📝 **Pendaftaran Pasien** - Manajemen data pasien dengan No. RM otomatis
- 🏥 **Kunjungan & EMR** - Electronic Medical Record untuk setiap kunjungan
- 🔬 **Laboratorium** - Permintaan dan hasil tes lab dengan print report
- 💊 **Farmasi** - Manajemen resep dan dispensing obat
- 📋 **Antrean** - Sistem antrian pasien per poli
- 🔗 **Integrasi BPJS** - Validasi peserta, SEP, rujukan (VClaim REST API)
- 🔗 **Integrasi SATUSEHAT** - Sinkronisasi data ke SATUSEHAT (FHIR R4)
- 📊 **Dashboard & Laporan** - Statistik kunjungan, export Excel
- 👥 **Role-based Access** - Admin, Dokter, Petugas Lab, Apoteker, Petugas Pendaftaran

## 📚 Dokumentasi Teknis

Untuk memahami arsitektur sistem dan struktur database:

- 🏗️ **[System Architecture](docs/ARCHITECTURE.md)** - Overview arsitektur sistem, technology stack, dan integration
- 📊 **[ERD (Entity Relationship Diagram)](docs/ERD.md)** - Skema database lengkap dengan relasi antar tabel
- 🔄 **[DFD (Data Flow Diagram)](docs/DFD.md)** - Alur data dan proses sistem
- 📖 **[Optimasi Performa](docs/OPTIMASI-PERFORMA.md)** - Panduan caching dan optimasi
- 🤝 **[Cara Sharing Project](SHARING.md)** - Panduan berbagi project dengan tim

## �📋 Prasyarat

- **PHP** 8.2 atau lebih tinggi
- **Composer** 2.x
- **Node.js** 18+ & npm
- **MySQL** 8.0 / MariaDB 10.6+
- **Web Server** Apache/Nginx atau Laragon (recommended untuk Windows)
- **Git** (untuk clone repository)

## 🚀 Instalasi & Setup

### Opsi 1: Setup dengan Laragon (Windows - Recommended)

1. **Install Laragon**
   - Download dari [laragon.org](https://laragon.org/)
   - Pastikan PHP 8.2+, Composer, Node.js, MySQL sudah aktif

2. **Clone Repository**
   ```powershell
   cd D:\laragon\www
   git clone https://github.com/Haluluya/Simpus.git
   cd Simpus
   ```

3. **Setup Environment**
   ```powershell
   # Copy file environment
   Copy-Item .env.example .env
   
   # Edit .env - sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD
   # Notepad .env
   ```

4. **Install Dependencies**
   ```powershell
   composer install
   npm install
   ```

5. **Generate App Key**
   ```powershell
   php artisan key:generate
   ```

6. **Setup Database**
   ```powershell
   # Buat database di MySQL (via Laragon → Menu → MySQL → Create Database)
   # Nama database: simpus
   
   # Atau via command line:
   # mysql -u root -p -e "CREATE DATABASE simpus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   
   # Jalankan migration dan seeder
   php artisan migrate --seed
   ```

7. **Create Storage Link**
   ```powershell
   php artisan storage:link
   ```

8. **Build Frontend Assets**
   ```powershell
   # Development
   npm run dev
   
   # Production
   npm run build
   ```

9. **Akses Aplikasi**
   - Tambahkan virtual host di Laragon: klik kanan Laragon → Apache → Add Virtual Host
   - Nama: `simpus.test`
   - Folder: `D:\laragon\www\Simpus\public`
   - Buka browser: `http://simpus.test`

### Opsi 2: Setup Manual (Linux/Mac/Windows)

```bash
# Clone repository
git clone https://github.com/Haluluya/Simpus.git
cd Simpus

# Setup environment
cp .env.example .env
# Edit .env sesuai kebutuhan

# Install dependencies
composer install
npm install

# Generate app key
php artisan key:generate

# Setup database
php artisan migrate --seed

# Storage link
php artisan storage:link

# Build assets
npm run build

# Jalankan server development
php artisan serve
# Akses: http://localhost:8000
```

### Opsi 3: Auto Setup Script (PowerShell)

Jalankan script otomatis:
```powershell
.\setup.ps1
```

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

## 🔧 Konfigurasi Integrasi

### BPJS VClaim

Edit `.env`:
```bash
BPJS_BASE_URL=https://new-api.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_CONS_ID=your_cons_id
BPJS_SECRET=your_secret_key
BPJS_USER_KEY=your_user_key
BPJS_USE_MOCK=false  # true untuk menggunakan mock data
```

**Mock Mode**: Jika kredensial kosong atau `BPJS_USE_MOCK=true`, sistem akan menggunakan data mock dari `storage/app/mocks/bpjs/`.

### SATUSEHAT FHIR

Edit `.env`:
```bash
SATUSEHAT_BASE_URL=https://api-satusehat.kemkes.go.id/fhir-r4
SATUSEHAT_AUTH_URL=https://api-satusehat.kemkes.go.id/oauth2/v1
SATUSEHAT_CLIENT_ID=your_client_id
SATUSEHAT_CLIENT_SECRET=your_client_secret
SATUSEHAT_ORGANIZATION_ID=your_org_id
SATUSEHAT_FACILITY_ID=your_facility_id
SATUSEHAT_USE_MOCK=false  # true untuk mock
```

### Queue Worker (untuk sinkronisasi SATUSEHAT)

Jalankan queue worker di terminal terpisah:
```powershell
php artisan queue:work --queue=default,satusehat
```

Atau setup dengan Supervisor (production).

## 📦 Perintah Artisan Penting

```powershell
# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear

# Optimize (production)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run tests
php artisan test

# Code formatting (Laravel Pint)
vendor/bin/pint --dirty
```

## 🧪 Testing dengan Postman

Import collection dari `postman/SIMPUS.postman_collection.json`:

- **Auth** → Login & Logout
- **Patients** → CRUD pasien
- **Visits** → Kunjungan & EMR
- **Lab Orders** → Permintaan lab
- **BPJS** → Validasi peserta, SEP
- **SATUSEHAT** → Sync Patient/Encounter
- **Reports** → Export Excel

## 📖 Dokumentasi Tambahan

- `docs/FITUR-LENGKAP-INTEGRASI.md` - Dokumentasi lengkap fitur & integrasi
- `docs/TESTING-BPJS-MOCK.md` - Panduan testing Mock API BPJS
- `docs/STATUS-INTEGRASI-QUICK-REFERENCE.md` - Quick reference status integrasi
- `docs/OPTIMASI-PERFORMA.md` - Panduan optimasi performa
- `.github/copilot-instructions.md` - Konvensi project untuk AI assist

## 🐛 Troubleshooting

### Error: "No application encryption key"
```powershell
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1049] Unknown database"
Buat database terlebih dahulu:
```powershell
mysql -u root -p -e "CREATE DATABASE simpus;"
```

### Error: "The stream or file could not be opened"
```powershell
# Fix permission (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Windows - jalankan sebagai Administrator atau pastikan folder writable
```

### Frontend tidak muncul / 404 assets
```powershell
npm run build
php artisan view:clear
```

### Queue tidak berjalan
```powershell
# Pastikan .env menggunakan queue driver
QUEUE_CONNECTION=database

# Buat table jobs jika belum
php artisan queue:table
php artisan migrate

# Jalankan worker
php artisan queue:work --queue=default,satusehat
```

## 🚢 Deployment Production

1. **Install dependencies (production)**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci
   npm run build
   ```

2. **Setup environment**
   ```bash
   cp .env.example .env
   # Edit .env untuk production (APP_ENV=production, APP_DEBUG=false)
   php artisan key:generate
   ```

3. **Database migration**
   ```bash
   php artisan migrate --force
   ```

4. **Cache optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Setup Queue Worker dengan Supervisor**
   Lihat contoh config di `deployment/supervisor/simpus-worker.conf`

6. **Setup Web Server**
   - Apache: Lihat `deployment/apache/simpus.conf`
   - Nginx: Lihat `deployment/nginx/simpus.conf`

## 🤝 Kontribusi & Kolaborasi

Untuk berkolaborasi:
1. Fork repository ini
2. Buat branch fitur (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📝 Lisensi

MIT License - lihat file `LICENSE` untuk detail.

## 📧 Kontak & Support

- Repository: [https://github.com/Haluluya/Simpus](https://github.com/Haluluya/Simpus)
- Issues: [https://github.com/Haluluya/Simpus/issues](https://github.com/Haluluya/Simpus/issues)

---

**Catatan untuk Developer Baru**: Pastikan sudah membaca `docs/FITUR-LENGKAP-INTEGRASI.md` dan `.github/copilot-instructions.md` untuk memahami struktur project dan konvensi coding yang digunakan
