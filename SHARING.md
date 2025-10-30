# 📤 Panduan Berbagi Project SIMPUS

Dokumen ini menjelaskan cara membagikan project SIMPUS ke developer lain atau tim.

## 🎯 Metode Sharing (Pilih Salah Satu)

### ✅ Metode 1: Via GitHub (Recommended)

**Kelebihan:**
- Version control otomatis
- Kolaborasi mudah dengan pull request
- History changes terlacak
- Bisa private atau public

**Langkah:**

1. **Repository sudah siap di GitHub:**
   ```
   https://github.com/Haluluya/Simpus.git
   ```

2. **Tambahkan Collaborator (untuk repo private):**
   - Buka GitHub → Repository Settings → Manage access
   - Klik "Add people"
   - Masukkan username/email GitHub collaborator
   - Pilih permission level (Write/Admin)

3. **Share Repository URL:**
   ```
   git clone https://github.com/Haluluya/Simpus.git
   cd Simpus
   ```

4. **Berikan akses .env:**
   - **JANGAN** commit file `.env` ke GitHub
   - Kirim file `.env` via secure channel (Signal, Telegram Secret, Email encrypted)
   - Atau berikan instruksi untuk mengisi `.env` dari `.env.example`

5. **Developer lain jalankan setup:**
   ```powershell
   # Copy .env dari yang diberikan atau isi manual
   Copy-Item .env.example .env
   # Edit .env sesuai environment masing-masing
   
   # Jalankan setup script
   .\setup.ps1
   ```

**Untuk Public Repository:**
- Pastikan tidak ada credentials dalam code
- Mock mode harus aktif by default (`BPJS_USE_MOCK=true`)
- Gunakan `.env.example` dengan placeholder values
- Tambahkan file `LICENSE` jika open source

---

### 📦 Metode 2: Via ZIP File

**Kelebihan:**
- Tidak perlu akun GitHub
- Bisa share via Google Drive, OneDrive, Email, dll
- Cocok untuk distribusi one-time

**Langkah:**

1. **Buat ZIP yang aman (tanpa secrets & dependencies):**

   ```powershell
   # Pastikan .env tidak ikut ter-zip
   # Compress tanpa vendor, node_modules, storage caches
   
   $excludePaths = @(
       "vendor",
       "node_modules",
       "storage\framework\cache\data",
       "storage\framework\sessions",
       "storage\framework\views",
       "storage\logs",
       ".env",
       ".git"
   )
   
   # Buat ZIP
   Compress-Archive -Path * -DestinationPath SIMPUS-v1.0.zip -CompressionLevel Optimal
   ```

   **Atau gunakan GUI:**
   - Hapus folder: `vendor/`, `node_modules/`, `.git/`, `storage/logs/`
   - Pastikan `.env` tidak ikut
   - Compress seluruh folder menjadi `SIMPUS-v1.0.zip`

2. **Export Database (opsional, untuk data sample):**

   ```powershell
   # Ganti dengan path mysqldump di Laragon Anda
   $mysqldump = "D:\laragon\bin\mysql\mysql-8.0.33\bin\mysqldump.exe"
   $dbName = "simpus"  # sesuai .env
   
   # Export
   & $mysqldump -u root -p $dbName > simpus_dump.sql
   
   # Atau tanpa password (jika root tanpa password)
   & $mysqldump -u root $dbName > simpus_dump.sql
   ```

   Sertakan `simpus_dump.sql` dalam ZIP atau upload terpisah.

3. **Buat README_SETUP.txt dalam ZIP:**

   ```text
   SIMPUS - Setup Instructions
   ============================
   
   1. Extract ZIP ke folder project (misal: D:\laragon\www\Simpus)
   
   2. Copy .env.example menjadi .env
      Edit .env dan sesuaikan:
      - DB_DATABASE, DB_USERNAME, DB_PASSWORD
      - Kredensial BPJS & SATUSEHAT (jika ada)
   
   3. Buat database di MySQL:
      CREATE DATABASE simpus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   4. Import database (jika ada simpus_dump.sql):
      mysql -u root -p simpus < simpus_dump.sql
      
      Atau jalankan migration & seeder:
      php artisan migrate --seed
   
   5. Install dependencies:
      composer install
      npm install
   
   6. Generate app key:
      php artisan key:generate
   
   7. Storage link:
      php artisan storage:link
   
   8. Build assets:
      npm run build
   
   9. Jalankan aplikasi:
      php artisan serve
      Atau gunakan Laragon dengan virtual host
   
   Default login:
   - admin@simpus.test / password
   - dokter@simpus.test / password
   
   Dokumentasi lengkap: lihat README.md
   ```

4. **Upload dan Share:**
   - **Google Drive:** Upload ZIP → Share link → Set permission (View/Download)
   - **OneDrive:** Upload → Share → Copy link
   - **Dropbox:** Upload → Share → Copy link
   - **Email:** Jika ukuran < 25MB, kirim via email attachment
   - **WeTransfer:** Untuk file besar (free up to 2GB)

---

## 🔒 Keamanan & Best Practices

### ❌ JANGAN Lakukan Ini:

- ❌ Commit file `.env` ke Git
- ❌ Hard-code credentials dalam code (controller, service, config)
- ❌ Share `.env` lewat public link atau group chat terbuka
- ❌ Upload ZIP yang berisi `vendor/` dan `node_modules/` (ukuran besar, tidak perlu)
- ❌ Commit file `cookies.txt` atau file temporary

