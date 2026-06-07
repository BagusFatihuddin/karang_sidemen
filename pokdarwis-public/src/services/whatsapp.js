const GLOBAL_WHATSAPP = "6287861842770";

export const buildWhatsAppUrl = (message) => {
    return `https://wa.me/${GLOBAL_WHATSAPP}?text=${encodeURIComponent(message)}`;
};
