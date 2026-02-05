import React from "react";
import { Link, usePage } from "@inertiajs/react";
import { getWhatsAppLink } from "@/Utils/whatsapp";

export default function ProductInfo({ product }) {
    return (
        <div className="col-12 col-lg-7 col-xxl-7">
            <div className="team-right ms-lg-4 ps-lg-2 ms-xxl-5 ps-xxl-3">
                <div className="team-content mb-25 d-md-flex justify-content-between">
                    <div className="media-body">
                        <h3 className="box-title">
                            <Link href="#">{product.name}</Link>
                        </h3>
                        <span className="team-desig">{product.category}</span>
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
                                            {product.brand?.name || "Brand"}
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
                <div className="team-btn d-flex align-items-center gap-3">
                    {/* WhatsApp Button */}
                    {(() => {
                        const { settings } = usePage().props;
                        const message = (
                            product.whatsapp_note ||
                            `Halo, saya tertarik dengan produk ${product.name}. Mohon info harga terbaik.`
                        ).replace("{Product Name}", product.name);

                        const link = getWhatsAppLink(settings?.whatsapp_number, message) || "#";

                        return (
                            <a
                                href={link}
                                target="_blank"
                                rel="noreferrer"
                                className="th-btn th-icon"
                            >
                                Permintaan Harga Terbaik{" "}
                                <i className="fa-solid fa-arrow-right"></i>
                            </a>
                        );
                    })()}

                    {/* Online Purchase Button */}
                    {product.link_accommerce && (
                        <a
                            href={product.link_accommerce}
                            target="_blank"
                            rel="noreferrer"
                            className="th-btn th-icon"
                            style={{
                                backgroundColor: "#0056b3",
                                borderColor: "#0056b3",
                                color: "#fff",
                            }}
                        >
                            Pembelian Online{" "}
                            <i className="fa-solid fa-cart-shopping"></i>
                        </a>
                    )}
                </div>
            </div>
        </div>
    );
}
