import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import apiClient from "../services/api/client";

const pageStyle = {
    maxWidth: "720px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    padding: "16px",
    background: "#ffffff",
};

const formStyle = {
    display: "grid",
    gap: "14px",
};

const fieldStyle = {
    display: "grid",
    gap: "6px",
};

const inputStyle = {
    padding: "10px",
    borderRadius: "6px",
    border: "1px solid #d1d5db",
};

const buttonStyle = {
    padding: "10px 14px",
    borderRadius: "6px",
    background: "#15803d",
    color: "#ffffff",
    border: "1px solid #15803d",
    fontWeight: 700,
    cursor: "pointer",
};

const textStyle = {
    color: "#4b5563",
    lineHeight: 1.6,
};

const errorStyle = {
    color: "#b91c1c",
    margin: "4px 0 0",
};

const successStyle = {
    color: "#166534",
    fontWeight: 700,
};

const skeletonStyle = {
    height: "260px",
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const previewStyle = {
    width: "120px",
    height: "120px",
    borderRadius: "8px",
    objectFit: "cover",
    border: "1px solid #e5e7eb",
};

const getTokenReview = async (token) => {
    return apiClient.get(`/review/${token}`);
};

const postTokenReview = async (token, formData) => {
    return apiClient.post(`/review/${token}`, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};

const getTokenState = (error) => {
    return error?.response?.data?.errors?.reason || "invalid";
};

const tokenMessages = {
    not_found: "Token tidak valid.",
    expired: "Token sudah expired.",
    used: "Token sudah digunakan.",
    invalid: "Token tidak valid.",
};

export default function ReviewTokenPage() {
    const { token } = useParams();
    const [form, setForm] = useState({
        reviewer_name: "",
        reviewer_city: "",
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
        onSuccess: (queryData) => {
            const tokenInfo = queryData?.data?.data ?? {};
            if (tokenInfo.token_valid) {
                setForm((current) => ({
                    ...current,
                    reviewer_name:
                        current.reviewer_name || tokenInfo.visitor_name || "",
                    reviewer_city:
                        current.reviewer_city || tokenInfo.visitor_city || "",
                }));
            }
        },
    });

    const tokenData = data?.data?.data ?? {};
    const tokenState = error ? getTokenState(error) : "";

    useEffect(() => {
        if (!form.photo) {
            return undefined;
        }

        const previewUrl = URL.createObjectURL(form.photo);

        return () => URL.revokeObjectURL(previewUrl);
    }, [form.photo]);

    const updateField = (field, value) => {
        setForm((current) => ({
            ...current,
            [field]: value,
        }));
    };

    const photoPreview = form.photo ? URL.createObjectURL(form.photo) : "";

    const handleSubmit = async (event) => {
        event.preventDefault();
        setValidationErrors({});
        setSubmitError("");
        setIsSubmitting(true);

        const formData = new FormData();
        formData.append("rating", form.rating);
        formData.append("review_text", form.review_text);
        formData.append("reviewer_name", form.reviewer_name);
        formData.append("reviewer_city", form.reviewer_city);

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

            setSubmitError(response?.message || "Review gagal dikirim.");
        } finally {
            setIsSubmitting(false);
        }
    };

    if (isLoading) {
        return (
            <main style={pageStyle}>
                <div style={skeletonStyle} />
            </main>
        );
    }

    if (tokenState) {
        return (
            <main style={pageStyle}>
                <section style={cardStyle}>
                    <h1>Review tidak tersedia</h1>
                    <p style={textStyle}>
                        {tokenMessages[tokenState] || tokenMessages.invalid}
                    </p>
                </section>
            </main>
        );
    }

    if (isSuccess) {
        return (
            <main style={pageStyle}>
                <section style={cardStyle}>
                    <h1>Terima kasih</h1>
                    <p style={successStyle}>Review berhasil dikirim.</p>
                </section>
            </main>
        );
    }

    return (
        <main style={pageStyle}>
            <section style={cardStyle}>
                <h1>Berikan Review</h1>
                <p style={textStyle}>
                    {tokenData.destination_name
                        ? `Destinasi: ${tokenData.destination_name}`
                        : "Silakan isi review Anda."}
                </p>

                <form onSubmit={handleSubmit} style={formStyle}>
                    <label style={fieldStyle}>
                        Nama
                        <input
                            type="text"
                            value={form.reviewer_name}
                            onChange={(event) =>
                                updateField("reviewer_name", event.target.value)
                            }
                            style={inputStyle}
                        />
                        {validationErrors.reviewer_name && (
                            <p style={errorStyle}>
                                {validationErrors.reviewer_name.join(" ")}
                            </p>
                        )}
                    </label>

                    <label style={fieldStyle}>
                        Kota
                        <input
                            type="text"
                            value={form.reviewer_city}
                            onChange={(event) =>
                                updateField("reviewer_city", event.target.value)
                            }
                            style={inputStyle}
                        />
                        {validationErrors.reviewer_city && (
                            <p style={errorStyle}>
                                {validationErrors.reviewer_city.join(" ")}
                            </p>
                        )}
                    </label>

                    <label style={fieldStyle}>
                        Rating
                        <select
                            value={form.rating}
                            onChange={(event) =>
                                updateField("rating", event.target.value)
                            }
                            style={inputStyle}
                        >
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                        {validationErrors.rating && (
                            <p style={errorStyle}>
                                {validationErrors.rating.join(" ")}
                            </p>
                        )}
                    </label>

                    <label style={fieldStyle}>
                        Review
                        <textarea
                            value={form.review_text}
                            onChange={(event) =>
                                updateField("review_text", event.target.value)
                            }
                            rows={6}
                            style={inputStyle}
                        />
                        {validationErrors.review_text && (
                            <p style={errorStyle}>
                                {validationErrors.review_text.join(" ")}
                            </p>
                        )}
                    </label>

                    <label style={fieldStyle}>
                        Foto
                        <input
                            type="file"
                            accept="image/*"
                            onChange={(event) =>
                                updateField(
                                    "photo",
                                    event.target.files?.[0] || null,
                                )
                            }
                            style={inputStyle}
                        />
                        {photoPreview && (
                            <img
                                src={photoPreview}
                                alt="Preview foto review"
                                style={previewStyle}
                            />
                        )}
                        {validationErrors.photo && (
                            <p style={errorStyle}>
                                {validationErrors.photo.join(" ")}
                            </p>
                        )}
                    </label>

                    {submitError && <p style={errorStyle}>{submitError}</p>}

                    <button
                        type="submit"
                        disabled={isSubmitting}
                        style={buttonStyle}
                    >
                        {isSubmitting ? "Mengirim..." : "Kirim Review"}
                    </button>
                </form>
            </section>
        </main>
    );
}
