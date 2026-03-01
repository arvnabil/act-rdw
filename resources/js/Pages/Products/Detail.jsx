import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductShowcase from "@/Components/Sections/ProductDetail/ProductShowcase";
import ProductTabs from "@/Components/Sections/ProductDetail/ProductTabs";
import RelatedProducts from "@/Components/Sections/ProductDetail/RelatedProducts";
import Seo from "@/Components/Common/Seo";

export default function ProductDetail({ product, seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />

            {product.show_breadcrumb && (
                <Breadcrumb
                    title={product.name}
                    bgImage={
                        product.breadcrumb_image ||
                        product.thumbnail_path ||
                        product.image_path
                    }
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
            )}

            {/* Product Details Area */}
            <section className="product-details space-extra-bottom space-top">
                <div className="container">
                    <ProductShowcase product={product} />

                    <ProductTabs product={product} />

                    <RelatedProducts
                        relatedProducts={product.related_products}
                    />
                </div>
            </section>
        </MainLayout>
    );
}
