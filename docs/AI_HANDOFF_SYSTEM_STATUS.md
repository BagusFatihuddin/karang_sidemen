# AI Handoff: Status Sistem POKDARWIS Karang Sidemen

Dokumen ini dibuat sebagai transfer knowledge untuk AI/engineer berikutnya yang akan melanjutkan pengembangan sistem POKDARWIS Karang Sidemen. Baca dokumen ini bersama `docs/FRONTEND_FILAMENT_ROADMAP.md`.

## Gambaran Sistem

Sistem ini adalah website desa wisata dan admin operasional untuk **Desa Wisata Karang Sidemen**, dikelola oleh **POKDARWIS Karang Sidemen**.

Arsitektur saat ini:

- Backend: Laravel 13, PHP 8.4, MySQL.
- Admin: Filament v5 di route `/admin`.
- Frontend publik: React/Vite di folder `pokdarwis-public`.
- API publik: Laravel route `routes/api_v1.php`.
- Media upload: Cloudinary.
- Konten utama: database-driven.

Identitas produk yang harus dijaga:

- Website publik harus terasa cinematic, hidup, immersive, dan visual-first.
- Homepage bukan website Datu Bajang saja. Identitas utama adalah Desa Wisata Karang Sidemen.
- Datu Bajang hanya salah satu destinasi/pengalaman di dalam desa.
- Admin harus mudah dipakai pengelola wisata, bukan terasa seperti CRUD mentah.

## Status Publik Frontend

Yang sudah berjalan:

- Route `/` memakai homepage cinematic dari `ExperienceConceptPage`.
- Route `/experience-concept` tetap tersedia sebagai backup/preview.
- Route publik utama:
  - `/destinasi`
  - `/destinasi/:id`
  - `/paket`
  - `/panduan`
  - `/reviews`
  - `/review/:token`
  - `/tentang`
  - `/event/:id`
- Halaman publik sudah punya layout, navbar, footer, dan floating WhatsApp.
- Floating WhatsApp muncul di halaman utama publik, tetapi disembunyikan di detail destinasi dan halaman token review.
- Footer sudah memuat identitas pengembang yang dikunci di kode:
  - Bagus Fatihuddin Abul Yasin
  - Muhammad Said
  - Universitas Bumogora
  - 2026

Homepage cinematic:

- Sudah punya hero cinematic.
- Sudah punya scroll zoom/portal reveal.
- Sudah punya horizontal immersive interruption.
- Sudah memakai destinasi database untuk kartu dan storytelling.
- Event aktif bisa muncul sebagai spotlight di hero.
- Hero image homepage sudah bisa dikelola dari admin settings.

Halaman publik lain:

- `/destinasi` sudah visual-first listing.
- `/destinasi/:id` sudah punya hero, gallery, highlights, CTA, map, dan review destinasi.
- `/paket` sudah dipoles sebagai experience cards.
- `/panduan` sudah dipoles sebagai profil guide lokal.
- `/reviews` sudah dipoles sebagai halaman testimoni visual.
- `/tentang` sudah memuat narasi desa, POKDARWIS, map, dan struktur organisasi opsional.

## Status Admin Filament

Admin sudah mulai diarahkan agar tidak terlalu default.

Yang sudah ada:

- Brand panel: `POKDARWIS Karang Sidemen`.
- Navigation group:
  - Konten Wisata
  - Pengunjung
  - Operasional
  - Laporan
  - Sistem
- Dashboard stats dibuat lebih operasional.
- Dashboard Quick Actions tersedia sesuai role.
- Resource utama:
  - Destinasi
  - Paket Wisata
  - Guide Lokal
  - Event (masih memakai tabel/model `promos`)
  - Wisatawan
  - Review
  - Booking
  - Daily Visit
  - User
- Review admin sudah punya:
  - approve
  - approve + pin destinasi
  - approve + pin global
  - pin/unpin destinasi
  - pin/unpin global
  - reject otomatis melepas pin
  - filter status, pinned global, pinned destinasi

Settings admin sudah dipisah menjadi hub:

- General Settings
- Brand / Logo
- Social Media
- Homepage Cinematic
- Halaman Destinasi
- Halaman Paket
- Halaman Panduan
- Halaman Review
- Halaman Tentang
- Footer
- Integrasi Sistem

