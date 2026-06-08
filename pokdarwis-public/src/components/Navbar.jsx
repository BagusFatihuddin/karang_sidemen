import { useEffect, useState } from "react";
import { NavLink } from "react-router-dom";

import { usePublicSettings } from "../hooks/usePublicSettings";

const links = [
    { to: "/", label: "Beranda" },
    { to: "/destinasi", label: "Destinasi" },
    { to: "/paket", label: "Paket" },
    { to: "/reviews", label: "Review" },
    { to: "/tentang", label: "Tentang" },
];

const headerStyle = {
    borderBottom: "1px solid #e5e7eb",
    background: "#ffffff",
};

const innerStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "14px 20px",
    display: "flex",
    alignItems: "center",
    justifyContent: "space-between",
    gap: "16px",
    fontFamily: "system-ui, sans-serif",
};

const brandStyle = {
    color: "#111827",
    textDecoration: "none",
};

const titleStyle = {
    margin: 0,
    fontSize: "18px",
    fontWeight: 700,
};

const taglineStyle = {
    margin: "2px 0 0",
    color: "#6b7280",
    fontSize: "13px",
};

const navStyle = {
    display: "flex",
    alignItems: "center",
    gap: "6px",
};

const navOpenMobileStyle = {
    position: "absolute",
    top: "66px",
    left: 0,
    right: 0,
    display: "grid",
    gap: "4px",
    padding: "12px 20px",
    borderBottom: "1px solid #e5e7eb",
    background: "#ffffff",
    zIndex: 10,
};

const linkStyle = ({ isActive }) => ({
    padding: "8px 10px",
    borderRadius: "6px",
    color: isActive ? "#166534" : "#374151",
    background: isActive ? "#dcfce7" : "transparent",
    textDecoration: "none",
    fontWeight: isActive ? 700 : 500,
});

const menuButtonStyle = {
    border: "1px solid #d1d5db",
    borderRadius: "6px",
    background: "#ffffff",
    padding: "8px",
    cursor: "pointer",
};

const menuIconStyle = {
    width: "20px",
    display: "grid",
    gap: "4px",
};

const menuBarStyle = {
    height: "2px",
    background: "#111827",
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

export default function Navbar() {
    const { data } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};
    const isMobile = useIsMobile();
    const [isOpen, setIsOpen] = useState(false);

    useEffect(() => {
        if (!isMobile) {
            setIsOpen(false);
        }
    }, [isMobile]);

    return (
        <header style={headerStyle}>
            <div style={{ ...innerStyle, position: "relative" }}>
                <NavLink to="/" style={brandStyle}>
                    <p style={titleStyle}>{settings.village_name || ""}</p>
                    {settings.tagline && (
                        <p style={taglineStyle}>{settings.tagline}</p>
                    )}
                </NavLink>

                {isMobile && (
                    <button
                        type="button"
                        aria-label="Buka navigasi"
                        aria-expanded={isOpen}
                        onClick={() => setIsOpen((current) => !current)}
                        style={menuButtonStyle}
                    >
                        <span aria-hidden="true" style={menuIconStyle}>
                            <span style={menuBarStyle} />
                            <span style={menuBarStyle} />
                            <span style={menuBarStyle} />
                        </span>
                    </button>
                )}

                {(!isMobile || isOpen) && (
                    <nav
                        aria-label="Navigasi utama"
                        style={isMobile ? navOpenMobileStyle : navStyle}
                    >
                        {links.map((link) => (
                            <NavLink
                                key={link.to}
                                to={link.to}
                                end={link.to === "/"}
                                style={linkStyle}
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
