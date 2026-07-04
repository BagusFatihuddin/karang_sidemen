import { useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { getDestinations } from "../services/api/destinations";
import { getReviews } from "../services/api/reviews";
import { usePublicSettings } from "../hooks/usePublicSettings";
import "./ReviewsPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const getInitials = (name) =>
    name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();

const getDestinationName = (review) => {
    if (typeof review.destination_name === "string") {
        return review.destination_name;
    }

    if (typeof review.destination?.name === "string") {
        return review.destination.name;
    }

    return "";
};

const getRatingStars = (rating) =>
    "★".repeat(Math.max(0, Math.min(Number(rating) || 0, 5)));

const defaultReviewsHeroImage =
    "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1800&q=88";

const formatDate = (value) => {
    if (!value) {
        return "";
    }

    return new Intl.DateTimeFormat("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    }).format(new Date(value));
};

export default function ReviewsPage() {
    const [destinationId, setDestinationId] = useState("");
    const [rating, setRating] = useState("");
    const [sort, setSort] = useState("latest");
    const [page, setPage] = useState(1);
    const { data: settingsData } = usePublicSettings();
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
    const destinations = useMemo(
        () => (Array.isArray(destinationsPayload) ? destinationsPayload : []),
        [destinationsPayload],
    );
    const reviewsPayload = data?.data ?? {};
    const reviews = Array.isArray(reviewsPayload.data)
        ? reviewsPayload.data
        : [];
    const pagination = reviewsPayload.pagination ?? {};
    const heroReview = reviews[0];
    const settings = settingsData?.data?.data ?? settingsData?.data ?? {};
    const heroImage =
        settings.media_reviews_hero_image_url || defaultReviewsHeroImage;

    return (
        <main
            className="reviews-page"
            style={{ "--reviews-hero-image": `url("${heroImage}")` }}
        >
            <section className="reviews-hero">
                <div className="reviews-hero__content">
                    <p>Suara pengunjung</p>
                    <h1>
                        Ulasan yang terasa lokal, singkat, dan bisa dipercaya
                    </h1>
                    <div className="reviews-hero__bottom">
                        <span>
                            {pagination.total ?? reviews.length ?? "..."} ulasan
                        </span>

                        <p>
                            Baca pengalaman orang yang sudah datang ke destinasi
                            Karang Sidemen. Ulasan dipilih agar tetap terasa
                            nyata dan bermanfaat bagi Anda.
                        </p>
                    </div>
                </div>
                {heroReview && (
                    <article className="reviews-hero-card">
                        {heroReview.photo_url && (
                            <img
                                className="reviews-hero-card__photo"
                                src={heroReview.photo_url}
                                alt=""
                            />
                        )}
                        <span>{getRatingStars(heroReview.rating)}</span>
                        <p>"{heroReview.review_text}"</p>
                        <strong>{heroReview.reviewer_name}</strong>
                    </article>
                )}
            </section>

            <section className="reviews-shell">
                <div className="reviews-controls">
                    <div>
                        <p>Filter ulasan</p>
                        <h2>Temukan cerita dari destinasi yang Anda incar.</h2>
                    </div>

                    <div className="reviews-controls__inputs">
                        <select
                            value={destinationId}
                            onChange={(event) => {
                                setDestinationId(event.target.value);
                                setPage(1);
                            }}
                        >
                            <option value="">Semua destinasi</option>
                            {destinations.map((destination) => (
                                <option
                                    key={destination.id}
                                    value={destination.id}
                                >
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

                {isLoading ? (
                    <div className="reviews-grid">
                        {[1, 2, 3, 4, 5, 6].map((item) => (
                            <div className="reviews-skeleton" key={item} />
                        ))}
                    </div>
                ) : reviews.length === 0 ? (
                    <section className="reviews-empty">
                        <p>Belum ada review untuk filter ini.</p>
                        <button
                            type="button"
                            onClick={() => {
                                setDestinationId("");
                                setRating("");
                                setSort("latest");
                                setPage(1);
                            }}
                        >
                            Tampilkan semua lagi
                        </button>
                    </section>
                ) : (
                    <>
                        <div className="reviews-grid">
                            {reviews.map((review) => {
                                const destinationName =
                                    getDestinationName(review);

                                return (
                                    <article
                                        className="review-card"
                                        key={review.id}
                                    >
                                        {review.photo_url && (
                                            <div className="review-card__photo">
                                                <img
                                                    src={review.photo_url}
                                                    alt=""
                                                />
                                            </div>
                                        )}
                                        <div className="review-card__head">
                                            <span>
                                                {getInitials(
                                                    review.reviewer_name,
                                                ) || "?"}
                                            </span>
                                            <div>
                                                <strong>
                                                    {review.reviewer_name}
                                                </strong>
                                                <small>
                                                    {review.reviewer_city ||
                                                        "Pengunjung"}
                                                </small>
                                            </div>
                                        </div>

                                        <div className="review-card__rating">
                                            {getRatingStars(review.rating)}
                                        </div>
                                        <p>"{review.review_text}"</p>

                                        <div className="review-card__foot">
                                            {destinationName ? (
                                                <Link
                                                    to={`/destinasi/${review.destination_id}`}
                                                >
                                                    {destinationName}
                                                </Link>
                                            ) : (
                                                <span>Karang Sidemen</span>
                                            )}
                                            {review.created_at && (
                                                <small>
                                                    {formatDate(
                                                        review.created_at,
                                                    )}
                                                </small>
                                            )}
                                        </div>
                                    </article>
                                );
                            })}
                        </div>

                        <div className="reviews-pagination">
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
        </main>
    );
}
