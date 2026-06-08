import { useState } from "react";
import { useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getDestination } from "../services/api/destinations";
import { getReviews } from "../services/api/reviews";
import { buildWhatsAppUrl } from "../services/whatsapp";

const pageStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const gridStyle = {
    display: "grid",
    gap: "24px",
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    padding: "16px",
    background: "#ffffff",
};

const imageWrapStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    overflow: "hidden",
    background: "#f3f4f6",
};

const imageStyle = {
    width: "100%",
    aspectRatio: "16 / 9",
    objectFit: "cover",
    display: "block",
};

const fallbackImageStyle = {
    ...imageStyle,
    display: "grid",
    placeItems: "center",
    color: "#6b7280",
    fontWeight: 700,
};

const rowStyle = {
    display: "flex",
    flexWrap: "wrap",
    gap: "10px",
    alignItems: "center",
};

const buttonStyle = {
    display: "inline-block",
    padding: "10px 14px",
    borderRadius: "6px",
    background: "#15803d",
    color: "#ffffff",
    border: "1px solid #15803d",
    textDecoration: "none",
    fontWeight: 700,
    cursor: "pointer",
};

const outlineButtonStyle = {
    ...buttonStyle,
    background: "#ffffff",
    color: "#166534",
    border: "1px solid #86efac",
};

const badgeStyle = {
    display: "inline-block",
    padding: "4px 8px",
    borderRadius: "999px",
    background: "#dcfce7",
    color: "#166534",
    fontSize: "12px",
    fontWeight: 700,
};

const textStyle = {
    color: "#4b5563",
    lineHeight: 1.6,
};

