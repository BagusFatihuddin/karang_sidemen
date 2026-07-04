import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getDestinations } from "../services/api/destinations";
import { getPromos } from "../services/api/promos";
import { getPinnedReviews } from "../services/api/reviewsPinned";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./HomePage.css";

const fallbackLandscape =
    "https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=2400&q=85";
const fallbackTexture =
    "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=85";

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

const getInitials = (name) => {
    return name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();
};

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const useHeroProgress = () => {
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        const update = () => {
            const viewport = Math.max(window.innerHeight, 1);
            setProgress(clamp(window.scrollY / (viewport * 0.85), 0, 1));
        };

        update();
        window.addEventListener("scroll", update, { passive: true });
        window.addEventListener("resize", update);

        return () => {
            window.removeEventListener("scroll", update);
            window.removeEventListener("resize", update);
        };
    }, []);

    return progress;
};

export default function HomePage() {
    const heroProgress = useHeroProgress();
    const { data: settingsData, isLoading: settingsLoading } =
        usePublicSettings();
    const { data: promosData, isLoading: promosLoading } = useQuery({
        queryKey: ["promos"],
        queryFn: getPromos,
    });
    const { data: destinationsData, isLoading: destinationsLoading } = useQuery(
        {
            queryKey: ["destinations"],
            queryFn: getDestinations,
        },
    );
    const { data: reviewsData, isLoading: reviewsLoading } = useQuery({
        queryKey: ["reviews", "pinned"],
        queryFn: getPinnedReviews,
    });
    const [activePromoIndex, setActivePromoIndex] = useState(0);
    const [activeReviewIndex, setActiveReviewIndex] = useState(0);

    const settings = getPayload(settingsData);
    const promos = getPayload(promosData);
    const promoItems = Array.isArray(promos) ? promos : [];
    const destinations = getPayload(destinationsData);
    const destinationItems = Array.isArray(destinations)
        ? destinations.slice(0, 6)
        : [];
    const reviews = getPayload(reviewsData);
    const reviewItems = Array.isArray(reviews) ? reviews : [];

    const heroDestination = destinationItems.find(
        (destination) => destination.thumbnail_url,
    );
    const heroLandscape =
        heroDestination?.thumbnail_url ||
        settings.media_about_hero_fallback_image_url ||
        fallbackLandscape;
    const heroTexture =
        promoItems.find((promo) => promo.image_url)?.image_url ||
        settings.media_footer_cta_image_url ||
        fallbackTexture;

    const heroStyle = {
        "--hero-progress": heroProgress,
        "--hero-landscape": `url("${heroLandscape}")`,
        "--hero-texture": `url("${heroTexture}")`,
    };

    useEffect(() => {
        if (promoItems.length <= 1) {
            return undefined;
        }

        const interval = window.setInterval(() => {
            setActivePromoIndex((current) => (current + 1) % promoItems.length);
        }, 5200);

        return () => window.clearInterval(interval);
    }, [promoItems.length]);

    useEffect(() => {
        if (reviewItems.length <= 1) {
            return undefined;
        }

        const interval = window.setInterval(() => {
            setActiveReviewIndex(
                (current) => (current + 1) % reviewItems.length,
            );
        }, 4400);

        return () => window.clearInterval(interval);
    }, [reviewItems.length]);

    const safePromoIndex =
        promoItems.length > 0 ? activePromoIndex % promoItems.length : 0;
    const safeReviewIndex =
        reviewItems.length > 0 ? activeReviewIndex % reviewItems.length : 0;
    const activePromo = promoItems[safePromoIndex];
    const activeReview = reviewItems[safeReviewIndex];
    const villageName = settings?.village_name || "Karang Sidemen";
    const tagline =
        settings?.tagline ||
        "Hidden paradise di kaki Rinjani, tempat air biru, hutan, dan udara gunung bertemu.";

    return (
        <main className="home-page">
            <section className="home-hero" style={heroStyle}>
                <div className="home-hero__landscape" aria-hidden="true" />
                <div className="home-hero__texture" aria-hidden="true" />
                <div
                    className="home-hero__mist home-hero__mist--one"
                    aria-hidden="true"
                />
                <div
                    className="home-hero__mist home-hero__mist--two"
                    aria-hidden="true"
                />

                <div className="home-hero__content">
                    <p className="home-hero__eyebrow">Hidden nature escape</p>

                    {settingsLoading ? (
                        <div className="home-skeleton home-skeleton--hero" />
                    ) : (
                        <>
                            <h1>{villageName}</h1>
                            <p className="home-hero__tagline">{tagline}</p>
                            <div className="home-hero__actions">
                                <Link to="/destinasi" className="home-button">
                                    Jelajahi Karang Sidemen
                                </Link>

                                <a
                                    href="#hidden-paradise"
                                    className="home-button home-button--ghost"
                                >
                                    Lihat Reveal
                                </a>
                            </div>
                        </>
                    )}
                </div>

                <div
                    className="home-hero__meta"
                    aria-label="Ringkasan pengalaman"
                >
                    <span>Danau biru</span>
                    <span>Air terjun</span>
                    <span>Camping</span>
                </div>

                <a className="home-hero__scroll" href="#hidden-paradise">
                    <span />
                    Scroll
                </a>
            </section>

            <section id="hidden-paradise" className="home-reveal">
                <div className="home-reveal__copy">
                    <p className="home-kicker">Keindahan tersembunyi</p>
                    <h2>
                        Air biru, batu sungai, hutan dingin—pelan-pelan terbuka.
                    </h2>
                </div>
                <div
                    className="home-reveal__words"
                    aria-label="Sorotan pengalaman"
                >
                    <span>Air terjun</span>
                    <span>Danau biru</span>
                    <span>Hutan yang menyejukkan</span>
                    <span>Camping</span>
                </div>
            </section>

            {promosLoading && (
                <section className="home-section">
                    <div className="home-skeleton home-skeleton--promo" />
                </section>
            )}

            {!promosLoading && activePromo && (
                <section className="home-section">
                    <div className="home-section__heading">
                        <p className="home-kicker">Sedang menarik</p>
                        <h2>Promo dan kabar terbaru</h2>
                    </div>
                    <article className="home-feature">
                        {activePromo.image_url && (
                            <img
                                src={activePromo.image_url}
                                alt={activePromo.title}
                            />
                        )}
                        <div className="home-feature__body">
                            <h3>{activePromo.title}</h3>
                            {activePromo.description && (
                                <p>{activePromo.description}</p>
                            )}
                            {activePromo.external_url && (
                                <a
                                    href={activePromo.external_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="home-button home-button--light"
                                >
                                    Lihat Promo
                                </a>
                            )}
                        </div>
                    </article>
                </section>
            )}

            <section className="home-section">
                <div className="home-section__heading">
                    <p className="home-kicker">Pengalaman destinasi</p>
                    <h2>Spot yang bikin orang berhenti sejenak.</h2>
                </div>

                {destinationsLoading ? (
                    <div className="home-destination-grid">
                        {[1, 2, 3, 4, 5, 6].map((item) => (
                            <div
                                key={item}
                                className="home-skeleton home-skeleton--card"
                            />
                        ))}
                    </div>
                ) : destinationItems.length === 0 ? (
                    <p className="home-empty">Belum ada destinasi tersedia.</p>
                ) : (
                    <div className="home-destination-grid">
                        {destinationItems.map((destination) => (
                            <article
                                key={destination.id}
                                className="home-destination-card"
                            >
                                {destination.thumbnail_url ? (
                                    <img
                                        src={destination.thumbnail_url}
                                        alt={destination.name}
                                    />
                                ) : (
                                    <div className="home-destination-card__fallback">
                                        Destinasi
                                    </div>
                                )}
                                <div className="home-destination-card__body">
                                    {destination.destination_type && (
                                        <span>
                                            {destination.destination_type}
                                        </span>
                                    )}
                                    <h3>{destination.name}</h3>
                                    <Link to={`/destinasi/${destination.id}`}>
                                        Detail
                                    </Link>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>

            {reviewsLoading && (
                <section className="home-section">
                    <div className="home-skeleton home-skeleton--testimonial" />
                </section>
            )}

            {!reviewsLoading && activeReview && (
                <section className="home-section">
                    <div className="home-section__heading">
                        <p className="home-kicker">Cerita Perjalanan</p>
                        <h2>Kesan yang dibawa pulang.</h2>
                    </div>

                    <article className="home-testimonial">
                        <div className="home-testimonial__header">
                            {activeReview.photo_url ? (
                                <img
                                    src={activeReview.photo_url}
                                    alt={activeReview.reviewer_name}
                                />
                            ) : (
                                <div>
                                    {getInitials(activeReview.reviewer_name) ||
                                        "?"}
                                </div>
                            )}
                            <div>
                                <h3>{activeReview.reviewer_name}</h3>
                                <p>
                                    Rating {activeReview.rating}/5
                                    {activeReview.origin_city
                                        ? ` - ${activeReview.origin_city}`
                                        : ""}
                                </p>
                            </div>
                        </div>
                        <p className="home-testimonial__quote">
                            "{activeReview.review_text}"
                        </p>
                        <div className="home-testimonial__controls">
                            <button
                                type="button"
                                onClick={() =>
                                    setActiveReviewIndex((current) =>
                                        current === 0
                                            ? reviewItems.length - 1
                                            : current - 1,
                                    )
                                }
                            >
                                Sebelumnya
                            </button>
                            <Link to="/reviews">Lihat semua ulasan</Link>

                            <button
                                type="button"
                                onClick={() =>
                                    setActiveReviewIndex(
                                        (current) =>
                                            (current + 1) % reviewItems.length,
                                    )
                                }
                            >
                                Berikutnya
                            </button>
                        </div>
                    </article>
                </section>
            )}

            {settings?.global_whatsapp && (
                <section className="home-final-cta">
                    <p className="home-kicker">Ready when you are</p>
                    <h2>Rencanakan kabur sebentar ke udara gunung.</h2>
                    <a
                        href={buildWhatsAppUrl(
                            "Halo, saya ingin tanya tentang wisata Karang Sidemen.",
                            settings?.global_whatsapp,
                        )}
                        target="_blank"
                        rel="noreferrer"
                        className="home-button"
                    >
                        Chat via WhatsApp
                    </a>
                </section>
            )}
        </main>
    );
}
