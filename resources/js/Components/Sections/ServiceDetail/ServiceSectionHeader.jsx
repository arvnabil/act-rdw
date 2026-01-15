import React from "react";

export default function ServiceSectionHeader({ title, subtitle }) {
    return (
        <div className="row justify-content-center text-center">
            <div className="col-xl-12">
                <span className="sub-title">{subtitle}</span>
                <h2 className="sec-title text-center mb-30">
                    {title || "Explore a full range of solutions"}
                </h2>
            </div>
        </div>
    );
}
