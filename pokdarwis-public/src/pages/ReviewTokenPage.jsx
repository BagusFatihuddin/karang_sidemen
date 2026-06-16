import { useEffect, useMemo, useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import apiClient from "../services/api/client";
import "./ReviewTokenPage.css";

const getTokenReview = async (token) => apiClient.get(`/review/${token}`);

const postTokenReview = async (token, formData) =>
    apiClient.post(`/review/${token}`, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });

const getTokenState = (error) =>
    error?.response?.data?.errors?.reason || "invalid";

const tokenMessages = {
    not_found: "Token review ini tidak valid.",
    expired: "Token review ini sudah expired.",
    used: "Token review ini sudah pernah digunakan.",
    invalid: "Token review ini tidak valid.",
};

const ratingOptions = [
    { label: "5", value: "5", hint: "Mantap" },
    { label: "4", value: "4", hint: "Bagus" },
    { label: "3", value: "3", hint: "Cukup" },
    { label: "2", value: "2", hint: "Kurang" },
    { label: "1", value: "1", hint: "Perlu dibenahi" },
];

const getFieldError = (errors, key) => {
    const value = errors?.[key];

    if (Array.isArray(value)) {
        return value.join(" ");
    }

    return value || "";
};

const getSubmitErrorMessage = (error) => {
    const response = error?.response?.data;
    const status = error?.response?.status;

    if (!error?.response) {
        return "Review belum terkirim. Cek koneksi internet kamu lalu coba lagi.";
    }

    if (status === 413) {
        return "Foto terlalu besar untuk dikirim. Coba kompres atau pilih foto lain.";
    }

    if (status === 422) {
        return response?.message || "Ada data yang belum sesuai. Cek lagi field yang ditandai.";
    }

    if (status === 429) {
        return "Terlalu banyak percobaan kirim review. Tunggu sebentar lalu coba lagi.";
    }

    if (status >= 500) {
        return "Server sedang bermasalah. Tunggu sebentar lalu coba lagi.";
    }

    return response?.message || "Review gagal dikirim. Coba ulangi sebentar lagi.";
};

