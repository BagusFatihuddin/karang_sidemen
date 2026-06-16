# Roadmap Pengembangan Publik UI dan Filament Admin

Dokumen ini menjadi pegangan arah kerja setelah homepage cinematic Karang Sidemen mulai terbentuk. Fokus besarnya adalah menjaga rasa website tetap hidup, immersive, dan tidak konvensional, sambil membuat seluruh konten penting bisa dikelola admin.

## Prinsip Produk

Website ini harus terasa sebagai website Desa Wisata Karang Sidemen, bukan hanya halaman Datu Bajang. Datu Bajang tetap penting, tetapi posisinya adalah salah satu pengalaman wisata di dalam desa.

Karakter pengalaman publik:

- Cinematic
- Hidup saat discroll
- Penuh visual alam
- Banyak rasa eksplorasi
- Mobile tetap premium
- Konten utama database-driven
- Admin bisa mengatur copy, gambar, kartu, review, dan destinasi

Karakter admin Filament:

- Tidak terasa default mentah
- Lebih rapi, nyaman, dan sesuai kebutuhan pengelola wisata
- Prioritas cepat dipakai oleh admin konten
- Data penting mudah dicari, diedit, dan divalidasi

## Status Saat Ini

Yang sudah ada:

- Homepage cinematic di route `/`.
- Route `/experience-concept` tetap tersedia sebagai preview/backup.
- Data destinasi sudah mulai database-driven.
- Halaman `/destinasi` sudah dipoles menjadi visual-first listing.
- Halaman `/destinasi/:id` sudah dipoles dengan hero, gallery, highlights, CTA, dan review destinasi.
- Halaman `/review/:token` sudah dipoles dan upload foto review sudah mendukung WEBP lebih aman.
- Halaman `/paket` sudah dipoles dengan desain experience cards dan empty state premium.
- Halaman `/panduan` sudah dipoles dengan desain profil guide dan empty state premium.
- Halaman `/reviews` sudah dipoles menjadi halaman testimoni visual dengan filter dan pagination.
- Footer global untuk halaman public layout sudah dipoles dan terintegrasi.
- Halaman `/tentang` sudah dipoles menjadi halaman identitas Desa Wisata dan POKDARWIS.
- Admin bisa mengatur destinasi, gambar destinasi, review, visitor, paket, guide, dan settings.
- Filament admin sudah mulai dirapikan:
  - Brand panel menjadi POKDARWIS Karang Sidemen.
  - Navigation dikelompokkan menjadi Konten Wisata, Pengunjung, Operasional, Laporan, dan Sistem.
  - Dashboard stats dibuat lebih operasional.
  - Dashboard Quick Actions ditambahkan untuk aksi harian sesuai role.
  - Guide Lokal resource ditambahkan agar halaman `/panduan` bisa diisi dari admin.
  - Promo resource mulai direlabel sebagai Event agar sesuai kebutuhan agenda/kegiatan POKDARWIS.
- Homepage copy utama sudah mulai bisa diatur dari Settings admin.
- Gambar global publik sudah mulai bisa diatur dari Settings admin lewat section Website Public Media.
- Brand/logo, hero homepage, hero destinasi, hero paket, hero panduan, hero review, hero about, gambar cerita about, struktur organisasi opsional, gambar card panduan, dan tombol WhatsApp melayang sudah mulai tersambung ke Settings admin.
- Identitas pengembang di footer dikunci langsung di kode agar tidak bisa dihapus admin.
- Destinasi bisa ditandai sebagai featured homepage.
- Event aktif bisa muncul sebagai spotlight di hero homepage cinematic dan punya halaman detail sederhana `/event/:id`.
- Card paket wisata sudah menampilkan guide pendamping dengan foto/avatar, nama, dan keahlian jika paket memiliki relasi guide aktif.
- Review sudah memiliki approval, approve + pin langsung, pin global, pin per destinasi, filter pinned, dan reject otomatis melepas pin.
- Optimasi awal admin/hosting sudah mulai diterapkan:
  - Settings dibaca lewat cache bulk, bukan query per field.
  - Cache publik destinasi dan review memakai cache version, bukan `Cache::flush()`.
  - Dashboard stats admin dicache pendek agar tidak hitung ulang di setiap render.
  - Index database untuk filter/sort admin utama sudah ditambahkan.

Yang belum ideal:

