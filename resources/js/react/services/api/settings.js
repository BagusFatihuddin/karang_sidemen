import apiClient from "./client";

export const getPublicSettings = async () => {
    return apiClient.get("/settings/public");
};
