import { useMemo } from "react";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getTripPackages } from "../services/api/tripPackages";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./PackagesPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const formatPrice = (price) => {
    if (!price || Number(price) === 0) {
        return "Hubungi pengelola";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(price));
};

const shortText = (text, maxLength = 160) => {
    if (!text || text.length <= maxLength) {
        return text;
    }

    return `${text.slice(0, maxLength).trim()}...`;
};

const defaultPackageImages = [
    "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88",
    "https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1800&q=88",
    "https://images.unsplash.com/photo-1448375240586-882707db888b?auto=format&fit=crop&w=1800&q=88",
];

const getFallbackImage = (images, index) => images[index % images.length];

const getInitials = (name) =>
    name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();

export default function PackagesPage() {
    const { data, isLoading } = useQuery({
        queryKey: ["trip-packages"],
        queryFn: getTripPackages,
    });
    const { data: settingsData } = usePublicSettings();
    const packages = getPayload(data);
    const settings = settingsData?.data?.data ?? settingsData?.data ?? {};
    const fallbackImages = [
        settings.media_package_card_fallback_1_url || defaultPackageImages[0],
        settings.media_package_card_fallback_2_url || defaultPackageImages[1],
        settings.media_package_card_fallback_3_url || defaultPackageImages[2],
    ];
    const activePackages = useMemo(
        () => (Array.isArray(packages) ? packages : []),
        [packages],
    );
    const heroImage =
        settings.media_packages_hero_fallback_image_url ||
        getFallbackImage(fallbackImages, 0);
    const emptyImage =
        settings.media_packages_empty_image_url || defaultPackageImages[1];
    const featuredDestinations = useMemo(
        () =>
            Array.from(
                new Map(
                    activePackages
                        .flatMap((tripPackage) => tripPackage.destinations || [])
                        .map((destination) => [destination.id, destination]),
                ).values(),
            ).slice(0, 6),
        [activePackages],
    );
    const whatsappNumber = settings?.global_whatsapp;

    return (
        <main
            className="packages-page"
            style={{
                "--packages-hero-image": `url("${heroImage}")`,
                "--packages-empty-image": `url("${emptyImage}")`,
            }}
        >
            <section className="packages-hero">
                <div className="packages-hero__image" aria-hidden="true" />
                <div className="packages-hero__content">
                    <p>Paket wisata Karang Sidemen</p>
                    <h1>Rangkaian perjalanan, bukan sekadar daftar tempat.</h1>
                    <div className="packages-hero__bottom">
                        <span>{activePackages.length || "..."} paket aktif</span>
                        <p>
                            Pilih pengalaman yang paling cocok: santai keluarga,
                            jelajah air, camping, atau perjalanan pendek dengan
                            pengelola lokal.
                        </p>
                    </div>
                </div>
            </section>

            <section className="packages-shell">
                <div className="packages-section-title">
                    <p>Pilih pengalaman</p>
                    <h2>Paket dibuat agar pengunjung lebih mudah mulai dari mana.</h2>
                </div>

                {isLoading ? (
                    <div className="packages-grid">
                        {[1, 2, 3].map((item) => (
                            <div className="packages-skeleton" key={item} />
                        ))}
                    </div>
                ) : activePackages.length === 0 ? (
                    <section className="packages-empty">
                        <div>
                            <p>Belum ada paket aktif</p>
                            <h2>Paket wisata belum dipublikasikan dari admin.</h2>
                            <span>
                                Isi paket di Filament Admin agar halaman ini langsung
                                berubah menjadi katalog pengalaman. Untuk sementara,
                                pengunjung bisa bertanya langsung lewat WhatsApp.
                            </span>
                        </div>
                        <a
                            href={buildWhatsAppUrl(
                                "Halo, saya ingin bertanya tentang paket wisata Karang Sidemen",
                                whatsappNumber,
                            )}
                            target="_blank"
                            rel="noreferrer"
                        >
                            Tanya paket via WhatsApp
                        </a>
                    </section>
                ) : (
                    <>
                        <div className="packages-grid">
                            {activePackages.map((tripPackage, index) => (
                                <article className="package-card" key={tripPackage.id}>
                                    <div className="package-card__media">
                                        <img
                                            src={
                                                tripPackage.image_url ||
                                                getFallbackImage(fallbackImages, index)
                                            }
                                            alt=""
                                        />
                                        <span>{String(index + 1).padStart(2, "0")}</span>
                                    </div>
                                    <div className="package-card__body">
                                        <div className="package-card__meta">
                                            <strong>{formatPrice(tripPackage.price)}</strong>
                                            <span>
                                                {(tripPackage.destinations || []).length} spot
                                            </span>
                                        </div>
                                        <h3>{tripPackage.name}</h3>
                                        <p>{shortText(tripPackage.description)}</p>

                                        {tripPackage.destinations?.length > 0 && (
                                            <div className="package-card__route">
                                                {tripPackage.destinations
                                                    .slice(0, 4)
                                                    .map((destination) => (
                                                        <Link
                                                            to={`/destinasi/${destination.id}`}
                                                            key={destination.id}
                                                        >
                                                            {destination.name}
                                                        </Link>
                                                    ))}
                                            </div>
                                        )}

                                        {tripPackage.guides?.length > 0 && (
                                            <div className="package-card__guides">
                                                {tripPackage.guides.slice(0, 2).map((guide) => (
                                                    <article key={guide.id}>
                                                        {guide.photo_url ? (
                                                            <img src={guide.photo_url} alt="" />
                                                        ) : (
                                                            <span>
                                                                {getInitials(guide.name) || "G"}
                                                            </span>
                                                        )}
                                                        <div>
                                                            <strong>{guide.name}</strong>
                                                            {(guide.experience || guide.bio) && (
                                                                <small>
                                                                    {guide.experience ||
                                                                        shortText(guide.bio, 70)}
                                                                </small>
                                                            )}
                                                        </div>
                                                    </article>
                                                ))}
                                            </div>
                                        )}

                                        <a
                                            href={buildWhatsAppUrl(
                                                `Halo, saya tertarik dengan paket ${tripPackage.name}`,
                                                whatsappNumber,
                                            )}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="package-card__cta"
                                        >
                                            Tanya via WhatsApp
                                        </a>
                                    </div>
                                </article>
                            ))}
                        </div>

                        {featuredDestinations.length > 0 && (
                            <section className="packages-route">
                                <div>
                                    <p>Rute yang sering muncul</p>
                                    <h2>Destinasi dalam paket wisata.</h2>
                                </div>
                                <div className="packages-route__list">
                                    {featuredDestinations.map((destination, index) => (
                                        <Link
                                            to={`/destinasi/${destination.id}`}
                                            key={destination.id}
                                        >
                                            <span>{String(index + 1).padStart(2, "0")}</span>
                                            <strong>{destination.name}</strong>
                                            {destination.destination_type && (
                                                <small>{destination.destination_type}</small>
                                            )}
                                        </Link>
                                    ))}
                                </div>
                            </section>
                        )}
                    </>
                )}
            </section>
        </main>
    );
}
