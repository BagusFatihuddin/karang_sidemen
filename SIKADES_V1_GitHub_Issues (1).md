# POKDARWIS V1 — GitHub Issues Implementation Plan

> Source of truth: PRD v2.0 (31 Mei 2026) + ERD Final v1
> Stack: Laravel 11+ · Filament 3 · React 18+ · MySQL · Cloudinary · Shared Hosting
> Target: Solo developer, AI-assisted, issue per issue

---

## Ringkasan Module Development

| #         | Module                 | Issues  | Jumlah        | Estimasi           |
| --------- | ---------------------- | ------- | ------------- | ------------------ |
| 0         | Foundation & Setup     | #01–#05 | 5             | 1 hari             |
| 1         | Database Migrations    | #06–#11 | 6             | 0.5 hari           |
| 2         | Authentication & Roles | #12–#14 | 3             | 0.5 hari           |
| 3         | Settings Module        | #15–#16 | 2             | 0.5 hari           |
| 4         | Destinations Module    | #17–#22 | 6             | 1.5 hari           |
| 5         | Visitor Registration   | #23–#25 | 3             | 1 hari             |
| 6         | Booking Module         | #26–#32 | 7             | 2 hari             |
| 7         | Review System          | #33–#41 | 9             | 2.5 hari           |
| 8         | Promo & Event          | #42–#43 | 2             | 0.5 hari           |
| 9         | Trip & Guide Module    | #44–#46 | 3             | 1 hari             |
| 10        | Reports & Dashboard    | #47–#49 | 3             | 1 hari             |
| 11        | Public API Layer       | #50–#51 | 2             | 0.5 hari           |
| 12        | React Public Website   | #52–#59 | 8             | 3 hari             |
| 13        | QA & Polish            | #60–#63 | 4             | 1 hari             |
| **Total** |                        |         | **63 issues** | **~16 hari kerja** |

---

---

# 📁 MODUL 0 — Foundation & Setup

---

## Issue #01

**Title:** `[SETUP] Inisialisasi project Laravel 11 + konfigurasi shared hosting`

**Objective:**
Membuat project Laravel yang berjalan di shared hosting sejak awal, bukan refactor belakangan.

**Scope:**

- `composer create-project laravel/laravel POKDARWIS`
- Konfigurasi `.env`: DB, APP_URL, timezone Asia/Makassar
- Setup `public_html` symlink ke `/public` (atau subdir di shared hosting)
- Set `APP_ENV=production` di `.env.example` sebagai template
- Buat `.htaccess` tweak untuk shared hosting (mod_rewrite, PHP version)
- Tambahkan `php.ini` lokal jika shared hosting membutuhkan: `upload_max_filesize`, `post_max_size`
- Verifikasi `php artisan --version` jalan

**Acceptance Criteria:**

- [ ] `php artisan serve` berjalan di lokal
- [ ] `.env.example` berisi semua key yang dibutuhkan project
- [ ] `APP_TIMEZONE=Asia/Makassar` di config
- [ ] `.htaccess` pada root project mengarahkan ke `/public`
- [ ] `storage/` dan `bootstrap/cache/` writable

**Dependencies:** Tidak ada

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Saya membuat project Laravel 11 untuk shared hosting Niagahoster. Bantu setup `.htaccess` root agar domain mengarah ke folder `/public`, dan buat `.env.example` lengkap untuk project ini. Berikut kebutuhan saya: [paste scope]"

---

## Issue #02

**Title:** `[SETUP] Install dan konfigurasi Filament 3`

**Objective:**
Admin panel Filament siap diakses dan terkonfigurasi sebagai internal system.

**Scope:**

