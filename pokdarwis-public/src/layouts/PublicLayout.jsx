import { useEffect } from "react";
import { Outlet, useLocation } from "react-router-dom";

import Footer from "../components/Footer";
import FloatingWhatsApp from "../components/FloatingWhatsApp";
import Navbar from "../components/Navbar";

const layoutStyle = {
    minHeight: "100vh",
    display: "flex",
    flexDirection: "column",
};

const contentStyle = {
    flex: 1,
};

export default function PublicLayout() {
    const location = useLocation();

    useEffect(() => {
        window.scrollTo({ top: 0, left: 0 });
    }, [location.pathname]);

    return (
        <div style={layoutStyle}>
            <Navbar />
            <div style={contentStyle}>
                <Outlet />
            </div>
            <Footer />
            <FloatingWhatsApp />
        </div>
    );
}
