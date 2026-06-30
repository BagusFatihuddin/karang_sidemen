const skeletonStyle = {
    borderRadius: "8px",
    background: "linear-gradient(90deg, #f3f4f6, #e5e7eb, #f3f4f6)",
    backgroundSize: "200% 100%",
    animation: "shimmer 2s infinite",
};

export default function PageSkeleton({ height = "200px", count = 1 }) {
    return (
        <>
            <style>{`
                @keyframes shimmer {
                    0% { background-position: 200% 0; }
                    100% { background-position: -200% 0; }
                }
            `}</style>
            {Array.from({ length: count }).map((_, i) => (
                <div
                    key={i}
                    style={{
                        ...skeletonStyle,
                        height,
                        marginBottom: i < count - 1 ? "16px" : "0",
                    }}
                />
            ))}
        </>
    );
}
