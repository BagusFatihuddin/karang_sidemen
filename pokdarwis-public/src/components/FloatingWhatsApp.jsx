import { useLocation } from "react-router-dom";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./FloatingWhatsApp.css";

const hiddenRoutes = [/^\/destinasi\/[^/]+/, /^\/review\/[^/]+/];

function WhatsAppIcon() {
    return (
        <svg
            className="floating-whatsapp__icon"
            viewBox="0 0 32 32"
            aria-hidden="true"
        >
            <path
                fill="currentColor"
                d="M16.1 4.4c-6.3 0-11.4 5-11.4 11.2 0 2.1.6 4.1 1.7 5.8L4 28l6.9-2.2c1.6.8 3.4 1.2 5.2 1.2 6.3 0 11.4-5 11.4-11.2S22.4 4.4 16.1 4.4Zm0 20.5c-1.7 0-3.2-.4-4.6-1.2l-.4-.2-4 1.3 1.3-3.8-.3-.4c-.9-1.5-1.4-3.2-1.4-4.9 0-5.1 4.2-9.2 9.3-9.2s9.3 4.1 9.3 9.2-4.1 9.2-9.2 9.2Zm5.1-6.9c-.3-.1-1.7-.8-1.9-.9-.3-.1-.5-.1-.7.1-.2.3-.8.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.3-.5.1-.2.1-.4 0-.5 0-.1-.7-1.6-.9-2.2-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.5s1.1 3 1.3 3.2c.1.2 2.1 3.3 5.3 4.5.7.3 1.3.5 1.8.6.8.2 1.4.1 2 0 .6-.1 1.7-.7 1.9-1.3.2-.7.2-1.2.2-1.3-.1-.3-.3-.4-.5-.5Z"
            />
        </svg>
    );
}

export default function FloatingWhatsApp() {
    const location = useLocation();
    const { data } = usePublicSettings();
    const settings = data?.data?.data ?? data?.data ?? {};
    const whatsappNumber = settings.global_whatsapp;

    if (!whatsappNumber || hiddenRoutes.some((pattern) => pattern.test(location.pathname))) {
        return null;
    }

    return (
        <a
            className="floating-whatsapp"
            href={buildWhatsAppUrl(
                "Halo, saya ingin bertanya tentang wisata Desa Karang Sidemen.",
                whatsappNumber,
            )}
            target="_blank"
            rel="noreferrer"
            aria-label="Chat WhatsApp POKDARWIS Karang Sidemen"
        >
            <span className="floating-whatsapp__bubble">
                <WhatsAppIcon />
            </span>
            <strong>Chat WA</strong>
        </a>
    );
}
