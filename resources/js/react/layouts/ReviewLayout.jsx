import { useEffect } from "react";
import { Outlet } from "react-router-dom";

const layoutStyle = {
    minHeight: "100vh",
    display: "flex",
    flexDirection: "column",
};

const contentStyle = {
    flex: 1,
};

const headerStyle = {
    padding: "12px 16px",
    borderBottom: "1px solid rgba(0,0,0,0.06)",
    background: "#fff",
};

const headerInnerStyle = {
    maxWidth: "980px",
    margin: "0 auto",
};

const titleStyle = {
    fontSize: "14px",
    fontWeight: 700,
    letterSpacing: "0.2px",
};

export default function ReviewLayout() {
    useEffect(() => {
        window.scrollTo({ top: 0, left: 0 });
    }, []);

    return (
        <div style={layoutStyle}>
            <header style={headerStyle}>
                <div style={headerInnerStyle}>
                    <div style={titleStyle}>Review Pengunjung</div>
                </div>
            </header>
            <div style={contentStyle}>
                <Outlet />
            </div>
        </div>
    );
}
