import apiClient from "./client";

export const getTripPackages = async () => {
    return apiClient.get("/trip-packages");
};
