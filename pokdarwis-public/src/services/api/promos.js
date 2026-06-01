import apiClient from "./client";

export const getPromos = async () => {
    return apiClient.get("/promos");
};