- Foto destinasi masih perlu diganti dengan foto asli Karang Sidemen.
- Beberapa halaman publik lain masih belum mengikuti kualitas cinematic homepage.
- Filament admin masih memakai nuansa default.
- Belum ada homepage builder bebas untuk menambah section custom non-destinasi.
- Mobile perlu audit visual langsung di perangkat atau viewport HP.
- Belum ada paket trip aktif di database, sehingga `/paket` saat ini menampilkan empty state.
- Belum ada guide aktif di database, sehingga `/panduan` saat ini menampilkan empty state.
- Field database event masih memakai tabel/model `promos` untuk menjaga kompatibilitas, tetapi label admin dan publik sudah diarahkan sebagai Event.

## Phase 4: Public Pages Alignment

Tujuan: semua halaman publik terasa satu dunia dengan homepage baru.

### 4.1 Destination Listing Page

Halaman `/destinasi` perlu diubah agar tidak terasa seperti daftar kartu biasa.

Target:

- Hero kecil yang tetap cinematic.
- Filter destinasi yang rapi dan ringan.
- Destination cards yang visual-first.
- Data dari API existing.
- Support gambar cover, tags, vibe, activity keywords.
- Mobile menggunakan swipe/snap atau grid yang nyaman.

Konten yang dibutuhkan:

- Cover image tiap destinasi.
- Short description.
- Tourism vibe.
- Activity keywords.
- Destination type.

Jika data kosong:

- Tampilkan fallback yang elegan.
- Di admin, beri field yang mudah diisi.

### 4.2 Destination Detail Page

Halaman detail destinasi harus menjadi halaman paling penting setelah homepage.

Target:

- Hero visual besar.
- Gallery immersive.
- Highlight features.
- Facilities.
- Pricing.
- Map/link lokasi.
- CTA WhatsApp.
- Review destinasi terkait.
- Related destinations.

Interaksi:

- Gallery bisa terasa hidup.
- Image reveal saat scroll.
- Sticky quick info di desktop.
- Mobile fokus pada foto, info penting, lalu CTA.

Data yang dibutuhkan:

- Gallery images.
- Long description.
- Facilities.
- Fees.
- Maps URL.
- WhatsApp.
- Pinned destination reviews.

### 4.3 Review Public Experience

Saat ini review sudah ada, tetapi pengalaman publiknya perlu dipoles.

Target:

- Review per destinasi muncul di detail destinasi.
- Review global tampil sebagai social proof.
- Review token page dibuat lebih ramah dan branded.
- Setelah submit review, user mendapat halaman success yang terasa hangat.

Admin workflow:

- Visitor dibuat.
- Admin kirim token review.
- Pengunjung isi review.
- Admin approve.
- Admin pin global atau pin destinasi.

### 4.4 Packages Page

Halaman paket jangan hanya jadi katalog.

Target:

- Paket dipresentasikan sebagai itinerary atau pengalaman.
- Setiap paket menampilkan destinasi yang termasuk.
- Visual lebih besar.
- CTA jelas ke WhatsApp.
- Cocok untuk keluarga, grup, sekolah, komunitas, atau camping.

Data yang perlu dicek:

- Apakah paket punya cover image?
- Apakah paket punya destination relation?
- Apakah harga dan durasi sudah cukup jelas?
- Apakah paket sudah dihubungkan dengan guide aktif yang punya foto dan keahlian?

### 4.5 Guides Page

Halaman guide harus terasa manusiawi, bukan list admin.

Target:

- Profil guide dengan foto.
- Keahlian atau area pengalaman.
- Bahasa atau kontak jika ada.
- CTA untuk booking/pesan.

Data yang mungkin belum ada:

- Foto guide.
- Bio pendek.
- Skill atau specialty.

### 4.6 About Page

Halaman tentang harus menjelaskan identitas:

- Desa Wisata Karang Sidemen.
- POKDARWIS Karang Sidemen.
- Narasi desa, alam, budaya, dan pengelolaan lokal.
- Tidak terlalu formal.
- Visual-first.

Konten yang perlu disiapkan manual:

- Cerita singkat POKDARWIS.
- Foto desa atau pengelola.
- Visi pengembangan wisata.

## Phase 5: Mobile Experience Polish

Tujuan: mobile terasa sama bagusnya dengan desktop, bukan versi yang dikorbankan.

Checklist:

