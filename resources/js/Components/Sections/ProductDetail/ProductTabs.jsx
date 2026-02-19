import React, { useState, useEffect } from "react";

export default function ProductTabs({ product }) {
    const [activeTab, setActiveTab] = useState("description");

    // Notify Lenis smooth scroll to recalculate page height when tab changes
    useEffect(() => {
        const timer = setTimeout(() => {
            window.dispatchEvent(new Event("resize"));
        }, 100);
        return () => clearTimeout(timer);
    }, [activeTab]);

    const getGroupedData = (data) => {
        const groups = {};
        const items = Array.isArray(data) ? data : [];

        items.forEach((item) => {
            let category = "General";
            let name = item.name;

            if (item.name && item.name.includes(" - ")) {
                const parts = item.name.split(" - ");
                category = parts[0].trim();
                name = parts.slice(1).join(" - ").trim();
            }

            if (!groups[category]) {
                groups[category] = [];
            }
            groups[category].push({ ...item, displayName: name });
        });

        return groups;
    };

    const getNormalizedSpecs = (data) => {
        if (!data || typeof data !== 'object') return {};
        
        // If it's already in the new nested format
        if (data.Spesifikasi) return data.Spesifikasi;

        // Otherwise, it's flat or mixed data
        const nested = {};
        Object.entries(data).forEach(([key, value]) => {
            let group = "General";
            let name = key;

            // Case 1: Key acts as separator "Group - Name"
            if (key.includes(" - ")) {
                const parts = key.split(" - ");
                group = parts[0].trim();
                name = parts.slice(1).join(" - ").trim();
                
                if (!nested[group]) nested[group] = {};
                nested[group][name] = value;
            } 
            // Case 2: Value is object/array (e.g. "Spesifikasi & Perincian" => [...])
            // Treat key as the group name.
            else if (typeof value === 'object' && value !== null) {
                nested[key] = value;
            }
            // Case 3: Flat key, scalar value
            else {
                if (!nested[group]) nested[group] = {};
                nested[group][name] = value;
            }
        });
        return nested;
    };

    const renderValue = (value) => {
        if (value === null || value === undefined) return "";
        if (typeof value !== 'object') return value;

        // Recursive flattening of arrays/objects
        return Object.values(value)
            .map(val => {
                if (typeof val === 'object' && val !== null) {
                    return renderValue(val);
                }
                return val;
            })
            .filter(val => val !== "" && val !== null && val !== undefined)
            .join(", ");
    };

    const specsData = getNormalizedSpecs(product.specification);
    const groupedFeatures = getGroupedData(product.features);

    return (
        <div className="mt-5 pt-5">
            <ul
                className="nav product-tab-style2"
                id="productTab"
                role="tablist"
            >
                <li className="nav-item" role="presentation">
                    <a
                        className={`nav-link ${
                            activeTab === "description" ? "active" : ""
                        }`}
                        id="description-tab"
                        onClick={(e) => {
                            e.preventDefault();
                            setActiveTab("description");
                        }}
                        href="#description"
                        role="tab"
                        aria-controls="description"
                        aria-selected={activeTab === "description"}
                    >
                        Description
                    </a>
                </li>
                <li className="nav-item" role="presentation">
                    <a
                        className={`nav-link ${
                            activeTab === "specification" ? "active" : ""
                        }`}
                        id="additional-tab"
                        onClick={(e) => {
                            e.preventDefault();
                            setActiveTab("specification");
                        }}
                        href="#additional"
                        role="tab"
                        aria-controls="additional"
                        aria-selected={activeTab === "specification"}
                    >
                        Specification
                    </a>
                </li>
                <li className="nav-item" role="presentation">
                    <a
                        className={`nav-link ${
                            activeTab === "features" ? "active" : ""
                        }`}
                        id="features-tab"
                        onClick={(e) => {
                            e.preventDefault();
                            setActiveTab("features");
                        }}
                        href="#features"
                        role="tab"
                        aria-controls="features"
                        aria-selected={activeTab === "features"}
                    >
                        Features
                    </a>
                </li>
            </ul>
            <div className="tab-content" id="productTabContent">
                <div
                    className={`tab-pane fade ${
                        activeTab === "description" ? "show active" : ""
                    }`}
                    id="description"
                    role="tabpanel"
                    aria-labelledby="description-tab"
                >
                    <div
                        className="mt-4"
                        dangerouslySetInnerHTML={{
                            __html: product.description,
                        }}
                    />
                </div>
                <div
                    className={`tab-pane fade ${
                        activeTab === "specification" ? "show active" : ""
                    }`}
                    id="additional"
                    role="tabpanel"
                    aria-labelledby="additional-tab"
                >
                    <div
                        className="mt-4 mb-4"
                        dangerouslySetInnerHTML={{
                            __html: product.specification_text,
                        }}
                    />
                    <div className="specs-container mt-4">
                        {Object.entries(specsData).map(([category, items], catIndex) => (
                            <div key={category} className="spec-group mb-4">
                                <h4 className="spec-group-title py-2 px-3 mb-0" style={{
                                    backgroundColor: "#f8f9fa",
                                    fontSize: "0.9rem",
                                    fontWeight: "700",
                                    textTransform: "uppercase",
                                    letterSpacing: "1px",
                                    color: "#212529",
                                    borderLeft: "4px solid #198754",
                                    borderRadius: "4px"
                                }}>
                                    {category}
                                </h4>
                                <div className="spec-items">
                                    {Object.entries(items).map(([key, value], index) => (
                                        <div 
                                            key={`${catIndex}-${index}`}
                                            className="spec-row d-flex flex-wrap py-3 px-3 align-items-center"
                                            style={{
                                                borderBottom: "1px solid #edf2f7",
                                                transition: "background-color 0.2s"
                                            }}
                                        >
                                            <div className="spec-label col-12 col-md-5 fw-bold text-dark mb-1 mb-md-0" style={{ fontSize: "0.95rem" }}>
                                                {key}
                                            </div>
                                            <div className="spec-value col-12 col-md-7 text-muted" style={{ fontSize: "0.95rem" }}>
                                                {renderValue(value)}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
                <div
                    className={`tab-pane fade ${
                        activeTab === "features" ? "show active" : ""
                    }`}
                    id="features"
                    role="tabpanel"
                    aria-labelledby="features-tab"
                >
                    {product.features_text && (
                        <div
                            className="mt-4 mb-4"
                            dangerouslySetInnerHTML={{
                                __html: product.features_text,
                            }}
                        />
                    )}
                    <div className="features-container mt-4">
                        {Object.entries(groupedFeatures).map(([category, items], catIndex) => (
                            <div key={category} className="feature-group mb-5">
                                {category !== "General" && (
                                    <h4 className="feature-group-title py-2 px-3 mb-4" style={{
                                        backgroundColor: "#f8f9fa",
                                        fontSize: "0.95rem",
                                        fontWeight: "700",
                                        textTransform: "uppercase",
                                        letterSpacing: "1.5px",
                                        color: "#212529",
                                        borderLeft: "5px solid #198754",
                                        borderRadius: "4px",
                                        boxShadow: "0 2px 4px rgba(0,0,0,0.02)"
                                    }}>
                                        {category}
                                    </h4>
                                )}
                                <div className="feature-items row gy-4">
                                    {items.map((feature, index) => (
                                        <div 
                                            key={`${catIndex}-${index}`}
                                            className="col-12"
                                        >
                                            <div className="feature-card p-4 h-100" style={{
                                                backgroundColor: "#ffffff",
                                                border: "1px solid #f0f0f0",
                                                borderRadius: "16px",
                                                transition: "all 0.3s ease",
                                                boxShadow: "0 4px 12px rgba(0,0,0,0.03)",
                                                display: "flex",
                                                gap: "20px"
                                            }}>
                                                <div className="feature-icon mt-1" style={{ flexShrink: 0 }}>
                                                    <div style={{
                                                        width: "28px",
                                                        height: "28px",
                                                        backgroundColor: "#E8F5E9",
                                                        borderRadius: "50%",
                                                        display: "flex",
                                                        alignItems: "center",
                                                        justifyContent: "center",
                                                        color: "#198754"
                                                    }}>
                                                        <i className="fas fa-check" style={{ fontSize: "12px" }}></i>
                                                    </div>
                                                </div>
                                                <div className="feature-content" style={{ flexGrow: 1 }}>
                                                    <div className="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-2">
                                                        <h5 className="mb-0 fw-bold text-dark" style={{ 
                                                            fontSize: "1.1rem",
                                                            letterSpacing: "-0.01em"
                                                        }}>
                                                            {feature.displayName}
                                                        </h5>
                                                        <div className="text-success fw-bold px-2 py-1 rounded" style={{ 
                                                            fontSize: "0.9rem",
                                                            backgroundColor: "#f0fdf4",
                                                            border: "1px solid #dcfce7"
                                                        }}>
                                                            {renderValue(feature.value)}
                                                        </div>
                                                    </div>
                                                    <p className="mb-0 text-muted" style={{ 
                                                        fontSize: "0.95rem", 
                                                        lineHeight: "1.6",
                                                        fontWeight: "400"
                                                    }}>
                                                        {renderValue(feature.additional)}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>

                    <style jsx="true">{`
                        .feature-card:hover {
                            transform: translateY(-4px);
                            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06) !important;
                            border-color: #198754 !important;
                            background-color: #fcfdfc !important;
                        }
                        .feature-card:hover .feature-icon div {
                            background-color: #198754 !important;
                            color: #ffffff !important;
                            transform: scale(1.1);
                            transition: all 0.3s ease;
                        }
                    `}</style>
                </div>
            </div>
        </div>
    );
}
