import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function WhyChooseUsSection({
    title,
    subtitle,
    description,
    features,
    images,
    video_url,
    show_button,
    button_text,
    button_url,
    builderMode,
}) {
    // Defaults
    const t = title || "Memberdayakan Masa Depan Anda dengan Teknologi Terbukti.";
    // Disable wow animations in builder mode to ensure visibility
    const wow = (cls) => (builderMode ? "" : cls);
    const st = subtitle || "Mengapa Memilih ACTiV";
    const desc =
        description ||
        "Kami berdedikasi untuk menyediakan solusi TIK dan Pendidikan komprehensif yang tidak hanya canggih tetapi juga andal. Dengan menggabungkan keahlian teknis dengan dukungan global, kami memastikan setiap investasi teknologi yang Anda buat memberikan nilai nyata yang berkelanjutan.";
    const vidUrl = video_url || "https://www.youtube.com/watch?v=hIIQbkkKnno";

    // Normalize show_button
    const shouldShowButton =
        show_button === true ||
        show_button === "true" ||
        show_button === 1 ||
        show_button === "1";

    const btnText = button_text || "Pelajari Selengkapnya";
    const btnUrl = button_url || "/about";

    const imgs =
        images && images.length > 0
            ? images.map((i) => i.url || i)
            : [
                "/assets/img/normal/about_4_1.jpg",
                "/assets/img/normal/about_4_2.jpg",
            ];

    const feats =
        features && features.length > 0
            ? features
            : [
                {
                    title: "Tim Ahli",
                    text: "Didukung oleh para profesional dengan pengalaman lebih dari 6 tahun di infrastruktur TIK.",
                    icon: "/assets/img/icon/shield.svg",
                },
                {
                    title: "Mitra Merek Bersertifikat",
                    text: "Kemitraan resmi yang memastikan keaslian produk dan dukungan teknis bersertifikat.",
                    icon: "/assets/img/icon/shield.svg",
                },
            ];

    return (
        <div
            className="bg-smoke position-relative overflow-hidden space"
            id="why-sec"
        >
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="ab5-wrapp mt-40">
                            <SectionTitle
                                align="title-area"
                                subTitle={st}
                                title={t}
                                mb=""
                            />
                            <p
                                className={`sec-text mb-30 ${wow("wow fadeInUp")}`}
                                data-wow-delay=".2s"
                            >
                                {desc}
                            </p>
                            <div className="about-item-wrap">
                                {feats.map((feat, i) => (
                                    <div
                                        className={`about-item ab5-item ${wow("wow fadeInUp")}`}
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
                                    className={`mt-35 mb-2 ${wow("wow fadeInUp")}`}
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
                    <div className="col-xl-6">
                        <div className="img-box4 ab5-imgbox space-bottom text-end">
                            <div className="img1 reveal">
                                <img src={imgs[0]} alt="About" />
                            </div>
                            <div className="img2">
                                <img src={imgs[1]} alt="About" />
                                <a
                                    href={vidUrl}
                                    className="play-btn popup-video"
                                >
                                    <i className="fa-sharp fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