const skeletonStyle = {
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const reviewCardStyle = {
    borderBottom: "1px solid #e5e7eb",
    padding: "14px 0",
};

const reviewHeaderStyle = {
    display: "flex",
    alignItems: "center",
    gap: "12px",
};

const reviewAvatarStyle = {
    width: "48px",
    height: "48px",
    borderRadius: "50%",
    objectFit: "cover",
    background: "#dcfce7",
    color: "#166534",
    display: "grid",
    placeItems: "center",
    fontWeight: 700,
};

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

const formatPrice = (price) => {
    if (price === null || price === undefined || Number(price) === 0) {
        return "Gratis";
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

const getInitials = (name) => {
    return name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();
};

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
    const images = normalizeImages(destination);
    const safeActiveImageIndex = images[activeImageIndex] ? activeImageIndex : 0;
    const activeImage = images[safeActiveImageIndex];
    const reviewsPayload = reviewsData?.data ?? {};
    const reviews = Array.isArray(reviewsPayload.data) ? reviewsPayload.data : [];
    const pagination = reviewsPayload.pagination ?? {};
    const whatsappNumber =
        destination?.whatsapp_number || settings?.global_whatsapp;

    if (isLoading) {
        return (
            <main style={pageStyle}>
                <div style={{ ...skeletonStyle, height: "360px" }} />
            </main>
        );
    }

    return (
        <main style={pageStyle}>
            <div style={gridStyle}>
                <section>
                    <div style={imageWrapStyle}>
                        {activeImage ? (
                            <img
                                src={activeImage}
                                alt={destination?.name}
                                style={imageStyle}
                            />
                        ) : (
                            <div style={fallbackImageStyle}>Destinasi</div>
                        )}
                    </div>

                    {images.length > 1 && (
                        <div style={{ ...rowStyle, marginTop: "12px" }}>
                            <button
                                type="button"
                                onClick={() =>
                                    setActiveImageIndex((current) =>
                                        current === 0 ? images.length - 1 : current - 1,
                                    )
                                }
                                style={outlineButtonStyle}
                            >
                                Sebelumnya
                            </button>
                            <button
                                type="button"
                                onClick={() =>
                                    setActiveImageIndex(
                                        (current) => (current + 1) % images.length,
                                    )
                                }
                                style={outlineButtonStyle}
                            >
                                Berikutnya
                            </button>
                        </div>
                    )}
                </section>

                <section style={cardStyle}>
                    {destination?.destination_type && (
                        <span style={badgeStyle}>
                            {destination.destination_type}
                        </span>
                    )}
                    <h1>{destination?.name}</h1>
                    <p style={textStyle}>{destination?.description}</p>

                    {destination?.facilities && (
                        <>
                            <h2>Fasilitas</h2>
                            <p style={textStyle}>{destination.facilities}</p>
                        </>
                    )}

                    <h2>Harga</h2>
                    <p style={textStyle}>Tiket masuk: {formatPrice(destination?.entry_fee)}</p>
                    <p style={textStyle}>Parkir: {formatPrice(destination?.parking_fee)}</p>
                    <p style={textStyle}>Sewa: {formatPrice(destination?.rental_price)}</p>

                    <div style={rowStyle}>
                        {destination?.maps_url && (
                            <a
                                href={destination.maps_url}
                                target="_blank"
                                rel="noreferrer"
                                style={outlineButtonStyle}
                            >
                                Lihat Maps
                            </a>
                        )}

                        {whatsappNumber && (
                            <a
                                href={buildWhatsAppUrl(
                                    `Halo, saya ingin booking di ${destination?.name}`,
                                    whatsappNumber,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                style={buttonStyle}
                            >
                                Booking WhatsApp
                            </a>
                        )}
                    </div>
                </section>

                <section style={cardStyle}>
                    <h2>Ulasan</h2>
                    <div style={rowStyle}>
                        <select
                            value={rating}
                            onChange={(event) => {
                                setRating(event.target.value);
                                setPage(1);
                            }}
                        >
                            <option value="">Semua rating</option>
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                        <select
                            value={sort}
                            onChange={(event) => {
                                setSort(event.target.value);
                                setPage(1);
                            }}
                        >
                            <option value="latest">Terbaru</option>
                            <option value="highest_rating">Rating tertinggi</option>
                        </select>
                    </div>

                    {reviewsLoading ? (
                        <div style={{ ...skeletonStyle, height: "180px", marginTop: "16px" }} />
                    ) : reviews.length === 0 ? (
                        <p style={textStyle}>Belum ada ulasan.</p>
                    ) : (
                        <>
                            {reviews.map((review) => (
                                <article key={review.id} style={reviewCardStyle}>
                                    <div style={reviewHeaderStyle}>
                                        {review.photo_url ? (
                                            <img
                                                src={review.photo_url}
                                                alt={review.reviewer_name}
                                                style={reviewAvatarStyle}
                                            />
                                        ) : (
                                            <div style={reviewAvatarStyle}>
                                                {getInitials(review.reviewer_name) || "?"}
                                            </div>
                                        )}
                                        <strong>{review.reviewer_name}</strong>
                                    </div>
                                    <p style={textStyle}>
                                        Rating {review.rating}/5
                                        {review.reviewer_city
                                            ? ` - ${review.reviewer_city}`
                                            : ""}
                                    </p>
                                    <p style={textStyle}>{review.review_text}</p>
                                </article>
                            ))}

                            <div style={{ ...rowStyle, marginTop: "16px" }}>
                                <button
                                    type="button"
                                    disabled={page <= 1}
                                    onClick={() => setPage((current) => current - 1)}
                                    style={outlineButtonStyle}
                                >
                                    Sebelumnya
                                </button>
                                <span style={textStyle}>
                                    Halaman {pagination.current_page ?? page}
                                </span>
                                <button
                                    type="button"
                                    disabled={
                                        pagination.last_page
                                            ? page >= pagination.last_page
                                            : reviews.length === 0
                                    }
                                    onClick={() => setPage((current) => current + 1)}
                                    style={outlineButtonStyle}
                                >
                                    Berikutnya
                                </button>
                            </div>
                        </>
                    )}
                </section>
            </div>
        </main>
    );
}