Catatan: route lama `admin/settings/page-media` masih ada karena class `PageMediaSettingsPage` masih ditemukan Filament, tetapi hub settings sudah diarahkan ke halaman-halaman setting baru yang lebih spesifik.

## Sistem Media yang Sudah Bisa Dikelola Admin

Sudah bisa dikelola:

- Brand/logo navbar dan footer.
- Hero homepage.
- Final CTA homepage.
- Hero destinasi.
- Hero paket.
- Empty state paket.
- Fallback image kartu paket.
- Hero panduan.
- Empty state panduan.
- Gambar card "Kenapa pakai guide?" di halaman panduan.
- Hero review.
- Hero about.
- About story image.
- Struktur organisasi POKDARWIS opsional.
- Footer CTA image.
- Destination gallery dan cover via sort order.
- Foto guide.
- Foto paket.
- Foto event.
- Foto review dari user.

Belum ideal:

- Belum ada Homepage Builder bebas untuk section custom.
- Belum ada validasi/panduan ukuran gambar yang matang di admin.
- Foto asli Karang Sidemen masih perlu diganti manual.
- Beberapa fallback masih memakai gambar Unsplash.

## Data dan Konten

Seeder Karang Sidemen sudah dibuat di:

- `database/seeders/KarangSidemenTourismSeeder.php`

Data destinasi sudah diarahkan ke Karang Sidemen, bukan Datu Bajang saja. Contoh destinasi yang dipakai:

- Danau Biru
- Penimproh Datu Bajang
- Air Terjun Batu Belah
- Camping Ground Antih Tuselak
- Tahura Nuraksa

Catatan penting:

- Jangan mengarang detail wisata yang belum diverifikasi.
- Jika fasilitas, harga, jam operasional, atau kontak belum pasti, gunakan fallback jujur dan tandai sebagai perlu dilengkapi.
- Event publik masih memakai tabel/model `promos` untuk kompatibilitas, tetapi label UI/admin sudah diarahkan sebagai Event.

## Optimasi yang Sudah Dilakukan

Optimasi prioritas pertama sudah diterapkan.

### 1. Settings Cache Bulk

File:

- `app/Support/AppSettings.php`
- `app/Filament/Admin/Pages/Settings/BaseSettingsPage.php`

Sebelumnya:

- `AppSettings::get()` melakukan query database per key.
- Halaman settings yang punya banyak field bisa memicu banyak query hanya untuk mount form.

Sekarang:

- `AppSettings::all()` membaca semua settings sekali dan menyimpannya di cache key `settings:all`.
- `AppSettings::get()` mengambil dari cache bulk.
- `AppSettings::set()` membersihkan cache settings.
- `BaseSettingsPage` memakai `AppSettings::all()` saat mount.

Dampak:

- Halaman settings lebih ringan.
- API public settings tetap bisa memakai cache whitelist.

### 2. Cache Version untuk Destinasi dan Review

File:

- `app/Support/CacheVersion.php`
- `app/Http/Controllers/Api/V1/DestinationController.php`
- `app/Http/Controllers/Api/V1/ReviewController.php`
- `app/Filament/Admin/Resources/Destinations/Pages/CreateDestination.php`
- `app/Filament/Admin/Resources/Destinations/Pages/EditDestination.php`
- `app/Filament/Admin/Resources/Reviews/Tables/ReviewsTable.php`
- `app/Filament/Admin/Resources/Reviews/Pages/ViewReview.php`

Sebelumnya:

- Beberapa action memakai `Cache::flush()`.
- Ini terlalu brutal karena membuang semua cache aplikasi.

Sekarang:

- Cache destinasi memakai version key `destinations:version`.
- Cache review memakai version key `reviews:version`.
- Saat destinasi/review berubah, sistem bump version key.
- Cache lama akan kedaluwarsa alami tanpa menghapus semua cache aplikasi.

Dampak:

- Public API tetap fresh setelah perubahan.
- Cache settings/dashboard tidak ikut hilang ketika destinasi/review diedit.

### 3. Dashboard Stats Cache

File:

- `app/Filament/Admin/Widgets/DashboardStats.php`

Sebelumnya:

- Dashboard menghitung visitor, booking, review, destinasi, paket, dan revenue setiap render.

Sekarang:

