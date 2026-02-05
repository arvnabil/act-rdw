import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function AboutSection({
    title,
    subtitle,
    description,
    features,
    images,
    button_text,
    button_url,
    show_button = true, // Default to true if not provided
}) {
    // Defaults
    const t = title || "Menjembatani Teknologi dan Pendidikan untuk Masa Depan yang Lebih Baik.";
    const st = subtitle || "Siapa Kami";
    const desc =
        description ||
        "ACTiV (PT Alfa Cipta Teknologi Virtual) adalah perusahaan dinamis yang berspesialisasi dalam penjualan dan penyewaan perangkat lunak, perangkat keras, dan aksesori pendukung, dengan fokus utama pada solusi Teknologi Informasi Komunikasi (TIK) dan Pendidikan. Didukung oleh tim dengan pengalaman lebih dari 6 tahun dan kemitraan resmi dengan merek TIK multinasional, kami berdedikasi untuk memberikan solusi teknologi komprehensif terbaik kepada klien kami.";
    const btnText = button_text || "Pelajari Selengkapnya";
    const btnUrl = button_url || "/about";

    // Normalize show_button to boolean (handle "false" string from some inputs)
    const shouldShowButton =
        show_button === true ||
        show_button === "true" ||
        show_button === 1 ||
        show_button === "1";

    const imgs =
        images && images.length > 0
            ? images.map((i) => i.url || i)
            : [
                "/assets/img/normal/about_7_1.jpg",
                "/assets/img/normal/about_7_2.jpg",
                "/assets/img/normal/about_7_3.jpg",
            ];

    const feats =
        features && features.length > 0
            ? features
            : [
                {
                    title: "Penyediaan Produk TIK & Pendidikan",
                    text: "Penyedia resmi perangkat keras dan lunak yang disesuaikan untuk pendidikan dan infrastruktur TIK.",
                    icon: "/assets/img/icon/shield.svg",
                },
                {
                    title: "Layanan Solusi & Pengembangan Kustom",
                    text: "Solusi teknis ahli dan pengembangan perangkat lunak kustom yang disesuaikan dengan kebutuhan spesifik Anda.",
                    icon: "/assets/img/icon/shield.svg",
                },
            ];

    return (
        <div
            className="about-area position-relative overflow-hidden space"
            id="about-sec"
        >
            <div className="container">
                <div className="row gy-40">
                    <div className="col-xl-6 col-lg-6">
                        <SectionTitle
                            subTitle={st}
                            title={t}
                            align="title-area mb-20"
                            mb="mb-20"
                        />
                        <p
                            className="sec-text mb-60 wow fadeInUp"
                            data-wow-delay=".2s"
                        >
                            {desc}
                        </p>
                        <div className="img-box8">
                            <div className="row gy-4">
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img1 reveal mb-0">
                                        <img src={imgs[0]} alt="About" />
                                    </div>
                                </div>
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img2 reveal mb-0">
                                        <img src={imgs[1]} alt="About" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6">
                        <div className="img-box8 ms-xl-5">
                            <div className="img3 reveal">
                                <img src={imgs[2]} alt="About" />
                            </div>
                            <div className="about-item-wrap">
                                {feats.map((feat, i) => (
                                    <div
                                        className="about-item wow fadeInUp"
                                        data-wow-delay={`.${3 + i}s`}
                                        key={i}
                                    >
                                        <div className="about-item_img d-flex justify-content-center align-items-center">
                                            <img src={feat.icon} alt="" />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">
                                                {feat.title}
                                            </h5>
                                            <p className="about-item_text">
                                                {feat.text}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {shouldShowButton && (
                                <div
                                    className="mt-35 wow fadeInUp"
                                    data-wow-delay=".5s"
                                >
                                    <Link
                                        href={btnUrl}
                                        className="th-btn th-radius th-icon"
                                    >
                                        {btnText}{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div >
    );
}