- Audit viewport 360px, 390px, 430px, 768px.
- Pastikan teks besar tidak keluar container.
- Pastikan horizontal interruption nyaman di-swipe.
- Pastikan scroll tidak terasa patah.
- Pastikan tidak ada browser horizontal overflow.
- Pastikan tombol CTA mudah disentuh.
- Pastikan gambar tetap tajam dan tidak terlalu berat.
- Pastikan review, cards, dan gallery punya snap behavior yang natural.

Prioritas mobile:

1. Homepage.
2. Destination listing.
3. Destination detail.
4. Review token page.
5. Packages dan guides.

## Phase 6: Content and Media System

Tujuan: semua komponen visual publik bisa dirawat admin.

Yang sudah bisa:

- Destination gallery.
- Destination cover via image sort order.
- Website Public Media untuk gambar global halaman publik:
  - Homepage final CTA image
  - Footer CTA image
  - About hero fallback image
  - Reviews hero image
  - Packages hero fallback dan empty state image
  - Package card fallback images
  - Guides hero fallback dan empty state image
- Brand/logo navbar dan footer lewat Brand Settings.
- Hero homepage, destinasi, paket, panduan, review, about, dan gambar pendukung halaman publik lewat Settings admin.
- Pengaturan media publik sudah dipisah per halaman:
  - Halaman Destinasi
  - Halaman Paket
  - Halaman Panduan
  - Halaman Review
- Identitas pengembang footer dikunci di kode:
  - Bagus Fatihuddin Abul Yasin
  - Muhammad Said
  - Universitas Bumogora
  - 2026
- About story image dan struktur organisasi opsional lewat About Page Settings.
- Floating WhatsApp memakai global WhatsApp dari General Settings dan hanya muncul di halaman utama publik.
- Homepage featured destination.
- Homepage section copy via settings.
- Review approve dan pin.

Yang perlu ditambah:

- Validasi kualitas gambar.
- Panduan ukuran gambar di admin.
- Optional hero image khusus homepage.
- Optional section image untuk final CTA.
- Optional gallery grouping untuk destinasi.

Solusi jangka pendek:

- Gunakan Destination gallery sebagai sumber visual utama.
- Gunakan Settings untuk copy section.
- Gunakan featured/sort order destinasi untuk kontrol kartu homepage.

Solusi jangka panjang:

- Buat Homepage Builder sederhana:
  - Section key
  - Title
  - Eyebrow
  - Body
  - Image
  - Items/cards
  - Sort order
  - Active toggle

## Phase 7: Filament Admin Redesign

Tujuan: admin tidak terasa default dan lebih cocok untuk pengelola wisata.

### 7.1 Admin Information Architecture

Susun ulang navigation:

- Dashboard
- Konten Wisata
  - Destinations
  - Packages
  - Guides
  - Promos
- Pengunjung
  - Visitors
  - Review Tokens
  - Reviews
- Website
  - Homepage Settings
  - General Settings
- Reports
  - Visits
  - Exports

Status awal:

- Navigation grouping dasar sudah diterapkan.
- Label beberapa resource utama sudah dibuat lebih ramah admin lokal.
- Perlu audit visual langsung di browser untuk spacing/sidebar setelah data nyata bertambah.

### 7.2 Dashboard Polish

Dashboard perlu menjadi pusat operasional.

Target widgets:

- Total visitor bulan ini.
- Review pending.
- Destinasi terpopuler.
- Booking/paket terbaru jika ada.
- Quick actions:
  - Tambah destinasi
  - Upload gambar
  - Approve review
  - Export data

Status awal:

- Dashboard stats sudah menampilkan pengunjung hari ini, pengunjung bulan ini, booking pending, review pending, destinasi aktif, paket aktif, dan pendapatan bulan ini.
- Quick action widget sudah dibuat untuk tambah destinasi, tambah paket, tambah guide, moderasi review, media website, laporan, registrasi wisatawan, dan verifikasi booking sesuai role.

### 7.3 Destination Admin UX

Form destinasi perlu dibuat lebih nyaman.

Target:

- Section jelas:
  - Basic info
  - Homepage display
  - Storytelling
  - Pricing
  - Location
  - Gallery
  - Source verification
- Preview cover image lebih bagus.
- Gallery reorder lebih smooth.
- Helper text yang jelas.
- Warning jika destinasi featured tetapi belum punya gambar.

### 7.4 Review Admin UX

Target:

