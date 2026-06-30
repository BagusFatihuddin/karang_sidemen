import { Link, useParams } from "react-router-dom";
import { useQuery } from "@tanstack/react-query";

import { usePublicSettings } from "../hooks/usePublicSettings";
import { getPromo } from "../services/api/promos";
import { buildWhatsAppUrl } from "../services/whatsapp";
import "./EventDetailPage.css";

const getPayload = (response) => response?.data?.data ?? response?.data ?? {};

const formatDate = (date) => {
    if (!date) {
        return "";
    }

    return new Intl.DateTimeFormat("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    }).format(new Date(date));
};

const eventDateText = (event) => {
    const start = formatDate(event.start_date);
    const end = formatDate(event.end_date);

    if (start && end && start !== end) {
        return `${start} - ${end}`;
    }

    return start || end || "Tanggal fleksibel";
};

export default function EventDetailPage() {
    const { id } = useParams();
    const { data: settingsData } = usePublicSettings();
    const { data, isLoading, error } = useQuery({
        queryKey: ["event", id],
        queryFn: () => getPromo(id),
        retry: false,
    });

    const settings = getPayload(settingsData);
    const event = getPayload(data);
    const image =
        event.image_url ||
        settings.media_footer_cta_image_url ||
        settings.media_about_hero_fallback_image_url;
    const whatsappNumber = settings.global_whatsapp;

    if (isLoading) {
        return (
            <main className="event-detail-page">
                <div className="event-detail-skeleton" />
            </main>
        );
    }

    if (error) {
        return (
            <main className="event-detail-page">
                <section className="event-detail-state">
                    <p>Event tidak tersedia</p>
                    <h1>Event ini belum aktif atau sudah selesai.</h1>
                    <Link to="/">Kembali ke beranda</Link>
                </section>
            </main>
        );
    }

    return (
        <main
            className="event-detail-page"
            style={{ "--event-hero-image": image ? `url("${image}")` : "none" }}
        >
            <section className="event-detail-hero">
                <div className="event-detail-hero__image" aria-hidden="true" />
                <div className="event-detail-hero__content">
                    <Link to="/" className="event-detail-back">
                        Kembali ke beranda
                    </Link>
                    <p>Event Karang Sidemen</p>
                    <h1>{event.title}</h1>
                    <div className="event-detail-hero__bottom">
                        <span>{eventDateText(event)}</span>
                        <p>
                            {event.description ||
                                "Informasi event dari POKDARWIS Karang Sidemen."}
                        </p>
                    </div>
                </div>
            </section>

            <section className="event-detail-shell">
                <article className="event-detail-card">
                    <div>
                        <p>Informasi event</p>
                        <h2>{event.title}</h2>
                    </div>
                    <p>
                        {event.description ||
                            "Detail event belum lengkap. Hubungi pengelola untuk informasi terbaru."}
                    </p>
                    <div className="event-detail-actions">
                        {event.external_url && (
                            <a
                                href={event.external_url}
                                target="_blank"
                                rel="noreferrer"
                            >
                                Buka info event
                            </a>
                        )}
                        {whatsappNumber && (
                            <a
                                href={buildWhatsAppUrl(
                                    `Halo, saya ingin bertanya tentang event ${event.title}`,
                                    whatsappNumber,
                                )}
                                target="_blank"
                                rel="noreferrer"
                                className="event-detail-actions__primary"
                            >
                                Tanya via WhatsApp
                            </a>
                        )}
                    </div>
                </article>
            </section>
        </main>
    );
}
