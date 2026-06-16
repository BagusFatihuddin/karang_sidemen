import { Link } from "react-router-dom";

import BrandMark from "./BrandMark";
import { usePublicSettings } from "../hooks/usePublicSettings";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./Footer.css";

const socialFields = [
    { key: "social_instagram", label: "Instagram" },
    { key: "social_facebook", label: "Facebook" },
    { key: "social_tiktok", label: "TikTok" },
];

const navigationLinks = [
    { to: "/destinasi", label: "Destinasi" },
    { to: "/paket", label: "Paket" },
    { to: "/panduan", label: "Panduan" },
    { to: "/reviews", label: "Review" },
    { to: "/tentang", label: "Tentang" },
];

const defaultFooterCtaImage =
    "https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&w=1800&q=88";

const developerCredit = {
    title: "Identitas pengembang",
    body: "Website ini dikembangkan sebagai bagian dari tugas akademik untuk membantu promosi pariwisata lokal dan pengelolaan informasi POKDARWIS Karang Sidemen.",
    people: ["Bagus Fatihuddin Abul Yasin", "Muhammad Said"],
    university: "Universitas Bumogora",
    year: "2026",
};

export default function Footer() {
    const { data } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};
    const socialLinks = socialFields.filter((item) => settings[item.key]);
    const villageName = settings.village_name || "Desa Wisata Karang Sidemen";
    const ctaImage = settings.media_footer_cta_image_url || defaultFooterCtaImage;

    return (
        <footer
            className="site-footer"
            style={{ "--footer-cta-image": `url("${ctaImage}")` }}
        >
            <div className="site-footer__inner">
                <section className="site-footer__cta">
                    <div>
                        <p>Rencanakan kunjungan</p>
                        <h2>Datang ke Karang Sidemen dengan cerita yang sudah kebayang.</h2>
                    </div>
                    {settings.global_whatsapp && (
                        <a
                            href={buildWhatsAppUrl(
                                "Halo, saya ingin bertanya tentang wisata Desa Karang Sidemen.",
                                settings.global_whatsapp,
                            )}
                            target="_blank"
                            rel="noreferrer"
                        >
                            Chat WhatsApp
                        </a>
                    )}
                </section>

                <section className="site-footer__main">
                    <div className="site-footer__brand">
                        <Link to="/" className="site-footer__mark">
                            <BrandMark
                                settings={settings}
                                className="site-footer__mark-inner"
                                imageClassName="site-footer__mark-logo"
                            />
                        </Link>
                        <div>
                            <h3>{villageName}</h3>
                            {settings.tagline && <p>{settings.tagline}</p>}
                        </div>
                    </div>

                    <div className="site-footer__links">
                        <div>
                            <strong>Jelajah</strong>
                            {navigationLinks.map((link) => (
                                <Link key={link.to} to={link.to}>
                                    {link.label}
                                </Link>
                            ))}
                        </div>

                        {(socialLinks.length > 0 || settings.global_whatsapp) && (
                            <div>
                                <strong>Kontak</strong>
                                {socialLinks.map((item) => (
                                    <a
                                        key={item.key}
                                        href={settings[item.key]}
                                        target="_blank"
                                        rel="noreferrer"
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
                                    >
                                        WhatsApp
                                    </a>
                                )}
                            </div>
                        )}
                    </div>
                </section>

                <section className="site-footer__credit">
                    <div>
                        <strong>{developerCredit.title}</strong>
                        <p>{developerCredit.body}</p>
                    </div>
                    <div>
                        {developerCredit.people.map((person) => (
                            <span key={person}>{person}</span>
                        ))}
                        <span>{developerCredit.university}</span>
                        <span>{developerCredit.year}</span>
                    </div>
                </section>

                <section className="site-footer__bottom">
                    <span>
                        {"\u00A9"} {new Date().getFullYear()} {villageName}
                    </span>
                    <span>Dikelola oleh POKDARWIS Karang Sidemen.</span>
                </section>
            </div>
        </footer>
    );
}
