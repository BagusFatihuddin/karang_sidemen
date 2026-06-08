import { useQuery } from "@tanstack/react-query";

import { getPublicSettings } from "../services/api/settings";

export const usePublicSettings = () => {
    return useQuery({
        queryKey: ["public-settings"],
        queryFn: getPublicSettings,
    });
};
