import apiClient from "./client";

export const getDestinations = async (type) => {
    const destinationType = typeof type === "string" ? type : "";

    return apiClient.get("/destinations", {
        params: destinationType ? { type: destinationType } : {},
    });
};

export const getDestination = async (id) => {
    return apiClient.get(`/destinations/${id}`);
};
