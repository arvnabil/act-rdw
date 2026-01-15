import React from "react";
import { Link } from "@inertiajs/react";

export default function ProductInfo({ product }) {
    return (
        <div className="col-xxl-7">
            <div className="team-right ms-xxl-5 ps-xxl-3">
                <div className="team-content mb-25 d-md-flex justify-content-between">
                    <div className="media-body">
                        <h3 className="box-title">
                            <Link href="#">{product.name}</Link>
                        </h3>
                        <span className="team-desig">
                            {product.specification.find(
                                (s) => s.name === "Brand"
                            )?.value || "Brand"}
                        </span>
                    </div>
                </div>
                <div className="team-infobox mb-40">
                    <div className="row gx-0">
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                <span className="team-info-icon">
                                    <i className="fa-solid fa-barcode"></i>
                                </span>
                                <div className="team-info-content">
                                    <span className="team-info-subtitle">
                                        SKU:{" "}
                                    </span>
                                    <h4 className="team-info-title">
                                        {product.sku}
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                <span className="team-info-icon">
                                    <i className="fa-solid fa-fire"></i>
                                </span>
                                <div className="team-info-content">
                                    <span className="team-info-subtitle">
                                        Solution Type
                                    </span>
                                    <h4 className="team-info-title">
                                        <a href="#">{product.solution_type}</a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                <span className="team-info-icon">
                                    <i className="fa-solid fa-copyright"></i>
                                </span>
                                <div className="team-info-content">
                                    <span className="team-info-subtitle">
                                        Brand
                                    </span>
                                    <h4 className="team-info-title">
                                        <a href="#">
                                            {product.specification.find(
                                                (s) => s.name === "Brand"
                                            )?.value || "Brand"}
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-lg-6 col-md-6">
                            <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                <span className="team-info-icon">
                                    <i className="fa-solid fa-download"></i>
                                </span>
                                <div className="team-info-content">
                                    <span className="team-info-subtitle">
                                        Datasheet
                                    </span>
                                    <h4 className="team-info-title">
                                        <a href={product.datasheet_url}>
                                            See datasheet
                                        </a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="product_meta mb-4">
                    <span className="posted_in">
                        Category:
                        <Link href="/products"> {product.category}</Link>
                    </span>
                    <span>
                        Tags:{" "}
                        {product.tags.map((tag, index) => (
                            <React.Fragment key={index}>
                                <Link href="/products">{tag}</Link>
                                {index < product.tags.length - 1 ? ", " : ""}
                            </React.Fragment>
                        ))}
                    </span>
                </div>
                <div className="team-btn">
                    <a href="/contact" className="th-btn th-icon">
                        Get A Quote <i className="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    );
}