- `composer require filament/filament`
- `php artisan filament:install --panels`
- Konfigurasi panel provider: path `/admin`, brand name "POKDARWIS", dark mode
- Set guard `web` untuk Filament
- Konfigurasi `canAccessPanel()` di model `User` (placeholder dulu, logic role di #10)
- Pastikan Filament assets terpublish

**Acceptance Criteria:**

- [ ] `/admin` dapat diakses di browser
- [ ] Login page Filament tampil dengan branding POKDARWIS
- [ ] Panel provider terdaftar di `bootstrap/providers.php`
- [ ] Tidak ada error di log setelah install

**Dependencies:** #01

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Bantu saya setup Filament 3 di Laravel 11. Panel path `/admin`, brand name 'POKDARWIS', guard `web`. Tunjukkan panel provider lengkap dan cara daftarkan di Laravel 11 (bukan `config/app.php` providers lama)."

---

## Issue #03

**Title:** `[SETUP] Install Cloudinary SDK + konfigurasi awal`

**Objective:**
Cloudinary SDK terpasang dan bisa digunakan untuk upload gambar dari Laravel.

**Scope:**

- `composer require Cloudinary SDK:
cloudinary/cloudinary_php`
- Publish config: `php artisan vendor:publish --provider="CloudinaryLabs\CloudinaryLaravel\CloudinaryServiceProvider"`
- Tambahkan env keys: `CLOUDINARY_URL`, `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`
- Buat helper `CloudinaryService` class dengan method: `upload(file, folder)`, `delete(public_id)`
- Test upload sederhana via `php artisan tinker`

**Acceptance Criteria:**

- [ ] SDK terinstall tanpa conflict
- [ ] `config/cloudinary.php` terpublish
- [ ] `CloudinaryService::upload()` berhasil upload file test ke Cloudinary
- [ ] `CloudinaryService::delete()` berhasil hapus file dari Cloudinary
- [ ] Jika credential kosong, tidak throw fatal error (graceful fail + log warning)

**Dependencies:** #01

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat `CloudinaryService` class di Laravel 11 menggunakan package `Cloudinary SDK:
cloudinary/cloudinary_php`. Saya butuh method `upload(\$file, string \$folder): array` yang return `['url' => ..., 'public_id' => ...]` dan method `delete(string \$publicId): bool`. Tangani error jika credential belum diset."

---

## Issue #04

**Title:** `[SETUP] API structure, versioning, CORS + JSON response standard`

**Objective:**
API Laravel siap dikonsumsi React dengan struktur versioning yang bersih, CORS yang benar, dan response format yang konsisten. Tanpa auth layer — semua endpoint publik di V1 tidak memerlukan Sanctum.

**Scope:**

- Konfigurasi `config/cors.php`: allowed_origins untuk domain production + `localhost:5173` (Vite dev)
- Buat prefix route `api/v1/` di `routes/api.php` dengan middleware group `api`
- Buat `App\Http\Responses\ApiResponse` class dengan method:
    - `success($data, string $message = 'OK', int $code = 200): JsonResponse`
    - `error(string $message, int $code = 400, $errors = null): JsonResponse`
- Format response standar:
    ```json
    { "status": "success", "message": "...", "data": {...} }
    { "status": "error", "message": "...", "errors": null }
    ```
- Buat middleware `ForceJsonResponse`: paksa semua response API selalu JSON (tidak pernah HTML)
- Health check endpoint: `GET /api/v1/ping` → `{"status":"ok","timestamp":"..."}`
- Tambahkan `APP_FRONTEND_URL` di `.env.example` untuk CORS whitelist

**Acceptance Criteria:**

- [ ] `GET /api/v1/ping` return JSON `{"status":"ok"}` tanpa error CORS dari `localhost:5173`
- [ ] Header `Access-Control-Allow-Origin` tampil di response
- [ ] `ApiResponse::success(['key' => 'value'])` menghasilkan JSON dengan struktur konsisten
- [ ] `ApiResponse::error('Not found', 404)` menghasilkan JSON error yang konsisten
- [ ] Semua route dalam prefix `api/v1` otomatis kena middleware `ForceJsonResponse`
- [ ] Request ke endpoint tidak ada → return JSON 404, bukan HTML Laravel default
- [ ] Tidak ada Sanctum atau auth middleware di routes publik

**Dependencies:** #01

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Setup API structure di Laravel 11 untuk public API tanpa auth. Saya butuh: (1) CORS config untuk localhost:5173 + domain production, (2) routes/api.php dengan prefix v1, (3) ApiResponse helper class dengan method success() dan error() yang return JSON konsisten, (4) middleware ForceJsonResponse agar API tidak pernah return HTML, (5) health check GET /api/v1/ping. Tidak perlu Sanctum — semua endpoint V1 ini publik."

---

## Issue #05

**Title:** `[SETUP] Setup React 18 project + struktur folder public website`

**Objective:**
React project siap dengan struktur yang clean dan bisa berkomunikasi ke Laravel API.

**Scope:**

- `npm create vite@latest POKDARWIS-public -- --template react`
- Install dependencies: `axios`, `react-router-dom`, `@tanstack/react-query`
- Konfigurasi `axios` baseURL ke `VITE_API_URL` dari `.env`
- Buat struktur folder: `pages/`, `components/`, `hooks/`, `services/`, `utils/`
- Buat `api/` folder dengan service files per module (destinations, reviews, promos)
- Setup React Router dengan route placeholder untuk semua halaman V1
- Konfigurasi Vite proxy untuk development

**Acceptance Criteria:**

- [ ] `npm run dev` berjalan di `localhost:5173`
- [ ] `axios` instance terkonfigurasi dengan `baseURL` dari env
- [ ] React Router terdaftar dengan semua route placeholder V1
- [ ] `npm run build` berhasil tanpa error
- [ ] Struktur folder sesuai konvensi yang disepakati

**Dependencies:** #04

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Setup React 18 + Vite project untuk public website. Saya butuh: axios instance dengan baseURL dari env, React Router DOM dengan routes: `/`, `/destinasi`, `/destinasi/:id`, `/paket`, `/reviews`, `/review/:token`, `/tentang`. Buat juga struktur folder `services/api/` dengan file terpisah per modul. Tunjukkan cara konfigurasi Vite proxy untuk dev."

---

---

# 📁 MODUL 1 — Database Migrations

---

## Issue #06

**Title:** `[DB] Migration: users`

**Objective:**
Tabel `users` sesuai ERD dengan kolom `role` ENUM dan `is_active`.

**Scope:**

- Modifikasi default Laravel users migration (JANGAN buat baru jika sudah ada)
- Tambahkan kolom: `role ENUM('super_admin','admin_konten','pimpinan','anggota_pokdarwis','petugas_lapangan')`, `is_active TINYINT(1) DEFAULT 1`
- Hapus kolom yang tidak dipakai dari default migration
- Update model `User`: tambah `$fillable`, `$hidden`, cast `is_active` ke boolean
- Buat factory `UserFactory` dengan state per role

**Acceptance Criteria:**

- [ ] Migration berhasil dijalankan
- [ ] Rollback berjalan bersih
- [ ] Model `User` punya `$fillable`, `$hidden` (password), cast yang benar
- [ ] `UserFactory::superAdmin()` state berfungsi untuk seeder
- [ ] Index pada kolom `email` (sudah unique = otomatis)

**Dependencies:** #02

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Modifikasi default Laravel 11 users migration untuk menambahkan kolom `role ENUM(...)` dan `is_active`. Berikut struktur ERD saya: [paste tabel users dari ERD]. Buat juga model User dengan fillable, hidden, cast yang sesuai, dan UserFactory dengan state untuk tiap role."

---

## Issue #07

**Title:** `[DB] Migration: destinations + destination_images`

**Objective:**
Dua tabel inti untuk modul destinasi sesuai ERD.

**Scope:**

- Migration `create_destinations_table`: semua kolom dari ERD, soft deletes
- Migration `create_destination_images_table`: FK ke destinations, kolom url, public_id, sort_order
- Model `Destination`: fillable, soft deletes, relasi `hasMany(DestinationImage::class)`
- Model `DestinationImage`: fillable, relasi `belongsTo(Destination::class)`
- Factory `DestinationFactory` (untuk seeder/testing)

**Acceptance Criteria:**

- [ ] Kedua migration berhasil naik dan rollback
- [ ] FK `destination_images.destination_id` dengan `onDelete('cascade')`
- [ ] `Destination::images()` relasi berfungsi
- [ ] `Destination::activeDestinations()` scope: `where('is_active', true)`
- [ ] Soft delete: deleted destination tidak muncul di query default

**Dependencies:** #06

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat migration dan model untuk tabel `destinations` dan `destination_images` sesuai ERD ini: [paste kedua tabel]. Sertakan soft delete pada destinations, relasi hasMany ke destination_images, scope `active()`, dan cascade delete pada FK image."

---

## Issue #08

**Title:** `[DB] Migration: visitors + daily_visits`

**Objective:**
Tabel wisatawan dan data harian dengan semua constraint.

**Scope:**

- Migration `create_visitors_table`: kolom dari ERD termasuk `origin_category ENUM` dan `origin_city VARCHAR(100)`
- Migration `create_daily_visits_table`: kolom dari ERD + `UNIQUE(destination_id, date)`
- Model `Visitor`: fillable, relasi ke destinations, users
- Model `DailyVisit`: fillable, relasi ke destinations, users, method `updateOrCreate` shortcut
- Index pada `visitors.whatsapp_number` (untuk lookup)

**Acceptance Criteria:**

- [ ] Migration naik dan rollback bersih
- [ ] `UNIQUE(destination_id, date)` constraint terdefinisi di daily_visits
- [ ] FK `visitors.destination_id` → destinations
- [ ] FK `visitors.recorded_by` → users
- [ ] Index pada `visitors.whatsapp_number`

**Dependencies:** #07

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat migration dan model untuk `visitors` dan `daily_visits` dari ERD ini: [paste kedua tabel]. Pastikan `UNIQUE(destination_id, date)` pada daily_visits, dan index pada `visitors.whatsapp_number`. Sertakan relasi Eloquent yang diperlukan."

---

## Issue #09

**Title:** `[DB] Migration: bookings`

**Objective:**
Tabel bookings dengan visitor_id nullable dan booking_code logic.

**Scope:**

- Migration `create_bookings_table`: kolom dari ERD, `visitor_id BIGINT UNSIGNED NULL`
- Model `Booking`: fillable, relasi ke visitor (withDefault), destination, user
- Trait atau method `generateBookingCode()`: format `KS-` + 5 karakter uppercase alphanumeric unique
- Scope: `pending()`, `confirmed()`, `completed()`, `cancelled()`
- Index pada `booking_code` (unique sudah handle ini)

**Acceptance Criteria:**

- [ ] Migration naik dan rollback bersih
- [ ] `visitor_id` nullable di migration dan model
- [ ] `generateBookingCode()` menghasilkan kode unik format `KS-XXXXX`
- [ ] Re-generate jika kode sudah ada (loop dengan max 10 attempt)
- [ ] `Booking::visitor()` menggunakan `withDefault(['name' => $this->guest_name])`
- [ ] Semua 4 scope status berfungsi

**Dependencies:** #08

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat migration dan model `Booking` dari ERD ini: [paste tabel bookings]. Sertakan: visitor_id nullable, withDefault pada relasi visitor, method `generateBookingCode()` yang menghasilkan kode unik format 'KS-' + 5 karakter uppercase alphanumeric, dan scope untuk tiap status."

---

## Issue #10

**Title:** `[DB] Migration: review_tokens + reviews`

**Objective:**
Sistem review berbasis token dengan semua constraint sesuai ERD.

**Scope:**

- Migration `create_review_tokens_table`: kolom dari ERD, `token VARCHAR(100) UNIQUE`
- Migration `create_reviews_table`: kolom dari ERD, `UNIQUE(review_token_id)`
- Model `ReviewToken`: relasi ke visitor, destination, user; method `isExpired()`, `isUsable()`
- Model `Review`: relasi ke reviewToken, visitor, destination, approvedBy; scope `approved()`, `pinnedGlobal()`, `pinnedDestination()`
- Index pada `reviews(destination_id, status)` untuk query performa

**Acceptance Criteria:**

- [ ] Migration naik dan rollback bersih
- [ ] `UNIQUE(review_token_id)` pada tabel reviews (enforce 1 token = 1 review)
- [ ] `ReviewToken::isUsable()`: return false jika `is_used = true` ATAU `expires_at < now()`
- [ ] `Review::approved()` scope berfungsi
- [ ] `Review::pinnedGlobal()` scope: `where('is_pinned_global', true)->where('status', 'approved')`
- [ ] Index composite `(destination_id, status)` pada reviews

**Dependencies:** #08

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat migration dan model untuk `review_tokens` dan `reviews` dari ERD ini: [paste kedua tabel]. Pastikan UNIQUE(review_token_id), method `isUsable()` pada ReviewToken, scope `approved()` dan `pinnedGlobal()` pada Review, dan index composite pada reviews(destination_id, status)."

---

## Issue #11

**Title:** `[DB] Migration: promos, trip_packages, guides, pivot tables, settings + Seeder`

**Objective:**
Semua tabel sisa selesai, seeder dasar berjalan.

**Scope:**

- Migration: `promos`, `trip_packages`, `trip_package_destinations`, `guides`, `trip_package_guides`, `settings`
- Model: `Promo` (scope `active()`), `TripPackage`, `Guide`, `Setting`
- `Promo::active()` scope: `is_active = 1 AND DATE(NOW()) BETWEEN start_date AND end_date`
- Setting helper class `AppSettings`: `get(key, default)`, `set(key, value)`
- Seeder `DatabaseSeeder`: jalankan `SettingsSeeder` + `UserSeeder` (Super Admin)
- `SettingsSeeder`: insert semua default keys dengan nilai kosong

**Acceptance Criteria:**

- [ ] Semua migration naik berurutan tanpa FK error
- [ ] `php artisan migrate:fresh --seed` berjalan sempurna
- [ ] `Promo::active()` query benar (test dengan tanggal dalam dan luar range)
- [ ] `AppSettings::get('village_name', 'Default')` berfungsi
- [ ] Seeder membuat 1 Super Admin dengan credential dari `.env`
- [ ] Semua settings keys terseed: `village_name`, `tagline`, `global_whatsapp`, `cloudinary_api_key`, `cloudinary_api_secret`, `cloudinary_cloud_name`, `social_instagram`, `social_facebook`, `social_tiktok`, `google_maps_embed_url`

**Dependencies:** #09, #10

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat migration, model, dan seeder untuk tabel: promos, trip_packages, trip_package_destinations, guides, trip_package_guides, settings. Sertakan scope `active()` pada Promo, helper class `AppSettings` dengan `get()` dan `set()`, dan DatabaseSeeder yang menjalankan SettingsSeeder + UserSeeder. ERD: [paste semua tabel tersisa]."

---

---

# 📁 MODUL 2 — Authentication & Roles

---

## Issue #12

**Title:** `[AUTH] Filament login + canAccessPanel per role`

**Objective:**
Semua role internal bisa login ke Filament, akses panel dibatasi dengan benar.

**Scope:**

- Method `canAccessPanel(Panel $panel)` di model `User`
- Semua role aktif (`is_active = true`) boleh login ke `/admin`
- Buat enum atau constant class `UserRole` untuk semua role string
- Test login dengan masing-masing role
- Redirect setelah login ke dashboard Filament

**Acceptance Criteria:**

- [ ] User dengan `is_active = false` ditolak login
- [ ] Semua 5 role aktif bisa masuk ke Filament
- [ ] `UserRole::SUPER_ADMIN`, `UserRole::PETUGAS_LAPANGAN`, dll tersedia sebagai konstanta
- [ ] Tidak ada hard-coded string role di luar `UserRole` class

**Dependencies:** #06, #11

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat method `canAccessPanel()` di model User Laravel 11 untuk Filament 3. User boleh akses jika `is_active = true`. Buat juga enum/class `UserRole` dengan semua konstanta role. Role yang ada: super_admin, admin_konten, pimpinan, anggota_pokdarwis, petugas_lapangan."

---

## Issue #13

**Title:** `[AUTH] Role-based navigation + resource visibility di Filament`

**Objective:**
Tiap role hanya melihat menu dan resource yang relevan di sidebar Filament.

**Scope:**

- Override `getNavigationItems()` atau gunakan `navigationGroup` + policy
- Mapping akses per resource:
    - `super_admin`: semua menu
    - `admin_konten`: destinasi, review, wisatawan, booking, daily_visits, promo, trip, kirim review, wa blast
    - `pimpinan`: laporan saja (read-only)
    - `anggota_pokdarwis`: dashboard + statistik saja (read-only)
    - `petugas_lapangan`: form visitor registration + verifikasi booking
- Buat `AuthorizationTrait` atau extend Filament Resource dengan `canViewAny()` override

**Acceptance Criteria:**

- [ ] Login sebagai `petugas_lapangan` → hanya tampil 2 menu
- [ ] Login sebagai `pimpinan` → hanya tampil menu laporan
- [ ] Login sebagai `admin_konten` → tidak tampil menu Settings dan User Management
- [ ] Login sebagai `super_admin` → semua menu tampil
- [ ] Akses paksa URL resource yang tidak berwenang → redirect dengan error

**Dependencies:** #12

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan role-based navigation di Filament 3 untuk 5 role: super_admin, admin_konten, pimpinan, anggota_pokdarwis, petugas_lapangan. Gunakan `canViewAny()` di masing-masing resource. Saya ingin pendekatan yang DRY — bisa pakai trait atau base class. Mapping akses: [paste mapping dari scope]."

---

## Issue #14

**Title:** `[AUTH] User management resource (Super Admin only)`

**Objective:**
Super Admin bisa CRUD user internal dari Filament.

**Scope:**

- Filament Resource `UserResource` — hanya tampil untuk `super_admin`
- Form: name, email, password (dengan confirm), role (select), is_active (toggle)
- Password hanya diisi saat create; edit bisa kosong (tidak ganti)
- Table: name, email, role badge, is_active, created_at
- Tidak bisa hapus diri sendiri (guard di `canDelete()`)

**Acceptance Criteria:**

- [ ] CRUD user berfungsi
- [ ] Password di-hash dengan `bcrypt` sebelum simpan
- [ ] Edit user dengan password kosong = tidak update password
- [ ] Badge role berwarna berbeda per role
- [ ] Super Admin tidak bisa hapus akun dirinya sendiri
- [ ] Role `pimpinan`, `anggota_pokdarwis` tidak bisa mengakses resource ini

**Dependencies:** #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `UserResource` untuk CRUD internal users. Hanya super_admin yang bisa akses. Form: name, email, password (opsional saat edit), role select, is_active toggle. Password harus di-hash. Guard: tidak bisa hapus diri sendiri. Gunakan Filament badge untuk kolom role."

---

---

# 📁 MODUL 3 — Settings Module

---

## Issue #15

**Title:** `[SETTINGS] AppSettings helper class + cache layer`

**Objective:**
Settings bisa dibaca dari mana saja di aplikasi dengan performa optimal.

**Scope:**

- Class `App\Services\AppSettings` dengan `get(string $key, $default = null)` dan `set(string $key, $value): void`
- Cache settings per key dengan `Cache::remember('setting.'.$key, 3600, ...)`
- `set()` invalidate cache setelah update
- Facade atau helper function `setting('key')` agar mudah dipanggil dari Blade/Controller
- Unit test sederhana untuk `get()` dan `set()`

**Acceptance Criteria:**

- [ ] `setting('village_name')` bisa dipanggil dari controller/blade
- [ ] Setting yang di-cache tidak hit DB setiap request
- [ ] `set()` langsung invalidate cache yang relevan
- [ ] Jika key tidak ada, return `$default` tanpa throw exception

**Dependencies:** #11

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat class `AppSettings` di Laravel 11 dengan method `get(key, default)` dan `set(key, value)`. Gunakan Laravel Cache agar tidak query DB tiap request. Buat juga helper function `setting()` yang bisa dipanggil global. Tunjukkan cara registrasi helper di `composer.json` atau `AppServiceProvider`."

---

## Issue #16

**Title:** `[SETTINGS] Filament Settings page (Super Admin only)`

**Objective:**
Super Admin bisa update semua konfigurasi sistem dari satu halaman Filament.

**Scope:**

- Filament custom Page (bukan Resource) `SettingsPage`
- Grup form fields:
    - Informasi Desa: `village_name`, `tagline`
    - Kontak: `global_whatsapp`
    - Social Media: `social_instagram`, `social_facebook`, `social_tiktok`
    - Maps: `google_maps_embed_url` (textarea)
    - Cloudinary: `cloudinary_cloud_name`, `cloudinary_api_key`, `cloudinary_api_secret` (password field)
- Cloudinary credentials disimpan dengan `encrypt()` / ditampilkan dengan `decrypt()`
- Hanya `super_admin` yang bisa akses

**Acceptance Criteria:**

- [ ] Semua settings keys tampil sebagai form yang bisa diedit
- [ ] Save berhasil mengupdate semua keys via `AppSettings::set()`
- [ ] Cloudinary keys tidak tampil plaintext di HTML (gunakan type="password")
- [ ] Akses oleh non-super_admin → redirect forbidden

**Dependencies:** #15, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 custom Page (bukan Resource) untuk Settings. Gunakan `FilamentForms` dengan Section/Fieldset untuk grup: Informasi Desa, Kontak, Social Media, Maps, Cloudinary. Data diload dari `AppSettings::get()` dan disimpan via `AppSettings::set()`. Hanya super_admin yang bisa akses."

---

---

# 📁 MODUL 4 — Destinations Module

---

## Issue #17

**Title:** `[DEST] Filament Resource: Destinations CRUD`

**Objective:**
Admin Konten bisa kelola data destinasi lengkap dari Filament.

**Scope:**

- Filament Resource `DestinationResource`
- Form fields: name, description (RichEditor), facilities (Textarea), entry_fee, parking_fee, rental_price (semua nullable), destination_type (Select), whatsapp_number, maps_url, is_active (Toggle)
- Table columns: name, type badge, is_active badge, created_at
- Filters: destination_type, is_active
- Soft delete: tab "Aktif" dan "Arsip"
- Auto-generate `cloudinary_folder` saat create: `destinations/` + slug nama

**Acceptance Criteria:**

- [ ] CRUD destinasi berfungsi penuh
- [ ] Validasi: name (required), destination_type (required), description (required)
- [ ] `cloudinary_folder` auto-fill saat create, tidak bisa diedit manual
- [ ] Tab "Arsip" menampilkan soft-deleted destinations
- [ ] Restore dan Force Delete dari tab Arsip
- [ ] `pimpinan` dan `anggota_pokdarwis` tidak bisa akses resource ini

**Dependencies:** #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `DestinationResource` untuk CRUD destinasi. Form: name, description (RichEditor), facilities, entry_fee, parking_fee, rental_price (nullable decimal), destination_type (select enum), whatsapp_number, maps_url, is_active. Sertakan soft delete dengan tab Aktif/Arsip dan auto-generate cloudinary_folder dari slug nama."

---

## Issue #18

**Title:** `[DEST] Single image upload destinasi via Cloudinary`

**Objective:**
Admin bisa upload satu gambar utama (thumbnail) per destinasi. Fondasi Cloudinary upload sebelum multi-image.

**Scope:**

- Tambahkan field `FileUpload` di `DestinationResource` form untuk gambar utama
- Upload ke Cloudinary menggunakan `CloudinaryService::upload($file, $folder)`
- Simpan `cloudinary_public_id` dan `url` ke tabel `destination_images` dengan `sort_order = 0`
- Tampilkan preview gambar yang sudah di-upload di form edit
- Hapus file lama dari Cloudinary jika gambar di-replace

**Acceptance Criteria:**

- [ ] Upload satu gambar berhasil tersimpan ke Cloudinary folder `destinations/{id}/`
- [ ] Record `destination_images` terbuat dengan `sort_order = 0`
- [ ] Preview gambar tampil di form edit
- [ ] Jika upload gambar baru saat edit → gambar lama terhapus dari Cloudinary
- [ ] Jika Cloudinary gagal → form tidak crash, tampilkan notifikasi error

**Dependencies:** #17, #03

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan single image upload ke DestinationResource Filament 3. Upload ke Cloudinary menggunakan CloudinaryService yang sudah ada (method upload(\$file, \$folder) return ['url', 'public_id']). Simpan ke tabel destination_images dengan sort_order=0. Tampilkan preview. Jika ada gambar lama saat edit, hapus dari Cloudinary dulu sebelum upload yang baru."

---

## Issue #19

**Title:** `[DEST] Multiple images upload destinasi (gallery)`

**Objective:**
Admin bisa upload lebih dari satu foto per destinasi untuk gallery slider.

**Scope:**

- Tambahkan sub-section "Galeri Foto Tambahan" di `DestinationResource` form, terpisah dari gambar utama
- Gunakan Filament `Repeater` dengan `FileUpload` per item
- Setiap upload: simpan ke Cloudinary + insert record `destination_images` baru
- Batas maksimal 10 gambar total per destinasi (termasuk gambar utama)
- Validasi: hanya JPEG/PNG/WEBP, maks 2MB per file

**Acceptance Criteria:**

- [ ] Bisa upload 2–9 gambar tambahan (di luar gambar utama)
- [ ] Setiap gambar menghasilkan record baru di `destination_images`
- [ ] Validasi format (JPEG/PNG/WEBP) dan ukuran (maks 2MB) berjalan
- [ ] Melebihi 10 gambar total → validasi error ditampilkan
- [ ] Semua upload berhasil sebelum form disimpan (bukan background job)

**Dependencies:** #18

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan multiple image upload ke DestinationResource Filament 3 menggunakan Repeater dengan FileUpload. Setiap item Repeater: satu FileUpload yang upload ke Cloudinary saat save. Batas 10 gambar total (termasuk gambar utama dari issue sebelumnya). Validasi: JPEG/PNG/WEBP, maks 2MB. Tunjukkan cara hitung total gambar existing + upload baru untuk validasi limit."

---

## Issue #20

**Title:** `[DEST] Reorder images destinasi (drag-and-drop sort_order)`

**Objective:**
Admin bisa mengatur urutan foto destinasi — urutan pertama menjadi thumbnail utama di public website.

**Scope:**

- Tambahkan UI reorder di `DestinationResource` view/edit page: list gambar yang bisa di-drag
- Setelah reorder dan save → update kolom `sort_order` di semua record `destination_images` terkait
- Gambar dengan `sort_order = 0` secara otomatis menjadi thumbnail di API response
- Tidak perlu real-time drag — cukup dengan tombol "Naik"/"Turun" atau number input sort_order

**Acceptance Criteria:**

- [ ] Urutan gambar bisa diubah dari form edit destinasi
- [ ] Setelah save, `sort_order` di DB terupdate sesuai urutan baru
- [ ] API `GET /api/v1/destinations/{id}` return gambar dalam urutan `sort_order` ASC
- [ ] Gambar `sort_order = 0` menjadi `thumbnail_url` di response API list destinasi

**Dependencies:** #19

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan reorder gambar destinasi di Filament 3. Tampilkan list gambar existing dengan tombol 'Naik'/'Turun' atau drag handle. Setelah save, loop dan update sort_order setiap destination_image record. Gambar sort_order=0 adalah thumbnail. Tunjukkan cara update sort_order batch dalam satu query di Laravel."

---

## Issue #21

**Title:** `[DEST] Model observer: cleanup Cloudinary saat destination/image dihapus`

**Objective:**
Tidak ada orphaned file di Cloudinary ketika destinasi atau gambarnya dihapus.

**Scope:**

- Buat `DestinationImageObserver`: pada event `deleting`, hapus file dari Cloudinary via `CloudinaryService::delete()`
- Daftarkan observer di `AppServiceProvider`
- Jika Cloudinary API error saat delete: log error, lanjutkan delete DB (jangan block)
- Unit test: mock CloudinaryService, verifikasi observer dipanggil

**Acceptance Criteria:**

- [ ] Hapus `DestinationImage` → file terhapus dari Cloudinary
- [ ] Cloudinary error tidak mencegah record DB terhapus
- [ ] Error di-log dengan `Log::warning()`
- [ ] Observer terdaftar di `AppServiceProvider`

**Dependencies:** #20

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat Laravel Observer `DestinationImageObserver` yang menghapus file dari Cloudinary ketika record dihapus. Gunakan event `deleting`. Jika Cloudinary API error, log warning tapi tetap lanjutkan delete DB. Tunjukkan cara daftarkan observer di AppServiceProvider Laravel 11."

---

## Issue #22

**Title:** `[DEST] Filament Resource: Guides CRUD`

**Objective:**
Admin Konten bisa kelola profil tour guide.

**Scope:**

- Filament Resource `GuideResource`
- Form: name, bio (Textarea), experience (TextInput), photo (upload ke Cloudinary folder `guides/`), is_active
- Table: photo thumbnail, name, is_active, experience
- Hapus guide → hapus foto dari Cloudinary (via Observer)
- Akses: `super_admin` dan `admin_konten` saja

**Acceptance Criteria:**

- [ ] CRUD guide berfungsi
- [ ] Upload foto ke Cloudinary folder `guides/`
- [ ] Hapus guide → foto di Cloudinary ikut terhapus
- [ ] Guide `is_active = false` tidak muncul di public website API

**Dependencies:** #21

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `GuideResource` untuk CRUD tour guide. Form: name, bio, experience, photo (upload ke Cloudinary folder 'guides/'), is_active toggle. Foto disimpan sebagai cloudinary_public_id + photo_url di kolom tabel. Hapus guide harus hapus foto dari Cloudinary."

---

---

# 📁 MODUL 5 — Visitor Registration

---

## Issue #23

**Title:** `[VISITOR] Filament Resource: Visitors list + detail (Admin Konten)`

**Objective:**
Admin Konten bisa melihat semua data wisatawan dengan filter dan search.

**Scope:**

- Filament Resource `VisitorResource` (ListRecords + ViewRecord saja — tidak ada CreateRecord/EditRecord dari sini)
- Table columns: name, origin_category badge, origin_city, visit_type, destination, recorded_by, visited_at
- Filters: origin_category, visit_type, destination, date range (visited_at)
- Search: name (tapi nomor WA tidak tampil di list)
- Akses: `super_admin`, `admin_konten` saja (bukan `anggota_pokdarwis`)

**Acceptance Criteria:**

- [ ] List visitor tampil dengan semua kolom
- [ ] `whatsapp_number` TIDAK tampil di table list (privacy)
- [ ] `whatsapp_number` HANYA tampil di view detail, dan hanya untuk `super_admin` dan `admin_konten`
- [ ] Filter kombinasi (origin + destination) berfungsi
- [ ] Export data ke Excel (gunakan Laravel Excel atau Filament export)

**Dependencies:** #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `VisitorResource` yang hanya punya ListRecords dan ViewRecord (tidak ada Create/Edit form di sini). Tampilkan semua kolom kecuali whatsapp_number di list. WhatsApp hanya tampil di detail view untuk super_admin dan admin_konten. Sertakan filter: origin_category, visit_type, destination_id, date range."

---

## Issue #24

**Title:** `[VISITOR] Form registrasi wisatawan untuk Petugas Lapangan`

**Objective:**
Petugas Lapangan punya form cepat yang mobile-friendly untuk input data wisatawan di lapangan.

**Scope:**

- Filament custom Page `VisitorRegistrationPage` khusus untuk role `petugas_lapangan`
- Form fields (semua wajib kecuali `referral_other`): name, whatsapp_number (validasi format WA), origin_category (select), origin_city (text), visit_type (select), group_size (number, default 1), destination_id (select), referral_source (select), referral_other (muncul jika pilih "lainnya"), visited_at (default now())
- `recorded_by` auto-fill dari `auth()->id()`
- Setelah submit: reset form, tampilkan success notification
- Mobile-friendly layout (stack vertikal, tombol besar)

**Acceptance Criteria:**

- [ ] Form submit berhasil menyimpan ke tabel `visitors`
- [ ] `whatsapp_number` divalidasi format Indonesia (08xx atau +628xx)
- [ ] Field `referral_other` hanya muncul jika `referral_source = 'lainnya'` (reactive)
- [ ] `visited_at` default ke waktu sekarang, bisa diubah
- [ ] Setelah submit, form reset dan siap untuk input berikutnya
- [ ] Petugas lapangan TIDAK bisa edit data yang sudah disubmit

**Dependencies:** #23, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 custom Page `VisitorRegistrationPage` untuk role petugas_lapangan. Form mobile-friendly dengan fields: name, whatsapp_number (validasi format WA Indonesia), origin_category (select enum), origin_city, visit_type, group_size, destination_id (select dari DB), referral_source, referral_other (conditional muncul jika lainnya). Reset setelah submit."

---

## Issue #25

**Title:** `[VISITOR] WA Blast V1 — filter + buka wa.me manual`

**Objective:**
Admin bisa filter wisatawan dan membuka WhatsApp dengan pesan template untuk blast manual.

**Scope:**

- Filament custom Page `WABlastPage`
- Filter: origin_category, visit_type, destination_id (bisa multiselect)
- Tampilkan daftar wisatawan hasil filter (nama + nomor WA)
- Textarea untuk pesan (dengan template default yang bisa diedit)
- Tombol "Buka WhatsApp" per nomor → `href="https://wa.me/{phone}?text={urlencode(pesan)}"`
- Tombol "Buka Semua" → buka tab baru untuk semua nomor (konfirmasi dulu jika > 5)
- Akses: `super_admin` dan `admin_konten`

**Acceptance Criteria:**

- [ ] Filter menghasilkan daftar wisatawan yang sesuai
- [ ] Format WA link: `https://wa.me/62{nomor_tanpa_leading_zero}?text={encoded_pesan}`
- [ ] Template default: "Halo [nama], ..."
- [ ] Konfirmasi muncul jika membuka > 5 nomor sekaligus
- [ ] Nomor WA di-format ke format internasional 628xx sebelum dibuat link

**Dependencies:** #23

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 custom Page `WABlastPage`. Filter wisatawan berdasarkan origin_category, visit_type, destination_id. Tampilkan hasil filter dengan nama + tombol WhatsApp per baris. Ada textarea pesan dengan default template. Format WA link: `https://wa.me/628xxx?text=encoded`. Buka semua butuh konfirmasi jika > 5."

---

---

# 📁 MODUL 6 — Booking Module

---

## Issue #26

**Title:** `[BOOKING] Filament Resource: basic Bookings CRUD`

**Objective:**
Fondasi resource booking di Filament — form sederhana dengan semua field, tanpa reactive visitor/guest mode dulu.

**Scope:**

- Filament Resource `BookingResource`
- Form create dengan semua field langsung: `destination_id` (select), `checkin_date`, `checkout_date` (optional), `total_price` (optional), `booking_status` (select), `visitor_id` (searchable select, optional), `guest_name`, `guest_phone`, `guest_city`
- `created_by` auto-fill dari auth user
- `booking_code` di-generate otomatis saat create (lihat #28) — untuk sementara isi hardcode atau random
- Table: booking_code, destination, checkin_date, booking_status badge, created_at
- Filter: booking_status, destination_id
- Akses: `super_admin` dan `admin_konten`

**Acceptance Criteria:**

- [ ] Create booking berhasil menyimpan ke DB
- [ ] Semua field tampil di form create
- [ ] `created_by` auto-fill dari `auth()->id()`
- [ ] Table list menampilkan semua booking dengan badge status
- [ ] Filter booking_status dan destination_id berfungsi
- [ ] View detail booking menampilkan semua data

**Dependencies:** #23, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `BookingResource` — basic CRUD dulu tanpa reactive form. Form: destination_id (select), visitor_id (searchable select, optional), guest_name, guest_phone, guest_city, checkin_date, checkout_date, total_price, booking_status. Kolom created_by auto-fill. Table dengan filter booking_status + destination. Belum perlu handle auto-generate booking_code atau reactive form."

---

## Issue #27

**Title:** `[BOOKING] Visitor vs guest mode — reactive form di BookingResource`

**Objective:**
Form booking punya dua mode yang saling exclusive: pilih visitor dari DB atau input guest manual.

**Scope:**

- Tambahkan toggle/radio "Pilih Wisatawan Terdaftar" vs "Input Manual" di form create BookingResource
- Jika mode "Terdaftar": field `visitor_id` aktif (searchable select), field `guest_*` disabled/hidden
- Jika mode "Manual": field `visitor_id` disabled/hidden, field `guest_name` + `guest_phone` + `guest_city` wajib diisi
- Gunakan Filament `$get()` reactive callback untuk show/hide/require field secara dinamis
- Validasi: salah satu dari (visitor_id) atau (guest_name + guest_phone) harus ada

**Acceptance Criteria:**

- [ ] Toggle mode berfungsi, field berubah sesuai pilihan
- [ ] Mode "Terdaftar": visitor*id wajib, guest*\* boleh kosong
- [ ] Mode "Manual": guest_name + guest_phone wajib, visitor_id null
- [ ] Submit dengan tidak ada visitor_id dan tidak ada guest_name → validation error
- [ ] Setelah create, record DB: visitor*id null jika manual, guest*\* null jika dari DB

**Dependencies:** #26

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan reactive form mode ke BookingResource Filament 3. Ada radio/toggle dua pilihan: 'Wisatawan Terdaftar' vs 'Input Manual'. Gunakan \$get() reactive callback di Filament 3 untuk: (1) show/hide field yang relevan, (2) ubah required rule berdasarkan mode. Tunjukkan pattern lengkap dengan custom validation rule yang cek: harus ada visitor_id ATAU (guest_name + guest_phone)."

---

## Issue #28

**Title:** `[BOOKING] Booking code generator — auto-generate format KS-XXXXX`

**Objective:**
Setiap booking mendapat kode unik otomatis yang tidak bisa diedit manual, siap dikirim ke wisatawan.

**Scope:**

- Tambahkan static method `Booking::generateBookingCode(): string` di model
- Format: `KS-` + 5 karakter uppercase alphanumeric (contoh: `KS-A3Z7P`)
- Re-generate jika kode sudah ada di DB (loop max 10 attempt, throw exception jika semua collision)
- Hook ke `creating` event model: auto-generate sebelum insert
- Tampilkan `booking_code` sebagai field read-only di form edit Filament (tidak bisa diubah)
- Tambahkan tombol "Salin Kode" (copy to clipboard) di view/edit page

**Acceptance Criteria:**

- [ ] Setiap booking baru otomatis dapat kode saat `creating` event
- [ ] Format `KS-XXXXX` (huruf besar + angka, 5 karakter setelah prefix)
- [ ] Kode unik — tidak ada duplikat di DB
- [ ] Field booking_code read-only di Filament form
- [ ] Tombol "Salin Kode" mengcopy ke clipboard dengan konfirmasi visual

**Dependencies:** #27

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Implementasikan auto-generate booking_code di model Booking Laravel 11. Gunakan boot() method dengan creating event. Format: 'KS-' + 5 karakter uppercase alphanumeric random. Loop cek uniqueness max 10 kali. Tunjukkan cara buat field booking_code read-only di Filament 3 form, dan tambahkan tombol 'Salin Kode' dengan JavaScript copy to clipboard."

---

## Issue #29

**Title:** `[BOOKING] Booking status workflow + verifikasi kedatangan`

**Objective:**
Admin bisa mengubah status booking via actions, dan Petugas Lapangan bisa verifikasi kedatangan wisatawan.

**Scope:**

- Tambahkan Filament Actions di BookingResource:
    - `ConfirmBooking`: pending → confirmed (muncul hanya jika status pending)
    - `CancelBooking`: pending/confirmed → cancelled (dengan konfirmasi modal)
- Validasi status flow: tidak bisa mundur dari `completed` atau `cancelled`
- `arrived_at` auto-fill saat status berubah ke `completed`
- Filament custom Page `BookingVerificationPage` khusus `petugas_lapangan`:
    - Input kode booking → tampilkan data booking
    - Tombol "Tandai Sudah Datang" → status = `completed`, `arrived_at = now()`
    - Handle state: kode tidak ada, sudah completed, dibatalkan

**Acceptance Criteria:**

- [ ] Action `ConfirmBooking` hanya muncul jika status `pending`
- [ ] Action `CancelBooking` muncul untuk `pending` dan `confirmed`, dengan konfirmasi
- [ ] Status tidak bisa berubah dari `completed` atau `cancelled`
- [ ] `arrived_at` terisi otomatis saat status → `completed`
- [ ] Petugas lapangan bisa akses `BookingVerificationPage`, masukkan kode, dan tandai datang
- [ ] Halaman verifikasi mobile-friendly

**Dependencies:** #28

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan booking status workflow di Filament 3. Tambahkan Actions: ConfirmBooking (pending→confirmed, conditional) dan CancelBooking (pending/confirmed→cancelled, dengan modal konfirmasi). Guard: status tidak bisa mundur dari completed/cancelled. Auto-fill arrived_at saat completed. Juga buat custom Page BookingVerificationPage untuk petugas_lapangan: input kode → tampil data → tombol tandai datang. Mobile-friendly."

---

## Issue #30

**Title:** `[BOOKING] Edit status booking + copy booking code`

**Objective:**
Admin bisa update status booking dan mudah meng-copy kode untuk dikirim ke wisatawan.

**Scope:**

- Edit form hanya untuk: booking_status, total_price, checkout_date, arrived_at
- Tombol "Salin Kode Booking" di detail view (JavaScript copy to clipboard)
- Action `ConfirmBooking`: ubah status ke `confirmed` dengan satu klik
- Action `CancelBooking`: ubah status ke `cancelled` + konfirmasi modal
- Validasi flow status: tidak bisa dari `completed` kembali ke `pending`

**Acceptance Criteria:**

- [ ] Tombol "Salin Kode" berhasil copy ke clipboard
- [ ] Action `ConfirmBooking` hanya muncul jika status masih `pending`
- [ ] Action `CancelBooking` muncul untuk `pending` dan `confirmed`
- [ ] Tidak bisa ubah status dari `completed` atau `cancelled`
- [ ] `arrived_at` diisi otomatis saat status berubah ke `completed`

**Dependencies:** #26

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan ke BookingResource: tombol copy booking_code ke clipboard di view page, Filament Action `ConfirmBooking` (pending→confirmed) dan `CancelBooking` (pending/confirmed→cancelled) dengan konfirmasi modal. Validasi: status tidak bisa mundur dari completed/cancelled. Auto-fill arrived_at saat completed."

---

## Issue #31

**Title:** `[BOOKING] Verifikasi booking oleh Petugas Lapangan`

**Objective:**
Petugas Lapangan bisa verifikasi kode booking dan tandai wisatawan sudah datang.

**Scope:**

- Filament custom Page `BookingVerificationPage` khusus `petugas_lapangan`
- Input: field kode booking (format KS-XXXXX)
- Tampilkan data booking: nama wisatawan (atau guest_name), destinasi, checkin_date, status saat ini
- Tombol "Tandai Sudah Datang" → update status ke `completed`, set `arrived_at = now()`
- Validasi: kode tidak ada → error message; sudah `completed` → info sudah diverifikasi; `cancelled` → error tidak bisa diverifikasi
- Mobile-friendly

**Acceptance Criteria:**

- [ ] Input kode booking → tampil data booking yang sesuai
- [ ] Kode tidak ditemukan → pesan error jelas
- [ ] Booking sudah `completed` → pesan "Sudah diverifikasi pada [tanggal]"
- [ ] Booking `cancelled` → pesan "Booking ini telah dibatalkan"
- [ ] Klik "Tandai Sudah Datang" → status `completed`, `arrived_at` terisi
- [ ] Halaman mobile-friendly (besar, mudah diklik di HP)

**Dependencies:** #30, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 custom Page `BookingVerificationPage` untuk role petugas_lapangan saja. Form: input kode booking → tampilkan data booking (nama, destinasi, tanggal, status). Tombol 'Tandai Sudah Datang' update status ke completed + set arrived_at. Handle semua state: tidak ditemukan, sudah completed, dibatalkan. Mobile-friendly."

---

## Issue #32

**Title:** `[BOOKING] Input Data Harian (Daily Visits)`

**Objective:**
Admin/Petugas bisa input data kunjungan harian per destinasi dengan upsert logic.

**Scope:**

- Filament Resource `DailyVisitResource`
- Form: destination_id (select), date (default hari ini), visitor_count, revenue, expense
- Logic simpan: `updateOrCreate(['destination_id' => ..., 'date' => ...], [...])`
- `recorded_by` auto-fill dari auth user
- Table: destination, date, visitor_count, revenue, expense
- Filter: destination, month picker
- Validasi: visitor_count dan revenue tidak boleh negatif

**Acceptance Criteria:**

- [ ] Input tanggal + destinasi yang sama = update, bukan insert baru
- [ ] `UNIQUE(destination_id, date)` constraint tidak menghasilkan SQL error (ditangani di service layer)
- [ ] Validasi: semua angka ≥ 0
- [ ] `recorded_by` terisi otomatis
- [ ] Filter per bulan menampilkan total di footer tabel

**Dependencies:** #13

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `DailyVisitResource`. Logic simpan menggunakan `updateOrCreate(['destination_id', 'date'], [...])`. Form: destination_id, date (datepicker, default today), visitor_count, revenue, expense. Semua angka harus ≥ 0. recorded_by auto-fill dari auth(). Tambahkan summary row total di footer tabel Filament."

---

---

# 📁 MODUL 7 — Review System

---

## Issue #33

**Title:** `[REVIEW] Service: GenerateReviewToken`

**Objective:**
Service class yang handle pembuatan token review dengan semua business rules.

**Scope:**

- Buat `App\Services\ReviewTokenService`
- Method `generate(Visitor $visitor, Destination $destination, User $generatedBy): ReviewToken`
- Logic: generate token dengan `Str::random(64)`, set `expires_at = now()->addDays(7)`, simpan ke DB
- Method `buildReviewUrl(string $token): string` → return `config('app.url') . '/review?token=' . $token`
- Method `buildWhatsAppLink(ReviewToken $token): string` → return wa.me link dengan pesan template
- Pesan WA template: "Halo {name}, terima kasih telah berkunjung ke {destination}. Tolong beri rating & ulasan Anda di sini: {url}"

**Acceptance Criteria:**

- [ ] `generate()` membuat record di `review_tokens` dengan `expires_at` 7 hari dari sekarang
- [ ] Token unik (64 karakter random, URL-safe)
- [ ] `buildReviewUrl()` menghasilkan URL yang benar
- [ ] `buildWhatsAppLink()` menghasilkan `https://wa.me/{phone}?text={encoded_message}`
- [ ] Nomor WA di-format ke format internasional (62xxx)
- [ ] Satu visitor boleh punya banyak token aktif (tidak ada batasan)

**Dependencies:** #10, #11

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat `ReviewTokenService` di Laravel 11 dengan method `generate(Visitor, Destination, User)` yang membuat record review_tokens, `buildReviewUrl(token)`, dan `buildWhatsAppLink(ReviewToken)`. Token: Str::random(64), expires 7 hari. WA link format: `https://wa.me/62{nomor}?text={encoded}`. Nomor WA dari visitor.whatsapp_number perlu dikonversi dari 08xx ke 628xx."

---

## Issue #34

**Title:** `[REVIEW] Filament Action: Kirim Link Review`

**Objective:**
Admin bisa memilih wisatawan, pilih destinasi, dan langsung membuka WhatsApp dengan link review.

**Scope:**

- Tambahkan Action `SendReviewLinkAction` di `VisitorResource` (row action)
- Modal: pilih destinasi (select dari DB aktif)
- Generate token via `ReviewTokenService::generate()`
- Tampilkan dua hal: (1) URL review (dengan tombol copy), (2) tombol "Buka WhatsApp" yang membuka wa.me link
- Catat token yang sudah digenerate (tampil di tab "Token Review" di view visitor)

**Acceptance Criteria:**

- [ ] Action muncul di setiap baris visitor di list
- [ ] Modal muncul dengan select destinasi
- [ ] Setelah submit modal: token tersimpan, URL review tampil, tombol WhatsApp tersedia
- [ ] Tombol "Buka WhatsApp" membuka link wa.me di tab baru
- [ ] Di view detail visitor, ada tab "Token Review" yang menampilkan history token (tanggal generate, destinasi, status used/expired)

**Dependencies:** #33, #23

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan Filament 3 Action `SendReviewLinkAction` ke VisitorResource. Action buka modal untuk pilih destinasi. Setelah submit: panggil ReviewTokenService::generate(), tampilkan URL review dengan tombol copy, dan tombol 'Buka WhatsApp' yang buka wa.me link dengan pesan template. Tunjukkan cara tambahkan tab 'Token Review' di ViewRecord VisitorResource."

---

## Issue #35

**Title:** `[REVIEW] API: validasi token + tampilkan data untuk form review publik`

**Objective:**
Endpoint yang dikonsumsi React untuk memvalidasi token dan mengisi form review secara otomatis.

**Scope:**

- `GET /api/v1/review/{token}` — validasi dan return data
- Logic validasi: token ada → tidak expired → belum digunakan → return data
- Response success: `{visitor_name, visitor_city, destination_name, destination_id, token_valid: true}`
- Response error: `{token_valid: false, reason: 'expired'|'used'|'not_found'}`
- Middleware `ValidateReviewToken` → jika tidak valid, return 422 dengan alasan

**Acceptance Criteria:**

- [ ] Token valid → return data visitor + destination
- [ ] Token expired → `{token_valid: false, reason: 'expired'}`
- [ ] Token sudah dipakai → `{token_valid: false, reason: 'used'}`
- [ ] Token tidak ada → `{token_valid: false, reason: 'not_found'}`
- [ ] Response TIDAK mengandung `whatsapp_number` atau data sensitif lainnya
- [ ] Rate limit: 10 request per menit per IP pada endpoint ini

**Dependencies:** #33, #04

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat endpoint `GET /api/v1/review/{token}` di Laravel 11. Validasi: token exists, belum expired (`expires_at > now()`), belum digunakan (`is_used = false`). Return data visitor + destinasi untuk pre-fill form. Response tidak boleh mengandung whatsapp_number. Buat middleware ValidateReviewToken dan rate limit 10 req/menit per IP."

---

## Issue #36

**Title:** `[REVIEW] API: basic review submit endpoint`

**Objective:**
Endpoint submit review berjalan dengan validasi dasar — tanpa photo upload dan tanpa transaction hardening dulu. Fokus pada happy path.

**Scope:**

- `POST /api/v1/review/{token}/submit`
- Request body: `reviewer_name` (required), `reviewer_city` (required), `rating` (required, 1–5), `review_text` (optional)
- Validasi request dengan Laravel FormRequest `SubmitReviewRequest`
- Logic: cek token valid (tidak expired, belum dipakai) → insert ke `reviews` dengan `status = 'pending'` → set `review_tokens.is_used = true`
- `reviewer_name` dan `reviewer_city` adalah **snapshot** — tidak mengubah data `visitors`
- Response sukses: `{"status":"success","message":"Review berhasil dikirim","data":{"review_id":...}}`
- Rate limit: 3 request per 10 menit per IP

**Acceptance Criteria:**

- [ ] Submit dengan data valid → review tersimpan `status = 'pending'`
- [ ] `review_tokens.is_used = true` setelah submit berhasil
- [ ] `reviewer_name` di `reviews` boleh berbeda dari `visitors.name`
- [ ] `reviewer_city` tersimpan sebagai snapshot, tidak mengubah `visitors.origin_city`
- [ ] Rating di luar 1–5 → 422 validation error
- [ ] Rate limit 3 req/10 menit per IP aktif
- [ ] Response JSON konsisten menggunakan `ApiResponse::success()`

**Dependencies:** #35

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat endpoint POST /api/v1/review/{token}/submit di Laravel 11. Validasi dengan FormRequest: reviewer_name (required), reviewer_city (required), rating (required, integer 1-5), review_text (optional). Cek token valid. Simpan ke tabel reviews dengan status='pending'. Set is_used=true pada review_token. reviewer_name dan reviewer_city adalah snapshot. Gunakan ApiResponse helper untuk response. Rate limit 3 req/10 menit."

---

## Issue #37

**Title:** `[REVIEW] Token used logic — mark & invalidate setelah submit`

**Objective:**
Pastikan satu token hanya bisa dipakai satu kali — logic marking `is_used` yang tepat dan informatif.

**Scope:**

- Tambahkan pengecekan eksplisit di controller sebelum proses submit:
    - Jika `is_used = true` → return 422 dengan `reason: 'already_used'`
    - Jika `expires_at < now()` → return 422 dengan `reason: 'expired'`
- Setelah review berhasil disimpan: update `is_used = true`, `used_at = now()`
- Verifikasi UNIQUE constraint `review_token_id` di `reviews` tabel bekerja sebagai safety net
- Tambahkan test case: submit kedua kali dengan token sama → error dengan reason yang benar

**Acceptance Criteria:**

- [ ] Submit dengan token yang sudah dipakai → `{"reason": "already_used", "message": "Token ini sudah pernah digunakan"}`
- [ ] Submit dengan token expired → `{"reason": "expired", "message": "Token sudah kadaluarsa"}`
- [ ] `used_at` terisi timestamp saat token di-mark used
- [ ] UNIQUE constraint pada `review_token_id` di `reviews` mencegah duplikasi di level DB
- [ ] Response error menggunakan `ApiResponse::error()` yang konsisten

**Dependencies:** #36

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Perkuat logika token invalidation di endpoint review submit Laravel 11. Tambahkan pengecekan eksplisit: jika is_used=true return 422 dengan reason 'already_used', jika expires_at < now() return 422 dengan reason 'expired'. Setelah submit berhasil, set is_used=true dan used_at=now(). Jelaskan juga bagaimana UNIQUE constraint di kolom review_token_id tabel reviews berfungsi sebagai safety net terakhir."

---

## Issue #38

**Title:** `[REVIEW] Optional photo upload saat submit review`

**Objective:**
Wisatawan bisa melampirkan foto saat submit review. Jika gagal upload, review tetap tersimpan tanpa foto.

**Scope:**

- Tambahkan field `photo` (optional, file) ke `SubmitReviewRequest`: maks 2MB, JPEG/PNG/WEBP
- Di controller: jika ada foto, upload ke Cloudinary folder `reviews/` menggunakan `CloudinaryService::upload()`
- Simpan `photo_url` dan `photo_public_id` ke record `reviews`
- **Graceful fallback**: jika Cloudinary upload gagal, tetap simpan review tanpa foto (jangan fail seluruh submission) + log error
- Jika tidak ada foto, `photo_url = null`, `photo_public_id = null`

**Acceptance Criteria:**

- [ ] Submit tanpa foto → review tersimpan, `photo_url = null`
- [ ] Submit dengan foto valid → foto di-upload, `photo_url` terisi URL Cloudinary
- [ ] Foto > 2MB → 422 validation error sebelum proses
- [ ] Foto format salah (bukan JPEG/PNG/WEBP) → 422 validation error
- [ ] Cloudinary gagal saat upload → review tetap tersimpan tanpa foto, error di-log
- [ ] `photo_public_id` tersimpan untuk keperluan cleanup nanti

**Dependencies:** #37, #03

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Tambahkan optional photo upload ke endpoint submit review. Validasi: maks 2MB, hanya JPEG/PNG/WEBP. Upload ke Cloudinary folder 'reviews/' jika ada foto. Jika Cloudinary gagal, lanjutkan simpan review tanpa foto (jangan return error ke user) dan log warning. Simpan photo_url dan photo_public_id ke kolom reviews."

---

## Issue #39

**Title:** `[REVIEW] Transaction hardening — cegah race condition double submit`

**Objective:**
Mencegah edge case dua request bersamaan dengan token yang sama berhasil membuat dua review.

**Scope:**

- Wrap seluruh logic submit review dalam `DB::transaction()`
- Gunakan `lockForUpdate()` saat query `review_tokens`: `ReviewToken::where('token', $token)->lockForUpdate()->first()`
- Urutan dalam transaction:
    1. `lockForUpdate()` pada token → re-validasi is_used + expired
    2. Insert review
    3. Update `is_used = true`
- Handle `Illuminate\Database\QueryException` jika UNIQUE constraint terlanggar → return 422 dengan pesan yang informatif

**Acceptance Criteria:**

- [ ] Seluruh operasi submit (lock → insert → update token) dalam satu transaction
- [ ] `lockForUpdate()` dipanggil pada query token
- [ ] `QueryException` dengan UNIQUE violation → return 422 `{"reason": "already_used"}`
- [ ] Jika salah satu step dalam transaction gagal → rollback otomatis, tidak ada data tergantung
- [ ] Behavior identik dengan #36 untuk happy path — ini hanya hardening, bukan perubahan fungsional

**Dependencies:** #38

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Hardening endpoint POST /api/v1/review/{token}/submit dengan DB transaction dan lockForUpdate. Wrap seluruh logic dalam DB::transaction(). Gunakan ReviewToken::where('token', \$token)->lockForUpdate()->first() untuk mencegah race condition. Handle QueryException dari UNIQUE constraint violation. Pastikan rollback terjadi jika ada langkah yang gagal. Jelaskan mengapa lockForUpdate dibutuhkan di sini."

---

## Issue #40

**Title:** `[REVIEW] Filament Resource: Review moderation (approve/reject)`

**Objective:**
Admin Konten bisa moderasi review pending dan approve/reject.

**Scope:**

- Filament Resource `ReviewResource`
- Default view: tab "Pending" (status=pending), "Approved", "Rejected"
- Table columns: reviewer_name, destination, rating (bintang), review_text (truncated), status, created_at
- Bulk actions: Approve (set status=approved, approved_by, approved_at), Reject
- Individual actions: Approve, Reject, View detail (termasuk foto)
- Hapus review (hanya Super Admin)
- Filter: destination, rating, date range

**Acceptance Criteria:**

- [ ] Tab Pending tampil saat buka halaman (default)
- [ ] Bulk approve/reject berfungsi
- [ ] `approved_by` terisi dengan auth user ID saat approve
- [ ] `approved_at` terisi dengan timestamp saat approve
- [ ] Foto review tampil di detail view (jika ada)
- [ ] Hapus review hanya bisa oleh super_admin
- [ ] Review yang di-reject tidak muncul di public API

**Dependencies:** #36, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `ReviewResource` untuk moderasi review. Tab: Pending (default), Approved, Rejected. Bulk action: Approve (set approved_by = auth()->id(), approved_at = now()) dan Reject. Individual view dengan foto. Delete hanya untuk super_admin. Filter: destination_id, rating, date range."

---

## Issue #41

**Title:** `[REVIEW] Filament: Pin/unpin review (destinasi & global)`

**Objective:**
Admin bisa pin review terbaik ke halaman destinasi (maks 10) dan landing page (maks 10 global).

**Scope:**

- Tambahkan actions `PinToDestination`, `UnpinFromDestination`, `PinToGlobal`, `UnpinFromGlobal` di `ReviewResource`
- Buat `ReviewPinService` dengan method:
    - `pinToDestination(Review $review): void` → cek count, throw exception jika ≥ 10
    - `pinToGlobal(Review $review): void` → cek count global, throw exception jika ≥ 10
    - `unpin(Review $review, string $type): void`
- Actions hanya muncul untuk review dengan `status = 'approved'`
- Visual indicator di table: ikon pin jika is_pinned

**Acceptance Criteria:**

- [ ] Pin destinasi ke-11 → Filament notification error "Maksimal 10 review per destinasi sudah tercapai"
- [ ] Pin global ke-11 → error "Maksimal 10 review global sudah tercapai"
- [ ] Unpin berfungsi tanpa batasan
- [ ] Satu review bisa di-pin keduanya (global + destinasi) secara bersamaan
- [ ] Ikon pin tampil di kolom table untuk review yang dipinned
- [ ] Hanya review `approved` yang bisa di-pin

**Dependencies:** #40

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat `ReviewPinService` di Laravel 11 dengan method `pinToDestination(Review)`, `pinToGlobal(Review)`, dan `unpin(Review, type)`. Batasan: maks 10 per destinasi dan maks 10 global. Tambahkan Filament 3 Actions ke ReviewResource. Hanya review approved yang bisa di-pin. Tunjukkan cara tambahkan ikon pin sebagai indikator visual di Filament table."

---

---

# 📁 MODUL 8 — Promo & Event

---

## Issue #42

**Title:** `[PROMO] Filament Resource: Promos CRUD`

**Objective:**
Admin Konten bisa kelola promo dan event untuk carousel landing page.

**Scope:**

- Filament Resource `PromoResource`
- Form: title, description, image (upload ke Cloudinary folder `promos/`), external_url, is_active (Toggle), start_date (Date), end_date (Date)
- Validasi: end_date harus setelah start_date
- Table: title, status badge (aktif/tidak aktif/kadaluarsa), start_date, end_date
- Status badge logic: aktif (is_active=1 + dalam range), tidak aktif (is_active=0), kadaluarsa (sudah lewat end_date)
- Observer: hapus gambar dari Cloudinary saat promo dihapus

**Acceptance Criteria:**

- [ ] CRUD promo berfungsi
- [ ] Upload gambar ke Cloudinary folder `promos/`
- [ ] Status badge menampilkan kondisi yang benar berdasarkan waktu
- [ ] Validasi: end_date > start_date
- [ ] Hapus promo → gambar Cloudinary ikut terhapus
- [ ] `Promo::active()` scope sesuai ERD: `is_active=1 AND NOW() BETWEEN start_date AND end_date`

**Dependencies:** #19

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `PromoResource`. Form: title, description, gambar (Cloudinary folder 'promos/'), external_url, is_active toggle, start_date, end_date (validasi end > start). Status badge di tabel: 'Aktif' (is_active=1 dan dalam range tanggal), 'Nonaktif' (is_active=0), 'Kadaluarsa' (lewat end_date). Hapus promo → hapus gambar dari Cloudinary."

---

## Issue #43

**Title:** `[PROMO] API endpoint: active promos`

**Objective:**
React landing page bisa mengambil data promo aktif.

**Scope:**

- `GET /api/v1/promos` → return semua promo aktif (scope `active()`)
- Response: id, title, description, image_url, external_url, start_date, end_date
- Cache response 15 menit (hapus cache saat ada update promo)
- Tidak ada pagination (promo aktif biasanya sedikit)

**Acceptance Criteria:**

- [ ] Hanya promo dengan `is_active=1 AND NOW() BETWEEN start_date AND end_date` yang tampil
- [ ] Response di-cache 15 menit
- [ ] Update promo di Filament → cache invalidated
- [ ] Jika tidak ada promo aktif → return array kosong `{data: []}`

**Dependencies:** #42, #04

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat endpoint `GET /api/v1/promos` di Laravel 11 yang return promo aktif saja (scope: is_active=1 AND now() BETWEEN start_date AND end_date). Gunakan Laravel Cache::remember untuk cache 15 menit. Cache harus di-invalidate saat PromoResource di Filament update/create/delete data. Gunakan Observer atau Model event."

---

---

# 📁 MODUL 9 — Trip & Tour Guide Module

---

## Issue #44

**Title:** `[TRIP] Filament Resource: Trip Packages CRUD + relasi destinasi & guide`

**Objective:**
Admin Konten bisa kelola paket trip lengkap dengan destinasi dan guide yang terlibat.

**Scope:**

- Filament Resource `TripPackageResource`
- Form: name, description, price (nullable), image (Cloudinary folder `packages/`), is_active
- Sub-section "Destinasi dalam Paket": Repeater untuk `trip_package_destinations` dengan select destination + sort_order
- Sub-section "Guide": CheckboxList atau Repeater untuk `trip_package_guides`
- Relasi via pivot tables (sync pada save)
- Observer: hapus gambar Cloudinary saat package dihapus

**Acceptance Criteria:**

- [ ] CRUD trip package berfungsi
- [ ] Bisa attach multiple destinasi dengan urutan drag-and-drop (sort_order)
- [ ] Bisa attach multiple guide via checkbox
- [ ] Simpan trip package → data pivot `trip_package_destinations` dan `trip_package_guides` tersinkron
- [ ] Hapus destinasi dari paket → record pivot terhapus

**Dependencies:** #20, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 Resource `TripPackageResource`. Form punya dua sub-section: (1) Repeater untuk destinasi yang di-attach (select destination_id + sort_order, sortable drag-drop), (2) CheckboxList untuk guide. Pivot tables: `trip_package_destinations` dan `trip_package_guides`. Tunjukkan cara sync pivot saat save di Filament 3."

---

## Issue #45

**Title:** `[TRIP] API: Trip packages + guides untuk public website`

**Objective:**
React halaman /paket bisa menampilkan semua paket trip aktif dengan destinasi dan guide.

**Scope:**

- `GET /api/v1/trip-packages` → list paket aktif dengan eager load destinations + guides
- `GET /api/v1/trip-packages/{id}` → detail paket
- `GET /api/v1/guides` → list guide aktif
- Response termasuk: destinasi (urut sort_order), guide (nama, foto, bio)
- Cache list 30 menit

**Acceptance Criteria:**

- [ ] Destinasi dalam paket urut berdasarkan `sort_order`
- [ ] Hanya paket `is_active=true` yang tampil
- [ ] Hanya guide `is_active=true` yang tampil
- [ ] Response tidak mengandung data internal (cloudinary_public_id tidak perlu di-expose)

**Dependencies:** #44, #04

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat 3 API endpoints: GET /api/v1/trip-packages (list aktif), GET /api/v1/trip-packages/{id} (detail), GET /api/v1/guides (list guide aktif). Eager load destinations (urut sort_order) dan guides. Gunakan Laravel API Resource untuk transform response. Cache list 30 menit. Jangan expose cloudinary_public_id ke publik."

---

## Issue #46

**Title:** `[TRIP] React halaman /paket dan /panduan`

**Objective:**
Pengunjung bisa melihat daftar paket trip dan profil guide.

**Scope:**

- Halaman `/paket`: grid kartu trip package (gambar, nama, harga, deskripsi singkat, destinasi list, tombol "Tanya via WhatsApp")
- Halaman `/panduan`: grid profil guide (foto, nama, bio singkat, pengalaman, tombol "Chat WhatsApp")
- WhatsApp link untuk paket: `wa.me/{global_whatsapp}?text=Halo, saya tertarik dengan paket {nama_paket}`
- WhatsApp link tidak ada di data API — dibangun di frontend dari `global_whatsapp` setting
- Loading skeleton saat fetch
- Empty state jika tidak ada data

**Acceptance Criteria:**

- [ ] Kartu trip package tampil dengan destinasi yang included
- [ ] Tombol WhatsApp membuka wa.me dengan pesan template
- [ ] Profil guide tampil dengan foto (fallback avatar jika tidak ada)
- [ ] Mobile-responsive
- [ ] Loading skeleton tampil saat fetching

**Dependencies:** #45, #05

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat dua halaman React: /paket dan /panduan. Fetch data dari API endpoints. Halaman /paket: kartu trip package dengan gambar, nama, harga, deskripsi, list destinasi, dan tombol WhatsApp. Halaman /panduan: grid profil guide dengan foto + fallback avatar. WA link dibangun dari nomor global_whatsapp (dari settings API). Sertakan loading skeleton dan empty state."

---

---

# 📁 MODUL 10 — Reports & Dashboard

---

## Issue #47

**Title:** `[REPORT] Filament Dashboard widgets: ringkasan hari ini`

**Objective:**
Dashboard Filament menampilkan metrics utama yang relevan per role.

**Scope:**

- Widget `TotalVisitorsToday`: query `visitors` dengan `visited_at >= today`
- Widget `PendingBookings`: count bookings dengan status `pending`
- Widget `PendingReviews`: count reviews dengan status `pending`
- Widget `RevenueThisMonth`: sum dari `daily_visits.revenue` bulan ini
- Semua widget di-scope: Super Admin dan Admin Konten lihat semua; Pimpinan dan Anggota Pokdarwis lihat hanya statistik (bukan pending)
- Petugas Lapangan tidak lihat dashboard widgets ini

**Acceptance Criteria:**

- [ ] 4 widget tampil di dashboard Filament
- [ ] Data realtime (tidak di-cache, query langsung)
- [ ] Widget pending tidak tampil untuk role pimpinan/anggota_pokdarwis
- [ ] Loading state ditangani Filament

**Dependencies:** #32, #31, #40, #13

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat 4 Filament 3 Widgets untuk dashboard: TotalVisitorsToday, PendingBookings, PendingReviews, RevenueThisMonth. Widget PendingBookings dan PendingReviews hanya tampil untuk super_admin dan admin_konten. Semua widget query langsung ke DB (tidak cache). Tunjukkan cara implementasi dengan `StatsOverviewWidget` di Filament 3."

---

## Issue #48

**Title:** `[REPORT] Filament: Laporan kunjungan & pendapatan (tabel)`

**Objective:**
Pimpinan dan Admin bisa melihat laporan tabel kunjungan dan pendapatan dengan filter periode.

**Scope:**

- Filament custom Page `ReportsPage`
- Filter: destination_id (multiselect), date range (bulan/tahun)
- Tabel 1: Kunjungan per hari per destinasi (dari `daily_visits`)
- Tabel 2: Summary per destinasi (total visitor, total revenue, total expense)
- Tabel 3: Sumber asal wisatawan (group by origin_category dari `visitors`)
- Tabel 4: Referral source breakdown (group by referral_source)
- Akses: semua role kecuali `petugas_lapangan`

**Acceptance Criteria:**

- [ ] Filter date range mengupdate semua tabel secara reaktif
- [ ] Tabel kunjungan harian tampil dengan benar
- [ ] Summary destinasi menampilkan total yang benar
- [ ] Breakdown origin_category dalam persentase
- [ ] Semua data sesuai dengan data yang diinput

**Dependencies:** #47

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Filament 3 custom Page `ReportsPage` dengan filter date range dan destination multiselect. Tampilkan 4 tabel: (1) daily_visits per hari, (2) summary per destinasi, (3) breakdown origin_category, (4) breakdown referral_source. Gunakan Livewire reactive properties untuk update tabel saat filter berubah. Akses untuk semua role kecuali petugas_lapangan."

---

## Issue #49

**Title:** `[REPORT] Export laporan ke Excel dan PDF`

**Objective:**
Pimpinan bisa export laporan dalam format Excel atau PDF.

**Scope:**

- Install `maatwebsite/excel` untuk export Excel
- Install `barryvdh/laravel-dompdf` untuk export PDF
- Export class `VisitReportExport` dan `VisitReportPdf`
- Tombol "Export Excel" dan "Export PDF" di `ReportsPage`
- Export menggunakan data yang sudah difilter (periode + destinasi yang dipilih)
- PDF template sederhana (tidak perlu desain kompleks)

**Acceptance Criteria:**

- [ ] Export Excel menghasilkan file `.xlsx` yang dapat dibuka
- [ ] Export PDF menghasilkan file `.pdf` yang dapat dicetak
- [ ] Data export sesuai dengan filter yang aktif
- [ ] Nama file: `laporan-kunjungan-{bulan}-{tahun}.xlsx`
- [ ] Tidak timeout di shared hosting (gunakan `queue` jika data > 1000 baris, tapi V1 cukup sync dulu)

**Dependencies:** #48

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan export Excel dan PDF di Filament 3 custom Page. Install maatwebsite/excel dan barryvdh/laravel-dompdf. Buat Export class yang menerima filter parameter (date range + destination_ids). Tambahkan dua tombol export di ReportsPage. Data yang diexport sesuai dengan filter aktif. Nama file: 'laporan-kunjungan-{bulan}-{tahun}'."

---

---

# 📁 MODUL 11 — Public API Layer

---

## Issue #50

**Title:** `[API] API Resource layer + Public API endpoints: destinations`

**Objective:**
API endpoints destinasi yang dikonsumsi React dengan struktur response yang konsisten.

**Scope:**

- Buat `DestinationResource` (API Resource, bukan Filament Resource)
- `GET /api/v1/destinations` → list aktif, 15 per halaman, include gambar pertama sebagai thumbnail
- `GET /api/v1/destinations/{id}` → detail + semua gambar + stats kunjungan (total dari daily_visits)
- Query parameter: `?type=camping` untuk filter
- Cache list 15 menit, cache detail 10 menit
- Response TIDAK boleh expose: `cloudinary_folder`, `cloudinary_public_id` (internal data)

**Acceptance Criteria:**

- [ ] Pagination 15 per halaman berfungsi
- [ ] Filter `?type=` berfungsi
- [ ] Detail destinasi menyertakan array `images` (url + sort_order)
- [ ] `total_visitors` di detail dihitung dari `sum(daily_visits.visitor_count)`
- [ ] Hanya destinasi `is_active = true` yang tampil
- [ ] Cache list di-invalidate saat ada perubahan destinasi di Filament

**Dependencies:** #11, #04

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat Laravel 11 API Resource `DestinationResource` dan endpoints GET /api/v1/destinations (paginated, filter type) dan GET /api/v1/destinations/{id} (detail). Include gambar (sort by sort_order) dan hitung total_visitors dari daily_visits. Cache list 15 menit, detail 10 menit. Jangan expose cloudinary_public_id atau cloudinary_folder di response."

---

## Issue #51

**Title:** `[API] Public API endpoints: reviews + settings`

**Objective:**
Endpoint review dan settings yang dibutuhkan React untuk landing page dan halaman review.

**Scope:**

- `GET /api/v1/reviews/pinned` → 10 review dengan `is_pinned_global=1, status=approved`
- `GET /api/v1/reviews?destination_id=&rating=&sort=` → semua review approved, paginated, filterable
- `GET /api/v1/settings/public` → return setting publik: village_name, tagline, global_whatsapp, social links, maps_url (TIDAK include cloudinary credentials)
- Cache pinned reviews 5 menit, settings 60 menit

**Acceptance Criteria:**

- [ ] `/reviews/pinned` return maks 10 review global pinned
- [ ] `/reviews` filter berdasarkan destination_id dan rating berfungsi
- [ ] `/reviews` sortable: `?sort=latest` atau `?sort=highest_rating`
- [ ] `/settings/public` TIDAK pernah mengandung cloudinary_api_key atau cloudinary_api_secret
- [ ] Review response menyertakan: reviewer_name, reviewer_city, rating, review_text, photo_url, destination_name

**Dependencies:** #50

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat API endpoints: GET /api/v1/reviews/pinned (10 review global pinned), GET /api/v1/reviews (semua approved, filter destination_id + rating, sort latest/highest), GET /api/v1/settings/public (setting publik saja, exclude credentials). Untuk settings public, whitelist explicit key yang boleh tampil. Cache masing-masing sesuai frekuensi perubahan."

---

---

# 📁 MODUL 12 — React Public Website

---

## Issue #52

**Title:** `[REACT] Komponen dasar: Layout, Navbar, Footer`

**Objective:**
Struktur layout dasar public website yang konsisten di semua halaman.

**Scope:**

- Komponen `Layout` yang wrap semua halaman
- `Navbar`: logo/nama desa (dari settings), link menu (Beranda, Destinasi, Paket, Review, Tentang)
- `Footer`: nama desa, tagline, social media links, copyright
- Data desa (nama, tagline, social links) difetch dari `GET /api/v1/settings/public`
- Responsive navbar (hamburger menu di mobile)
- Custom hook `usePublicSettings()` untuk data settings

**Acceptance Criteria:**

- [ ] Navbar tampil di semua halaman
- [ ] Nama desa dan tagline dari API (bukan hardcode)
- [ ] Hamburger menu berfungsi di mobile
- [ ] Footer menampilkan social links dari settings
- [ ] Active link di navbar highlight berdasarkan current route

**Dependencies:** #51, #05

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat komponen React: Layout (wrapper), Navbar (responsive dengan hamburger), Footer. Data nama desa + social links dari API `GET /api/v1/settings/public`. Buat custom hook `usePublicSettings()` dengan React Query. Navbar harus highlight active route. Tailwind CSS untuk styling."

---

## Issue #53

**Title:** `[REACT] Landing page: Hero + Promo carousel + Destinasi highlights`

**Objective:**
Landing page menampilkan konten dinamis dari API.

**Scope:**

- Section Hero: nama desa besar, tagline, CTA button "Jelajahi Destinasi"
- Section Promo carousel (kondisional: tampil hanya jika ada promo aktif)
- Section Destinasi: grid 6 destinasi pertama (thumbnail, nama, tipe badge, tombol detail)
- CTA WhatsApp global: "Butuh bantuan? Hubungi kami via WhatsApp"
- Loading skeleton untuk semua section
- Fetch parallel: promos + destinations dalam satu useEffect/query

**Acceptance Criteria:**

- [ ] Hero menampilkan village_name dan tagline dari settings
- [ ] Promo carousel auto-scroll (opsional: manual arrow juga)
- [ ] Grid destinasi: 2 kolom mobile, 3 kolom tablet, 3 kolom desktop
- [ ] Section promo tidak tampil jika `/api/v1/promos` return array kosong
- [ ] Tombol CTA WhatsApp menggunakan `global_whatsapp` dari settings

**Dependencies:** #52, #43, #50

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat landing page React dengan: Hero section (village_name + tagline dari API), Promo carousel (kondisional jika ada data dari /api/v1/promos), Grid destinasi highlights (6 pertama dari /api/v1/destinations). Fetch data parallel dengan React Query. Carousel auto-scroll. Loading skeleton tiap section. CTA WhatsApp dari global_whatsapp setting."

---

## Issue #54

**Title:** `[REACT] Landing page: Review slider (pinned global)`

**Objective:**
Section testimonial di landing page menampilkan review yang dipinned admin.

**Scope:**

- Section "Testimonial" di landing page (setelah destinasi)
- Fetch dari `GET /api/v1/reviews/pinned`
- Tampilan slider: nama, asal kota, rating bintang, ulasan (truncated 150 char), foto (opsional), nama destinasi
- Auto-scroll setiap 4 detik
- Tombol "Lihat Semua Review" → `/reviews`
- Kondisional: section tidak tampil jika tidak ada pinned review

**Acceptance Criteria:**

- [ ] Slider auto-scroll dengan interval 4 detik
- [ ] Manual navigation (dots/arrows)
- [ ] Foto review tampil jika ada, default avatar jika tidak ada
- [ ] Rating tampil sebagai bintang (1-5)
- [ ] Link "Lihat Semua" mengarah ke `/reviews`
- [ ] Section tidak tampil jika data kosong

**Dependencies:** #53

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat komponen React ReviewSlider untuk landing page. Data dari GET /api/v1/reviews/pinned. Auto-scroll setiap 4 detik dengan manual navigation. Tiap slide: foto (atau avatar default), nama wisatawan, asal, rating bintang, ulasan (maks 150 char, truncate), nama destinasi. Kondisional: tidak render jika data kosong."

---

## Issue #55

**Title:** `[REACT] Halaman /destinasi: list dengan filter`

**Objective:**
Pengunjung bisa browse semua destinasi dengan filter tipe.

**Scope:**

- Halaman `/destinasi`
- Fetch dari `GET /api/v1/destinations` (paginated)
- Filter pills berdasarkan `destination_type` (semua, camping, air, edukasi, dll)
- Grid kartu: gambar thumbnail, nama, tipe badge, fee masuk, tombol "Lihat Detail"
- Pagination (next/prev atau infinite scroll — pilih infinite scroll untuk mobile UX)
- Skeleton loading

**Acceptance Criteria:**

- [ ] Filter pills mengupdate grid tanpa reload halaman
- [ ] Pagination/infinite scroll berfungsi
- [ ] Kartu destinasi menampilkan gambar dengan fallback jika tidak ada gambar
- [ ] "Rp 0" atau "Gratis" tampil jika entry_fee = null
- [ ] Mobile responsive 2 kolom, desktop 3 kolom

**Dependencies:** #52, #50

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat halaman React /destinasi. Fetch dari GET /api/v1/destinations dengan filter ?type=. Filter pills di atas grid. Infinite scroll (atau pagination sederhana). Kartu destinasi: gambar thumbnail, nama, tipe badge, entry_fee ('Gratis' jika null), tombol detail. Skeleton loading. Mobile 2 kolom, desktop 3 kolom."

---

## Issue #56

**Title:** `[REACT] Halaman /destinasi/:id: detail + foto + review`

**Objective:**
Halaman detail destinasi dengan semua informasi dan review yang approved.

**Scope:**

- Fetch dari `GET /api/v1/destinations/{id}` + `GET /api/v1/reviews?destination_id={id}`
- Foto slider (swipeable di mobile)
- Info: nama, deskripsi, fasilitas, entry_fee, parking_fee, rental_price, tipe
- Google Maps embed dari `maps_url`
- Tombol "Booking via WhatsApp": buka `wa.me/{destination.whatsapp || global_whatsapp}?text=Halo, saya ingin booking di {nama_destinasi}`
- Section review: filter rating, urut terbaru/tertinggi, pagination

**Acceptance Criteria:**

- [ ] Foto slider swipeable di mobile
- [ ] Maps embed tampil jika `maps_url` ada
- [ ] "Fee: Gratis" jika null, sinon tampilkan format Rupiah
- [ ] WhatsApp tombol menggunakan nomor WA destinasi (fallback ke global)
- [ ] Review section hanya tampil review `status = approved`
- [ ] Review pinned tampil di urutan pertama

**Dependencies:** #55, #51

**Complexity:** 🔴 Hard

**Suggested AI Prompt Focus:**

> "Buat halaman React /destinasi/:id. Fetch detail destinasi + reviews (filter destination_id). Foto slider dengan swipe mobile. Format currency Rupiah untuk fee (null = 'Gratis'). WhatsApp booking button: gunakan destination.whatsapp_number atau fallback ke global_whatsapp dari settings. Review section: filter rating, sort latest/highest, pagination. Review pinned ditampilkan duluan."

---

## Issue #57

**Title:** `[REACT] Halaman /reviews: semua review publik`

**Objective:**
Halaman yang menampilkan semua review dari semua destinasi dengan filter.

**Scope:**

- Fetch dari `GET /api/v1/reviews`
- Filter: destination (select dropdown), rating (1-5 star filter)
- Sort: Terbaru, Rating Tertinggi
- Kartu review: foto, nama, asal, rating, ulasan, nama destinasi, tanggal
- Pagination
- Empty state jika tidak ada review

**Acceptance Criteria:**

- [ ] Filter destination dan rating berfungsi
- [ ] Sort berfungsi
- [ ] Pagination berfungsi
- [ ] Foto review tampil jika ada
- [ ] Empty state informatif

**Dependencies:** #51, #52

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Buat halaman React /reviews. Fetch dari GET /api/v1/reviews dengan query params filter (destination_id, rating) dan sort (latest, highest_rating). Filter dropdown destinasi diisi dari API. Kartu review: foto opsional, nama, asal kota, bintang, ulasan, nama destinasi. Pagination. Empty state jika kosong."

---

## Issue #58

**Title:** `[REACT] Halaman /review: form submit review publik (token-based)`

**Objective:**
Wisatawan bisa mengisi dan submit review via token dari WhatsApp tanpa login.

**Scope:**

- Route `/review?token={token}` (atau `/review/{token}`)
- Saat load: `GET /api/v1/review/{token}` untuk validasi dan pre-fill
- Jika token tidak valid/expired/used → tampilkan halaman error yang ramah
- Form: reviewer_name (pre-filled, editable), reviewer_city (pre-filled, editable), rating bintang (interactive, wajib), review_text (textarea, opsional), photo upload (opsional)
- Submit → `POST /api/v1/review/{token}/submit`
- Loading state saat submit
- Halaman sukses setelah submit

**Acceptance Criteria:**

- [ ] Token invalid → halaman error dengan pesan yang jelas sesuai alasan (expired/used/not_found)
- [ ] reviewer_name dan reviewer_city pre-filled dari API, bisa diedit
- [ ] Rating bintang interactive (klik untuk pilih)
- [ ] Upload foto opsional dengan preview sebelum submit
- [ ] Loading spinner saat submit
- [ ] Halaman sukses setelah submit berhasil
- [ ] Setelah sukses, tombol kembali ke halaman destinasi

**Dependencies:** #36, #52

**Complexity:** 🔴 Hard

**Suggested AI Prompt Focus:**

> "Buat halaman React /review?token={token}. On load: GET /api/v1/review/{token} — jika tidak valid tampilkan error page sesuai reason (expired, used, not_found). Jika valid: pre-fill form dengan visitor_name dan visitor_city (editable). Rating bintang interaktif. Upload foto dengan preview. Submit ke POST /api/v1/review/{token}/submit. Loading state dan sukses page."

---

## Issue #59

**Title:** `[REACT] Halaman /tentang: info Pokdarwis + kontak`

**Objective:**
Halaman statis tentang Pokdarwis dengan informasi kontak dari settings.

**Scope:**

- Fetch data dari `GET /api/v1/settings/public`
- Konten: deskripsi Pokdarwis (bisa hardcode atau dari settings), nomor WA, social media links
- Tombol WhatsApp langsung
- Link social media (Instagram, Facebook, TikTok)
- Maps embed (dari `google_maps_embed_url` settings)

**Acceptance Criteria:**

- [ ] Nomor WA dari `global_whatsapp` settings
- [ ] Social media links dari settings (kondisional: tidak tampil jika kosong)
- [ ] Maps embed tampil jika `google_maps_embed_url` diset
- [ ] Mobile responsive

**Dependencies:** #52, #51

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Buat halaman React /tentang. Data kontak dari GET /api/v1/settings/public (global_whatsapp, social_instagram, social_facebook, social_tiktok, google_maps_embed_url). Tampilkan tombol WA, link social media (hanya jika ada isinya), dan Google Maps embed. Sederhana, mobile-friendly."

---

---

# 📁 MODUL 13 — QA & Polish

---

## Issue #60

**Title:** `[QA] Rate limiting & security hardening API`

**Objective:**
Proteksi API publik dari abuse dan brute force.

**Scope:**

- Rate limit `POST /api/v1/review/{token}/submit`: 3 request/10 menit per IP
- Rate limit semua endpoint `/api/v1/*`: 60 request/menit per IP
- Tambahkan middleware `ForceJsonResponse` untuk semua API routes (selalu return JSON, bukan HTML error)
- Header security: `X-Content-Type-Options`, `X-Frame-Options`
- Validasi environment: pastikan `APP_KEY` ter-set sebelum deploy

**Acceptance Criteria:**

- [ ] Submit review lebih dari 3x dalam 10 menit dari IP sama → 429 response
- [ ] API selalu return JSON (tidak pernah HTML error page)
- [ ] Rate limit headers tampil di response (`X-RateLimit-Limit`, `X-RateLimit-Remaining`)
- [ ] Error 500 di API return JSON bukan HTML stack trace

**Dependencies:** #36, #51

**Complexity:** 🟢 Easy

**Suggested AI Prompt Focus:**

> "Implementasikan rate limiting dan security hardening untuk Laravel 11 API. Rate limit: /api/v1/review/{token}/submit (3 req/10 menit per IP), semua /api/\* (60 req/menit per IP). Buat middleware ForceJsonResponse yang wrap semua API routes. Tambahkan header security X-Content-Type-Options dan X-Frame-Options."

---

## Issue #61

**Title:** `[QA] Cloudinary fallback + error handling global`

**Objective:**
Sistem tidak crash jika Cloudinary error atau credential belum diset.

**Scope:**

- `CloudinaryService`: jika credential kosong, throw `CloudinaryNotConfiguredException` dengan pesan yang jelas
- Di Filament upload form: catch exception, tampilkan Filament notification error
- Di API submit review: jika Cloudinary gagal saat upload foto, simpan review TANPA foto (jangan fail seluruh submission)
- Global exception handler: log semua error Cloudinary ke `storage/logs/cloudinary.log`

**Acceptance Criteria:**

- [ ] Credential belum diset → error message jelas di Filament: "Cloudinary belum dikonfigurasi. Pergi ke Settings untuk mengisi API key."
- [ ] Submit review dengan foto gagal upload Cloudinary → review tetap tersimpan tanpa foto
- [ ] Error Cloudinary di-log ke file terpisah
- [ ] Aplikasi tidak white screen saat Cloudinary error

**Dependencies:** #03, #16

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan graceful error handling untuk Cloudinary di Laravel 11. Jika credential belum diset, CloudinaryService throw exception dengan pesan jelas. Di Filament: catch dan tampilkan notification. Di API review submit: jika upload foto gagal, lanjutkan simpan review tanpa foto (jangan fail submission). Log semua error Cloudinary ke file terpisah."

---

## Issue #62

**Title:** `[QA] React: error boundaries + 404 + loading states global`

**Objective:**
React app punya error handling yang baik dan tidak crash saat API error.

**Scope:**

- React Error Boundary di root app
- Komponen `ErrorPage` untuk halaman error (404, 500, network error)
- React Query global error handler: jika API 5xx, tampilkan toast "Terjadi kesalahan, coba lagi"
- Komponen `PageSkeleton` untuk loading state per halaman
- 404 route: halaman not found yang ramah dengan link kembali ke beranda

**Acceptance Criteria:**

- [ ] Error Boundary mencegah full app crash
- [ ] API error → toast notifikasi (bukan console.error saja)
- [ ] Halaman 404 tampil untuk route tidak dikenal
- [ ] Loading skeleton tampil selama fetch API
- [ ] Network error (offline) → pesan khusus, bukan blank screen

**Dependencies:** #52

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Implementasikan error handling di React 18 + React Query: (1) Error Boundary di root, (2) React Query global onError yang tampilkan toast, (3) Komponen 404 page, (4) PageSkeleton component. Gunakan react-hot-toast atau komponen notifikasi sendiri. Handle network error (fetch failed) dengan pesan yang user-friendly."

---

## Issue #63

**Title:** `[QA] Optimasi performa shared hosting: cache + query optimization`

**Objective:**
Performa aplikasi dapat diterima di shared hosting dengan optimasi yang tepat.

**Scope:**

- Tambahkan index database yang belum ada: `visitors.created_at`, `bookings.created_at`, `bookings.booking_status`, `daily_visits.date`
- `php artisan route:cache` dan `config:cache` dalam deployment notes
- Eager loading: pastikan tidak ada N+1 query di semua API endpoints (gunakan Laravel Debugbar di dev)
- Cache halaman API yang berat (reviews list, destinations list) dengan invalidation yang tepat
- Kompres gambar sebelum upload ke Cloudinary: `quality: 80` di `CloudinaryService::upload()`

**Acceptance Criteria:**

- [ ] Tidak ada N+1 query di API endpoints (verifikasi dengan Debugbar)
- [ ] Index database terdefinisi di migration final
- [ ] `CloudinaryService::upload()` mengupload dengan quality 80
- [ ] Response time API list destinations < 500ms di lokal
- [ ] Deployment checklist: route:cache, config:cache, optimize:clear

**Dependencies:** #61

**Complexity:** 🟡 Medium

**Suggested AI Prompt Focus:**

> "Audit dan optimasi performa Laravel 11 untuk shared hosting. Tambahkan index migration: visitors.created_at, bookings.booking_status+created_at, daily_visits.date. Periksa N+1 query di API endpoints dan perbaiki dengan eager loading. Tambahkan quality:80 di Cloudinary upload. Buat deployment checklist (artisan commands yang perlu dijalankan)."

---

---

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# RECOMMENDED DEVELOPMENT ORDER

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

## Week 1 — Foundation + Database + Auth

```
#01 → #02 → #03 → #04 → #05          (Foundation & Setup)
#06 → #07 → #08 → #09 → #10 → #11   (Database Migrations)
#12 → #13 → #14                       (Auth & Roles)
#15 → #16                             (Settings)
```

## Week 2 — Destinations + Visitors + Booking

```
#17 → #18 → #19 → #20 → #21 → #22   (Destinations: CRUD + single upload + gallery + reorder + observer + guides)
#23 → #24 → #25                       (Visitors)
#26 → #27 → #28 → #29                 (Booking: basic CRUD + reactive mode + code gen + status/verify)
#30 → #31 → #32                       (Booking: edit status + lapangan verify + daily visits)
```

## Week 3 — Review System + Content Modules

```
#33 → #34 → #35 → #36               (Review: token service + kirim link + validate API + basic submit)
#37 → #38 → #39                       (Review: token logic + photo upload + transaction hardening)
#40 → #41                             (Review: moderation + pin)
#42 → #43                             (Promo)
#44 → #45 → #46                       (Trip & Guide)
#47 → #48 → #49                       (Reports)
```

## Week 4 — Public API + React Website

```
#50 → #51                             (API layer)
#52 → #53 → #54                       (Landing page)
#55 → #56 → #57 → #58 → #59          (Halaman lainnya)
#60 → #61 → #62 → #63                 (QA & Polish)
```

---

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# MILESTONE V1 — MVP PATH

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

### Milestone 0: "System Alive" (Issues #01–#05)

> Laravel jalan, Filament aktif, React siap, API terkoneksi

### Milestone 1: "Database Ready" (+ #06–#16)

> Semua tabel ada, seeder jalan, admin bisa login, settings bisa diubah

### Milestone 2: "Core Operations" (+ #17–#32)

> Destinasi dikelola lengkap, wisatawan bisa diinput, booking bisa dibuat & diverifikasi

### Milestone 3: "Review System Live" (+ #33–#41)

> Token digenerate, review disubmit (4 tahap: basic → token logic → foto → hardening), moderasi & pin berfungsi

### Milestone 4: "Complete Backend" (+ #42–#49)

> Promo, Trip, Guide, Laporan semua berfungsi — Filament feature-complete

### Milestone 5: "Public Website Live" (+ #50–#59)

> React website online, semua halaman bisa diakses pengunjung

### Milestone 6: "Production Ready" (+ #60–#63)

> Rate limit, error handling, performa — siap deploy ke production

---

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# TOP 5 RISKY MODULES

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

### 🔴 RISK 1: Issue #39 — Review Transaction Hardening (Race Condition)

Dua request bersamaan dengan token yang sama bisa lolos validasi `is_used` sebelum keduanya set `true`.
**Mitigasi wajib:** `DB::transaction() + lockForUpdate()` pada query token sebelum insert review. Dipecah menjadi issue terpisah (#39) agar bisa dikerjakan setelah basic submit (#36) sudah stabil.

### 🟡 RISK 2: Issue #19 — Multiple Images Upload (Gallery)

Shared hosting punya timeout pendek dan memory limit ketat. Upload batch bisa timeout.
**Mitigasi:** Upload satu per satu via AJAX (bukan batch), progress indicator, timeout setting di `php.ini` lokal. Issue #18 (single upload) dikerjakan dulu sebagai fondasi sebelum ke #19.

### 🟡 RISK 3: Issue #27 — Booking Reactive Form (Visitor vs Guest Mode)

Form dengan dua mode yang saling exclusive membutuhkan reactive Filament form. Setelah dipecah, issue ini hanya fokus pada reactive logic — tidak tercampur code generator atau status workflow.
**Mitigasi:** Gunakan Filament `$get()` reactive callbacks. Kerjakan setelah #26 (basic CRUD) sudah jalan dan teruji.

### 🟡 RISK 4: Issue #56 — Detail Destinasi (WhatsApp Fallback)

Nomor WA bisa dari destinasi atau global settings — logic fallback harus benar.
**Mitigasi:** Buat utility function `getBookingWhatsApp(destination)` yang handle fallback dengan jelas.

### 🟡 RISK 5: Issue #48 — Laporan (Query Performa)

Query agregasi bisa lambat di shared hosting jika data besar tanpa index yang tepat.
**Mitigasi:** Index pada `daily_visits(destination_id, date)`, `visitors(created_at)`, limit date range maksimal 1 tahun.

---

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# ESTIMASI TOTAL COMPLEXITY

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

| Complexity | Jumlah Issue | Estimasi per Issue | Total             |
| ---------- | ------------ | ------------------ | ----------------- |
| 🟢 Easy    | 19 issue     | 30–60 menit        | ~15 jam           |
| 🟡 Medium  | 38 issue     | 1–2 jam            | ~57 jam           |
| 🔴 Hard    | 6 issue      | 2–3 jam            | ~15 jam           |
| **Total**  | **63 issue** |                    | **~87 jam kerja** |

> Catatan: 3 issue Hard sebelumnya (booking form, multi-image, review submit) sudah dipecah menjadi issue Medium/Easy.
> Dengan AI-assisted development, estimasi ini lebih pendek ~40%.
> Efektif: **~16 hari kerja** dengan tempo 5 jam produktif per hari.

---

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# REKOMENDASI ISSUE PERTAMA UNTUK MULAI

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

**Mulai dengan Issue #06 (Migration: users)** — bukan #01.

**Alasan:**

1. Setup Laravel (#01–#05) biasanya sudah familiar, bisa dikerjakan cepat tanpa banyak AI assistance
2. Issue #06 adalah titik di mana PRD + ERD pertama kali "menjadi kode" — paling representatif
3. Dari #06, langsung lanjut #07 dan #08 dalam satu sesi — migration mudah dirangkai
4. Setelah #06–#11 selesai, Anda punya fondasi database yang solid dan bisa verifikasi ERD sebelum lanjut

**Prompt AI yang disarankan untuk Issue #06:**

```
Saya membangun sistem POKDARWIS (Sistem Informasi Wisata Desa).
Tech stack: Laravel 11, Filament 3, MySQL.

Bantu saya modifikasi default users migration Laravel 11 untuk menambahkan:
- role ENUM('super_admin','admin_konten','pimpinan','anggota_pokdarwis','petugas_lapangan') NOT NULL
- is_active TINYINT(1) DEFAULT 1

Selain itu:
1. Update model User: $fillable, $hidden (password, remember_token), $casts
2. Buat UserFactory dengan state per role (superAdmin(), adminKonten(), petugasLapangan(), dll)
3. Buat UserSeeder yang membuat 1 super admin dari env variable

Jangan overengineering. Ini untuk solo developer dengan shared hosting.
```
