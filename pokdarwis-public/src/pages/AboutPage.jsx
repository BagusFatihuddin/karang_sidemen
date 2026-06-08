import { usePublicSettings } from "../hooks/usePublicSettings";
import { buildWhatsAppUrl } from "../services/whatsapp";

const pageStyle = {
    fontFamily: "system-ui, sans-serif",
};

const sectionStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "32px 20px",
};

const heroStyle = {
    ...sectionStyle,
    paddingTop: "48px",
    paddingBottom: "48px",
    textAlign: "center",
};

const titleStyle = {
    margin: "0 0 10px",
    fontSize: "36px",
    fontWeight: 700,
    color: "#111827",
};

const taglineStyle = {
    margin: "0 0 28px",
    fontSize: "16px",
    color: "#4b5563",
    lineHeight: 1.6,
};

const cardStyle = {
    border: "1px solid #e5e7eb",
    borderRadius: "8px",
    padding: "24px",
    background: "#ffffff",
    marginBottom: "24px",
};

const cardTitleStyle = {
    margin: "0 0 16px",
    fontSize: "20px",
    fontWeight: 700,
    color: "#111827",
};

const textStyle = {
    margin: "0 0 12px",
    color: "#4b5563",
    lineHeight: 1.6,
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
    marginBottom: "12px",
    marginRight: "8px",
};

const linkStyle = {
    display: "inline-block",
    padding: "10px 14px",
    borderRadius: "6px",
    color: "#166534",
    fontWeight: 600,
    textDecoration: "none",
    border: "1px solid #86efac",
    marginBottom: "8px",
    marginRight: "8px",
};

const socialLinkStyle = {
    display: "inline-block",
    marginRight: "12px",
    marginBottom: "12px",
};

const linkWrapStyle = {
    display: "flex",
    flexWrap: "wrap",
    gap: "8px",
};

const iframeWrapStyle = {
    borderRadius: "8px",
    overflow: "hidden",
    background: "#f3f4f6",
    marginTop: "16px",
};

const iframeStyle = {
    width: "100%",
    minHeight: "400px",
    border: "none",
    display: "block",
};

const skeletonStyle = {
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
};

const socialFields = [
    { key: "social_instagram", label: "Instagram" },
    { key: "social_facebook", label: "Facebook" },
    { key: "social_tiktok", label: "TikTok" },
];

export default function AboutPage() {
    const { data, isLoading } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};

    const socialLinks = socialFields.filter((item) => settings[item.key]);
    const hasGoogleMaps = !!settings.google_maps_embed_url;

    if (isLoading) {
        return (
            <main style={pageStyle}>
                <section style={heroStyle}>
                    <div
                        style={{
                            ...skeletonStyle,
                            height: "50px",
                            marginBottom: "16px",
                        }}
                    />
                    <div
                        style={{
                            ...skeletonStyle,
                            height: "24px",
                            width: "70%",
                            margin: "0 auto 40px",
                        }}
                    />
                </section>

                <section style={sectionStyle}>
                    <div
                        style={{
                            ...skeletonStyle,
                            height: "180px",
                            marginBottom: "24px",
                        }}
                    />
                    <div
                        style={{
                            ...skeletonStyle,
                            height: "180px",
                            marginBottom: "24px",
                        }}
                    />
                    <div style={{ ...skeletonStyle, height: "400px" }} />
                </section>
            </main>
        );
    }

    return (
        <main style={pageStyle}>
            {/* Hero Section */}
            <section style={heroStyle}>
                <h1 style={titleStyle}>
                    {settings.village_name || "Tentang Desa Wisata"}
                </h1>
                {settings.tagline && (
                    <p style={taglineStyle}>{settings.tagline}</p>
                )}
            </section>

            <section style={sectionStyle}>
                {/* WhatsApp Section */}
                {settings.global_whatsapp && (
                    <div style={cardStyle}>
                        <h2 style={cardTitleStyle}>Hubungi Kami</h2>
                        <p style={textStyle}>
                            Tertarik untuk mengetahui lebih lanjut? Hubungi kami
                            melalui WhatsApp.
                        </p>
                        <a
                            href={buildWhatsAppUrl(
                                "Halo, saya ingin mengetahui informasi tentang desa wisata.",
                            )}
                            target="_blank"
                            rel="noreferrer"
                            style={buttonStyle}
                        >
                            💬 WhatsApp
                        </a>
                    </div>
                )}

                {/* Social Media Section */}
                {socialLinks.length > 0 && (
                    <div style={cardStyle}>
                        <h2 style={cardTitleStyle}>Ikuti Kami</h2>
                        <p style={textStyle}>
                            Dapatkan update terbaru dari desa wisata melalui
                            media sosial kami.
                        </p>
                        <div style={linkWrapStyle}>
                            {socialLinks.map((item) => (
                                <a
                                    key={item.key}
                                    href={settings[item.key]}
                                    target="_blank"
                                    rel="noreferrer"
                                    style={linkStyle}
                                >
                                    {item.label}
                                </a>
                            ))}
                        </div>
                    </div>
                )}

                {/* Google Maps Section */}
                {hasGoogleMaps && (
                    <div style={cardStyle}>
                        <h2 style={cardTitleStyle}>Lokasi Kami</h2>
                        <p style={textStyle}>
                            Temukan lokasi desa wisata kami di Google Maps.
                        </p>

                        {/* Try to embed if URL looks like an embed code */}
                        {settings.google_maps_embed_url.includes("iframe") ? (
                            <div style={iframeWrapStyle}>
                                <div
                                    dangerouslySetInnerHTML={{
                                        __html: settings.google_maps_embed_url,
                                    }}
                                />
                            </div>
                        ) : (
                            <a
                                href={settings.google_maps_embed_url}
                                target="_blank"
                                rel="noreferrer"
                                style={buttonStyle}
                            >
                                📍 Buka Google Maps
                            </a>
                        )}
                    </div>
                )}
            </section>
        </main>
    );
}
