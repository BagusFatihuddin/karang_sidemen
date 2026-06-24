import apiClient from "./client";

export const getDestinations = async (params = {}) => {
    const requestParams =
        typeof params === "string" ? { type: params } : { ...params };

    Object.keys(requestParams).forEach((key) => {
        if (!requestParams[key]) {
            delete requestParams[key];
        }
    });

    return apiClient.get("/destinations", {
        params: requestParams,
    });
};

export const getDestination = async (id) => {
    return apiClient.get(`/destinations/${id}`);
};
