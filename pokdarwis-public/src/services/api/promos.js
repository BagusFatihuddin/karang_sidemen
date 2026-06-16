import apiClient from "./client";

export const getPromos = async () => {
    return apiClient.get("/promos");
};

export const getPromo = async (id) => {
    return apiClient.get(`/promos/${id}`);
};
