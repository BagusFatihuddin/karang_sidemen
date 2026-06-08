import { useState } from "react";
import { useQuery } from "@tanstack/react-query";

import { getDestinations } from "../services/api/destinations";
import { getReviews } from "../services/api/reviews";

const pageStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const controlsStyle = {
    display: "flex",
    flexWrap: "wrap",
    gap: "10px",
    marginBottom: "20px",
};

const inputStyle = {
    padding: "9px 10px",
    borderRadius: "6px",
    border: "1px solid #d1d5db",
    background: "#ffffff",
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    padding: "16px",
    background: "#ffffff",
};

const listStyle = {
    display: "grid",
    gap: "14px",
};

const headerStyle = {
    display: "flex",
    alignItems: "center",
    gap: "12px",
};

const avatarStyle = {
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

const textStyle = {
    color: "#4b5563",
    lineHeight: 1.6,
};

const buttonStyle = {
    padding: "9px 12px",
    borderRadius: "6px",
    background: "#ffffff",
    color: "#166534",
    border: "1px solid #86efac",
    fontWeight: 700,
    cursor: "pointer",
};

const paginationStyle = {
    display: "flex",
    flexWrap: "wrap",
    alignItems: "center",
    gap: "10px",
    marginTop: "20px",
};

const skeletonStyle = {
    height: "150px",
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const getInitials = (name) => {
    return name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();
};

const getDestinationName = (review) => {
    if (typeof review.destination_name === "string") {
        return review.destination_name;
    }

    if (typeof review.destination?.name === "string") {
        return review.destination.name;
    }

    return "";
};

export default function ReviewsPage() {
    const [destinationId, setDestinationId] = useState("");
    const [rating, setRating] = useState("");
    const [sort, setSort] = useState("latest");
    const [page, setPage] = useState(1);
    const { data: destinationsData } = useQuery({
        queryKey: ["destinations"],
        queryFn: getDestinations,
    });
    const { data, isLoading } = useQuery({
        queryKey: ["reviews", destinationId, rating, sort, page],
        queryFn: () =>
            getReviews({
                destination_id: destinationId,
                rating,
                sort,
                page,
            }),
    });

    const destinationsPayload = getPayload(destinationsData);
    const destinations = Array.isArray(destinationsPayload)
        ? destinationsPayload
        : [];
    const reviewsPayload = data?.data ?? {};
    const reviews = Array.isArray(reviewsPayload.data) ? reviewsPayload.data : [];
    const pagination = reviewsPayload.pagination ?? {};

    return (
        <main style={pageStyle}>
            <h1>Ulasan</h1>

            <div style={controlsStyle}>
                <select
                    value={destinationId}
                    onChange={(event) => {
                        setDestinationId(event.target.value);
                        setPage(1);
                    }}
                    style={inputStyle}
                >
                    <option value="">Semua destinasi</option>
                    {destinations.map((destination) => (
                        <option key={destination.id} value={destination.id}>
                            {destination.name}
                        </option>
                    ))}
                </select>

                <select
                    value={rating}
                    onChange={(event) => {
                        setRating(event.target.value);
                        setPage(1);
                    }}
                    style={inputStyle}
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
                    style={inputStyle}
                >
                    <option value="latest">Terbaru</option>
                    <option value="highest_rating">Rating tertinggi</option>
                </select>
            </div>

            {isLoading ? (
                <div style={listStyle}>
                    {[1, 2, 3].map((item) => (
                        <div key={item} style={skeletonStyle} />
                    ))}
                </div>
            ) : reviews.length === 0 ? (
                <p style={textStyle}>Belum ada ulasan.</p>
            ) : (
                <>
                    <div style={listStyle}>
                        {reviews.map((review) => {
                            const destinationName = getDestinationName(review);

                            return (
                                <article key={review.id} style={cardStyle}>
                                    <div style={headerStyle}>
                                        {review.photo_url ? (
                                            <img
                                                src={review.photo_url}
                                                alt={review.reviewer_name}
                                                style={avatarStyle}
                                            />
                                        ) : (
                                            <div style={avatarStyle}>
                                                {getInitials(review.reviewer_name) || "?"}
                                            </div>
                                        )}
                                        <div>
                                            <strong>{review.reviewer_name}</strong>
                                            <p style={{ ...textStyle, margin: "4px 0 0" }}>
                                                Rating {review.rating}/5
                                                {review.reviewer_city
                                                    ? ` - ${review.reviewer_city}`
                                                    : ""}
                                            </p>
                                        </div>
                                    </div>

                                    <p style={textStyle}>{review.review_text}</p>
                                    {destinationName && (
                                        <p style={textStyle}>
                                            Destinasi: {destinationName}
                                        </p>
                                    )}
                                    {review.created_at && (
                                        <p style={textStyle}>{review.created_at}</p>
                                    )}
                                </article>
                            );
                        })}
                    </div>

                    <div style={paginationStyle}>
                        <button
                            type="button"
                            disabled={page <= 1}
                            onClick={() => setPage((current) => current - 1)}
                            style={buttonStyle}
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
                            style={buttonStyle}
                        >
                            Berikutnya
                        </button>
                    </div>
                </>
            )}
        </main>
    );
}