- Stats dicache pendek sekitar 75 detik.

Dampak:

- Dashboard lebih ringan saat admin berpindah halaman atau refresh berulang.
- Data dashboard masih cukup fresh untuk operasional harian.

### 4. Database Index Admin

Migration:

- `database/migrations/2026_06_16_120000_add_admin_performance_indexes.php`

Index yang ditambahkan:

- `visitors.visited_at`
- `visitors.origin_category`
- `visitors.visit_type`
- `reviews.status, created_at`
- `reviews.status, is_pinned_global`
- `reviews.destination_id, status, is_pinned_destination`
- `destinations.is_active`
- `destinations.destination_type`
- `destinations.is_featured_homepage, homepage_sort_order`
- `trip_packages.is_active, created_at`
- `guides.is_active, created_at`
- `promos.is_active, start_date, end_date`

Status:

- Migration sudah dijalankan.
- `php artisan migrate:status --pending` menunjukkan tidak ada pending migrations.

### 5. Quick Actions Setting

File:

- `app/Filament/Admin/Widgets/QuickActionsWidget.php`

Perubahan:

- Quick action `Website Media` diarahkan ke hub `SettingsPage`, bukan ke halaman media lama.

## Optimasi yang Belum Dilakukan

Prioritas berikutnya:

### 1. Production Environment

Saat audit terakhir, `php artisan about` masih menunjukkan:

- `APP_ENV=local`
- `APP_DEBUG=true`
- `LOG_LEVEL=debug`
- `CACHE_STORE=file`
- `SESSION_DRIVER=file`
- `QUEUE_CONNECTION=sync`
- Routes not cached
- Events not cached
- Filament panel components not cached
- Blade icons not cached

Untuk hosting, siapkan environment production:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `LOG_LEVEL=error`
- `CACHE_STORE=database` atau `redis`
- `SESSION_DRIVER=database` atau `redis`
- `QUEUE_CONNECTION=database` atau `redis`
- Jalankan:
  - `php artisan config:cache`
  - `php artisan route:cache`
  - `php artisan view:cache`
  - `php artisan event:cache`
  - command cache Filament/icons jika tersedia di versi yang dipakai

### 2. Queue Worker

Upload gambar ke Cloudinary dan export sebaiknya tidak semuanya sync.

Belum dilakukan:

- Memindahkan proses upload/export berat ke queue.
- Menyiapkan queue worker production.

### 3. Reports Page

File:

- `app/Filament/Admin/Pages/ReportsPage.php`

Masalah potensial:

- Reports menghitung data bulanan saat halaman dibuka.
- Kalau data visitor/daily visit membesar, halaman laporan bisa berat.

Solusi yang disarankan:

- Jangan auto-run report saat mount, tampilkan empty state dulu.
- Hitung setelah klik `Terapkan Filter`.
- Cache hasil report berdasarkan filter.
- Batasi default date range.

### 4. Filament Visual Polish

Belum dilakukan penuh:

- Custom theme admin yang matang.
- Dashboard visual yang lebih brand-aligned.
- Form destinasi lebih ergonomis.
- Settings cards lebih polished.
- Responsive admin belum diaudit langsung.

### 5. Frontend Build Issue

Sebelumnya `npm run build` di `pokdarwis-public` gagal dengan error environment Windows/Vite:

- `spawn EPERM`

Ini terlihat seperti masalah environment/local permissions saat Vite load config, bukan error React spesifik dari perubahan terbaru.

`npm run lint` juga pernah gagal karena rule React Fast Refresh di `src/router/index.jsx`.

Saran:

- Periksa environment Node/Vite di mesin.
- Jalankan build ulang sebelum deploy.
- Jika perlu, adjust eslint rule untuk router file atau pisahkan lazy components.

## Catatan Keamanan Hosting

Penting:

- Credential Cloudinary pernah terlihat di `.env` lokal.
- Sebelum hosting, rotate Cloudinary API secret jika pernah masuk screenshot, log, atau git.
- Jangan commit `.env`.
- Buat user database production khusus untuk database `pokdarwis`, jangan pakai root.
- MySQL user production sebaiknya tidak bisa melihat database lain.
- Gunakan HTTPS.
- Pastikan CORS/API frontend URL production benar.

## Rekomendasi Hosting

Rekomendasi aman:

