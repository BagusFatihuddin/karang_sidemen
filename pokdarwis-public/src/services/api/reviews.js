import apiClient from "./client";

export const getReviews = async () => {
    return apiClient.get("/reviews");
};
