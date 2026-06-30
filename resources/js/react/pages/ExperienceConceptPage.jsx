import { useEffect, useMemo, useRef, useState } from "react";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import Footer from "../components/Footer";
import FloatingWhatsApp from "../components/FloatingWhatsApp";
import Navbar from "../components/Navbar";
import { getDestinations } from "../services/api/destinations";
import { getPromos } from "../services/api/promos";
import { getPinnedReviews } from "../services/api/reviewsPinned";
import { normalizeList } from "../utils/normalizeList";
import "./ExperienceConceptPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

const getImageUrl = (destination) =>
    destination?.thumbnail_url || destination?.images?.[0]?.url || "";

const getDestinationBySlug = (destinations, slug) =>
    destinations.find((destination) => destination.slug === slug);

const settingValue = (settings, key, fallback) => settings?.[key] || fallback;

const normalizeHomepageItems = (value) => {
    if (Array.isArray(value)) {
        return value;
    }

    if (typeof value === "string" && value.trim().startsWith("[")) {
        try {
            const parsed = JSON.parse(value);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }

    return [];
};

const normalizeHomepageZoomItems = (value) => {
    return normalizeHomepageItems(value)
        .filter((item) => item && item.is_active !== false)
        .map((item, index) => ({
            title: item.title || "",
            description: item.description || item.subtitle || "",
            zoomOutImage:
                item.zoom_out_image_url || item.zoomOutImage || item.zoom_out_image || "",
            zoomInImage:
                item.zoom_in_image_url || item.zoomInImage || item.zoom_in_image || "",
            displayOrder: Number.isFinite(Number(item.display_order))
                ? Number(item.display_order)
                : index + 1,
        }))
        .filter(
            (item) =>
                item.title ||
                item.description ||
                item.zoomOutImage ||
                item.zoomInImage,
        )
        .sort(
            (a, b) =>
                a.displayOrder - b.displayOrder || a.title.localeCompare(b.title),
        );
};

const normalizeHomepageHorizontalItems = (value) => {
    return normalizeHomepageItems(value)
        .filter((item) => item && item.is_active !== false)
        .map((item, index) => ({
            title: item.title || "",
            description: item.description || "",
            imageUrl: item.image_url || item.imageUrl || "",
            linkUrl: item.link_url || item.linkUrl || "",
            displayOrder: Number.isFinite(Number(item.display_order))
                ? Number(item.display_order)
                : index + 1,
        }))
        .filter((item) => item.title || item.description || item.imageUrl)
        .sort(
            (a, b) =>
                a.displayOrder - b.displayOrder || a.title.localeCompare(b.title),
        );
};

const isExternalUrl = (url) => /^https?:\/\//i.test(url);

const HorizontalStoryPanel = ({ item, index }) => {
    const content = (
        <>
            {item.imageUrl && <img src={item.imageUrl} alt="" />}
            <div className="concept-horizontal__shade" />
            <div className="concept-horizontal__content">
                <span>{String(index + 1).padStart(2, "0")}</span>
                <h3>{item.title}</h3>
                {item.description && <p>{item.description}</p>}
            </div>
        </>
    );

    const className = "concept-horizontal__panel";
    const style = { "--panel-index": index };

    if (!item.linkUrl) {
        return (
            <article className={className} style={style}>
                {content}
            </article>
        );
    }

    if (isExternalUrl(item.linkUrl)) {
        return (
            <a
                href={item.linkUrl}
                className={className}
                style={style}
                target="_blank"
                rel="noreferrer"
                aria-label={`Buka ${item.title}`}
            >
                {content}
            </a>
        );
    }

    return (
        <Link
            to={item.linkUrl}
            className={className}
            style={style}
            aria-label={`Buka ${item.title}`}
            onClick={() => window.scrollTo({ top: 0, left: 0 })}
        >
            {content}
        </Link>
    );
};

const getHomepageOrder = (destination, fallback = 999) =>
    Number.isFinite(Number(destination?.homepage_sort_order))
        ? Number(destination.homepage_sort_order)
        : fallback;

const getRatingStars = (rating) => "★".repeat(clamp(Number(rating) || 0, 0, 5));

const defaultFinalImage =
    "https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=2200&q=88";

const usePageProgress = () => {
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        const update = () => {
            const scrollable =
                document.documentElement.scrollHeight - window.innerHeight;
            setProgress(scrollable > 0 ? clamp(window.scrollY / scrollable, 0, 1) : 0);
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

const useSectionProgress = () => {
    const ref = useRef(null);
    const [progress, setProgress] = useState(0);

    useEffect(() => {
        const update = () => {
            if (!ref.current) {
                return;
            }

            const rect = ref.current.getBoundingClientRect();
            const travel = rect.height - window.innerHeight;
            const raw = travel > 0 ? -rect.top / travel : 0;
            setProgress(clamp(raw, 0, 1));
        };

        update();
        window.addEventListener("scroll", update, { passive: true });
        window.addEventListener("resize", update);

        return () => {
            window.removeEventListener("scroll", update);
            window.removeEventListener("resize", update);
        };
    }, []);

    return [ref, progress];
};

const useHorizontalInterruption = () => {
    const sectionRef = useRef(null);
    const trackRef = useRef(null);
    const animationRef = useRef(null);
    const offsetRef = useRef(0);
    const maxOffsetRef = useRef(0);

    useEffect(() => {
        const getPanelCount = () =>
            trackRef.current?.querySelectorAll(".concept-horizontal__panel")
                .length ?? 0;

        const measure = () => {
            if (!trackRef.current) {
                return 0;
            }

            const firstPanel = trackRef.current.querySelector(
                ".concept-horizontal__panel",
            );
            const panelWidth = firstPanel?.getBoundingClientRect().width ?? 0;
            const centeredEdge = Math.max((window.innerWidth - panelWidth) / 2, 24);
            const maxOffset = Math.max(
                trackRef.current.scrollWidth - window.innerWidth + centeredEdge,
                0,
            );

            maxOffsetRef.current = maxOffset;
            return maxOffset;
        };

        const applyOffset = (offset) => {
            if (!sectionRef.current || !trackRef.current) {
                return;
            }

            const maxOffset = maxOffsetRef.current || measure();
            const nextOffset = clamp(offset, 0, maxOffset);
            const progress = maxOffset > 0 ? nextOffset / maxOffset : 0;
            const panelCount = Math.max(getPanelCount() - 1, 1);
            const activeIndex = Math.round(progress * panelCount);

            offsetRef.current = nextOffset;
            sectionRef.current.style.setProperty(
                "--horizontal-progress",
                String(progress),
            );
            sectionRef.current.style.setProperty(
                "--horizontal-active",
                String(activeIndex),
            );
            trackRef.current.style.transform = `translate3d(${-nextOffset}px, 0, 0)`;
        };

        const update = () => {
            animationRef.current = null;

            if (!sectionRef.current || !trackRef.current) {
                return;
            }

            const rect = sectionRef.current.getBoundingClientRect();
            const isPinned =
                rect.top <= 2 && rect.bottom >= window.innerHeight - 2;

            if (isPinned) {
                measure();
                return;
            }

            const travel = rect.height - window.innerHeight;
            const progress = travel > 0 ? clamp(-rect.top / travel, 0, 1) : 0;
            applyOffset(progress * measure());
        };

        const scheduleUpdate = () => {
            if (animationRef.current) {
                return;
            }

            animationRef.current = window.requestAnimationFrame(update);
        };

        scheduleUpdate();
        window.addEventListener("scroll", scheduleUpdate, { passive: true });
        window.addEventListener("resize", scheduleUpdate);

        const handleWheel = (event) => {
            if (!sectionRef.current || window.innerWidth <= 760) {
                return;
            }

            const rect = sectionRef.current.getBoundingClientRect();
            const isPinned =
                rect.top <= 2 && rect.bottom >= window.innerHeight - 2;

            if (!isPinned) {
                return;
            }

            const maxOffset = maxOffsetRef.current || measure();
            const currentOffset = offsetRef.current;
            const wantsForward = event.deltaY > 0;
            const wantsBackward = event.deltaY < 0;
            const canMoveForward = currentOffset < maxOffset - 2;
            const canMoveBackward = currentOffset > 2;

            if (
                (wantsForward && canMoveForward) ||
                (wantsBackward && canMoveBackward)
            ) {
                event.preventDefault();
                applyOffset(currentOffset + event.deltaY * 1.15);
            }
        };

        window.addEventListener("wheel", handleWheel, { passive: false });

        return () => {
            if (animationRef.current) {
                window.cancelAnimationFrame(animationRef.current);
            }

            window.removeEventListener("scroll", scheduleUpdate);
            window.removeEventListener("resize", scheduleUpdate);
            window.removeEventListener("wheel", handleWheel);
        };
    }, []);

    return [sectionRef, trackRef];
};

export default function ExperienceConceptPage() {
    const pageProgress = usePageProgress();
    const [reelRef, reelProgress] = useSectionProgress();
    const [zoomRef, zoomProgress] = useSectionProgress();
    const [horizontalSectionRef, horizontalTrackRef] =
        useHorizontalInterruption();
    const { data: settingsData } = usePublicSettings();
    const { data: destinationsData, isLoading: destinationsLoading } = useQuery({
        queryKey: ["destinations", "experience-concept"],
        queryFn: getDestinations,
    });
    const { data: promosData } = useQuery({
        queryKey: ["events", "homepage-feature"],
        queryFn: getPromos,
    });
    const { data: reviewsData } = useQuery({
        queryKey: ["reviews", "pinned", "experience-concept"],
        queryFn: getPinnedReviews,
    });

    const settings = getPayload(settingsData);
    const destinations = getPayload(destinationsData);
    const destinationItems = useMemo(
        () => (Array.isArray(destinations) ? destinations : []),
        [destinations],
    );
    const reviews = getPayload(reviewsData);
    const reviewItems = useMemo(
        () => (Array.isArray(reviews) ? reviews : []),
        [reviews],
    );
    const promos = getPayload(promosData);
    const eventItems = useMemo(
        () => (Array.isArray(promos) ? promos : []),
        [promos],
    );
    const activeEvent = eventItems[0];

    const featuredDestinations = useMemo(() => {
        const preferredSlugs = [
            "danau-biru",
            "penimproh-datu-bajang",
            "air-terjun-batu-belah",
            "camping-ground-antih-tuselak",
            "glamping-lembah-surga",
            "tahura-nuraksa",
        ];

        const homepageItems = destinationItems
            .filter(
                (destination) =>
                    destination.is_featured_homepage && getImageUrl(destination),
            )
            .sort(
                (a, b) =>
                    getHomepageOrder(a) - getHomepageOrder(b) ||
                    a.name.localeCompare(b.name),
            );

        if (homepageItems.length > 0) {
            return [
                ...homepageItems,
                ...destinationItems.filter(
                    (destination) =>
                        !homepageItems.some((item) => item.id === destination.id) &&
                        getImageUrl(destination),
                ),
            ];
        }

        return [
            ...preferredSlugs
                .map((slug) => getDestinationBySlug(destinationItems, slug))
                .filter(Boolean),
            ...destinationItems.filter(
                (destination) => !preferredSlugs.includes(destination.slug),
            ),
        ].filter((destination) => getImageUrl(destination));
    }, [destinationItems]);

    const danauBiru =
        getDestinationBySlug(destinationItems, "danau-biru") ||
        featuredDestinations[0];
    const heroImage =
        settings.media_homepage_hero_image_url ||
        getImageUrl(danauBiru) ||
        getImageUrl(featuredDestinations[0]);
    const finalImage =
        settings.media_homepage_final_image_url || defaultFinalImage;
    const zoomItems = useMemo(
        () => normalizeHomepageZoomItems(settings?.homepage_zoom_items),
        [settings?.homepage_zoom_items],
    );
    const breathingDestination =
        getDestinationBySlug(destinationItems, "tahura-nuraksa") ||
        getDestinationBySlug(destinationItems, "danau-biru") ||
        featuredDestinations[0];
    const breathingTitle =
        settings.homepage_breathing_title || breathingDestination?.name || "Karang Sidemen";
    const breathingBody =
        settings.homepage_breathing_body ||
        breathingDestination?.short_description ||
        breathingDestination?.tourism_vibe ||
        "Ruang jeda untuk merasakan lanskap Karang Sidemen sebelum lanjut mengeksplorasi cerita berikutnya.";
    const breathingImage =
        settings.media_homepage_breathing_image_url || getImageUrl(breathingDestination);
    const managedHorizontalItems = useMemo(
        () => normalizeHomepageHorizontalItems(settings?.homepage_horizontal_items),
        [settings?.homepage_horizontal_items],
    );
    const fallbackHorizontalItems = useMemo(() => {
        return featuredDestinations.slice(0, 5).map((destination) => ({
            title: destination.homepage_label || destination.name,
            description:
                destination.tourism_vibe ||
                destination.short_description ||
                destination.name,
            imageUrl: getImageUrl(destination),
            linkUrl: `/destinasi/${destination.id}`,
        }));
    }, [featuredDestinations]);
    const horizontalItems =
        managedHorizontalItems.length > 0
            ? managedHorizontalItems
            : fallbackHorizontalItems;

    const zoomItemCount = Math.max(zoomItems.length, 1);
    const activeZoomIndex = zoomItems.length
        ? Math.min(zoomItems.length - 1, Math.floor(zoomProgress * zoomItems.length))
        : 0;
    const activeZoomItem = zoomItems[activeZoomIndex];
    const nextZoomItem = zoomItems[(activeZoomIndex + 1) % zoomItems.length];
    const zoomItemProgress = zoomItems.length
        ? clamp(zoomProgress * zoomItems.length - activeZoomIndex, 0, 1)
        : 0;
    const zoomIn = zoomItemProgress <= 0.52 ? zoomItemProgress / 0.52 : 1;
    const zoomOut =
        zoomItemProgress > 0.52 ? (zoomItemProgress - 0.52) / 0.48 : 0;
    const portalScale = 0.82 + zoomIn * 1.42 - zoomOut * 1.32;
    const portalRadius = 42 - zoomIn * 34 + zoomOut * 20;
    const portalCopyOpacity = clamp(1 - zoomItemProgress * 1.45, 0, 1);
    const portalImageOpacity = clamp((zoomItemProgress - 0.14) / 0.62, 0, 1);
    const portalOutImage =
        activeZoomItem?.zoomOutImage || activeZoomItem?.zoomInImage || "";
    const portalInImage =
        activeZoomItem?.zoomInImage || activeZoomItem?.zoomOutImage || "";

    return (
        <>
            <main
                className="concept-page"
                style={{
                    "--page-progress": pageProgress,
                    "--zoom-progress": zoomProgress,
                    "--reel-progress": reelProgress,
                    "--portal-scale": portalScale,
                    "--portal-radius": `${portalRadius}px`,
                    "--portal-copy-opacity": portalCopyOpacity,
                    "--portal-in-opacity": portalImageOpacity,
                    "--zoom-item-progress": zoomItemProgress,
                    "--zoom-item-count": zoomItemCount,
                    "--image-hero": heroImage ? `url("${heroImage}")` : "none",
                    "--image-final": `url("${finalImage}")`,
                    "--image-portal-out": portalOutImage
                        ? `url("${portalOutImage}")`
                        : "none",
                    "--image-portal-in": portalInImage
                        ? `url("${portalInImage}")`
                        : "none",
                    "--image-breathing": breathingImage
                        ? `url("${breathingImage}")`
                        : "none",
                }}
            >
            <Navbar />

            <section className="concept-hero">
                <div className="concept-hero__image" aria-hidden="true" />
                <div className="concept-hero__shade" aria-hidden="true" />
                <div className="concept-hero__content">
                    <p>
                        {settingValue(
                            settings,
                            "homepage_hero_eyebrow",
                            "POKDARWIS Karang Sidemen",
                        )}
                    </p>
                    <h1>
                        <span>
                            {settingValue(settings, "homepage_hero_title_line_1", "Karang")}
                        </span>
                        <span>
                            {settingValue(settings, "homepage_hero_title_line_2", "Sidemen")}
                        </span>
                    </h1>
                <div className="concept-hero__bottom">
                    <p>
                        {settings?.tagline ||
                                "Desa wisata alam di kaki Rinjani dengan danau, air terjun, hutan, budaya lokal, dan pengalaman camping."}
                        </p>
                    </div>
                </div>

                {activeEvent && (
                    <Link
                        to={`/event/${activeEvent.id}`}
                        className={
                            activeEvent.image_url
                                ? "concept-event-card"
                                : "concept-event-card concept-event-card--no-image"
                        }
                        aria-label={`Buka event ${activeEvent.title}`}
                    >
                        {activeEvent.image_url && (
                            <img src={activeEvent.image_url} alt="" />
                        )}
                        <div>
                            <span>Event aktif</span>
                            <strong>{activeEvent.title}</strong>
                            {activeEvent.description && (
                                <p>{activeEvent.description}</p>
                            )}
                        </div>
                    </Link>
                )}

                {featuredDestinations.slice(0, 2).map((destination, index) => (
                    <Link
                        to={`/destinasi/${destination.id}`}
                        className={`concept-floating-card concept-floating-card--${
                            index === 0 ? "one" : "two"
                        }`}
                        key={destination.id}
                        aria-label={`Buka detail ${destination.name}`}
                    >
                        <img src={getImageUrl(destination)} alt="" />
                        <span>{destination.name}</span>
                    </Link>
                ))}
            </section>

            <section id="reels" className="concept-reel" ref={reelRef}>
                <div className="concept-section-title">
                    <p className="concept-kicker">
                        {settingValue(
                            settings,
                            "homepage_reel_eyebrow",
                            "Desa wisata, bukan satu spot",
                        )}
                    </p>
                    <h2>
                        {settingValue(
                            settings,
                            "homepage_reel_title",
                            "Karang Sidemen punya beberapa pengalaman alam yang saling nyambung.",
                        )}
                    </h2>
                    <span className="concept-swipe-hint">Geser</span>
                </div>
                {destinationsLoading ? (
                    <div className="concept-loading">Memuat destinasi...</div>
                ) : (
                    <div className="concept-reel__track" aria-label="Destinasi Karang Sidemen">
                        {[...featuredDestinations, ...featuredDestinations].map(
                            (destination, index) => (
                                <figure key={`${destination.id}-${index}`}>
                                    <img src={getImageUrl(destination)} alt="" />
                                    <figcaption>{destination.name}</figcaption>
                                </figure>
                            ),
                        )}
                    </div>
                )}
            </section>

            <section
                id="portal"
                ref={zoomRef}
                className={`concept-portal-section${
                    zoomItems.length === 0 ? " concept-portal-section--empty" : ""
                }`}
            >
                <div className="concept-portal">
                    {zoomItems.length > 0 ? (
                        <>
                            <div className="concept-portal__copy">
                                <p className="concept-kicker">
                                    {settingValue(
                                        settings,
                                        "homepage_portal_eyebrow",
                                        "Scroll zoom moment",
                                    )}
                                </p>
                                <h2>{activeZoomItem.title}</h2>
                                <p>
                                    {activeZoomItem.description ||
                                        settingValue(
                                            settings,
                                            "homepage_portal_body",
                                            "Momen ini menjaga interaksi cinematic: visual membesar saat scroll, lalu mengecil lagi untuk membuka cerita berikutnya.",
                                        )}
                                </p>
                            </div>
                            <div className="concept-portal__frame">
                                <div
                                    className="concept-portal__image concept-portal__image--out"
                                    aria-hidden="true"
                                />
                                <div
                                    className="concept-portal__image concept-portal__image--in"
                                    aria-hidden="true"
                                />
                                {nextZoomItem && zoomItems.length > 1 && (
                                    <div className="concept-portal__next">
                                        <span>next scene</span>
                                        <strong>{nextZoomItem.title}</strong>
                                    </div>
                                )}
                            </div>
                        </>
                    ) : (
                        <div className="concept-portal__empty">
                            <p className="concept-kicker">Scroll zoom moment</p>
                            <h2>Zoom story belum dikurasi.</h2>
                            <p>Tambahkan momen Zoom dari Pengaturan Halaman Utama.</p>
                        </div>
                    )}
                </div>
            </section>

            {(breathingTitle || breathingBody || breathingImage) && (
                <section className="concept-breathing">
                    <div className="concept-breathing__image" aria-hidden="true" />
                    <div className="concept-breathing__content">
                        <p className="concept-kicker">
                            {settingValue(
                                settings,
                                "homepage_breathing_eyebrow",
                                "Tarik napas sebentar",
                            )}
                        </p>
                        <h2>{breathingTitle}</h2>
                        <p>{breathingBody}</p>
                    </div>
                </section>
            )}

            {horizontalItems.length > 0 && (
                <section
                    className="concept-horizontal"
                    ref={horizontalSectionRef}
                    aria-label="Explore Karang Sidemen"
                >
                    <div className="concept-horizontal__sticky">
                        <div className="concept-horizontal__intro">
                            <p className="concept-kicker">
                                {settingValue(
                                    settings,
                                    "homepage_horizontal_eyebrow",
                                    "Explore Karang Sidemen",
                                )}
                            </p>
                            <h2>
                                {settingValue(
                                    settings,
                                    "homepage_horizontal_title",
                                    "Geser vertikal, tapi rasanya masuk ke rute tersembunyi.",
                                )}
                            </h2>
                            <span className="concept-horizontal__hint">
                                {settingValue(
                                    settings,
                                    "homepage_horizontal_hint",
                                    "Scroll down to move sideways",
                                )}
                            </span>
                            <span className="concept-swipe-hint">Geser</span>
                        </div>
                        <div
                            className="concept-horizontal__track"
                            ref={horizontalTrackRef}
                        >
                            {horizontalItems.map((item, index) => (
                                <HorizontalStoryPanel
                                    item={item}
                                    index={index}
                                    key={`${item.title}-${index}`}
                                />
                            ))}
                        </div>
                        <div className="concept-horizontal__meter" aria-hidden="true">
                            <span />
                        </div>
                    </div>
                </section>
            )}

            <section id="experiences" className="concept-experiences">
                <div className="concept-section-title">
                    <p className="concept-kicker">
                        {settingValue(
                            settings,
                            "homepage_experience_eyebrow",
                            "Database-driven experiences",
                        )}
                    </p>
                    <h2>
                        {settingValue(
                            settings,
                            "homepage_experience_title",
                            "Setiap kartu datang dari data destinasi yang bisa dikelola admin.",
                        )}
                    </h2>
                    <span className="concept-swipe-hint">Geser</span>
                </div>
                <div className="concept-experience-grid">
                    {featuredDestinations.slice(0, 8).map((destination, index) => (
                        <Link
                            to={`/destinasi/${destination.id}`}
                            className="concept-experience-card"
                            key={destination.id}
                            aria-label={`Buka detail ${destination.name}`}
                        >
                            <img src={getImageUrl(destination)} alt="" />
                            <div>
                                <span>{String(index + 1).padStart(2, "0")}</span>
                                <h3>{destination.name}</h3>
                                <p>
                                    {destination.short_description ||
                                        destination.tourism_vibe ||
                                        destination.description}
                                </p>
                            </div>
                        </Link>
                    ))}
                </div>
            </section>

            <section className="concept-split">
                <div>
                    <p className="concept-kicker">
                        {settingValue(
                            settings,
                            "homepage_highlight_eyebrow",
                            "Highlight terverifikasi",
                        )}
                    </p>
                    <h2>
                        {settingValue(
                            settings,
                            "homepage_highlight_title",
                            "Air, hutan, camping, budaya, dan edukasi jadi cerita besar desa.",
                        )}
                    </h2>
                    <div className="concept-tags">
                        {Array.from(
                                new Set(
                                    destinationItems
                                    .flatMap((destination) =>
                                        normalizeList(destination.activity_keywords),
                                    )
                                    .slice(0, 14),
                            ),
                        ).map((tag) => (
                            <span key={tag}>{tag}</span>
                        ))}
                    </div>
                </div>
                <div className="concept-split__visual">
                    {featuredDestinations.slice(0, 3).map((destination) => (
                        <img
                            src={getImageUrl(destination)}
                            alt=""
                            key={destination.id}
                        />
                    ))}
                </div>
            </section>

            {reviewItems.length > 0 && (
                <section className="concept-reviews">
                    <div className="concept-section-title">
                        <p className="concept-kicker">
                            {settingValue(
                                settings,
                                "homepage_reviews_eyebrow",
                                "Suara pengunjung",
                            )}
                        </p>
                        <h2>
                            {settingValue(
                                settings,
                                "homepage_reviews_title",
                                "Review dibuat pendek, lokal, dan masuk akal.",
                            )}
                        </h2>
                        <span className="concept-swipe-hint">Geser</span>
                    </div>
                    <div className="concept-review-grid">
                        {reviewItems.slice(0, 3).map((review) => (
                            <article key={review.id}>
                                {review.photo_url && (
                                    <img
                                        className="concept-review-card__photo"
                                        src={review.photo_url}
                                        alt=""
                                    />
                                )}
                                <div className="concept-review-card__meta">
                                    <span>{getRatingStars(review.rating)}</span>
                                    {review.destination?.name && (
                                        <strong>{review.destination.name}</strong>
                                    )}
                                </div>
                                <p>"{review.review_text}"</p>
                                <div className="concept-review-card__person">
                                    <span>{review.reviewer_name}</span>
                                    {review.origin_city && <small>{review.origin_city}</small>}
                                </div>
                            </article>
                        ))}
                    </div>
                </section>
            )}

            <section className="concept-final">
                <div>
                    <p className="concept-kicker">
                        {settingValue(settings, "homepage_final_eyebrow", "Final pull")}
                    </p>
                    <h2>
                        {settingValue(
                            settings,
                            "homepage_final_title",
                            "Karang Sidemen harus terasa sebagai desa wisata hidup, bukan halaman destinasi tunggal.",
                        )}
                    </h2>
                </div>
                <Link to="/destinasi">
                    {settingValue(settings, "homepage_final_cta_label", "Lihat destinasi")}
                </Link>
            </section>
            </main>
            <Footer />
            <FloatingWhatsApp />
        </>
    );
}
