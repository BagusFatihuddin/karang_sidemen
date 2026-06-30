import apiClient from "./client";

export const getPinnedReviews = () =>
    apiClient.get("/reviews/pinned");