- VPS kecil 1-2 GB RAM.
- Nginx atau Apache.
- PHP 8.4 sesuai local, atau PHP stabil yang kompatibel dengan Laravel 13.
- MySQL/MariaDB.
- Redis opsional tapi disarankan jika traffic naik.
- Queue worker aktif.
- Scheduler aktif.
- Cloudinary untuk media.

Shared hosting mungkin bisa, tetapi kurang ideal untuk:

- Filament admin
- queue worker
- upload media
- React build/static delivery
- cache/worker/scheduler yang stabil

## Perintah Verifikasi yang Pernah Dipakai

Backend:

```bash
php -l app/Support/AppSettings.php
php artisan migrate
php artisan migrate:status --pending
php artisan route:list --path=admin/settings
php artisan db:table reviews
php artisan db:table destinations
php artisan db:table visitors
php artisan about
php artisan optimize:clear
```

Frontend:

```bash
cd pokdarwis-public
npm run build
npm run lint
```

Catatan: build/lint frontend terakhir belum clean karena issue di atas.

## File Penting untuk AI Berikutnya

Roadmap:

- `docs/FRONTEND_FILAMENT_ROADMAP.md`
- `docs/AI_HANDOFF_SYSTEM_STATUS.md`

Settings/admin:

- `app/Support/AppSettings.php`
- `app/Support/CacheVersion.php`
- `app/Filament/Admin/Pages/SettingsPage.php`
- `app/Filament/Admin/Pages/Settings/*`
- `resources/views/filament/admin/pages/settings-page.blade.php`

Homepage/public:

- `pokdarwis-public/src/pages/ExperienceConceptPage.jsx`
- `pokdarwis-public/src/pages/ExperienceConceptPage.css`
- `pokdarwis-public/src/router/index.jsx`
- `pokdarwis-public/src/layouts/PublicLayout.jsx`
- `pokdarwis-public/src/components/Navbar.jsx`
- `pokdarwis-public/src/components/Footer.jsx`
- `pokdarwis-public/src/components/FloatingWhatsApp.jsx`

Destinasi/review/API:

- `app/Http/Controllers/Api/V1/DestinationController.php`
- `app/Http/Controllers/Api/V1/ReviewController.php`
- `app/Http/Controllers/Api/V1/SettingController.php`
- `app/Http/Resources/DestinationResource.php`
- `app/Models/Destination.php`
- `app/Models/Review.php`

Filament resources:

- `app/Filament/Admin/Resources/Destinations/*`
- `app/Filament/Admin/Resources/Reviews/*`
- `app/Filament/Admin/Resources/TripPackages/*`
- `app/Filament/Admin/Resources/Guides/*`
- `app/Filament/Admin/Resources/Promos/*`

Performance migration:

- `database/migrations/2026_06_16_120000_add_admin_performance_indexes.php`

Seeder:

- `database/seeders/KarangSidemenTourismSeeder.php`

## Prinsip Lanjutan

Untuk AI/engineer berikutnya:

- Jangan redesign homepage cinematic tanpa instruksi eksplisit.
- Jangan downgrade motion/immersive feeling.
- Jangan hardcode konten yang seharusnya bisa dikelola admin.
- Jika data wisata tidak valid/terverifikasi, jangan mengarang.
- Jangan pakai `Cache::flush()` untuk perubahan konten biasa.
- Hindari perubahan besar sekaligus; kerjakan step-by-step dan verifikasi.
- Jangan menghapus route `/experience-concept`.
- Event publik masih model `Promo`, jadi hati-hati saat rename agar tidak merusak kompatibilitas.
- Identitas footer pengembang sengaja dikunci di kode agar admin tidak bisa menghapus.

## Prioritas Pekerjaan Berikutnya

Urutan yang disarankan:

1. Buat `.env.production.example` atau checklist production env.
2. Bereskan frontend build/lint agar siap deploy.
3. Optimasi Reports page agar tidak query berat saat mount.
4. Audit mobile langsung di viewport 360px, 390px, 430px, 768px.
5. Lanjut polish UI Filament admin tanpa mengganggu optimasi yang sudah ada.
6. Lengkapi data/foto asli Karang Sidemen.
7. Pertimbangkan Homepage Builder sederhana jika admin butuh kontrol section yang lebih bebas.
