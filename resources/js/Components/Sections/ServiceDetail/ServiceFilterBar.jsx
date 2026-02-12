import React from "react";

export default function ServiceFilterBar({ filters }) {
    return (
        <div className="row">
            <div className="col-xl-12">
                <style>{`
                    /* Mobile Optimization for Filter Tabs - Clean Wrap */
                    @media (max-width: 991px) {
                        .case-menu {
                            display: flex;
                            flex-wrap: wrap;       /* Allow wrapping */
                            justify-content: center; /* Center align buttons */
                            gap: 12px;
                            padding: 0 10px;
                        }

                        .case4-btn {
                            margin: 0 !important; /* Override template margins */
                            font-size: 13px;
                            padding: 10px 18px;
                            border-radius: 20px;
                            background: #f4f7f9;
                            border: 1px solid transparent;
                            flex: 0 0 auto;
                            width: auto;
                            line-height: 1.4;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
                        }
                        
                        .case4-btn.active {
                            background: var(--theme-color);
                            color: #fff;
                            box-shadow: 0 4px 12px rgba(13, 80, 93, 0.2);
                        }

                        /* Override any potentially interfering selectors */
                        .case4-btn:not(:last-child) {
                            margin-right: 0 !important;
                            margin-bottom: 0 !important;
                        }
                    }
                `}</style>
                <div className="case-menu mb-5 text-center">
                    {filters.map((filter, index) => (
                        <button
                            key={index}
                            className={`case4-btn ${index === 0 ? "active" : ""
                                }`}
                            data-filter={
                                filter.value === "*"
                                    ? "*"
                                    : filter.value.startsWith(".")
                                        ? filter.value
                                        : `.${filter.value}`
                            }
                        >
                            {filter.label}
                        </button>
                    ))}
                </div>
            </div>
        </div>
    );
}
