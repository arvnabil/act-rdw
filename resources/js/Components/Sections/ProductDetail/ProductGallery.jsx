import React from "react";
import { getImageUrl } from "@/Utils/image";

export default function ProductGallery({ image, name }) {
    return (
        <div className="col-12 col-lg-5 col-xxl-5 mb-4">
            <div className="th-team team-grid">
                <div className="team-img m-auto text-center text-xxl-start">
                    <img
                        src={getImageUrl(image)}
                        alt={name}
                        className="img-fluid"
                        style={{ maxHeight: '400px', objectFit: 'contain' }}
                    />
                </div>
            </div>
        </div>
    );
}
