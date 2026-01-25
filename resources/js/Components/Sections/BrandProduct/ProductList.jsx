import React from "react";
import ProductCard from "@/Components/Common/ProductCard";
import { Link } from "@inertiajs/react";

export default function ProductList({ products }) {
    return (
        <div className="row gy-4">
            {products.data.map((product) => (
                <div
                    key={product.id}
                    className="col-xl-3 col-lg-4 col-md-6 col-sm-6"
                >
                    <ProductCard product={product} />
                </div>
            ))}

            {/* Pagination */}
            {products.links && products.links.length > 3 && (
                <div className="th-pagination text-center mt-50">
                    <ul>
                        {products.links.map((link, i) => {
                            let label = link.label;
                            if (label.includes("&laquo;"))
                                label = '<i class="far fa-arrow-left"></i>';
                            if (label.includes("&raquo;"))
                                label = '<i class="far fa-arrow-right"></i>';
                            if (label.includes("Previous"))
                                label = '<i class="far fa-arrow-left"></i>';
                            if (label.includes("Next"))
                                label = '<i class="far fa-arrow-right"></i>';

                            return (
                                <li key={i}>
                                    {link.url ? (
                                        <Link
                                            href={link.url}
                                            className={
                                                link.active ? "active" : ""
                                            }
                                            preserveScroll
                                            dangerouslySetInnerHTML={{
                                                __html: label,
                                            }}
                                        ></Link>
                                    ) : (
                                        <span
                                            className="text-muted"
                                            style={{
                                                display: "inline-block",
                                                padding: "0 15px",
                                                opacity: 0.5,
                                                cursor: "not-allowed",
                                            }}
                                            dangerouslySetInnerHTML={{
                                                __html: label,
                                            }}
                                        ></span>
                                    )}
                                </li>
                            );
                        })}
                    </ul>
                </div>
            )}
        </div>
    );
}
