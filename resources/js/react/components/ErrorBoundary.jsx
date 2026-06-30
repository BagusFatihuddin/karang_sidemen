import { Component } from "react";

const errorContainerStyle = {
    minHeight: "100vh",
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    background: "#f9fafb",
    fontFamily: "system-ui, sans-serif",
    padding: "20px",
};

const errorCardStyle = {
    maxWidth: "500px",
    background: "#ffffff",
    border: "1px solid #fecaca",
    borderRadius: "8px",
    padding: "40px 24px",
    textAlign: "center",
    boxShadow: "0 1px 3px rgba(0, 0, 0, 0.1)",
};

const errorTitleStyle = {
    margin: "0 0 12px",
    fontSize: "24px",
    fontWeight: 700,
    color: "#b91c1c",
};

const errorMessageStyle = {
    margin: "0 0 24px",
    fontSize: "14px",
    color: "#4b5563",
    lineHeight: 1.6,
};

const errorDetailsStyle = {
    margin: "0 0 24px",
    padding: "12px",
    background: "#fef2f2",
    border: "1px solid #fecaca",
    borderRadius: "4px",
    fontSize: "12px",
    color: "#7f1d1d",
    textAlign: "left",
    maxHeight: "150px",
    overflow: "auto",
    fontFamily: "monospace",
    whiteSpace: "pre-wrap",
    wordBreak: "break-word",
};

const buttonStyle = {
    padding: "10px 16px",
    background: "#15803d",
    color: "#ffffff",
    border: "none",
    borderRadius: "6px",
    fontWeight: 700,
    cursor: "pointer",
    fontSize: "14px",
};

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error("ErrorBoundary caught an error:", error, errorInfo);
    }

    handleReload = () => {
        window.location.reload();
    };

    render() {
        if (this.state.hasError) {
            return (
                <div style={errorContainerStyle}>
                    <div style={errorCardStyle}>
                        <h1 style={errorTitleStyle}>⚠️ Terjadi Kesalahan</h1>
                        <p style={errorMessageStyle}>
                            Maaf, aplikasi mengalami kesalahan yang tidak terduga. Silakan muat ulang halaman.
                        </p>
                        {this.state.error && (
                            <details style={{ marginBottom: "24px", textAlign: "left" }}>
                                <summary style={{ cursor: "pointer", color: "#666" }}>
                                    Detail teknis
                                </summary>
                                <div style={errorDetailsStyle}>
                                    {this.state.error.toString()}
                                </div>
                            </details>
                        )}
                        <button style={buttonStyle} onClick={this.handleReload}>
                            🔄 Muat Ulang Halaman
                        </button>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary;
