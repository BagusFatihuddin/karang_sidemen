import { Outlet } from "react-router-dom";

import Footer from "../components/Footer";
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
    return (
        <div style={layoutStyle}>
            <Navbar />
            <div style={contentStyle}>
                <Outlet />
            </div>
            <Footer />
        </div>
    );
}
