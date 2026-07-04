import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getDestinations } from "../services/api/destinations";
import { getReviews } from "../services/api/reviews";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./AboutPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const socialFields = [
    { key: "social_instagram", label: "Instagram" },
    { key: "social_facebook", label: "Facebook" },
    { key: "social_tiktok", label: "TikTok" },
];

const storyCards = [
    {
        title: "Dikelola lokal",
        body: "POKDARWIS menjadi penghubung antara pengunjung, warga, dan pengalaman wisata desa.",
    },

    {
        title: "Alam sebagai cerita utama",
        body: "Danau, sungai, air terjun, hutan, camping, dan budaya lokal menjadi identitas besar Karang Sidemen.",
    },
    {
        title: "Data bisa terus tumbuh",
        body: "Destinasi, ulasan, paket, guide, dan foto terus diperbarui agar informasi lapangan selalu terasa segar untuk Anda.",
    },
];

const defaultAboutHeroImage =
    "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88";

export default function AboutPage() {
    const { data, isLoading } = usePublicSettings();
    const { data: destinationsData } = useQuery({
        queryKey: ["destinations", "about"],
        queryFn: getDestinations,
    });
    const { data: reviewsData } = useQuery({
        queryKey: ["reviews", "about"],
        queryFn: () => getReviews({ page: 1 }),
    });

    const settings = data?.data?.data ?? data?.data ?? {};
    const destinationsPayload = getPayload(destinationsData);
    const destinations = Array.isArray(destinationsPayload)
        ? destinationsPayload
        : [];
    const reviewsPayload = reviewsData?.data ?? {};
    const reviewTotal = reviewsPayload.pagination?.total ?? 0;
    const socialLinks = socialFields.filter((item) => settings[item.key]);
    const hasGoogleMaps = !!settings.google_maps_embed_url;
    const villageName = settings.village_name || "Desa Wisata Karang Sidemen";
    const heroImage =
        settings.media_about_hero_fallback_image_url ||
        destinations.find((destination) => destination.thumbnail_url)
            ?.thumbnail_url ||
        defaultAboutHeroImage;
    const storyImage = settings.media_about_story_image_url || "";
    const organizationImage =
        settings.media_about_organization_chart_image_url || "";

    if (isLoading) {
        return (
            <main className="about-page">
                <div className="about-skeleton about-skeleton--hero" />
            </main>
        );
    }

    return (
        <main
            className="about-page"
            style={{ "--about-hero-image": `url("${heroImage}")` }}
        >
            <section className="about-hero">
                <div className="about-hero__image" aria-hidden="true" />
                <div className="about-hero__content">
                    <p>Tentang desa wisata</p>
                    <h1>Desa Wisata Karang Sidemen</h1>
                    <div className="about-hero__bottom">
                        <span>POKDARWIS Karang Sidemen</span>
                        <p>
                            {settings.tagline ||
                                "Desa wisata alam yang tumbuh dari danau, air terjun, hutan, budaya lokal, dan pengelolaan warga."}
                        </p>
                    </div>
                </div>
            </section>

            <section className="about-shell">
                <section
                    className={
                        storyImage
                            ? "about-story about-story--with-image"
                            : "about-story"
                    }
                >
                    <div>
                        <p>Tentang pengelolaan</p>
                        <h2>
                            Wisata desa harus terasa hidup karena orang-orang
                            lokalnya ikut hadir.
                        </h2>
                    </div>
                    <div className="about-story__body">
                        {storyImage && (
                            <img
                                className="about-story__image"
                                src={storyImage}
                                alt=""
                            />
                        )}
                        <article className="about-story__editorial-card">
                            <p>Sudut pandang lokal</p>
                            <h3>
                                Karang Sidemen menjadi jembatan antara cerita
                                warga dan rencana perjalanan pengunjung.
                            </h3>

                            <span>
                                Karang Sidemen dipresentasikan sebagai desa
                                wisata, bukan satu destinasi tunggal. Pengunjung
                                bisa memahami pilihan pengalaman, melihat
                                review, menghubungi pengelola, dan menemukan
                                cerita yang tepat sebelum datang.
                            </span>
                        </article>
                    </div>
                </section>

                <section className="about-stats">
                    <article>
                        <span>{destinations.length}</span>
                        <p>Destinasi aktif</p>
                    </article>
                    <article>
                        <span>{reviewTotal}</span>
                        <p>Ulasan terkurasi</p>
                    </article>

                    <article>
                        <span>1</span>
                        <p>Desa wisata utama</p>
                    </article>
                </section>

                <section className="about-card-grid">
                    {storyCards.map((card) => (
                        <article key={card.title}>
                            <h3>{card.title}</h3>
                            <p>{card.body}</p>
                        </article>
                    ))}
                </section>

                <section className="about-contact">
                    <div>
                        <p>Kontak dan lokasi</p>
                        <h2>
                            Mulai dari chat singkat, lalu susun rencana
                            kunjungan.
                        </h2>
                    </div>
                    <div className="about-contact__panel">
                        {settings.global_whatsapp && (
                            <a
                                href={buildWhatsAppUrl(
                                    "Halo, saya ingin mengetahui informasi tentang Desa Wisata Karang Sidemen.",
                                    settings.global_whatsapp,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                className="about-contact__primary"
                            >
                                Chat WhatsApp
                            </a>
                        )}

                        <Link to="/destinasi">Lihat destinasi</Link>
                        <Link to="/reviews">Baca review</Link>

                        {socialLinks.length > 0 && (
                            <div className="about-socials">
                                {socialLinks.map((item) => (
                                    <a
                                        key={item.key}
                                        href={settings[item.key]}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        {item.label}
                                    </a>
                                ))}
                            </div>
                        )}
                    </div>
                </section>

                {organizationImage && (
                    <section className="about-organization">
                        <div>
                            <p>POKDARWIS</p>
                            <h2>
                                {settings.about_organization_title ||
                                    "Struktur Organisasi POKDARWIS"}
                            </h2>
                        </div>
                        <img
                            src={organizationImage}
                            alt="Struktur organisasi POKDARWIS"
                        />
                    </section>
                )}

                {hasGoogleMaps && (
                    <section className="about-map">
                        <div>
                            <p>Lokasi</p>
                            <h2>Temukan Karang Sidemen di peta.</h2>
                        </div>
                        {settings.google_maps_embed_url.includes("iframe") ? (
                            <div
                                className="about-map__embed"
                                dangerouslySetInnerHTML={{
                                    __html: settings.google_maps_embed_url,
                                }}
                            />
                        ) : (
                            <a
                                href={settings.google_maps_embed_url}
                                target="_blank"
                                rel="noreferrer"
                                className="about-map__link"
                            >
                                Buka Google Maps
                            </a>
                        )}
                    </section>
                )}
            </section>
        </main>
    );
}
