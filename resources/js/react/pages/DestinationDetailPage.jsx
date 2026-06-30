import { useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getDestination } from "../services/api/destinations";
import { getReviews } from "../services/api/reviews";
import { buildWhatsAppUrl } from "../services/whatsapp";
import { normalizeList } from "../utils/normalizeList";
import "./DestinationDetailPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

const getRatingStars = (rating) => "★".repeat(Math.max(0, Math.min(Number(rating) || 0, 5)));

const formatPrice = (price) => {
    if (price === null || price === undefined || Number(price) === 0) {
        return "Konfirmasi pengelola";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(price));
};

const normalizeImages = (destination) => {
    const images = Array.isArray(destination?.images)
        ? destination.images.map((image) => image.url).filter(Boolean)
        : [];

    if (destination?.thumbnail_url && !images.includes(destination.thumbnail_url)) {
        return [destination.thumbnail_url, ...images];
    }

    return images;
};

const getInitials = (name) =>
    name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();

export default function DestinationDetailPage() {
    const { id } = useParams();
    const [activeImageIndex, setActiveImageIndex] = useState(0);
    const [rating, setRating] = useState("");
    const [sort, setSort] = useState("latest");
    const [page, setPage] = useState(1);
    const { data: settingsData } = usePublicSettings();
    const { data, isLoading } = useQuery({
        queryKey: ["destination", id],
        queryFn: () => getDestination(id),
    });
    const { data: reviewsData, isLoading: reviewsLoading } = useQuery({
        queryKey: ["reviews", id, rating, sort, page],
        queryFn: () =>
            getReviews({
                destination_id: id,
                rating,
                sort,
                page,
            }),
    });

    const destination = getPayload(data);
    const settings = getPayload(settingsData);
    const images = useMemo(() => normalizeImages(destination), [destination]);
    const safeActiveImageIndex = images[activeImageIndex] ? activeImageIndex : 0;
    const activeImage = images[safeActiveImageIndex];
    const reviewsPayload = reviewsData?.data ?? {};
    const reviews = Array.isArray(reviewsPayload.data) ? reviewsPayload.data : [];
    const pagination = reviewsPayload.pagination ?? {};
    const whatsappNumber = destination?.whatsapp_number || settings?.global_whatsapp;
    const tags = [
        ...normalizeList(destination?.activity_keywords),
        ...normalizeList(destination?.tags),
    ].filter(Boolean);
    const uniqueTags = Array.from(new Set(tags)).slice(0, 8);
    const highlights = normalizeList(destination?.highlights);
    const hasRentalPrice =
        destination?.rental_price !== null &&
        destination?.rental_price !== undefined &&
        Number(destination.rental_price) > 0;

    if (isLoading) {
        return (
            <main className="destination-detail-page">
                <div className="destination-detail-skeleton destination-detail-skeleton--hero" />
            </main>
        );
    }

    return (
        <main
            className="destination-detail-page"
            style={{
                "--detail-hero-image": activeImage ? `url("${activeImage}")` : "none",
            }}
        >
            <section className="destination-detail-hero">
                <div className="destination-detail-hero__image" aria-hidden="true" />
                <div className="destination-detail-hero__content">
                    <Link to="/destinasi" className="destination-detail-back">
                        Kembali ke destinasi
                    </Link>
                    <p>{destination?.destination_type || "Destinasi Karang Sidemen"}</p>
                    <h1>{destination?.name}</h1>
                    <div className="destination-detail-hero__bottom">
                        <span>
                            {destination?.homepage_label ||
                                destination?.tourism_vibe ||
                                "Hidden gem"}
                        </span>
                        <p>
                            {destination?.short_description ||
                                destination?.tourism_vibe ||
                                "Pengalaman wisata alam Desa Wisata Karang Sidemen."}
                        </p>
                    </div>
                </div>
            </section>

            <section className="destination-detail-shell">
                <div className="destination-detail-layout">
                    <div className="destination-detail-main">
                        <section className="destination-gallery">
                            <div className="destination-gallery__stage">
                                {activeImage ? (
                                    <img src={activeImage} alt={destination?.name} />
                                ) : (
                                    <div className="destination-gallery__fallback">
                                        Karang Sidemen
                                    </div>
                                )}
                            </div>

                            {images.length > 1 && (
                                <div className="destination-gallery__thumbs">
                                    {images.map((image, index) => (
                                        <button
                                            className={
                                                index === safeActiveImageIndex
                                                    ? "destination-gallery__thumb destination-gallery__thumb--active"
                                                    : "destination-gallery__thumb"
                                            }
                                            key={`${image}-${index}`}
                                            type="button"
                                            onClick={() => setActiveImageIndex(index)}
                                            aria-label={`Lihat gambar ${index + 1}`}
                                        >
                                            <img src={image} alt="" />
                                        </button>
                                    ))}
                                </div>
                            )}
                        </section>

                        <section className="destination-story">
                            <p className="destination-detail-kicker">Cerita destinasi</p>
                            <h2>{destination?.tourism_vibe || destination?.name}</h2>
                            <p>{destination?.description}</p>

                            {uniqueTags.length > 0 && (
                                <div className="destination-detail-tags">
                                    {uniqueTags.map((tag) => (
                                        <span key={tag}>{tag}</span>
                                    ))}
                                </div>
                            )}
                        </section>

                        {highlights.length > 0 && (
                            <section className="destination-highlights">
                                <p className="destination-detail-kicker">Yang terasa di sini</p>
                                <div className="destination-highlights__grid">
                                    {highlights.map((highlight, index) => (
                                        <article key={highlight}>
                                            <span>{String(index + 1).padStart(2, "0")}</span>
                                            <p>{highlight}</p>
                                        </article>
                                    ))}
                                </div>
                            </section>
                        )}

                        {destination?.maps_url && (
                            <section className="destination-map">
                                <div>
                                    <p className="destination-detail-kicker">Lokasi</p>
                                    <h2>Buka titik lokasi sebelum berangkat.</h2>
                                    <span>
                                        Pastikan rute terakhir dikonfirmasi ke pengelola,
                                        terutama untuk spot alam yang aksesnya berubah
                                        mengikuti kondisi lapangan.
                                    </span>
                                </div>
                                <a
                                    href={destination.maps_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="destination-map__link"
                                >
                                    Buka Google Maps
                                </a>
                            </section>
                        )}

                        <section className="destination-reviews">
                            <div className="destination-reviews__head">
                                <div>
                                    <p className="destination-detail-kicker">
                                        Review destinasi
                                    </p>
                                    <h2>Suara pengunjung yang sudah datang.</h2>
                                </div>
                                <div className="destination-review-controls">
                                    <select
                                        value={rating}
                                        onChange={(event) => {
                                            setRating(event.target.value);
                                            setPage(1);
                                        }}
                                    >
                                        <option value="">Semua rating</option>
                                        <option value="5">5 bintang</option>
                                        <option value="4">4 bintang</option>
                                        <option value="3">3 bintang</option>
                                        <option value="2">2 bintang</option>
                                        <option value="1">1 bintang</option>
                                    </select>
                                    <select
                                        value={sort}
                                        onChange={(event) => {
                                            setSort(event.target.value);
                                            setPage(1);
                                        }}
                                    >
                                        <option value="latest">Terbaru</option>
                                        <option value="highest_rating">
                                            Rating tertinggi
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {reviewsLoading ? (
                                <div className="destination-detail-skeleton destination-detail-skeleton--reviews" />
                            ) : reviews.length === 0 ? (
                                <div className="destination-reviews__empty">
                                    Belum ada review publik untuk destinasi ini.
                                </div>
                            ) : (
                                <>
                                    <div className="destination-review-list">
                                        {reviews.map((review) => (
                                            <article
                                                className="destination-review-card"
                                                key={review.id}
                                            >
                                                {review.photo_url && (
                                                    <div className="destination-review-card__photo">
                                                        <img src={review.photo_url} alt="" />
                                                    </div>
                                                )}
                                                <div className="destination-review-card__avatar">
                                                    <span>
                                                        {getInitials(review.reviewer_name) ||
                                                            "?"}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div className="destination-review-card__meta">
                                                        <strong>
                                                            {review.reviewer_name}
                                                        </strong>
                                                        <span>
                                                            {getRatingStars(review.rating)}
                                                        </span>
                                                    </div>
                                                    <small>
                                                        {review.reviewer_city ||
                                                            "Pengunjung"}
                                                    </small>
                                                    <p>{review.review_text}</p>
                                                </div>
                                            </article>
                                        ))}
                                    </div>

                                    <div className="destination-pagination">
                                        <button
                                            type="button"
                                            disabled={page <= 1}
                                            onClick={() =>
                                                setPage((current) => current - 1)
                                            }
                                        >
                                            Sebelumnya
                                        </button>
                                        <span>
                                            Halaman {pagination.current_page ?? page}
                                        </span>
                                        <button
                                            type="button"
                                            disabled={
                                                pagination.last_page
                                                    ? page >= pagination.last_page
                                                    : reviews.length === 0
                                            }
                                            onClick={() =>
                                                setPage((current) => current + 1)
                                            }
                                        >
                                            Berikutnya
                                        </button>
                                    </div>
                                </>
                            )}
                        </section>
                    </div>

                    <aside className="destination-detail-aside">
                        <div className="destination-booking-card">
                            <p className="destination-detail-kicker">Rencana kunjungan</p>
                            <h2>Datang dengan info yang jelas.</h2>
                            <dl>
                                <div>
                                    <dt>Tiket masuk</dt>
                                    <dd>{formatPrice(destination?.entry_fee)}</dd>
                                </div>
                                <div>
                                    <dt>Parkir</dt>
                                    <dd>{formatPrice(destination?.parking_fee)}</dd>
                                </div>
                                {hasRentalPrice && (
                                    <div>
                                        <dt>Harga sewa</dt>
                                        <dd>{formatPrice(destination?.rental_price)}</dd>
                                    </div>
                                )}
                            </dl>

                            {destination?.facilities && (
                                <div className="destination-facilities">
                                    <strong>Fasilitas</strong>
                                    <p>{destination.facilities}</p>
                                </div>
                            )}

                            <div className="destination-booking-card__actions">
                                {destination?.maps_url && (
                                    <a
                                        href={destination.maps_url}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        Lihat Maps
                                    </a>
                                )}

                                {whatsappNumber && hasRentalPrice && (
                                    <a
                                        href={buildWhatsAppUrl(
                                            `Halo, saya ingin bertanya atau booking di ${destination?.name}`,
                                            whatsappNumber,
                                        )}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="destination-booking-card__primary"
                                    >
                                        Booking WhatsApp
                                    </a>
                                )}
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </main>
    );
}
