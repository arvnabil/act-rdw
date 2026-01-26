import React, { useState, useEffect, useRef } from "react";

const SerpPreview = ({
    title = "",
    description = "",
    url = "",
    siteName = "",
    initialMode = "desktop",
}) => {
    const [mode, setMode] = useState(initialMode);
    const [truncatedTitle, setTruncatedTitle] = useState("");
    const [truncatedDesc, setTruncatedDesc] = useState("");

    // Google Pixel Width Limits (Approximate)
    // Desktop: Title ~580px, Desc ~920px (max width of container is often cited around 600px for title line)
    // Mobile: Title ~350-400px (depends on device), Desc ~680px (lines wrap)
    const LIMITS = {
        desktop: { title: 580, desc: 920, container: "600px" },
        mobile: { title: 360, desc: 750, container: "100%" }, // Mobile title wraps, but we simulate visual density
    };

    // Canvas for text measurement
    const canvasRef = useRef(null);

    const measureText = (text, font) => {
        if (!canvasRef.current) {
            const canvas = document.createElement("canvas");
            canvasRef.current = canvas.getContext("2d");
        }
        const context = canvasRef.current;
        context.font = font;
        return context.measureText(text || "").width;
    };

    const truncateByPixel = (text, limit, font) => {
        if (!text) return "";
        const width = measureText(text, font);
        if (width <= limit) return text;

        let left = 0;
        let right = text.length;
        let mid;
        let result = text;

        // Binary search for optimal length
        while (left <= right) {
            mid = Math.floor((left + right) / 2);
            const substr = text.slice(0, mid) + "...";
            if (measureText(substr, font) <= limit) {
                result = substr;
                left = mid + 1;
            } else {
                right = mid - 1;
            }
        }
        return result;
    };

    useEffect(() => {
        // Google Fonts approximation
        // Title: 20px, Arial/Sans-serif (Desktop) | 18px (Mobile)
        // Desc: 14px, Arial/Sans-serif

        const titleFont =
            mode === "desktop"
                ? "20px arial, sans-serif"
                : "18px arial, sans-serif";
        const descFont = "14px arial, sans-serif";

        // Logic: use provided title, or fallback to placeholder.
        // Always append | Site Name to mimic typical Laravel SEO/Inertia behavior unless title already contains it.
        const baseTitle = title || "Page Title Preview";
        const suffix = ` | ${siteName || "Site Name"}`;

        const displayTitle = baseTitle.includes(siteName)
            ? baseTitle
            : `${baseTitle}${suffix}`;

        const displayDesc =
            description ||
            "Please enter a meta description to see how it looks in search results.";

        // Title Truncation
        // Mobile titles can wrap to 2 lines, desktop usually 1 line (sometimes 2 depending on query).
        // We will strictly truncate for preview safety.
        setTruncatedTitle(
            truncateByPixel(displayTitle, LIMITS[mode].title, titleFont),
        );

        // Description Truncation
        setTruncatedDesc(
            truncateByPixel(displayDesc, LIMITS[mode].desc, descFont),
        );
    }, [title, description, mode, siteName]);

    // Breadcrumb formatting: domain.com > category > page
    const formatUrl = (rawUrl) => {
        if (!rawUrl) return "example.com › ...";
        try {
            const urlObj = new URL(rawUrl);
            const path = urlObj.pathname.split("/").filter(Boolean).join(" › ");
            return `${urlObj.hostname}${path ? " › " + path : ""}`;
        } catch (e) {
            return rawUrl;
        }
    };

    return (
        <div className="font-sans antialiased bg-white dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
            {/* Header / Toggle */}
            <div className="flex items-center justify-between mb-4 pb-4 border-b border-gray-100 dark:border-gray-800">
                <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Google Search Preview
                </h3>
                <div
                    style={{
                        display: "flex",
                        gap: "8px",
                        background: "#f3f4f6",
                        padding: "4px",
                        borderRadius: "8px",
                    }}
                    className="bg-gray-100 dark:bg-gray-800"
                >
                    <button
                        onClick={() => setMode("desktop")}
                        style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "6px",
                            padding: "6px 12px",
                            borderRadius: "6px",
                            fontSize: "12px",
                            fontWeight: "500",
                            backgroundColor:
                                mode === "desktop" ? "#ffffff" : "transparent",
                            color: mode === "desktop" ? "#1f2937" : "#6b7280",
                            boxShadow:
                                mode === "desktop"
                                    ? "0 1px 2px 0 rgba(0, 0, 0, 0.05)"
                                    : "none",
                            border:
                                mode === "desktop"
                                    ? "1px solid #e5e7eb"
                                    : "1px solid transparent",
                            cursor: "pointer",
                        }}
                    >
                        <svg
                            style={{ width: "14px", height: "14px" }}
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                        Desktop
                    </button>
                    <button
                        onClick={() => setMode("mobile")}
                        style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "6px",
                            padding: "6px 12px",
                            borderRadius: "6px",
                            fontSize: "12px",
                            fontWeight: "500",
                            backgroundColor:
                                mode === "mobile" ? "#ffffff" : "transparent",
                            color: mode === "mobile" ? "#1f2937" : "#6b7280",
                            boxShadow:
                                mode === "mobile"
                                    ? "0 1px 2px 0 rgba(0, 0, 0, 0.05)"
                                    : "none",
                            border:
                                mode === "mobile"
                                    ? "1px solid #e5e7eb"
                                    : "1px solid transparent",
                            cursor: "pointer",
                        }}
                    >
                        <svg
                            style={{ width: "14px", height: "14px" }}
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"
                            />
                        </svg>
                        Mobile
                    </button>
                </div>
            </div>

            {/* Preview Container */}
            <div
                className="mx-auto"
                style={{
                    maxWidth: mode === "desktop" ? "600px" : "375px",
                    transition: "max-width 0.3s ease",
                    marginTop: "20px",
                }}
            >
                <div
                    className="bg-white dark:bg-gray-900"
                    style={{
                        padding: "24px",
                        borderRadius: "16px",
                        boxShadow:
                            "0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)",
                        border: "1px solid #f3f4f6",
                    }}
                >
                    {/* Header: Favicon + Site + URL */}
                    <div
                        style={{
                            display: "flex",
                            alignItems: "center",
                            gap: "12px",
                            marginBottom: "8px",
                        }}
                    >
                        <div
                            className="bg-gray-100 dark:bg-gray-800 rounded-full shrink-0"
                            style={{ padding: "2px" }}
                        >
                            {/* Generic Globe Favicon */}
                            <div
                                style={{
                                    width: "28px",
                                    height: "28px",
                                    backgroundColor: "#f1f3f4",
                                    borderRadius: "50%",
                                    display: "flex", // Explicit flex
                                    alignItems: "center", // Vertical center
                                    justifyContent: "center", // Horizontal center
                                    color: "#9aa0a6",
                                }}
                            >
                                <svg
                                    style={{ width: "18px", height: "18px" }}
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z" />
                                </svg>
                            </div>
                        </div>
                        <div
                            className="flex flex-col leading-snug min-w-0"
                            style={{ fontFamily: "arial, sans-serif" }}
                        >
                            <span
                                className="font-normal truncate"
                                style={{ fontSize: "14px", color: "#202124" }}
                            >
                                {siteName || "Site Name"}
                            </span>
                            <span
                                className="truncate"
                                style={{ fontSize: "12px", color: "#5f6368" }}
                            >
                                {formatUrl(url)}
                            </span>
                        </div>
                        <div className="ml-auto shrink-0 text-gray-400">
                            <svg
                                style={{ width: "16px", height: "16px" }}
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                            </svg>
                        </div>
                    </div>

                    {/* Title */}
                    <h3
                        className="font-normal leading-snug cursor-pointer hover:underline"
                        style={{
                            color: "#1a0dab",
                            fontSize: mode === "desktop" ? "20px" : "18px",
                            fontFamily: "arial, sans-serif",
                            margin: 0,
                        }}
                    >
                        {truncatedTitle}
                    </h3>

                    {/* Description */}
                    <div style={{ marginTop: "4px" }}>
                        <p
                            className="leading-[1.58] break-words"
                            style={{
                                fontSize: "14px",
                                color: "#4d5156",
                                fontFamily: "arial, sans-serif",
                                margin: 0,
                            }}
                        >
                            {truncatedDesc}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SerpPreview;