export default function ReviewTokenPage() {
    const { token } = useParams();
    const [form, setForm] = useState({
        reviewer_name: null,
        reviewer_city: null,
        rating: "5",
        review_text: "",
        photo: null,
    });
    const [validationErrors, setValidationErrors] = useState({});
    const [submitError, setSubmitError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [isSuccess, setIsSuccess] = useState(false);
    const { data, isLoading, error } = useQuery({
        queryKey: ["review-token", token],
        queryFn: () => getTokenReview(token),
        retry: false,
    });

    const tokenData = data?.data?.data ?? {};
    const tokenState = error ? getTokenState(error) : "";
    const reviewerName = form.reviewer_name ?? tokenData.visitor_name ?? "";
    const reviewerCity = form.reviewer_city ?? tokenData.visitor_city ?? "";
    const photoPreview = useMemo(
        () => (form.photo ? URL.createObjectURL(form.photo) : ""),
        [form.photo],
    );

    useEffect(() => {
        if (!photoPreview) {
            return undefined;
        }

        return () => URL.revokeObjectURL(photoPreview);
    }, [photoPreview]);

    const updateField = (field, value) => {
        setForm((current) => ({
            ...current,
            [field]: value,
        }));
        setValidationErrors((current) => ({
            ...current,
            [field]: undefined,
        }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setValidationErrors({});
        setSubmitError("");

        if (!form.review_text.trim()) {
            setValidationErrors({
                review_text: ["Review wajib diisi."],
            });
            setSubmitError("Tulis review dulu sebelum dikirim.");
            return;
        }

        setIsSubmitting(true);

        const formData = new FormData();
        formData.append("rating", form.rating);
        formData.append("review_text", form.review_text.trim());
        formData.append("reviewer_name", reviewerName.trim());
        formData.append("reviewer_city", reviewerCity.trim());

        if (form.photo) {
            formData.append("photo", form.photo);
        }

        try {
            await postTokenReview(token, formData);
            setIsSuccess(true);
        } catch (submitRequestError) {
            const response = submitRequestError?.response?.data;

            if (response?.errors && typeof response.errors === "object") {
                setValidationErrors(response.errors);
            }

            setSubmitError(getSubmitErrorMessage(submitRequestError));
        } finally {
            setIsSubmitting(false);
        }
    };

    if (isLoading) {
        return (
            <main className="review-token-page">
                <div className="review-token-shell">
                    <div className="review-token-skeleton" />
                </div>
            </main>
        );
    }

    if (tokenState) {
        return (
            <main className="review-token-page">
                <section className="review-token-state">
                    <p>Review tidak tersedia</p>
                    <h1>{tokenMessages[tokenState] || tokenMessages.invalid}</h1>
                    <Link to="/">Kembali ke homepage</Link>
                </section>
            </main>
        );
    }

    if (isSuccess) {
        return (
            <main className="review-token-page review-token-page--success">
                <section className="review-token-state">
                    <p>Terima kasih</p>
                    <h1>Review berhasil dikirim.</h1>
                    <span>
                        Review akan tampil setelah disetujui admin POKDARWIS.
                    </span>
                    <Link to="/">Kembali ke homepage</Link>
                </section>
            </main>
        );
    }

    return (
        <main className="review-token-page">
            <section className="review-token-shell">
                <div className="review-token-intro">
                    <Link to="/" className="review-token-back">
                        Desa Wisata Karang Sidemen
                    </Link>
                    <p>Review pengunjung</p>
                    <h1>Bantu pengunjung berikutnya merasa lebih yakin.</h1>
                    <div className="review-token-destination">
                        <span>Destinasi</span>
                        <strong>
                            {tokenData.destination_name || "Karang Sidemen"}
                        </strong>
                    </div>
                </div>

                <form className="review-token-form" onSubmit={handleSubmit}>
                    <div className="review-token-form__header">
                        <p>Isi singkat saja</p>
                        <h2>Ceritakan pengalamanmu dengan jujur.</h2>
                    </div>

                    <div className="review-token-grid">
                        <label className="review-token-field">
                            <span>Nama</span>
                            <input
                                type="text"
                                value={reviewerName}
                                onChange={(event) =>
                                    updateField("reviewer_name", event.target.value)
                                }
                                placeholder="Nama kamu"
                            />
                            {getFieldError(validationErrors, "reviewer_name") && (
                                <small>
                                    {getFieldError(validationErrors, "reviewer_name")}
                                </small>
                            )}
                        </label>

                        <label className="review-token-field">
                            <span>Kota</span>
                            <input
                                type="text"
                                value={reviewerCity}
                                onChange={(event) =>
                                    updateField("reviewer_city", event.target.value)
                                }
                                placeholder="Contoh: Mataram"
                            />
                            {getFieldError(validationErrors, "reviewer_city") && (
                                <small>
                                    {getFieldError(validationErrors, "reviewer_city")}
                                </small>
                            )}
                        </label>
                    </div>

                    <fieldset className="review-token-rating">
                        <legend>Rating</legend>
                        <div>
                            {ratingOptions.map((option) => (
                                <label
                                    className={
                                        form.rating === option.value
                                            ? "review-token-rating__option review-token-rating__option--active"
                                            : "review-token-rating__option"
                                    }
                                    key={option.value}
                                >
                                    <input
                                        type="radio"
                                        name="rating"
                                        value={option.value}
                                        checked={form.rating === option.value}
                                        onChange={(event) =>
                                            updateField("rating", event.target.value)
                                        }
                                    />
                                    <strong>{option.label}</strong>
                                    <span>{option.hint}</span>
                                </label>
                            ))}
                        </div>
                        {getFieldError(validationErrors, "rating") && (
                            <small>{getFieldError(validationErrors, "rating")}</small>
                        )}
                    </fieldset>

                    <label className="review-token-field">
                        <span>Review</span>
                        <textarea
                            value={form.review_text}
                            onChange={(event) =>
                                updateField("review_text", event.target.value)
                            }
                            rows={6}
                            required
                            placeholder="Contoh: Airnya dingin, tempatnya enak buat santai pagi."
                        />
                        {getFieldError(validationErrors, "review_text") && (
                            <small>
                                {getFieldError(validationErrors, "review_text")}
                            </small>
                        )}
                    </label>

                    <label className="review-token-photo">
                        <input
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            onChange={(event) =>
                                updateField("photo", event.target.files?.[0] || null)
                            }
                        />
                        <span>
                            {photoPreview ? "Ganti foto" : "Tambah foto opsional"}
                        </span>
                        <small>JPG, PNG, atau WEBP. Maksimal 2MB.</small>
                    </label>

                    {photoPreview && (
                        <div className="review-token-preview">
                            <img src={photoPreview} alt="Preview foto review" />
                            <button
                                type="button"
                                onClick={() => updateField("photo", null)}
                            >
                                Hapus foto
                            </button>
                        </div>
                    )}

                    {getFieldError(validationErrors, "photo") && (
                        <p className="review-token-error">
                            {getFieldError(validationErrors, "photo")}
                        </p>
                    )}
                    {submitError && (
                        <p className="review-token-error">{submitError}</p>
                    )}

                    <button
                        className="review-token-submit"
                        type="submit"
                        disabled={isSubmitting}
                    >
                        {isSubmitting ? "Mengirim..." : "Kirim Review"}
                    </button>
                </form>
            </section>
        </main>
    );
}
