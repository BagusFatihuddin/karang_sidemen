import { useEffect, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { getDestinations } from "../services/api/destinations";

const typeOptions = [
    { label: "Semua", value: "" },
    { label: "Alam", value: "alam" },
    { label: "Budaya", value: "budaya" },
    { label: "Religi", value: "religi" },
    { label: "Edukasi", value: "edukasi" },
    { label: "Buatan", value: "buatan" },
];

const pageStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const textStyle = {
    color: "#4b5563",
    lineHeight: 1.6,
};

const pillsStyle = {
    display: "flex",
    flexWrap: "wrap",
    gap: "8px",
    marginBottom: "20px",
};

const pillStyle = (isActive) => ({
    padding: "8px 12px",
    borderRadius: "999px",
    border: "1px solid #86efac",
    background: isActive ? "#dcfce7" : "#ffffff",
    color: "#166534",
    textDecoration: "none",
    fontWeight: isActive ? 700 : 500,
});

const gridStyle = (isMobile) => ({
    display: "grid",
    gridTemplateColumns: isMobile ? "repeat(2, minmax(0, 1fr))" : "repeat(3, minmax(0, 1fr))",
    gap: "16px",
});

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    overflow: "hidden",
    background: "#ffffff",
};

const imageStyle = {
    width: "100%",
    aspectRatio: "4 / 3",
    objectFit: "cover",
    background: "#f3f4f6",
    display: "block",
};

const fallbackImageStyle = {
    ...imageStyle,
    display: "grid",
    placeItems: "center",
    color: "#6b7280",
    fontWeight: 700,
};

const cardBodyStyle = {
    padding: "12px",
};

const badgeStyle = {
    display: "inline-block",
    marginBottom: "8px",
    padding: "4px 8px",
    borderRadius: "999px",
    background: "#dcfce7",
    color: "#166534",
    fontSize: "12px",
    fontWeight: 700,
};

const buttonStyle = {
    display: "inline-block",
    marginTop: "10px",
    padding: "9px 12px",
    borderRadius: "6px",
    background: "#ffffff",
    color: "#166534",
    border: "1px solid #86efac",
    textDecoration: "none",
    fontWeight: 700,
};

const skeletonStyle = {
    height: "240px",
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const getPayload = (response) => response?.data?.data ?? response?.data ?? [];

const formatEntryFee = (entryFee) => {
    if (entryFee === null || entryFee === undefined || Number(entryFee) === 0) {
        return "Gratis";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(entryFee));
};

const useIsMobile = () => {
    const [isMobile, setIsMobile] = useState(false);

    useEffect(() => {
        const mediaQuery = window.matchMedia("(max-width: 720px)");
        const update = () => setIsMobile(mediaQuery.matches);

        update();
        mediaQuery.addEventListener("change", update);

        return () => mediaQuery.removeEventListener("change", update);
    }, []);

    return isMobile;
};

export default function DestinationsPage() {
    const isMobile = useIsMobile();
    const [searchParams] = useSearchParams();
    const activeType = searchParams.get("type") || "";
    const { data, isLoading } = useQuery({
        queryKey: ["destinations", activeType],
        queryFn: () => getDestinations(activeType),
    });
    const payload = getPayload(data);
    const destinations = Array.isArray(payload) ? payload : [];

    return (
        <main style={pageStyle}>
            <h1>Destinasi</h1>

            <div style={pillsStyle}>
                {typeOptions.map((type) => (
                    <Link
                        key={type.label}
                        to={type.value ? `/destinasi?type=${type.value}` : "/destinasi"}
                        style={pillStyle(activeType === type.value)}
                    >
                        {type.label}
                    </Link>
                ))}
            </div>

            {isLoading ? (
                <div style={gridStyle(isMobile)}>
                    {[1, 2, 3, 4, 5, 6].map((item) => (
                        <div key={item} style={skeletonStyle} />
                    ))}
                </div>
            ) : destinations.length === 0 ? (
                <p style={textStyle}>Belum ada destinasi tersedia.</p>
            ) : (
                <div style={gridStyle(isMobile)}>
                    {destinations.map((destination) => (
                        <article key={destination.id} style={cardStyle}>
                            {destination.thumbnail_url ? (
                                <img
                                    src={destination.thumbnail_url}
                                    alt={destination.name}
                                    style={imageStyle}
                                />
                            ) : (
                                <div style={fallbackImageStyle}>Destinasi</div>
                            )}
                            <div style={cardBodyStyle}>
                                {destination.destination_type && (
                                    <span style={badgeStyle}>
                                        {destination.destination_type}
                                    </span>
                                )}
                                <h2>{destination.name}</h2>
                                <p style={textStyle}>
                                    {formatEntryFee(destination.entry_fee)}
                                </p>
                                <Link
                                    to={`/destinasi/${destination.id}`}
                                    style={buttonStyle}
                                >
                                    Detail
                                </Link>
                            </div>
                        </article>
                    ))}
                </div>
            )}
        </main>
    );
}
