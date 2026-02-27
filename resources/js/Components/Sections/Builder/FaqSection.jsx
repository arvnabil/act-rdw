import React, { useState } from "react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function FaqSection({
    title = "Frequently Asked Questions",
    subtitle = "FAQ",
    items = [],
}) {
    const [activeIndex, setActiveIndex] = useState(0);

    const toggleAccordion = (index) => {
        setActiveIndex(activeIndex === index ? null : index);
    };

    // Default FAQ data if none provided
    const faqItems = items.length > 0 ? items : [
        {
            question: "Apa itu ACTiV (PT Alfa Cipta Teknologi Virtual)?",
            answer: "ACTiV adalah penyedia solusi TIK dan Pendidikan terkemuka di Indonesia, yang mengkhususkan diri dalam penjualan dan penyewaan perangkat keras, perangkat lunak, serta aksesori teknologi. Kami telah berpengalaman lebih dari 6 tahun dalam industri ini."
        },
        {
            question: "Layanan apa saja yang disediakan oleh ACTiV?",
            answer: "Kami menyediakan berbagai layanan termasuk integrasi sistem, penyewaan perangkat IT untuk event atau perkantoran, implementasi smart classroom, solusi video conference, serta penyediaan perangkat infrastruktur server dan storage."
        },
        {
            question: "Di mana lokasi kantor operasional ACTiV?",
            answer: "Kantor pusat kami berlokasi di Infinity Office, Belleza BSA 1st Floor Unit 106, Jakarta Selatan. Kami juga memiliki kantor perwakilan di Ruko Golden Boulevard, BSD City, Tangerang Selatan."
        },
        {
            question: "Apakah ACTiV merupakan partner resmi dari brand global?",
            answer: "Ya, kami adalah mitra resmi bersertifikat dari berbagai brand teknologi global terkemuka seperti Logitech, Maxhub, Microsoft, Cisco, dan banyak lagi, sehingga kami menjamin keaslian produk dan dukungan teknis resmi."
        },
        {
            question: "Bagaimana cara mendapatkan penawaran (quotation) proyek?",
            answer: "Anda dapat menghubungi tim penjualan kami melalui WhatsApp di nomor resmi yang tertera di website, atau mengisi formulir kontak. Tim kami akan segera merespons untuk mendiskusikan kebutuhan spesifik bisnis Anda."
        }
    ];

    return (
        <section className="faq-area space-bottom space" id="faq-sec">
            <style>{`
                .faq-accordion {
                    max-width: 900px;
                    margin: 0 auto;
                }
                .faq-item {
                    background: #fff;
                    border: 1px solid #eee;
                    border-radius: 12px;
                    margin-bottom: 15px;
                    overflow: hidden;
                    transition: all 0.3s ease;
                }
                .faq-item.active {
                    border-color: var(--theme-color);
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                }
                .faq-header {
                    padding: 20px 25px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    cursor: pointer;
                    user-select: none;
                }
                .faq-question {
                    font-size: 18px;
                    font-weight: 700;
                    color: var(--title-color);
                    margin: 0;
                    flex: 1;
                    padding-right: 20px;
                }
                .faq-icon {
                    width: 30px;
                    height: 30px;
                    background: #f5f7f8;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--theme-color);
                    transition: all 0.3s ease;
                    flex-shrink: 0;
                }
                .faq-item.active .faq-icon {
                    background: var(--theme-color);
                    color: #fff;
                    transform: rotate(180deg);
                }
                .faq-body {
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s ease-in-out;
                }
                .faq-item.active .faq-body {
                    max-height: 500px;
                }
                .faq-content {
                    padding: 0 25px 25px 25px;
                    color: var(--body-color);
                    line-height: 1.7;
                    font-size: 16px;
                }
                @media (max-width: 768px) {
                    .faq-question {
                        font-size: 16px;
                    }
                }
            `}</style>
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-8 text-center">
                        <SectionTitle
                            subtitle={subtitle}
                            title={title}
                            centerAlign={true}
                        />
                    </div>
                </div>
                <div className="faq-accordion">
                    {faqItems.map((item, index) => (
                        <div 
                            key={index} 
                            className={`faq-item ${activeIndex === index ? "active" : ""}`}
                            onClick={() => toggleAccordion(index)}
                        >
                            <div className="faq-header">
                                <h3 className="faq-question">{item.question}</h3>
                                <div className="faq-icon">
                                    <i className={`fas fa-chevron-${activeIndex === index ? "up" : "down"}`}></i>
                                </div>
                            </div>
                            <div className="faq-body">
                                <div className="faq-content">
                                    {item.answer}
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
