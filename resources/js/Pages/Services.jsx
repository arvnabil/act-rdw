import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';
import SectionTitle from '@/Components/Common/SectionTitle';
import ClientSection from '@/Components/Sections/ClientSection';
import SolutionSection from '@/Components/Sections/SolutionSection';

export default function Services() {
    return (
        <MainLayout>
            <Head title="Our Services" />

            <Breadcrumb
                title="Services"
                items={[
                    { label: 'Home', link: '/' },
                    { label: 'Services' }
                ]}
            />

            {/* Service Area */}
            <section className="position-relative bg-top-center overflow-hidden space-top" id="service-sec">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            <SectionTitle
                                subTitle="What We’re Offering"
                                title="Dealing in all professional IT services"
                                align="title-area service-title-box text-center"
                            />
                            <div className="text-center">
                                <p className="sec-text mb-50 wow fadeInUp" data-wow-delay=".3s">
                                    IT solutions refer to a broad range of services and technologies designed to address <br />
                                    specific business needs, streamline operations, and drive growth.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div className="service-area">
                        <div className="row gy-30 justify-content-center">
                            {[
                                { img: 'sv-1.jpg', title: 'IT Strategy & Planning', desc: 'Assessing current IT infrastructure and aligning it with business goals. Developing a roadmap.' },
                                { img: 'sv-2.jpg', title: 'Web Development', desc: 'Building responsive and high-performance websites to establish your online presence.' },
                                { img: 'sv-3.jpg', title: 'Cloud Consulting', desc: 'Expert guidance on cloud migration, optimization, and management for better scalability.' },
                                { img: 'sv-4.jpg', title: 'Machine Learning', desc: 'Leveraging AI and machine learning to unlock operational efficiency and data insights.' },
                                { img: 'sv-5.jpg', title: 'House Renovation', desc: 'Smart home integrations and technology upgrades for modern living spaces.' },
                                { img: 'sv-6.jpg', title: 'Material Supply', desc: 'Supply of high-quality IT hardware and accessories for your infrastructure needs.' },
                                { img: 'sv-7.jpg', title: 'General Contracting', desc: 'End-to-end project management for IT infrastructure setup and renovation.' },
                                { img: 'sv-8.jpg', title: 'Architecture Design', desc: 'Designing robust and scalable network and system architectures.' },
                            ].map((service, index) => (
                                <div className="col-xl-3 col-md-6" key={index}>
                                    <div className="service-box service-style-1">
                                        <div className="service-img">
                                            <Link href="/services">
                                                <img src={`/assets/img/service/${service.img}`} alt={service.title} />
                                            </Link>
                                        </div>
                                        <div className="service-content">
                                            <h3 className="box-title">
                                                <Link href="/services">{service.title}</Link>
                                            </h3>
                                            <p className="service-box_text">{service.desc}</p>
                                            <Link className="th-btn style4" href="/services">
                                                Read More <i className="fa-light fa-arrow-right-long"></i>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* CTA Area */}
            <div className="position-relative overflow-hidden space">
                <div className="cta-sec6 theme-bg position-relative overflow-hidden">
                    <div className="container">
                        <div className="row">
                            <div className="col-md-6 col-lg-6">
                                <div className="cta-area6 text-center text-md-start space position-relative">
                                    <div className="title-area mb-40">
                                        <h2 className="sec-title text-white pe-xl-5 me-xl-4 mt-n3 text-anime-style-2">
                                            <span className="discount-text">Grab up to 35% off</span>Have any project to work with us
                                        </h2>
                                        <p className="text-white wow fadeInUp" data-wow-delay=".3s">
                                            Limited time offer, don't miss the opportunity
                                        </p>
                                    </div>
                                    <div className="btn-group wow fadeInUp" data-wow-delay=".4s">
                                        <Link href="/contact" className="th-btn style5 th-radius th-icon">
                                            Contact With Us <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="shape-mockup" data-bottom="0%" data-right="0">
                        <img src="/assets/img/normal/cta-img-6.jpg" alt="" />
                    </div>
                </div>
            </div>

            <ClientSection />

            <SolutionSection />

        </MainLayout>
    );
}
