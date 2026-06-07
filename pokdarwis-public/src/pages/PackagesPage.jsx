import { useQuery } from "@tanstack/react-query";

import { getTripPackages } from "../services/api/tripPackages";
import { buildWhatsAppUrl } from "../services/whatsapp";

const pageStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
    fontFamily: "system-ui, sans-serif",
};

const gridStyle = {
    display: "grid",
    gridTemplateColumns: "repeat(auto-fit, minmax(260px, 1fr))",
    gap: "20px",
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    overflow: "hidden",
    background: "#ffffff",
};

const imageStyle = {
    width: "100%",
    aspectRatio: "16 / 9",
    objectFit: "cover",
    background: "#f3f4f6",
    display: "block",
};

const bodyStyle = {
    padding: "16px",
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
    height: "320px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const formatPrice = (price) => {
    if (!price) {
        return "Hubungi admin";
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(Number(price));
};

const shortText = (text, maxLength = 140) => {
    if (!text || text.length <= maxLength) {
        return text;
    }

    return `${text.slice(0, maxLength)}...`;
};

export default function PackagesPage() {
    const { data, isLoading } = useQuery({
        queryKey: ["trip-packages"],
        queryFn: getTripPackages,
    });

    const packages = data?.data?.data ?? [];

    return (
        <main style={pageStyle}>
            <h1>Paket Trip</h1>

            {isLoading && (
                <div style={gridStyle}>
                    {[1, 2, 3].map((item) => (
                        <div key={item} style={skeletonStyle} />
                    ))}
                </div>
            )}

            {!isLoading && packages.length === 0 && (
                <p>Belum ada paket trip aktif.</p>
            )}

            {!isLoading && packages.length > 0 && (
                <div style={gridStyle}>
                    {packages.map((tripPackage) => (
                        <article key={tripPackage.id} style={cardStyle}>
                            {tripPackage.image_url ? (
                                <img
                                    src={tripPackage.image_url}
                                    alt={tripPackage.name}
                                    style={imageStyle}
                                />
                            ) : (
                                <div style={imageStyle} />
                            )}

                            <div style={bodyStyle}>
                                <h2>{tripPackage.name}</h2>
                                <strong>{formatPrice(tripPackage.price)}</strong>
                                <p>{shortText(tripPackage.description)}</p>

                                {tripPackage.destinations?.length > 0 && (
                                    <>
                                        <h3>Destinasi</h3>
                                        <ul>
                                            {tripPackage.destinations.map((destination) => (
                                                <li key={destination.id}>
                                                    {destination.name}
                                                </li>
                                            ))}
                                        </ul>
                                    </>
                                )}

                                <a
                                    href={buildWhatsAppUrl(
                                        `Halo, saya tertarik dengan paket ${tripPackage.name}`,
                                    )}
                                    target="_blank"
                                    rel="noreferrer"
                                    style={buttonStyle}
                                >
                                    Tanya via WhatsApp
                                </a>
                            </div>
                        </article>
                    ))}
                </div>
            )}
        </main>
    );
}
