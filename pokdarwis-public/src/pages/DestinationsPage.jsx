import { useEffect, useMemo, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { getDestinations } from "../services/api/destinations";
import { usePublicSettings } from "../hooks/usePublicSettings";
import { normalizeList } from "../utils/normalizeList";
import "./DestinationsPage.css";

const typeOptions = [
    { label: "Semua", value: "" },
    { label: "Air", value: "air" },
    { label: "Camping", value: "camping" },
    { label: "Alam", value: "alam" },
    { label: "Edukasi", value: "edukasi" },
    { label: "Kuliner", value: "kuliner" },
    { label: "Lainnya", value: "lainnya" },
];

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const getImageUrl = (destination) =>
    destination?.thumbnail_url || destination?.images?.[0]?.url || "";

const formatEntryFee = (entryFee) => {
    if (entryFee === null || entryFee === undefined || Number(entryFee) === 0) {
        return "Konfirmasi pengelola";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(entryFee));
};

const getDestinationOrder = (destination, fallback = 999) =>
    Number.isFinite(Number(destination?.homepage_sort_order))
        ? Number(destination.homepage_sort_order)
        : fallback;

export default function DestinationsPage() {
    const [searchParams] = useSearchParams();
    const activeType = searchParams.get("type") || "";
    const [page, setPage] = useState(1);

    useEffect(() => {
        setPage(1);
    }, [activeType]);

    const { data, isLoading } = useQuery({
        queryKey: ["destinations", activeType, page],
        queryFn: () => getDestinations({ type: activeType, page }),
    });
    const { data: settingsData } = usePublicSettings();
    const settings = settingsData?.data?.data ?? settingsData?.data ?? {};

    const responsePayload = data?.data ?? {};
    const payload = getPayload(data);
    const pagination = responsePayload.pagination ?? {};
    const destinations = useMemo(
        () =>
            (Array.isArray(payload) ? payload : []).sort(
                (a, b) =>
                    getDestinationOrder(a) - getDestinationOrder(b) ||
                    a.name.localeCompare(b.name),
            ),
        [payload],
    );
    const heroDestination =
        destinations.find((destination) => destination.is_featured_homepage) ||
        destinations[0];
    const heroImage =
        settings.media_destinations_hero_image_url || getImageUrl(heroDestination);
    const activityTags = useMemo(
        () =>
            Array.from(
                new Set(
                    destinations
                        .flatMap((destination) =>
                            normalizeList(destination.activity_keywords),
                        )
                        .filter(Boolean),
                ),
            ).slice(0, 9),
        [destinations],
    );

    return (
        <main
            className="destinations-page"
            style={{
                "--destination-hero-image": heroImage ? `url("${heroImage}")` : "none",
            }}
        >
            <section className="destinations-hero">
                <div className="destinations-hero__image" aria-hidden="true" />
                <div className="destinations-hero__content">
                    <p>Explore Karang Sidemen</p>
                    <h1>Destinasi alam yang hidup dari cerita desa.</h1>
                    <div className="destinations-hero__bottom">
                        <span>
                            {(pagination.total ?? destinations.length) || "..."} destinasi aktif
                        </span>
                        <p>
                            Danau, air terjun, hutan, camping, dan ruang tenang
                            yang bisa dikelola langsung dari admin.
                        </p>
                    </div>
                </div>
            </section>

            <section className="destinations-shell">
                <div className="destinations-filter">
                    <div>
                        <p className="destinations-kicker">Pilih vibe</p>
                        <h2>
                            {activeType
                                ? `Mode ${activeType}`
                                : "Semua pengalaman wisata"}
                        </h2>
                    </div>
                    <div className="destinations-filter__chips">
                        {typeOptions.map((type) => (
                            <Link
                                key={type.label}
                                to={
                                    type.value
                                        ? `/destinasi?type=${type.value}`
                                        : "/destinasi"
                                }
                                className={
                                    activeType === type.value
                                        ? "destinations-chip destinations-chip--active"
                                        : "destinations-chip"
                                }
                            >
                                {type.label}
                            </Link>
                        ))}
                    </div>
                </div>

                {activityTags.length > 0 && (
                    <div className="destinations-tags" aria-label="Aktivitas wisata">
                        {activityTags.map((tag) => (
                            <span key={tag}>{tag}</span>
                        ))}
                    </div>
                )}

                {isLoading ? (
                    <div className="destinations-grid">
                        {[1, 2, 3, 4, 5, 6].map((item) => (
                            <div className="destinations-skeleton" key={item} />
                        ))}
                    </div>
                ) : destinations.length === 0 ? (
                    <div className="destinations-empty">
                        <p>Belum ada destinasi untuk filter ini.</p>
                        <Link to="/destinasi">Lihat semua destinasi</Link>
                    </div>
                ) : (
                    <>
                        <div className="destinations-grid">
                            {destinations.map((destination, index) => {
                                const itemNumber =
                                    (pagination.from ?? (page - 1) * 15 + 1) + index;

                                return (
                                    <article className="destination-card" key={destination.id}>
                                        <Link
                                            to={`/destinasi/${destination.id}`}
                                            className="destination-card__media"
                                            aria-label={`Buka detail ${destination.name}`}
                                        >
                                            {getImageUrl(destination) ? (
                                                <img src={getImageUrl(destination)} alt="" />
                                            ) : (
                                                <div className="destination-card__fallback">
                                                    Karang Sidemen
                                                </div>
                                            )}
                                            <span>{String(itemNumber).padStart(2, "0")}</span>
                                        </Link>
                                        <div className="destination-card__body">
                                            <div className="destination-card__meta">
                                                {destination.destination_type && (
                                                    <span>{destination.destination_type}</span>
                                                )}
                                                <strong>{formatEntryFee(destination.entry_fee)}</strong>
                                            </div>
                                            <h3>{destination.name}</h3>
                                            <p>
                                                {destination.short_description ||
                                                    destination.tourism_vibe ||
                                                    destination.description}
                                            </p>
                                            <div className="destination-card__tags">
                                                {[
                                                    ...normalizeList(
                                                        destination.activity_keywords,
                                                    ),
                                                    ...normalizeList(destination.tags),
                                                ]
                                                    .slice(0, 3)
                                                    .map((tag) => (
                                                        <span key={tag}>{tag}</span>
                                                    ))}
                                            </div>
                                            <Link
                                                to={`/destinasi/${destination.id}`}
                                                className="destination-card__link"
                                            >
                                                Buka detail
                                            </Link>
                                        </div>
                                    </article>
                                );
                            })}
                        </div>

                        <div className="destinations-pagination">
                            <button
                                type="button"
                                disabled={page <= 1}
                                onClick={() => setPage((current) => current - 1)}
                            >
                                Sebelumnya
                            </button>
                            <span>
                                Halaman {pagination.current_page ?? page}
                                {pagination.last_page ? ` dari ${pagination.last_page}` : ""}
                            </span>
                            <button
                                type="button"
                                disabled={
                                    pagination.last_page
                                        ? page >= pagination.last_page
                                        : destinations.length === 0
                                }
                                onClick={() => setPage((current) => current + 1)}
                            >
                                Berikutnya
                            </button>
                        </div>
                    </>
                )}
            </section>
        </main>
    );
}
