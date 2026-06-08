import apiClient from "./client";

export const getDestinations = async (type) => {
    return apiClient.get("/destinations", {
        params: type ? { type } : {},
    });
};
