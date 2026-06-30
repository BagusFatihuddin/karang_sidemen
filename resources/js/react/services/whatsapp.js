const GLOBAL_WHATSAPP = "6287861842770";

const normalizeWhatsAppNumber = (number) => {
    return String(number || "").replace(/[^\d]/g, "");
};

export const buildWhatsAppUrl = (message, number = GLOBAL_WHATSAPP) => {
    const whatsappNumber = normalizeWhatsAppNumber(number);

    return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
};
