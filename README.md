# POKDARWIS Karang Sidemen

Platform digital untuk **Desa Wisata Karang Sidemen, Lombok**. Aplikasi ini menyatukan
halaman publik untuk pengunjung dengan panel admin untuk mengelola konten wisata,
pengunjung, ulasan, paket perjalanan, dan operasional desa wisata.

## Tentang proyek

POKDARWIS Karang Sidemen membantu pengunjung menemukan pengalaman wisata alam dan
budaya lokal, sekaligus membantu pengelola memperbarui informasi dari satu tempat.
Konten publik disajikan melalui frontend React, sedangkan data dan pengelolaan
operasional ditangani oleh Laravel dan Filament.

### Fitur utama

- Halaman publik responsif untuk beranda, destinasi, paket wisata, guide, promo,
  event, ulasan, dan halaman tentang desa wisata.
- Panel admin di `/admin` untuk mengelola destinasi, paket perjalanan, guide,
  promo, booking, pengunjung, ulasan, laporan, dan pengaturan website.
- API publik versi 1 untuk destinasi, paket, guide, promo, ulasan, dan pengaturan.
- Pengelolaan media dengan Cloudinary.
- Ekspor laporan kunjungan ke Excel dan PDF.
- Pengaturan brand, konten halaman, media, tautan sosial, WhatsApp, dan Google Maps.
- Seeder konten awal destinasi wisata Karang Sidemen.

## Teknologi

- **Backend:** Laravel 13, PHP 8.3+
- **Admin panel:** Filament 5
- **Frontend:** React 19, React Router, TanStack React Query
- **Build tool:** Vite 8
- **Database:** MySQL atau SQLite untuk pengembangan
- **Media:** Cloudinary
- **Laporan:** Laravel Excel dan Dompdf

## Menjalankan secara lokal

### Prasyarat

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL atau SQLite

### Instalasi cepat

```bash
git clone https://github.com/BagusFatihuddin/karang_sidemen.git
cd karang_sidemen
composer setup
```

Perintah `composer setup` memasang dependency, membuat `.env`, membuat application
key, menjalankan migrasi, memasang dependency frontend, dan membuat build produksi.

Jika ingin menjalankan langkah secara manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Sesuaikan koneksi database dan konfigurasi layanan pada `.env`. Untuk fitur media
produksi, isi kredensial Cloudinary sesuai kebutuhan deployment.

### Mode development

```bash
composer dev
```

Perintah tersebut menjalankan server Laravel, queue listener, log viewer, dan Vite.
Frontend dapat diakses melalui URL yang ditampilkan oleh Laravel/Vite, sedangkan
panel admin tersedia di:

```text
/admin
```

### Data demo

Untuk mengisi konten awal desa wisata:

```bash
php artisan db:seed
```

Seeder membuat data destinasi dan pengaturan awal yang dipakai oleh halaman publik.

## Struktur penting

```text
app/
├── Filament/Admin/        # Panel admin dan resource pengelolaan
├── Http/Controllers/Api/  # API publik /api/v1
├── Models/                # Model domain aplikasi
├── Services/              # Integrasi layanan eksternal
└── Support/               # Helper dan pengaturan aplikasi
database/
├── migrations/            # Struktur database
└── seeders/               # Data awal Karang Sidemen
resources/
├── js/react/              # Frontend publik React
├── css/                   # Style aplikasi
└── views/                 # Blade shell dan view admin
routes/
├── api_v1.php             # Endpoint API publik
└── web.php                # Entry point frontend dan panel web
```

## Endpoint API

Endpoint publik menggunakan prefix `/api/v1`:

```text
GET  /api/v1/ping
GET  /api/v1/destinations
GET  /api/v1/promos
GET  /api/v1/trip-packages
GET  /api/v1/guides
GET  /api/v1/reviews
GET  /api/v1/reviews/pinned
GET  /api/v1/settings/public
GET  /api/v1/review/{token}
POST /api/v1/review/{token}
```

## Pengembangan

Build frontend:

```bash
npm run build
```

Menjalankan test:

```bash
composer test
```

Sebelum membuka pull request, pastikan build dan test berhasil serta perubahan
database sudah disertai migration atau seeder yang relevan.

## Status proyek

Proyek ini sedang dikembangkan sebagai platform pengelolaan dan promosi Desa Wisata
Karang Sidemen. Informasi destinasi, harga, fasilitas, dan ketersediaan sebaiknya
diverifikasi kembali dengan pengelola sebelum dipublikasikan.