- Pending review mudah terlihat.
- Pin global dan pin destinasi lebih jelas.
- Destination context terlihat.
- Cache refresh otomatis sudah ditangani.
- Bisa filter:
  - pending
  - approved
  - pinned global
  - pinned destination

Status awal:

- Filter status, pinned global, dan pinned destinasi sudah tersedia.
- Review pending bisa langsung `Approve + Pin Destinasi` atau `Approve + Pin Global`.
- Review approved tetap bisa pin/unpin global dan pin/unpin per destinasi.
- Reject review otomatis melepas pin agar review yang ditolak tidak tampil di publik.

### 7.5 Settings Admin UX

Settings sekarang mulai banyak. Perlu dipisah agar tidak melelahkan.

Target:

- General Settings
- Social and Contact
- Homepage Copy
- Homepage Media
- Integrations
- Cloudinary

Jika terlalu penuh, buat custom page:

- Homepage Settings
- System Settings

Status awal:

- Halaman Settings utama sudah diubah menjadi hub kartu kategori.
- Sub-halaman settings sudah dipisah dengan tombol simpan masing-masing:
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
- Admin Konten bisa mengakses pengaturan konten/visual publik.
- Integrasi teknis seperti Cloudinary tetap dibatasi untuk Super Admin.

### 7.6 Visual Branding Filament

Target visual:

- Warna hijau alam, cream, dan aksen air.
- Navigation lebih rapi.
- Brand name jelas.
- Dashboard widgets lebih visual.
- Jangan terlalu ramai.
- Tetap cepat dan admin-friendly.

## Phase 8: Data Completeness Audit

Tujuan: tahu data mana yang masih kosong sebelum UI final.

Audit per destinasi:

- Cover image asli
- Minimal 3 gallery images
- Short description
- Long description
- Tourism vibe
- Tags
- Highlights
- Activity keywords
- Facilities
- Entry fee
- Parking fee
- Rental price
- Maps URL
- WhatsApp
- Source URLs
- Pinned reviews

Output audit:

- Data lengkap
- Data kurang
- Data tidak terverifikasi
- Perlu foto lapangan
- Perlu konfirmasi pengelola

## Phase 9: Performance and QA

Tujuan: cinematic tetap smooth.

Checklist:

- Cache settings tidak query satu-satu.
- Tidak memakai `Cache::flush()` untuk perubahan destinasi/review.
- Dashboard stats memakai cache pendek.
- Index database untuk review, destinasi, visitor, paket, guide, dan event tersedia.
- Build production berhasil.
- Lighthouse mobile.
- Image size audit.
- No horizontal browser overflow.
- Scroll interaction desktop aman.
- Swipe mobile aman.
- API fallback aman.
- Empty data state rapi.
- Review/token flow dites.
- Admin create/edit dites.

## Prioritas Eksekusi Berikutnya

Urutan kerja yang paling masuk akal:

1. Selesaikan migrasi dan seeder setelah MySQL hidup.
2. Audit homepage mobile langsung di browser.
3. Polish `/destinasi`.
4. Polish `/destinasi/:id`.
5. Integrasikan review destinasi di detail page.
6. Polish review token page.
7. Polish packages dan guides.
8. Audit data dan foto asli.
9. Redesign Filament dashboard.
10. Pecah Settings menjadi Homepage Settings dan System Settings.

## Keputusan yang Perlu Dibuat

Hal yang perlu diputuskan sebelum fase besar berikutnya:

- Apakah homepage cukup dikontrol lewat Settings + Destinations, atau perlu Homepage Builder bebas?
- Apakah semua foto akan di-upload manual oleh admin, atau kita siapkan batch import?
- Apakah style publik semua halaman harus cinematic berat, atau sebagian dibuat lebih ringan?
- Apakah Filament redesign cukup theme/config, atau perlu custom dashboard dan custom pages?
- Apakah review destinasi hanya tampil jika pinned, atau semua approved review boleh tampil?

## Catatan Data Hilang

Data yang belum aman untuk dianggap final:

- Foto asli semua destinasi.
- Detail fasilitas beberapa spot.
- Biaya terbaru beberapa spot.
- Jam operasional.
- Kontak resmi per destinasi.
- Cerita resmi POKDARWIS.
- Struktur paket wisata final.
- Profil guide lengkap.

Jika data belum ada, jangan dipaksa mengarang. Gunakan fallback yang jujur, lalu tandai di admin sebagai perlu dilengkapi.
