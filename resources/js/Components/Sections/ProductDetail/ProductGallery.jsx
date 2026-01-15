import React from "react";

export default function ProductGallery({ image, name }) {
    return (
        <div className="col-xxl-5 mb-4">
            <div className="th-team team-grid">
                <div className="team-img m-auto text-center text-xxl-start">
                    <img src={image} alt={name} />
                </div>
            </div>
        </div>
    );
}
