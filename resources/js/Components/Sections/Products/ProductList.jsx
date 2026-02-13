import ProductCard from "@/Components/Common/ProductCard";
import { Link } from "@inertiajs/react";
import { getImageUrl } from "@/Utils/image";
import EmptyState from "@/Components/Common/EmptyState";

export default function ProductList({ products, viewMode }) {

    if (!products || !products.data || products.data.length === 0) {
        return (
            <EmptyState
                title="Produk Tidak Ditemukan"
                message="Maaf, tidak ada produk yang sesuai dengan kriteria pencarian Anda. Coba sesuaikan kata kunci atau filter Anda."
            />
        );
    }

    return (
        <div className="row gy-40">
            <div className="tab-content" id="nav-tabContent">
                <div
                    className={`tab-pane fade ${viewMode === "grid" ? "active show" : ""
                        }`}
                    id="tab-grid"
                >
                    <div className="row gy-40">
                        {products.data.map((product) => (
                            <div className="col-xl-4 col-sm-6" key={product.id}>
                                <div className="h-100">
                                    <ProductCard product={product} />
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
                <div
                    className={`tab-pane fade ${viewMode === "list" ? "active show" : ""
                        }`}
                    id="tab-list"
                >
                    <div className="row gy-30">
                        {products.data.map((product) => (
                            <div className="col-md-6" key={product.id}>
                                <div className="th-product list-view">
                                    <div className="product-img">
                                        <img
                                            src={getImageUrl(product.image_path)}
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
