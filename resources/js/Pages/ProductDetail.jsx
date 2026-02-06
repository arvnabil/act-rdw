import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductGallery from "@/Components/Sections/ProductDetail/ProductGallery";
import ProductInfo from "@/Components/Sections/ProductDetail/ProductInfo";
import ProductTabs from "@/Components/Sections/ProductDetail/ProductTabs";
import RelatedProducts from "@/Components/Sections/ProductDetail/RelatedProducts";
import Seo from "@/Components/Common/Seo";

export default function ProductDetail({ product, seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />

            <Breadcrumb
                title={product.name}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Products", link: "/products" },
                    {
                        label: product.brand.name,
                        link: `/${product.brand.slug}/products`,
                    },
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
