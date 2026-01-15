import React, { useState } from "react";

export default function ProductTabs({ product }) {
    const [activeTab, setActiveTab] = useState("description");

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
                    <p className="mt-4">{product.description}</p>
                </div>
                <div
                    className={`tab-pane fade ${
                        activeTab === "specification" ? "show active" : ""
                    }`}
                    id="additional"
                    role="tabpanel"
                    aria-labelledby="additional-tab"
                >
                    <p className="mt-4 mb-4">{product.specification_text}</p>
                    <div className="woocommerce-Reviews">
                        <div className="th-comments-wrap">
                            <table className="cart_table">
                                <thead>
                                    <tr>
                                        <th className="cart-col-image">
                                            Specification
                                        </th>
                                        <th className="cart-col-productname">
                                            Details
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {product.specification.map(
                                        (spec, index) => (
                                            <tr
                                                className="cart_item"
                                                key={index}
                                            >
                                                <td data-title="Specification">
                                                    <span className="amount">
                                                        {spec.name}
                                                    </span>
                                                </td>
                                                <td data-title="Details">
                                                    <span className="amount">
                                                        {spec.value}
                                                    </span>
                                                </td>
                                            </tr>
                                        )
                                    )}
                                </tbody>
                            </table>
                        </div>
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
                    <p className="mt-4 mb-4">{product.features_text}</p>
                    <div className="woocommerce-Reviews">
                        <div className="th-comments-wrap">
                            <table className="cart_table">
                                <thead>
                                    <tr>
                                        <th className="cart-col-image">
                                            Features
                                        </th>
                                        <th className="cart-col-productname">
                                            Description
                                        </th>
                                        <th className="cart-col-price">
                                            Additional
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {product.features.map((feature, index) => (
                                        <tr className="cart_item" key={index}>
                                            <td data-title="Features">
                                                <a
                                                    className="cart-productname"
                                                    href="#"
                                                >
                                                    {feature.name}
                                                </a>
                                            </td>
                                            <td data-title="Description">
                                                <a
                                                    className="cart-productname"
                                                    href="#"
                                                >
                                                    {feature.description}
                                                </a>
                                            </td>
                                            <td data-title="Additional">
                                                <span className="amount">
                                                    {feature.additional}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
