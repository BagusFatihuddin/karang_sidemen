<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\Review;
use App\Models\ReviewToken;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KarangSidemenTourismSeeder extends Seeder
{
    private const JADESTA_VILLAGE = 'https://jadesta.kemenpar.go.id/desa/karang_sidemen_1';

    private const GO_MANDALIKA = 'https://gomandalika.com/destinasi/desa-wisata-karang-sidemen/';

    private const GENPI_DANAU_BIRU = 'https://ntb.genpi.co/wisata/4141/menikmati-keindahan-danau-biru-desa-karang-sidemen';

    private const LOMBOK_DISPATCH_DANAU_BIRU = 'https://lombokdispatch.id/2025/11/08/the-blue-lake-lombok/';

    /**
     * Seed real-world Karang Sidemen tourism content.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedSettings();
            $destinations = $this->seedDestinations();
            $this->deactivateLegacyDuplicates();
            $this->seedReviews($destinations);
        });

        Cache::flush();
    }

    /**
     * @return array<string, Destination>
     */
    private function seedDestinations(): array
    {
        $items = [
            [
                'name' => 'Danau Biru',
                'slug' => 'danau-biru',
                'description' => 'Danau Biru adalah daya tarik utama Desa Wisata Karang Sidemen. Lokasinya berada di kawasan hutan Karang Sidemen, dengan air hijau kebiruan, pepohonan rindang, dan suasana sejuk. Beberapa sumber menyebut danau ini terbentuk dari pertemuan dua sungai, dan aksesnya melewati hutan serta kebun warga.',
                'short_description' => 'Danau kecil berair hijau kebiruan di kawasan hutan Karang Sidemen.',
                'tourism_vibe' => 'tenang, sejuk, fotogenik, cocok untuk santai dan bermain air',
                'facilities' => 'Area parkir, jalur turun ke danau, musholla, toilet, ruang ganti, berugak/gazebo, pedagang lokal, spot foto, petugas keselamatan.',
                'destination_type' => 'air',
                'entry_fee' => 5000,
                'parking_fee' => null,
                'rental_price' => 25000,
                'whatsapp_number' => '081936151172',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Danau+Biru+Karang+Sidemen',
                'tags' => ['danau', 'air biru', 'hutan', 'spot foto', 'keluarga'],
                'highlights' => [
                    'Air berwarna hijau kebiruan',
                    'Suasana hutan yang teduh',
                    'Berenang dan bermain air',
                    'Berkuda di sekitar kawasan wisata',
                    'Berugak dan pedagang lokal',
                ],
                'activity_keywords' => ['swimming', 'photography', 'nature', 'relaxation', 'family', 'horse riding'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                    self::GO_MANDALIKA,
                    self::GENPI_DANAU_BIRU,
                    self::LOMBOK_DISPATCH_DANAU_BIRU,
                    'https://jadesta.kemenpar.go.id/atraksi/destinasi_wisata_danau_biru',
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Penimproh Datu Bajang',
                'slug' => 'penimproh-datu-bajang',
                'description' => 'Penimproh Datu Bajang adalah atraksi air terjun di Desa Wisata Karang Sidemen. Jadesta mencatatnya sebagai destinasi wisata alam di Dusun Selojan, dekat dari pusat desa. Daya tariknya adalah suasana air terjun berbatu yang eksotis dan menenangkan.',
                'short_description' => 'Air terjun berbatu di Karang Sidemen dengan suasana alami dan tenang.',
                'tourism_vibe' => 'air terjun, batu hitam, alami, dekat dengan cerita lokal',
                'facilities' => 'Persewaan alat dan tempat makan tercatat pada profil atraksi Jadesta.',
                'destination_type' => 'air',
                'entry_fee' => 20000,
                'parking_fee' => null,
                'rental_price' => null,
                'whatsapp_number' => '081936151172',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Datu+Bajang+Karang+Sidemen',
                'tags' => ['air terjun', 'bebatuan', 'datu bajang', 'selojan'],
                'highlights' => [
                    'Air terjun di kawasan Desa Wisata Karang Sidemen',
                    'Lanskap batu dan aliran air',
                    'Tercatat sebagai atraksi alam di Jadesta',
                ],
                'activity_keywords' => ['waterfall', 'river', 'photography', 'nature', 'relaxation'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                    'https://jadesta.kemenpar.go.id/atraksi/destinasi_penimproh_datu_bajang',
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1546182990-dffeafbe841d?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Air Terjun Batu Belah',
                'slug' => 'air-terjun-batu-belah',
                'description' => 'Air Terjun Batu Belah disebut sebagai potensi tersembunyi di Desa Karang Sidemen. Go Mandalika menulis bahwa lokasinya berada di tengah hutan dan perjalanan menuju titik air terjun memerlukan pemandu lokal sekitar 40 menit.',
                'short_description' => 'Air terjun tersembunyi di tengah hutan Karang Sidemen.',
                'tourism_vibe' => 'hidden waterfall, petualangan hutan, butuh pemandu lokal',
                'facilities' => 'Perlu konfirmasi lapangan. Sumber publik menyarankan menggunakan tour guide setempat.',
                'destination_type' => 'air',
                'entry_fee' => null,
                'parking_fee' => null,
                'rental_price' => null,
                'whatsapp_number' => '081281750493',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Air+Terjun+Batu+Belah+Karang+Sidemen',
                'tags' => ['air terjun', 'hutan', 'hidden gem', 'guide lokal'],
                'highlights' => [
                    'Disebut sebagai air terjun tersembunyi',
                    'Akses hutan dan perjalanan sekitar 40 menit menurut Go Mandalika',
                    'Lebih cocok untuk wisata minat alam',
                ],
                'activity_keywords' => ['waterfall', 'trekking', 'adventure', 'nature', 'photography'],
                'source_urls' => [
                    self::GO_MANDALIKA,
                    self::JADESTA_VILLAGE,
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1546182990-dffeafbe841d?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Camping Ground Antih Tuselak',
                'slug' => 'camping-ground-antih-tuselak',
                'description' => 'Camping Ground Antih Tuselak tercatat sebagai atraksi wisata alam Karang Sidemen di Jadesta. Deskripsinya menekankan suasana hutan lindung, ketenangan malam, suara hewan malam, serta aktivitas berkuda pada pagi hari.',
                'short_description' => 'Area camping bernuansa hutan lindung dengan suasana tenang.',
                'tourism_vibe' => 'camping hutan, sunyi, meditatif, cocok untuk jeda dari kota',
                'facilities' => 'Berkuda, kamar mandi umum, persewaan alat, selfie area, tempat makan.',
                'destination_type' => 'camping',
                'entry_fee' => 150000,
                'parking_fee' => null,
                'rental_price' => 250000,
                'whatsapp_number' => '081907861655',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Camping+Ground+Antih+Tuselak+Karang+Sidemen',
                'tags' => ['camping', 'hutan', 'berkuda', 'malam'],
                'highlights' => [
                    'Camping di daerah hutan lindung',
                    'Suasana malam yang tenang',
                    'Atraksi berkuda pagi hari',
                    'Persewaan alat tersedia menurut Jadesta',
                ],
                'activity_keywords' => ['camping', 'nature', 'relaxation', 'horse riding', 'family'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                    'https://jadesta.kemenpar.go.id/atraksi/camping_ground_antih_tuselak',
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Glamping Lembah Surga',
                'slug' => 'glamping-lembah-surga',
                'description' => 'Glamping Lembah Surga tercatat sebagai atraksi wisata alam di halaman Jadesta Karang Sidemen. Lombok Dispatch juga menyebut area Lembah Surga sedang dikembangkan sebagai atraksi alam tambahan di sekitar Danau Biru.',
                'short_description' => 'Pengalaman glamping yang dikembangkan di kawasan wisata Karang Sidemen.',
                'tourism_vibe' => 'glamping, lembah hijau, dekat pengalaman Danau Biru',
                'facilities' => 'Detail fasilitas perlu dikonfirmasi manual dengan pengelola.',
                'destination_type' => 'camping',
                'entry_fee' => 45000,
                'parking_fee' => null,
                'rental_price' => null,
                'whatsapp_number' => '081281750493',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Glamping+Lembah+Surga+Karang+Sidemen',
                'tags' => ['glamping', 'lembah', 'alam', 'danau biru'],
                'highlights' => [
                    'Tercatat sebagai atraksi alam Karang Sidemen',
                    'Disebut sebagai pengembangan atraksi sekitar Danau Biru',
                ],
                'activity_keywords' => ['camping', 'nature', 'relaxation', 'photography'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                    self::LOMBOK_DISPATCH_DANAU_BIRU,
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Tahura Nuraksa',
                'slug' => 'tahura-nuraksa',
                'description' => 'Taman Hutan Raya Nuraksa atau Tahura Nuraksa disebut dalam daftar atraksi Karang Sidemen. Sumber Go Mandalika juga menempatkan Taman Hutan Raya Nurakasa sebagai rute yang dapat dijelajahi melalui aktivitas berkuda.',
                'short_description' => 'Kawasan hutan raya yang menjadi bagian pengalaman alam Karang Sidemen.',
                'tourism_vibe' => 'hutan, edukasi alam, trekking ringan, konservasi',
                'facilities' => 'Detail fasilitas destinasi spesifik perlu dikonfirmasi lapangan.',
                'destination_type' => 'alam',
                'entry_fee' => 35000,
                'parking_fee' => null,
                'rental_price' => null,
                'whatsapp_number' => '081281750493',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Tahura+Nuraksa+Karang+Sidemen',
                'tags' => ['tahura', 'hutan', 'edukasi', 'konservasi'],
                'highlights' => [
                    'Kawasan hutan dalam pengalaman Karang Sidemen',
                    'Terkait aktivitas berkuda dan jelajah alam',
                    'Potensial untuk edukasi lingkungan',
                ],
                'activity_keywords' => ['nature', 'trekking', 'education', 'photography', 'adventure'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                    self::GO_MANDALIKA,
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
                    'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
            [
                'name' => 'Pemandian Spiritual Nyeredet',
                'slug' => 'pemandian-spiritual-nyeredet',
                'description' => 'Pemandian Spiritual Nyeredet tercatat sebagai atraksi wisata alam Karang Sidemen di Jadesta. Informasi publik yang tersedia masih terbatas, sehingga konten perlu dilengkapi kembali oleh pengelola.',
                'short_description' => 'Atraksi pemandian spiritual yang tercatat dalam daftar wisata Karang Sidemen.',
                'tourism_vibe' => 'pemandian, spiritual, lokal, tenang',
                'facilities' => 'Detail fasilitas perlu dikonfirmasi manual.',
                'destination_type' => 'air',
                'entry_fee' => 5000,
                'parking_fee' => null,
                'rental_price' => null,
                'whatsapp_number' => '081281750493',
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Pemandian+Spiritual+Nyeredet+Karang+Sidemen',
                'tags' => ['pemandian', 'spiritual', 'air', 'lokal'],
                'highlights' => [
                    'Tercatat sebagai atraksi Karang Sidemen',
                    'Memerlukan verifikasi konten dan foto lapangan',
                ],
                'activity_keywords' => ['water', 'relaxation', 'local culture'],
                'source_urls' => [
                    self::JADESTA_VILLAGE,
                ],
                'images' => [
                    'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
                ],
            ],
        ];

        $homepageDestinations = [
            'danau-biru' => [1, 'Blue Lake'],
            'penimproh-datu-bajang' => [2, 'Cold River'],
            'air-terjun-batu-belah' => [3, 'Hidden Falls'],
            'camping-ground-antih-tuselak' => [4, 'Hidden Camping'],
            'tahura-nuraksa' => [5, 'Forest Escape'],
            'glamping-lembah-surga' => [6, 'Valley Stay'],
        ];

        $destinations = [];

        foreach ($items as $item) {
            $images = $item['images'];
            unset($item['images']);
            $homepage = $homepageDestinations[$item['slug']] ?? null;

            $destination = Destination::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'cloudinary_folder' => 'destinations/' . $item['slug'],
                    'is_active' => true,
                    'is_featured_homepage' => filled($homepage),
                    'homepage_sort_order' => $homepage[0] ?? null,
                    'homepage_label' => $homepage[1] ?? null,
                ]
            );

            DestinationImage::query()
                ->where('destination_id', $destination->id)
                ->where('cloudinary_public_id', 'like', 'seed/%')
                ->delete();

            foreach ($images as $index => $imageUrl) {
                DestinationImage::query()->create([
                    'destination_id' => $destination->id,
                    'cloudinary_public_id' => 'seed/' . $item['slug'] . '/' . ($index + 1),
                    'url' => $imageUrl,
                    'sort_order' => $index,
                ]);
            }

            $destinations[$item['slug']] = $destination;
        }

        return $destinations;
    }

    private function deactivateLegacyDuplicates(): void
    {
        $seededNames = [
            'Danau Biru',
            'Penimproh Datu Bajang',
            'Air Terjun Batu Belah',
            'Camping Ground Antih Tuselak',
            'Glamping Lembah Surga',
            'Tahura Nuraksa',
            'Pemandian Spiritual Nyeredet',
        ];

        Destination::query()
            ->whereNull('slug')
            ->whereIn(DB::raw('TRIM(name)'), $seededNames)
            ->update(['is_active' => false]);
    }

    private function seedSettings(): void
    {
        $settings = [
            'village_name' => 'Desa Wisata Karang Sidemen',
            'tagline' => 'Wisata alam kaki Rinjani: Danau Biru, air terjun, hutan, budaya Sasak, dan camping yang dikelola POKDARWIS Karang Sidemen.',
            'global_whatsapp' => '081281750493',
            'public_frontend_url' => 'http://localhost:5173',
            'brand_mark_text' => 'KS',
            'brand_logo_url' => '',
            'brand_logo_alt' => 'Desa Wisata Karang Sidemen',
            'social_instagram' => 'https://www.instagram.com/desawisata_karangsidemen',
            'social_facebook' => 'https://www.facebook.com/search/top?q=pokdarwis%20selendang%20biru%20rinjani',
            'social_tiktok' => '',
            'google_maps_embed_url' => 'https://www.google.com/maps/search/?api=1&query=Desa+Wisata+Karang+Sidemen+Batukliang+Utara',
            'homepage_hero_eyebrow' => 'POKDARWIS Karang Sidemen',
            'homepage_hero_title_line_1' => 'Karang',
            'homepage_hero_title_line_2' => 'Sidemen',
            'homepage_hero_cta_label' => 'Lihat momen zoom',
            'homepage_reel_eyebrow' => 'Desa wisata, bukan satu spot',
            'homepage_reel_title' => 'Karang Sidemen punya beberapa pengalaman alam yang saling nyambung.',
            'homepage_portal_eyebrow' => 'Scroll zoom moment',
            'homepage_portal_title' => 'Masuk ke {portal}. Keluar lagi ke {next}.',
            'homepage_portal_body' => 'Momen ini menjaga interaksi cinematic: visual membesar saat scroll, lalu mengecil lagi untuk membuka destinasi berikutnya.',
            'homepage_zoom_items' => [
                [
                    'title' => 'Danau Biru',
                    'description' => 'Wide landscape dan detail air biru untuk membuka rasa eksplorasi.',
                    'zoom_out_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
                    'zoom_in_image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1800&q=88',
                    'display_order' => 1,
                    'is_active' => true,
                ],
                [
                    'title' => 'Camping Kaki Rinjani',
                    'description' => 'Dari suasana lembah menuju detail tenda dan api unggun.',
                    'zoom_out_image_url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=1800&q=88',
                    'zoom_in_image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
                    'display_order' => 2,
                    'is_active' => true,
                ],
            ],
            'homepage_breathing_eyebrow' => 'Tarik napas sebentar',
            'homepage_breathing_title' => 'Hutan, air, dan udara yang pelan.',
            'homepage_breathing_body' => 'Setelah momen zoom, beri pengunjung jeda visual sebelum masuk ke eksplorasi horizontal Karang Sidemen.',
            'media_homepage_breathing_image_url' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
            'homepage_horizontal_eyebrow' => 'Explore Karang Sidemen',
            'homepage_horizontal_title' => 'Geser vertikal, tapi rasanya masuk ke rute tersembunyi.',
            'homepage_horizontal_hint' => 'Scroll down to move sideways',
            'homepage_horizontal_items' => [
                [
                    'title' => 'Danau Biru',
                    'description' => 'Air tenang, warna biru, dan suasana hutan yang menjadi pembuka cerita desa wisata.',
                    'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1800&q=88',
                    'link_url' => '/destinasi',
                    'display_order' => 1,
                    'is_active' => true,
                ],
                [
                    'title' => 'Camping Kaki Rinjani',
                    'description' => 'Ruang menginap alam untuk pengunjung yang ingin tinggal lebih lama di lanskap Karang Sidemen.',
                    'image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
                    'link_url' => '/paket',
                    'display_order' => 2,
                    'is_active' => true,
                ],
            ],
            'homepage_experience_eyebrow' => 'Database-driven experiences',
            'homepage_experience_title' => 'Setiap kartu datang dari data destinasi yang bisa dikelola admin.',
            'homepage_highlight_eyebrow' => 'Highlight terverifikasi',
            'homepage_highlight_title' => 'Air, hutan, camping, budaya, dan edukasi jadi cerita besar desa.',
            'homepage_reviews_eyebrow' => 'Suara pengunjung',
            'homepage_reviews_title' => 'Review dibuat pendek, lokal, dan masuk akal.',
            'homepage_final_eyebrow' => 'Final pull',
            'homepage_final_title' => 'Karang Sidemen harus terasa sebagai desa wisata hidup, bukan halaman destinasi tunggal.',
            'homepage_final_cta_label' => 'Lihat destinasi',
            'media_homepage_hero_image_url' => '',
            'media_homepage_final_image_url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=2200&q=88',
            'media_footer_cta_image_url' => 'https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=1800&q=88',
            'media_destinations_hero_image_url' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
            'media_about_hero_fallback_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
            'media_about_story_image_url' => 'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=1800&q=88',
            'media_about_organization_chart_image_url' => '',
            'about_organization_title' => 'Struktur Organisasi POKDARWIS',
            'media_reviews_hero_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
            'media_packages_hero_fallback_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
            'media_packages_empty_image_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
            'media_package_card_fallback_1_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
            'media_package_card_fallback_2_url' => 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88',
            'media_package_card_fallback_3_url' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
            'media_guides_hero_fallback_image_url' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88',
            'media_guides_empty_image_url' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88',
            'media_guides_note_image_url' => 'https://images.unsplash.com/photo-1537953773345-d172ccf13cf1?auto=format&fit=crop&w=1800&q=88',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value,
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * @param array<string, Destination> $destinations
     */
    private function seedReviews(array $destinations): void
    {
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        $reviews = [
            [
                'destination' => 'danau-biru',
                'name' => 'Lia',
                'city' => 'Praya',
                'rating' => 5,
                'text' => 'Airnya dingin banget, enak buat santai. Datang pagi lebih sepi dan warnanya bagus buat foto.',
            ],
            [
                'destination' => 'penimproh-datu-bajang',
                'name' => 'Rizal',
                'city' => 'Mataram',
                'rating' => 4,
                'text' => 'Tempatnya masih alami. Batu-batunya licin, jadi memang harus hati-hati, tapi suasananya adem.',
            ],
            [
                'destination' => 'camping-ground-antih-tuselak',
                'name' => 'Nanda',
                'city' => 'Lombok Tengah',
                'rating' => 5,
                'text' => 'Campingnya tenang. Malam cuma dengar suara hutan, pagi bisa jalan santai lihat sekitar.',
            ],
            [
                'destination' => 'air-terjun-batu-belah',
                'name' => 'Fikri',
                'city' => 'Praya',
                'rating' => 4,
                'text' => 'Jalurnya perlu ditemani orang lokal. Begitu sampai, suasananya sepi dan airnya segar.',
            ],
            [
                'destination' => 'tahura-nuraksa',
                'name' => 'Maya',
                'city' => 'Mataram',
                'rating' => 4,
                'text' => 'Enak buat jalan pelan-pelan. Hutannya adem, cocok kalau mau cari suasana yang tidak ramai.',
            ],
            [
                'destination' => 'glamping-lembah-surga',
                'name' => 'Dika',
                'city' => 'Lombok Tengah',
                'rating' => 5,
                'text' => 'Paling suka suasana paginya. Masih perlu banyak foto asli di website biar orang kebayang tempatnya.',
            ],
            [
                'destination' => 'pemandian-spiritual-nyeredet',
                'name' => 'Sari',
                'city' => 'Batukliang Utara',
                'rating' => 4,
                'text' => 'Tempatnya sederhana dan lokal sekali. Cocok untuk yang cari suasana air yang lebih tenang.',
            ],
        ];

        foreach ($reviews as $reviewData) {
            $destination = $destinations[$reviewData['destination']] ?? null;

            if (! $destination) {
                continue;
            }

            if (Review::query()->where('reviewer_name', $reviewData['name'])->exists()) {
                continue;
            }

            $visitor = Visitor::query()->create([
                'name' => $reviewData['name'],
                'whatsapp_number' => '080000000' . random_int(100, 999),
                'origin_category' => $reviewData['city'] === 'Lombok Tengah'
                    ? 'lombok_tengah'
                    : 'lombok_lainnya',
                'origin_city' => $reviewData['city'],
                'visit_type' => 'pasangan',
                'group_size' => 2,
                'referral_source' => 'instagram',
                'destination_id' => $destination->id,
                'recorded_by' => $user->id,
                'visited_at' => now()->subDays(random_int(12, 80)),
            ]);

            $token = ReviewToken::query()->create([
                'token' => Str::uuid()->toString(),
                'visitor_id' => $visitor->id,
                'destination_id' => $destination->id,
                'generated_by' => $user->id,
                'is_used' => true,
                'expires_at' => now()->addDays(7),
                'used_at' => now()->subDays(random_int(3, 10)),
                'created_at' => now()->subDays(random_int(12, 80)),
            ]);

            Review::query()->create([
                'review_token_id' => $token->id,
                'visitor_id' => $visitor->id,
                'destination_id' => $destination->id,
                'reviewer_name' => $reviewData['name'],
                'reviewer_city' => $reviewData['city'],
                'rating' => $reviewData['rating'],
                'review_text' => $reviewData['text'],
                'status' => 'approved',
                'is_pinned_destination' => true,
                'is_pinned_global' => true,
                'approved_by' => $user->id,
                'approved_at' => now()->subDays(random_int(2, 8)),
            ]);
        }
    }
}
