export const normalizeList = (value) => {
    if (Array.isArray(value)) {
        return value.filter(Boolean);
    }

    if (typeof value === "string") {
        try {
            const parsed = JSON.parse(value);

            if (Array.isArray(parsed)) {
                return parsed.filter(Boolean);
            }
        } catch {
            // Support legacy comma-separated values.
        }

        return value
            .split(",")
            .map((item) => item.trim())
            .filter(Boolean);
    }

    return [];
};
