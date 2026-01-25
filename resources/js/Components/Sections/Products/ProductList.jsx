import ProductCard from "@/Components/Common/ProductCard";
import { Link } from "@inertiajs/react";

export default function ProductList({ products, viewMode }) {
    if (!products || !products.data || products.data.length === 0) {
        return (
            <div className="text-center py-5">
                <div className="mb-4">
                    <i className="fa-regular fa-box-open fa-3x text-muted"></i>
                </div>
                <h3>No Products Found</h3>
                <p className="text-muted">
                    Try adjusting your search or filter to find what you're
                    looking for.
                </p>
            </div>
        );
    }

    return (
        <div className="row gy-40">
            <div className="tab-content" id="nav-tabContent">
                <div
                    className={`tab-pane fade ${
                        viewMode === "grid" ? "active show" : ""
                    }`}
                    id="tab-grid"
                >
                    <div className="row gy-40">
                        {products.data.map((product) => (
                            <div className="col-xl-4 col-sm-6" key={product.id}>
                                <ProductCard product={product} />
                            </div>
                        ))}
                    </div>
                </div>
                <div
                    className={`tab-pane fade ${
                        viewMode === "list" ? "active show" : ""
                    }`}
                    id="tab-list"
                >
                    <div className="row gy-30">
                        {products.data.map((product) => (
                            <div className="col-md-6" key={product.id}>
                                <div className="th-product list-view">
                                    <div className="product-img">
                                        <img
                                            src={
                                                product.image_path ||
                                                "/assets/img/product/product_1_1.png"
                                            }
                                            alt={product.name}
                                        />
                                        <div className="actions">
                                            <Link
                                                href={`/products/${product.slug}`}
                                                className="icon-btn"
                                            >
                                                <i className="far fa-eye"></i>
                                            </Link>
                                        </div>
                                    </div>
                                    <div className="product-content">
                                        <span className="category">
                                            {product.category?.name ||
                                                product.service?.name ||
                                                "General"}
                                        </span>
                                        <h3 className="product-title">
                                            <Link
                                                href={`/products/${product.slug}`}
                                            >
                                                {product.name}
                                            </Link>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
