import { createBrowserRouter } from "react-router-dom";

import HomePage from "../pages/HomePage";
import DestinationsPage from "../pages/DestinationsPage";
import DestinationDetailPage from "../pages/DestinationDetailPage";
import PackagesPage from "../pages/PackagesPage";
import GuidesPage from "../pages/GuidesPage";
import ReviewsPage from "../pages/ReviewsPage";
import ReviewTokenPage from "../pages/ReviewTokenPage";
import AboutPage from "../pages/AboutPage";
import NotFoundPage from "../pages/NotFoundPage";
import PublicLayout from "../layouts/PublicLayout";

const router = createBrowserRouter([
    {
        path: "/",
        element: <PublicLayout />,
        children: [
            {
                index: true,
                element: <HomePage />,
            },
            {
                path: "destinasi",
                element: <DestinationsPage />,
            },
            {
                path: "destinasi/:id",
                element: <DestinationDetailPage />,
            },
            {
                path: "paket",
                element: <PackagesPage />,
            },
            {
                path: "panduan",
                element: <GuidesPage />,
            },
            {
                path: "reviews",
                element: <ReviewsPage />,
            },
            {
                path: "review/:token",
                element: <ReviewTokenPage />,
            },
            {
                path: "tentang",
                element: <AboutPage />,
            },
        ],
    },
    {
        path: "*",
        element: <NotFoundPage />,
    },
]);

export default router;
