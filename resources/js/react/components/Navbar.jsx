import { useEffect, useState } from "react";
import { NavLink } from "react-router-dom";

import BrandMark from "./BrandMark";
import { usePublicSettings } from "../hooks/usePublicSettings";
import "./Navbar.css";

const links = [
    { to: "/", label: "Beranda" },
    { to: "/destinasi", label: "Destinasi" },
    { to: "/paket", label: "Paket" },
    { to: "/panduan", label: "Panduan" },
    { to: "/reviews", label: "Review" },
    { to: "/tentang", label: "Tentang" },
];

const useIsMobile = () => {
    const [isMobile, setIsMobile] = useState(false);

    useEffect(() => {
        const mediaQuery = window.matchMedia("(max-width: 760px)");
        const update = () => setIsMobile(mediaQuery.matches);

        update();
        mediaQuery.addEventListener("change", update);

        return () => mediaQuery.removeEventListener("change", update);
    }, []);

    return isMobile;
};

const useHasScrolled = () => {
    const [hasScrolled, setHasScrolled] = useState(false);

    useEffect(() => {
        const update = () => setHasScrolled(window.scrollY > 32);

        update();
        window.addEventListener("scroll", update, { passive: true });

        return () => window.removeEventListener("scroll", update);
    }, []);

    return hasScrolled;
};

export default function Navbar() {
    const { data } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};
    const isMobile = useIsMobile();
    const hasScrolled = useHasScrolled();
    const [isOpen, setIsOpen] = useState(false);

    const headerClassName = [
        "site-navbar",
        hasScrolled || (isMobile && isOpen) ? "site-navbar--scrolled" : "",
    ]
        .filter(Boolean)
        .join(" ");

    return (
        <header className={headerClassName}>
            <div className="site-navbar__inner">
                <NavLink
                    to="/"
                    className="site-navbar__brand"
                    onClick={() => setIsOpen(false)}
                >
                    <BrandMark
                        settings={settings}
                        className="site-navbar__brand-mark"
                        imageClassName="site-navbar__brand-logo"
                    />
                    <span className="site-navbar__brand-copy">
                        <span className="site-navbar__title">
                            {settings.village_name || "Karang Sidemen"}
                        </span>
                        {settings.tagline && (
                            <span className="site-navbar__tagline">
                                {settings.tagline}
                            </span>
                        )}
                    </span>
                </NavLink>

                {isMobile && (
                    <button
                        type="button"
                        className="site-navbar__menu-button"
                        aria-label="Buka navigasi"
                        aria-expanded={isOpen}
                        onClick={() => setIsOpen((current) => !current)}
                    >
                        <span aria-hidden="true" />
                        <span aria-hidden="true" />
                        <span aria-hidden="true" />
                    </button>
                )}

                {(!isMobile || isOpen) && (
                    <nav
                        aria-label="Navigasi utama"
                        className={
                            isMobile
                                ? "site-navbar__links site-navbar__links--mobile"
                                : "site-navbar__links"
                        }
                    >
                        {links.map((link) => (
                            <NavLink
                                key={link.to}
                                to={link.to}
                                end={link.to === "/"}
                                className={({ isActive }) =>
                                    [
                                        "site-navbar__link",
                                        isActive ? "site-navbar__link--active" : "",
                                    ]
                                        .filter(Boolean)
                                        .join(" ")
                                }
                                onClick={() => setIsOpen(false)}
                            >
                                {link.label}
                            </NavLink>
                        ))}
                    </nav>
                )}
            </div>
        </header>
    );
}
