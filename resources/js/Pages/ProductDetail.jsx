import React from "react";
import { Head } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductGallery from "@/Components/Sections/ProductDetail/ProductGallery";
import ProductInfo from "@/Components/Sections/ProductDetail/ProductInfo";
import ProductTabs from "@/Components/Sections/ProductDetail/ProductTabs";
import RelatedProducts from "@/Components/Sections/ProductDetail/RelatedProducts";

export default function ProductDetail({ product }) {
    return (
        <MainLayout>
            <Head title={product.name} />

            <Breadcrumb
                title={product.name}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Products", link: "/products" },
                    { label: product.name },
                ]}
            />

            {/* Product Details Area */}
            <section className="product-details space-extra-bottom space-top">
                <div className="container">
                    <div className="team-details">
                        <div className="row">
                            <ProductGallery
                                image={product.image}
                                name={product.name}
                            />
                            <ProductInfo product={product} />
                        </div>
                    </div>

                    <ProductTabs product={product} />

                    <RelatedProducts
                        relatedProducts={product.related_products}
                    />
                </div>
            </section>
        </MainLayout>
    );
}
