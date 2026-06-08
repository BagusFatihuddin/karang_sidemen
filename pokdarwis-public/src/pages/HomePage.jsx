import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getDestinations } from "../services/api/destinations";
import { getPromos } from "../services/api/promos";
import { buildWhatsAppUrl } from "../services/whatsapp";

const pageStyle = {
    fontFamily: "system-ui, sans-serif",
};

const sectionStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "36px 20px",
};

const heroStyle = {
    ...sectionStyle,
    paddingTop: "56px",
    paddingBottom: "56px",
};

const titleStyle = {
    margin: "0 0 10px",
    fontSize: "36px",
    color: "#111827",
};

const textStyle = {
    margin: "0 0 18px",
    color: "#4b5563",
    lineHeight: 1.6,
};

const buttonStyle = {
    display: "inline-block",
    padding: "10px 14px",
    borderRadius: "6px",
    background: "#15803d",
    color: "#ffffff",
    textDecoration: "none",
    fontWeight: 700,
};

const outlineButtonStyle = {
    ...buttonStyle,
    background: "#ffffff",
    color: "#166534",
    border: "1px solid #86efac",
};

const promoStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    overflow: "hidden",
    background: "#ffffff",
};

const promoImageStyle = {
    width: "100%",
    maxHeight: "320px",
    objectFit: "cover",
    background: "#f3f4f6",
    display: "block",
};

const promoBodyStyle = {
    padding: "16px",
};

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

const skeletonStyle = {
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const sectionTitleStyle = {
    margin: "0 0 16px",
    color: "#111827",
};

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

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

export default function HomePage() {
    const isMobile = useIsMobile();
    const { data: settingsData, isLoading: settingsLoading } = usePublicSettings();
    const { data: promosData, isLoading: promosLoading } = useQuery({
        queryKey: ["promos"],
        queryFn: getPromos,
    });
    const { data: destinationsData, isLoading: destinationsLoading } = useQuery({
        queryKey: ["destinations"],
        queryFn: getDestinations,
    });
    const [activePromoIndex, setActivePromoIndex] = useState(0);

    const settings = getPayload(settingsData);
    const promos = getPayload(promosData);
    const promoItems = Array.isArray(promos) ? promos : [];
    const destinations = getPayload(destinationsData);
    const destinationItems = Array.isArray(destinations)
        ? destinations.slice(0, 6)
        : [];

    useEffect(() => {
        if (promoItems.length <= 1) {
            return undefined;
        }

        const interval = window.setInterval(() => {
            setActivePromoIndex((current) => (current + 1) % promoItems.length);
        }, 5000);

        return () => window.clearInterval(interval);
    }, [promoItems.length]);

    useEffect(() => {
        if (activePromoIndex >= promoItems.length) {
            setActivePromoIndex(0);
        }
    }, [activePromoIndex, promoItems.length]);

    const activePromo = promoItems[activePromoIndex];

    return (
        <main style={pageStyle}>
            <section style={heroStyle}>
                {settingsLoading ? (
                    <div style={{ ...skeletonStyle, height: "156px" }} />
                ) : (
                    <>
                        <h1 style={titleStyle}>{settings?.village_name}</h1>
                        {settings?.tagline && (
                            <p style={textStyle}>{settings?.tagline}</p>
                        )}
                        <Link to="/destinasi" style={buttonStyle}>
                            Jelajahi Destinasi
                        </Link>
                    </>
                )}
            </section>

            {promosLoading && (
                <section style={sectionStyle}>
                    <div style={{ ...skeletonStyle, height: "280px" }} />
                </section>
            )}

            {!promosLoading && activePromo && (
                <section style={sectionStyle}>
                    <h2 style={sectionTitleStyle}>Promo</h2>
                    <article style={promoStyle}>
                        {activePromo.image_url && (
                            <img
                                src={activePromo.image_url}
                                alt={activePromo.title}
                                style={promoImageStyle}
                            />
                        )}
                        <div style={promoBodyStyle}>
                            <h3>{activePromo.title}</h3>
                            {activePromo.description && (
                                <p style={textStyle}>{activePromo.description}</p>
                            )}
                            {activePromo.external_url && (
                                <a
                                    href={activePromo.external_url}
                                    target="_blank"
                                    rel="noreferrer"
                                    style={outlineButtonStyle}
                                >
                                    Lihat Promo
                                </a>
                            )}
                        </div>
                    </article>
                </section>
            )}

            <section style={sectionStyle}>
                <h2 style={sectionTitleStyle}>Destinasi Pilihan</h2>
                {destinationsLoading ? (
                    <div style={gridStyle(isMobile)}>
                        {[1, 2, 3, 4, 5, 6].map((item) => (
                            <div
                                key={item}
                                style={{ ...skeletonStyle, height: "220px" }}
                            />
                        ))}
                    </div>
                ) : destinationItems.length === 0 ? (
                    <p style={textStyle}>Belum ada destinasi tersedia.</p>
                ) : (
                    <div style={gridStyle(isMobile)}>
                        {destinationItems.map((destination) => (
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
                                    <h3>{destination.name}</h3>
                                    <Link
                                        to={`/destinasi/${destination.id}`}
                                        style={outlineButtonStyle}
                                    >
                                        Detail
                                    </Link>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>

            {settings?.global_whatsapp && (
                <section style={sectionStyle}>
                    <a
                        href={buildWhatsAppUrl(
                            "Halo, saya butuh bantuan tentang wisata desa.",
                            settings?.global_whatsapp,
                        )}
                        target="_blank"
                        rel="noreferrer"
                        style={buttonStyle}
                    >
                        Butuh bantuan? Hubungi kami via WhatsApp
                    </a>
                </section>
            )}
        </main>
    );
}
