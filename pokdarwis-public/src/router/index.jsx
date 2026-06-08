import React, { Suspense } from "react";
import { createBrowserRouter } from "react-router-dom";

import PublicLayout from "../layouts/PublicLayout";

const HomePage = React.lazy(() => import("../pages/HomePage"));
const DestinationsPage = React.lazy(() => import("../pages/DestinationsPage"));
const DestinationDetailPage = React.lazy(
    () => import("../pages/DestinationDetailPage"),
);
const PackagesPage = React.lazy(() => import("../pages/PackagesPage"));
const GuidesPage = React.lazy(() => import("../pages/GuidesPage"));
const ReviewsPage = React.lazy(() => import("../pages/ReviewsPage"));
const ReviewTokenPage = React.lazy(() => import("../pages/ReviewTokenPage"));
const AboutPage = React.lazy(() => import("../pages/AboutPage"));
const NotFoundPage = React.lazy(() => import("../pages/NotFoundPage"));

const SuspenseFallback = () => (
    <div
        style={{
            display: "flex",
            alignItems: "center",
            justifyContent: "center",
            minHeight: "100vh",
            fontFamily: "system-ui, sans-serif",
            color: "#4b5563",
        }}
    >
        Memuat halaman...
    </div>
);

const router = createBrowserRouter([
    {
        path: "/",
        element: <PublicLayout />,
        children: [
            {
                index: true,
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <HomePage />
                    </Suspense>
                ),
            },
            {
                path: "destinasi",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <DestinationsPage />
                    </Suspense>
                ),
            },
            {
                path: "destinasi/:id",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <DestinationDetailPage />
                    </Suspense>
                ),
            },
            {
                path: "paket",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <PackagesPage />
                    </Suspense>
                ),
            },
            {
                path: "panduan",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <GuidesPage />
                    </Suspense>
                ),
            },
            {
                path: "reviews",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <ReviewsPage />
                    </Suspense>
                ),
            },
            {
                path: "review/:token",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <ReviewTokenPage />
                    </Suspense>
                ),
            },
            {
                path: "tentang",
                element: (
                    <Suspense fallback={<SuspenseFallback />}>
                        <AboutPage />
                    </Suspense>
                ),
            },
        ],
    },
    {
        path: "*",
        element: (
            <Suspense fallback={<SuspenseFallback />}>
                <NotFoundPage />
            </Suspense>
        ),
    },
]);

export default router;
