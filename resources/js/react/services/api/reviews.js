import apiClient from "./client";

export const getReviews = async (params = {}) => {
    return apiClient.get("/reviews", {
        params,
    });
};
