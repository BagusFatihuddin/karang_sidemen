import { Link } from "react-router-dom";

const containerStyle = {
    maxWidth: "1120px",
    margin: "0 auto",
    padding: "80px 20px",
    textAlign: "center",
    fontFamily: "system-ui, sans-serif",
};

const titleStyle = {
    margin: "0 0 16px",
    fontSize: "48px",
    fontWeight: 700,
    color: "#111827",
};

const messageStyle = {
    margin: "0 0 32px",
    fontSize: "16px",
    color: "#4b5563",
    lineHeight: 1.6,
};

const buttonStyle = {
    display: "inline-block",
    padding: "10px 16px",
    background: "#15803d",
    color: "#ffffff",
    border: "none",
    borderRadius: "6px",
    textDecoration: "none",
    fontWeight: 700,
    cursor: "pointer",
    fontSize: "14px",
};

export default function NotFoundPage() {
    return (
        <main style={containerStyle}>
            <h1 style={titleStyle}>404</h1>
            <p style={messageStyle}>
                Halaman tidak ditemukan. Halaman yang Anda cari mungkin telah dihapus atau alamatnya tidak benar.
            </p>
            <Link to="/" style={buttonStyle}>
                ← Kembali ke Beranda
            </Link>
        </main>
    );
}
