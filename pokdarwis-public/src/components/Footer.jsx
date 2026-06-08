import { usePublicSettings } from "../hooks/usePublicSettings";
import { buildWhatsAppUrl } from "../services/whatsapp";

const footerStyle = {
    borderTop: "1px solid #e5e7eb",
    background: "#f9fafb",
    fontFamily: "system-ui, sans-serif",
};

const innerStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "24px 20px",
    display: "grid",
    gap: "14px",
};

const titleStyle = {
    margin: 0,
    fontSize: "18px",
    fontWeight: 700,
    color: "#111827",
};

const textStyle = {
    margin: 0,
    color: "#4b5563",
};

const linkWrapStyle = {
    display: "flex",
    flexWrap: "wrap",
    gap: "12px",
};

const linkStyle = {
    color: "#166534",
    fontWeight: 600,
    textDecoration: "none",
};

const socialFields = [
    { key: "social_instagram", label: "Instagram" },
    { key: "social_facebook", label: "Facebook" },
    { key: "social_tiktok", label: "TikTok" },
];

export default function Footer() {
    const { data } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};
    const socialLinks = socialFields.filter((item) => settings[item.key]);

    return (
        <footer style={footerStyle}>
            <div style={innerStyle}>
                <div>
                    <p style={titleStyle}>{settings.village_name || ""}</p>
                    {settings.tagline && (
                        <p style={textStyle}>{settings.tagline}</p>
                    )}
                </div>

                {(socialLinks.length > 0 || settings.global_whatsapp) && (
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

                        {settings.global_whatsapp && (
                            <a
                                href={buildWhatsAppUrl(
                                    "Halo, saya ingin bertanya tentang wisata desa.",
                                    settings.global_whatsapp,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                style={linkStyle}
                            >
                                WhatsApp
                            </a>
                        )}
                    </div>
                )}

                <p style={textStyle}>
                    {"\u00a9"} {new Date().getFullYear()} {settings.village_name || ""}
                </p>
            </div>
        </footer>
    );
}
