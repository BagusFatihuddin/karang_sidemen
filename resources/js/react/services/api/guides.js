import apiClient from "./client";

export const getGuides = async () => {
    return apiClient.get("/guides");
};
