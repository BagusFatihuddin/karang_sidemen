import { useMemo } from "react";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getGuides } from "../services/api/guides";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./GuidesPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const shortText = (text, maxLength = 150) => {
    if (!text || text.length <= maxLength) {
        return text;
    }

    return `${text.slice(0, maxLength).trim()}...`;
};

const initials = (name) =>
    name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();

const defaultGuideImages = [
    "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88",
    "https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88",
];

export default function GuidesPage() {
    const { data, isLoading } = useQuery({
        queryKey: ["guides"],
        queryFn: getGuides,
    });
    const { data: settingsData } = usePublicSettings();
    const payload = getPayload(data);
    const settings = settingsData?.data?.data ?? settingsData?.data ?? {};
    const guides = useMemo(
        () => (Array.isArray(payload) ? payload : []),
        [payload],
    );
    const heroImage =
        settings.media_guides_hero_fallback_image_url || defaultGuideImages[0];
    const emptyImage =
        settings.media_guides_empty_image_url || defaultGuideImages[1];
    const noteImage =
        settings.media_guides_note_image_url ||
        settings.media_guides_hero_fallback_image_url ||
        defaultGuideImages[0];
    const whatsappNumber = settings?.global_whatsapp;

    return (
        <main
            className="guides-page"
            style={{
                "--guides-hero-image": `url("${heroImage}")`,
                "--guides-empty-image": `url("${emptyImage}")`,
                "--guides-note-image": `url("${noteImage}")`,
            }}
        >
            <section className="guides-hero">
                <div className="guides-hero__image" aria-hidden="true" />
                <div className="guides-hero__content">
                    <p>Guide Lokal</p>
                    <h1>
                        Pengalaman alam terasa beda ketika ditemani orang yang
                        paham tempatnya.
                    </h1>

                    <div className="guides-hero__bottom">
                        <span>{guides.length || "..."} guide tersedia</span>

                        <p>
                            Guide membantu pengunjung memahami jalur, cerita
                            lokal, waktu terbaik, dan cara menikmati Karang
                            Sidemen dengan lebih nyaman.
                        </p>
                    </div>
                </div>
            </section>

            <section className="guides-shell">
                <div className="guides-section-title">
                    <p>Orang lokal, pengalaman nyata</p>
                    <h2>
                        Pilih pendamping yang cocok dengan gaya perjalananmu.
                    </h2>
                </div>

                {isLoading ? (
                    <div className="guides-grid">
                        {[1, 2, 3].map((item) => (
                            <div className="guides-skeleton" key={item} />
                        ))}
                    </div>
                ) : guides.length === 0 ? (
                    <section className="guides-empty">
                        <div>
                            <p>Belum ada guide aktif</p>
                            <h2>Panduan sedang dipersiapkan.</h2>
                            <span>
                                Sambil menunggu, Anda tetap bisa menikmati
                                Karang Sidemen dengan rekomendasi terbaik dari
                                kami.
                            </span>
                        </div>

                        <a
                            href={buildWhatsAppUrl(
                                "Halo, saya ingin bertanya tentang panduan wisata Karang Sidemen",
                                whatsappNumber,
                            )}
                            target="_blank"
                            rel="noreferrer"
                        >
                            Tanya guide via WhatsApp
                        </a>
                    </section>
                ) : (
                    <div className="guides-grid">
                        {guides.map((guide, index) => (
                            <article className="guide-card" key={guide.id}>
                                <div className="guide-card__visual">
                                    {guide.photo_url ? (
                                        <img src={guide.photo_url} alt="" />
                                    ) : (
                                        <div>
                                            {initials(guide.name) || "KS"}
                                        </div>
                                    )}
                                    <span>
                                        {String(index + 1).padStart(2, "0")}
                                    </span>
                                </div>
                                <div className="guide-card__body">
                                    <p>Guide Lokal</p>

                                    <h3>{guide.name}</h3>
                                    {guide.experience && (
                                        <strong>{guide.experience}</strong>
                                    )}
                                    <span>
                                        {shortText(guide.bio) ||
                                            "Bio guide belum diisi."}
                                    </span>
                                    <a
                                        href={buildWhatsAppUrl(
                                            `Halo, saya ingin bertanya tentang panduan ${guide.name}`,
                                            whatsappNumber,
                                        )}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        Chat WhatsApp
                                    </a>
                                </div>
                            </article>
                        ))}
                    </div>
                )}

                <section className="guides-note">
                    <div className="guides-note__image" aria-hidden="true" />
                    <div>
                        <p>Kenapa pakai guide?</p>
                        <h2>
                            Karena beberapa tempat lebih aman dan lebih hidup
                            saat diceritakan langsung.
                        </h2>
                    </div>
                    <div className="guides-note__items">
                        <span>Jalur dan akses</span>
                        <span>Cerita lokal</span>
                        <span>Waktu terbaik</span>
                        <span>Etika kunjungan</span>
                    </div>
                </section>
            </section>
        </main>
    );
}
