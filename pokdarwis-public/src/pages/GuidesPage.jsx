import { useQuery } from "@tanstack/react-query";

import { getGuides } from "../services/api/guides";
import { buildWhatsAppUrl } from "../services/whatsapp";

const pageStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const gridStyle = {
    display: "grid",
    gridTemplateColumns: "repeat(auto-fit, minmax(240px, 1fr))",
    gap: "20px",
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    padding: "16px",
    background: "#ffffff",
};

const photoStyle = {
    width: "96px",
    height: "96px",
    borderRadius: "50%",
    objectFit: "cover",
    background: "#f3f4f6",
    display: "grid",
    placeItems: "center",
    fontSize: "32px",
    fontWeight: 700,
    color: "#4b5563",
};

const buttonStyle = {
    display: "inline-block",
    marginTop: "14px",
    padding: "10px 14px",
    borderRadius: "6px",
    background: "#15803d",
    color: "#ffffff",
    textDecoration: "none",
    fontWeight: 600,
};

const skeletonStyle = {
    ...cardStyle,
    height: "260px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const shortText = (text, maxLength = 120) => {
    if (!text || text.length <= maxLength) {
        return text;
    }

    return `${text.slice(0, maxLength)}...`;
};

const initials = (name) => {
    return name
        ?.split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0])
        .join("")
        .toUpperCase();
};

export default function GuidesPage() {
    const { data, isLoading } = useQuery({
        queryKey: ["guides"],
        queryFn: getGuides,
    });

    const guides = data?.data?.data ?? [];

    return (
        <main style={pageStyle}>
            <h1>Panduan</h1>

            {isLoading && (
                <div style={gridStyle}>
                    {[1, 2, 3].map((item) => (
                        <div key={item} style={skeletonStyle} />
                    ))}
                </div>
            )}

            {!isLoading && guides.length === 0 && (
                <p>Belum ada profil panduan aktif.</p>
            )}

            {!isLoading && guides.length > 0 && (
                <div style={gridStyle}>
                    {guides.map((guide) => (
                        <article key={guide.id} style={cardStyle}>
                            {guide.photo_url ? (
                                <img
                                    src={guide.photo_url}
                                    alt={guide.name}
                                    style={photoStyle}
                                />
                            ) : (
                                <div style={photoStyle}>
                                    {initials(guide.name) || "?"}
                                </div>
                            )}

                            <h2>{guide.name}</h2>
                            {guide.experience && <strong>{guide.experience}</strong>}
                            <p>{shortText(guide.bio)}</p>

                            <a
                                href={buildWhatsAppUrl(
                                    `Halo, saya ingin bertanya tentang panduan ${guide.name}`,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                style={buttonStyle}
                            >
                                Chat WhatsApp
                            </a>
                        </article>
                    ))}
                </div>
            )}
        </main>
    );
}
