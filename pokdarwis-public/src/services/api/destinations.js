import apiClient from "./client";

export const getDestinations = async () => {
    return apiClient.get("/destinations");
};
