import React from "react";

export default function ServiceFilterBar({ filters }) {
    return (
        <div className="row">
            <div className="col-xl-12">
                <div className="case-menu mb-5 text-center">
                    {filters.map((filter, index) => (
                        <button
                            key={index}
                            className={`case4-btn ${
                                index === 0 ? "active" : ""
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
