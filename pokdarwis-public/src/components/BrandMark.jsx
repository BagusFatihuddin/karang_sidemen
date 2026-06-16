export default function BrandMark({ settings = {}, className = "", imageClassName = "" }) {
    const logoUrl = settings.brand_logo_url;
    const logoAlt = settings.brand_logo_alt || settings.village_name || "Karang Sidemen";
    const fallbackText = settings.brand_mark_text || "KS";

    if (logoUrl) {
        return (
            <span className={className}>
                <img className={imageClassName} src={logoUrl} alt={logoAlt} />
            </span>
        );
    }

    return <span className={className}>{fallbackText}</span>;
}
