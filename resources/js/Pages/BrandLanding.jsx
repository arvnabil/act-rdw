import { Head } from "@inertiajs/react";
import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import BrandHeroSection from "@/Components/Sections/BrandLanding/BrandHeroSection";
import BrandServiceSolutionsSection from "@/Components/Sections/BrandLanding/BrandServiceSolutionsSection";
import BrandCategoryListSection from "@/Components/Sections/BrandLanding/BrandCategoryListSection";
import LatestProductsSection from "@/Components/Sections/BrandLanding/LatestProductsSection";
import WorkShowcaseSection from "@/Components/Sections/Common/WorkShowcaseSection";

const BrandLanding = ({
    brand,
    products,
    categories,
    pageSite,
    relatedServices,
}) => {
    const [thumbsSwiper, setThumbsSwiper] = React.useState(null);
    const [lightboxImage, setLightboxImage] = React.useState(null);

    React.useEffect(() => {
        const elements = document.querySelectorAll(".scroll-text-ani");
        elements.forEach((line) => {
            gsap.to(line, {
                backgroundImage:
                    "linear-gradient(to right, #0B1422 100%, #D5D7DA 100%)",
                ease: "none",
                scrollTrigger: {
                    trigger: line,
                    start: "top bottom",
                    end: "top center",
                    scrub: true,
                },
            });
        });
    }, []);

    // Helper for images
    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    const getBrandSlug = (b) => b.slug || b.name.toLowerCase();

    // Dummy Data for PageSite (until module is ready)
    const pageData = pageSite || {
        hero_styles: {
            background_color: "#444A572C", // Dark Slate Blue
            background_image:
                "https://activ.co.id/wp-content/uploads/2023/11/Group-97-1.png?id=5807",
        },
    };

    const staticShowcase = [
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Project Alpha",
            category: "Yealink",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Meeting Room B",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Conference Hall",
            category: "Jabra",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Huddle Space",
            category: "Maxhub",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Executive Office",
            category: "Zoom",
            image: "/assets/img/project/project_3_9_.jpg",
        },
    ];

    // --- Default Configuration (Fallback) ---
    const defaults = {
        hero: {
            enabled: true,
            title: pageData?.title ?? brand.name,
            subtitle: "Authorized Partner",
            desc:
                pageData?.meta_desc ??
                `Discover premium ${brand.name} solutions.`,
            background_image: null, // Component handles fallback
        },
        solutions: {
            enabled: true,
            title: "Our Solutions",
        },
        categories: {
            enabled: true,
            title: "Semua Kategori",
        },
        showcase: {
            enabled: true,
            title: "Work Showcase",
            items: [],
        },
        products: {
            enabled: true,
            title: "Latest Products",
            count: 8,
        },
    };

    // --- Config Resolver ---
    // Merges defaults with brand config and validates section readiness
    const resolveBrandSection = (
        sectionKey,
        defaultConfig,
        brandConfig,
        validatorFn = () => true,
    ) => {
        // 1. Determine enabled state (Config takes precedence > default > true)
        const isExplicitlyDisabled = brandConfig?.enabled === false;
        const isExplicitlyEnabled = brandConfig?.enabled === true;
        const defaultEnabled = defaultConfig.enabled ?? true;

        const enabled = isExplicitlyDisabled
            ? false
            : isExplicitlyEnabled
              ? true
              : defaultEnabled;

        // 2. Deep merge config (Brand config overrides defaults)
        const config = { ...defaultConfig, ...(brandConfig || {}) };

        // 3. Safety Check
        const safe = validatorFn(config);

        return { enabled, config, safe };
    };

    // --- Resolve Sections ---
    const hero = resolveBrandSection(
        "hero",
        defaults.hero,
        brand.landing_config?.hero,
    );

    const solutions = resolveBrandSection(
        "solutions",
        defaults.solutions,
        brand.landing_config?.solutions,
        () => relatedServices?.length > 0,
    );

    const categoriesSection = resolveBrandSection(
        "categories",
        defaults.categories,
        brand.landing_config?.categories,
        () => categories?.length > 0,
    );

    // Showcase safety: Safe if (config items exist) OR (static fallback exists)
    const showcase = resolveBrandSection(
        "showcase",
        defaults.showcase,
        brand.landing_config?.showcase,
        (cfg) => cfg.items?.length > 0 || staticShowcase?.length > 0,
    );

    const productsSection = resolveBrandSection(
        "products",
        defaults.products,
        brand.landing_config?.products,
        () => products?.length > 0,
    );

    return (
        <MainLayout>
            <Head title={brand.name} />

            {/* Hero Section */}
            {hero.enabled && hero.safe && (
                <BrandHeroSection
                    brand={brand}
                    pageData={pageData}
                    relatedServices={relatedServices}
                    getImageUrl={getImageUrl}
                    setLightboxImage={setLightboxImage}
                    config={hero.config}
                />
            )}

            {/* Dynamic Service Solutions Sections */}
            {solutions.enabled && solutions.safe && (
                <BrandServiceSolutionsSection
                    relatedServices={relatedServices}
                    getImageUrl={getImageUrl}
                    brand={brand}
                    config={solutions.config}
                />
            )}

            {/* All Categories Section */}
            {categoriesSection.enabled && categoriesSection.safe && (
                <BrandCategoryListSection
                    categories={categories}
                    brand={brand}
                    getBrandSlug={getBrandSlug}
                    getImageUrl={getImageUrl}
                    config={categoriesSection.config}
                />
            )}

            {/* Latest Products Section */}
            {productsSection.enabled && productsSection.safe && (
                <LatestProductsSection
                    products={products}
                    getImageUrl={getImageUrl}
                    config={productsSection.config}
                />
            )}

            {/* Work Showcase Section */}
            {showcase.enabled && showcase.safe && (
                <WorkShowcaseSection
                    staticShowcase={showcase.config.items || staticShowcase}
                    config={showcase.config}
                />
            )}
            <style>{`
                .hover-opacity:hover {
                    opacity: 0.7;
                }
                .hover-white:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                    transition: all 0.3s ease;
                }
                .hover-dark:hover {
                    background-color: rgba(0, 0, 0, 0.1) !important;
                    border-color: #000000 !important;
                    color: #000000 !important;
                }
                .room-card:hover img {
                    transform: scale(1.1);
                }
                .room-card {
                    overflow: hidden;
                    backface-visibility: hidden;
                }
                .category-card:hover {
                    background-color: #E5E7EB !important;
                    transform: translateY(-5px);
                }
                .product-card-simple:hover img {
                    transform: scale(1.05) !important;
                    transition: transform 0.3s ease !important;
                }
                .room-img-height {
                    height: 500px;
                }
                .room-btn {
                    padding: 10px 25px;
                }
                .latest-product-height {
                    height: 400px;
                }
                @media (max-width: 576px) {
                    .room-img-height {
                        height: 380px;
                    }
                    .room-btn {
                        padding: 8px 15px !important;
                        font-size: 14px;
                        width: 100%;
                        justify-content: center;
                    }
                    .latest-product-height {
                        height: 350px;
                    }
                }
                .swiper-pagination-progressbar-fill {
                    background-color: #ffffff !important;
                }
                .swiper-pagination-lock {
                    display: none !important;
                }
                .btn-ghost-green {
                    background-color: transparent !important;
                    border: 1px solid #ffffff !important;
                    color: #ffffff !important;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                }
                .btn-ghost-green:hover {
                    background-color: #28a745 !important;
                    border-color: #28a745 !important;
                }
                .btn-ghost-green::before,
                .btn-ghost-green::after {
                    display: none !important;
                }
                .scroll-text-ani {
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
             `}</style>

            {/* Lightbox Overlay */}
            {lightboxImage && (
                <div
                    className="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                    style={{
                        zIndex: 9999,
                        backgroundColor: "rgba(0,0,0,0.9)",
                    }}
                    onClick={() => setLightboxImage(null)}
                >
                    <button
                        className="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                        onClick={() => setLightboxImage(null)}
                        aria-label="Close"
                        style={{
                            width: "2rem",
                            height: "2rem",
                            backgroundSize: "contain",
                        }}
                    ></button>
                    <img
                        src={lightboxImage}
                        alt="Award Full View"
                        className="img-fluid"
                        style={{
                            maxHeight: "90vh",
                            maxWidth: "90vw",
                            objectFit: "contain",
                            animation: "zoomIn 0.3s ease-out",
                        }}
                        onClick={(e) => e.stopPropagation()} // Prevent closing when clicking image
                    />
                    <style>{`
                        @keyframes zoomIn {
                            from { transform: scale(0.9); opacity: 0; }
                            to { transform: scale(1); opacity: 1; }
                        }
                    `}</style>
                </div>
            )}
        </MainLayout>
    );
};

export default BrandLanding;
