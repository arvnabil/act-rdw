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
            background_color: "#444A572C", // Dark Slate Blue (Darkened "Use background not pink")
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

    const heroSlides = [
        {
            subtitle: "Business Solution",
            title: "VIDEO CONFERENCING",
            desc: "Explore video conferencing products, including conference cameras, room solutions, webcams, headsets, collaboration tools, and accessories.",
            image: "https://activ.co.id/wp-content/uploads/2023/11/Banner-Utama-2.png",
        },
    ];

    return (
        <MainLayout>
            <Head title={brand.name} />

            {/* Hero Section */}
            <BrandHeroSection
                brand={brand}
                pageData={pageData}
                relatedServices={relatedServices}
                getImageUrl={getImageUrl}
                setLightboxImage={setLightboxImage}
            />

            {/* Dynamic Service Solutions Sections */}
            <BrandServiceSolutionsSection
                relatedServices={relatedServices}
                getImageUrl={getImageUrl}
                brand={brand}
            />

            {/* All Categories Section */}
            <BrandCategoryListSection
                categories={categories}
                brand={brand}
                getBrandSlug={getBrandSlug}
                getImageUrl={getImageUrl}
            />

            {/* Latest Products Section */}
            <LatestProductsSection
                products={products}
                getImageUrl={getImageUrl}
            />

            {/* Work Showcase Section */}
            <WorkShowcaseSection staticShowcase={staticShowcase} />
            <style>{`
                .hover-opacity:hover {
                    opacity: 0.7;
                }
                .hover-white:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
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
                    color: #ffffff !important;
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
