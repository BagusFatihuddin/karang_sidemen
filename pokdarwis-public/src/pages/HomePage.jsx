import { useEffect } from "react";
import apiClient from "../services/api/client";

export default function HomePage() {
    useEffect(() => {
        const checkApi = async () => {
            try {
                const response = await apiClient.get("/ping");
                console.log(response.data);
            } catch (error) {
                console.error(error);
            }
        };

        checkApi();
    }, []);

    return <h1>Home Page</h1>;
}
