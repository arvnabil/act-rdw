import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';
import SectionTitle from '@/Components/Common/SectionTitle';
import TestimonialSection from '@/Components/Sections/TestimonialSection';
import ContactSection from '@/Components/Sections/ContactSection';

export default function About() {
    return (
        <MainLayout>
            <Head title="About Us" />

            <Breadcrumb
                title="About Us"
                items={[
                    { label: 'Home', link: '/' },
                    { label: 'About Us' }
                ]}
            />

            {/* About Area */}
            <div className="about-area position-relative overflow-hidden space" id="about-sec">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-6 col-xxl-7">
                            <div className="img-box2">
                                <div className="img1 reveal">
                                    <img src="/assets/img/normal/about_4_1.jpg" alt="About" />
                                </div>
                                <div className="img2 wow fadeInUp">
                                    <img src="/assets/img/normal/about_4_2.jpg" alt="About" />
                                    <a href="https://www.youtube.com/watch?v=hIIQbkkKnno" className="play-btn popup-video">
                                        <i className="fa-sharp fa-solid fa-play"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6 col-xxl-5">
                            <div>
                                <SectionTitle
                                    subTitle="About Us"
                                    title="Bridging Technology and Education for a Better Future."
                                    align="title-area about4-titlebox mb-20"
                                    mb="mb-20"
                                />
                                <p className="sec-text mb-30 wow fadeInUp" data-wow-delay=".2s">
                                    ACTiV (PT Alfa Cipta Teknologi Virtual) is a dynamic company specializing in the sales and rental of software, hardware, and supporting accessories, with a primary focus on Information Communication Technology (ICT) and Education solutions. Backed by a team with over 6 years of experience and official partnerships with multinational ICT brands, we are dedicated to delivering the best comprehensive technology solutions to our clients.
                                </p>
                                <div className="about-item-wrap">
                                    <div className="about-item wow fadeInUp" data-wow-delay=".2s">
                                        <div className="about-item_img">
                                            <img src="/assets/img/icon/guide.svg" alt="" />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">Expert Team</h5>
                                            <p className="about-item_text">
                                                There are many variations of passages of available but the majority.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="about-item wow fadeInUp" data-wow-delay=".3s">
                                        <div className="about-item_img">
                                            <img src="/assets/img/icon/shield.svg" alt="" />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">Certified Brand Partners</h5>
                                            <p className="about-item_text">
                                                Official partnerships ensuring product authenticity and certified technical support.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="about-item wow fadeInUp" data-wow-delay=".4s">
                                        <div className="about-item_img">
                                            <img src="/assets/img/icon/headphone.svg" alt="" />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">24/7 Customer Support</h5>
                                            <p className="about-item_text">
                                                A support system operating non-stop through various channels (phone, chat, email, etc.) to provide instant or timely responses and resolutions.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div className="mt-45 wow fadeInUp" data-wow-delay=".5s">
                                    <Link href="/contact" className="th-btn style12 btn-2 th-radius th-icon">
                                        Contact Us <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* History Area (Vision, Mission, Goal) */}
            <div className="history-sec1 overflow-hidden space-bottom" id="story-sec">
                <div className="container">
                    <div className="row gy-4 justify-content-center">
                        <div className="col-xl-4 col-md-6">
                            <div className="story-box inner-style">
                                <span className="story-box_icon">
                                    <img src="/assets/img/icon/history_1_2.svg" alt="" />
                                </span>
                                <h3 className="box-title">Our Vision</h3>
                                <div className="about-item style-16">
                                    <div className="about-content">
                                        <div className="about-featured-box">
                                            <div className="about-feature">
                                                <ul>
                                                    <li className="wow fadeInUp" data-wow-delay=".1s">
                                                        <p className="story-box_text mb-0">
                                                            To be the leading national company in providing services, sales, and rentals in the field of ICT (Information Communication Technology) and Education in Indonesia.
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-4 col-md-6">
                            <div className="story-box inner-style">
                                <span className="story-box_icon">
                                    <img src="/assets/img/icon/history_1_1.svg" alt="" />
                                </span>
                                <h3 className="box-title">Our Mission</h3>
                                <div className="about-item style-16">
                                    <div className="about-content">
                                        <div className="about-featured-box">
                                            <div className="about-feature">
                                                <ul>
                                                    <li className="wow fadeInUp" data-wow-delay=".1s">
                                                        <p className="story-box_text mb-0">
                                                            To provide the best consultation services to customers to help resolve issues or enhance customer business processes to be more productive using ICT (Information Communication Technology).
                                                        </p>
                                                    </li>
                                                    <li className="wow fadeInUp" data-wow-delay=".2s">
                                                        <p className="story-box_text mb-0">
                                                            To offer a variety of well-designed solution blueprints at economical prices.
                                                        </p>
                                                    </li>
                                                    <li className="wow fadeInUp" data-wow-delay=".3s">
                                                        <p className="story-box_text mb-0">
                                                            To provide ease and flexibility in transactions, both directly and through marketplaces.
                                                        </p>
                                                    </li>
                                                    <li className="wow fadeInUp" data-wow-delay=".3s">
                                                        <p className="story-box_text mb-0">
                                                            To provide Support & Maintenance (After-Sale) services following the sale of goods.
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-4 col-md-6">
                            <div className="story-box inner-style">
                                <span className="story-box_icon">
                                    <img src="/assets/img/icon/history_1_3.svg" alt="" />
                                </span>
                                <h3 className="box-title">Our Goal</h3>
                                <div className="about-item style-16">
                                    <div className="about-content">
                                        <div className="about-featured-box">
                                            <div className="about-feature">
                                                <ul>
                                                    <li className="wow fadeInUp" data-wow-delay=".1s">
                                                        <p className="story-box_text mb-0">
                                                            To be a trusted partner in providing the best consultative ICT solutions to enhance productivity and resolve customer business problems economically.
                                                        </p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Testimonial Area */}
            <TestimonialSection />

            {/* Contact & Map Area */}
            <ContactSection />



        </MainLayout>
    );
}