### ✅ HARUS Dilakukan:

- ✅ Gunakan `.env.example` dengan placeholder
- ✅ Tambahkan `.env` ke `.gitignore`
- ✅ Share credentials via secure channel (1-on-1)
- ✅ Gunakan mock mode by default untuk development
- ✅ Review commits sebelum push (pastikan tidak ada secrets)
- ✅ Gunakan `.gitignore` yang proper

### 🔐 Cara Aman Share Credentials:

1. **Environment Variables Terpisah:**
   - Share template `.env.example`
   - Kirim actual values via:
     - Signal (encrypted messaging)
     - Telegram Secret Chat
     - Password-protected file (7zip with password)
     - Secure notes (1Password, Bitwarden shared vault)

2. **Untuk Production:**
   - Gunakan environment variables di server (tidak simpan dalam file)
   - Atau gunakan secrets management (AWS Secrets Manager, HashiCorp Vault)

---

## 📋 Checklist Sebelum Share

### Pre-Share Checklist:

- [ ] `.env` tidak ada dalam Git history (`git log --all -- .env`)
- [ ] `.gitignore` sudah mencakup `.env`, `vendor/`, `node_modules/`
- [ ] `.env.example` sudah up-to-date dengan semua keys yang diperlukan
- [ ] File sensitive lain tidak ter-commit (cookies.txt, credentials.json, dll)
- [ ] README.md sudah jelas dan lengkap
- [ ] Seeders berfungsi dengan baik (atau sertakan SQL dump)
- [ ] Tests berjalan tanpa error (`php artisan test`)
- [ ] Mock mode by default aktif untuk integrasi eksternal
- [ ] Dokumentasi API/Postman collection sudah disertakan

### Post-Share Checklist (untuk Developer Penerima):

- [ ] Clone/extract project
- [ ] Copy `.env.example` → `.env` dan edit
- [ ] Buat database
- [ ] Jalankan `composer install` dan `npm install`
- [ ] Jalankan `php artisan key:generate`
- [ ] Jalankan migration: `php artisan migrate --seed` atau import SQL
- [ ] Jalankan `php artisan storage:link`
- [ ] Build assets: `npm run build` atau `npm run dev`
- [ ] Test akses aplikasi
- [ ] Test queue worker (jika diperlukan)

---

## 🚀 Quick Commands untuk Developer Baru

**PowerShell (Windows/Laragon):**
```powershell
# Clone repository
git clone https://github.com/Haluluya/Simpus.git
cd Simpus

# Auto setup (recommended)
.\setup.ps1

# Manual setup
Copy-Item .env.example .env
# Edit .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

**Bash (Linux/Mac):**
```bash
# Clone repository
git clone https://github.com/Haluluya/Simpus.git
cd Simpus

# Setup
cp .env.example .env
# Edit .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

---

## 🛠️ Troubleshooting untuk Developer Baru

### Issue: "Composer install failed"
```powershell
# Clear composer cache
composer clear-cache
composer install --ignore-platform-reqs
```

### Issue: "npm install failed"
```powershell
# Clear npm cache
npm cache clean --force
npm install
```

### Issue: "Migration failed"
```powershell
# Cek koneksi database
php artisan tinker
>>> DB::connection()->getPdo();

# Buat database manual
mysql -u root -p -e "CREATE DATABASE simpus;"

# Re-run migration
php artisan migrate:fresh --seed
```

### Issue: "Permission denied" (Linux/Mac)
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue: "Class not found"
```powershell
# Regenerate autoload
composer dump-autoload
php artisan clear-compiled
php artisan optimize:clear
```

---

## 📞 Support untuk Developer Baru

Jika developer baru mengalami kesulitan:

1. Cek dokumentasi di `README.md` dan folder `docs/`
2. Lihat Postman collection untuk contoh API usage
3. Cek file `.github/copilot-instructions.md` untuk konvensi project
4. Buka issue di GitHub jika menemukan bug
5. Hubungi maintainer untuk akses credentials production

---

## 🎓 Onboarding Developer Baru (Recommended Flow)

**Day 1 - Setup:**
1. Clone repository
2. Setup environment local (jalankan `setup.ps1`)
3. Akses aplikasi, explore fitur dengan akun seeder
4. Baca `README.md` dan `docs/FITUR-LENGKAP-INTEGRASI.md`

**Day 2 - Familiarization:**
1. Import Postman collection, test semua endpoint
2. Baca structure folder (`app/`, `routes/`, `resources/`)
3. Review key files: controllers, services, models
4. Jalankan test suite: `php artisan test`

**Day 3 - Development:**
1. Buat branch baru untuk fitur/bugfix
2. Ikuti coding conventions (lihat `.github/copilot-instructions.md`)
3. Test perubahan secara lokal
4. Commit dan push untuk review

---

## 📚 Resources

- **Repository:** https://github.com/Haluluya/Simpus
- **Issues:** https://github.com/Haluluya/Simpus/issues
- **Documentation:** `docs/` folder
- **API Collection:** `postman/SIMPUS.postman_collection.json`
- **Laravel Docs:** https://laravel.com/docs/12.x

---

**Catatan Penting:**

Dokumen ini harus di-update seiring perkembangan project. Jika ada perubahan setup workflow, credentials baru, atau dependency tambahan, update dokumen ini dan `.env.example`.
