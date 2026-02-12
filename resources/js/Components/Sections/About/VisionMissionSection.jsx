import React from "react";

export default function VisionMissionSection({
    vision_title = "Our Vision",
    vision_text = "To be the leading national company in providing services, sales, and rentals in the field of ICT (Information Communication Technology) and Education in Indonesia.",
    mission_title = "Our Mission",
    mission_items = [
        {
            text: "To provide the best consultation services to customers.",
        },
        {
            text: "To help resolve issues or enhance customer business processes to be more productive using ICT.",
        },
        {
            text: "To offer a variety of well-designed solution blueprints at economical prices.",
        },
        {
            text: "To provide ease and flexibility in transactions, both directly and through marketplaces.",
        },
        {
            text: "To provide Support & Maintenance (After-Sale) services following the sale of goods.",
        },
    ],
    goal_title = "Our Goal",
    goal_text = "To be a trusted partner in providing the best consultative ICT solutions to enhance productivity and resolve customer business problems economically.",
}) {
    return (
        <div className="vision-mission-area position-relative overflow-hidden space-top space-extra-bottom" id="vision-mission">
            <style>{`
                .vm-card {
                    background: #fff;
                    padding: 40px 30px;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    height: 100%;
                    transition: all 0.4s ease;
                    border: 1px solid rgba(0,0,0,0.02);
                    position: relative;
                    overflow: hidden;
                    z-index: 1;
                }
                .vm-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 40px rgba(0,0,0,0.08);
                    border-color: var(--theme-color, #0D505D);
                }
                .vm-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 100px;
                    height: 100px;
                    background: var(--theme-color, #0D505D);
                    opacity: 0.05;
                    border-radius: 0 0 0 100%;
                    transition: all 0.4s ease;
                    z-index: -1;
                }
                .vm-card:hover::before {
                    width: 100%;
                    height: 100%;
                    border-radius: 0;
                    opacity: 0.03;
                }
                .vm-icon {
                    width: 70px;
                    height: 70px;
                    background: rgba(13, 80, 93, 0.1);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 25px;
                    transition: all 0.4s ease;
                }
                .vm-card:hover .vm-icon {
                    background: var(--theme-color, #0D505D);
                    transform: rotateY(180deg);
                }
                .vm-icon img {
                    width: 35px;
                    transition: all 0.4s ease;
                    filter: brightness(0) saturate(100%) invert(26%) sepia(16%) saturate(1968%) hue-rotate(152deg) brightness(96%) contrast(91%); /* Matches standard theme color approximately */
                }
                .vm-card:hover .vm-icon img {
                    filter: brightness(0) invert(1);
                    transform: rotateY(-180deg);
                }
                .vm-title {
                    font-size: 24px;
                    font-weight: 700;
                    margin-bottom: 20px;
                    color: var(--title-color, #1A1D2D);
                }
                .vm-text {
                    color: var(--body-color, #5E626B);
                    line-height: 1.7;
                    font-size: 16px;
                }
                .vm-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }
                .vm-list li {
                    position: relative;
                    padding-left: 25px;
                    margin-bottom: 15px;
                    color: var(--body-color, #5E626B);
                    line-height: 1.6;
                }
                .vm-list li:last-child {
                    margin-bottom: 0;
                }
                .vm-list li::before {
                    content: '\\f00c';
                    font-family: "Font Awesome 6 Pro";
                    position: absolute;
                    left: 0;
                    top: 2px;
                    color: var(--theme-color, #0D505D);
                    font-weight: 600;
                    font-size: 14px;
                }
            `}</style>
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <div className="title-area text-center mb-50">
                            <span className="sub-title justify-content-center">
                                <span className="shape left"><span className="dots"></span></span>
                                Our Philosophy
                                <span className="shape right"><span className="dots"></span></span>
                            </span>
                            <h2 className="sec-title">Driven by Purpose, Guided by Values</h2>
                        </div>
                    </div>
                </div>

                <div className="row gy-4">
                    {/* Vision */}
                    <div className="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div className="vm-card">
                            <div className="vm-icon">
                                <img src="/assets/img/icon/history_1_2.svg" alt="Vision" />
                            </div>
                            <h3 className="vm-title">{vision_title}</h3>
                            <p className="vm-text">{vision_text}</p>
                        </div>
                    </div>

                    {/* Mission */}
                    <div className="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.2s">
                        <div className="vm-card">
                            <div className="vm-icon">
                                <img src="/assets/img/icon/history_1_1.svg" alt="Mission" />
                            </div>
                            <h3 className="vm-title">{mission_title}</h3>
                            <ul className="vm-list">
                                {mission_items.map((item, index) => (
                                    <li key={index}>
                                        {item.text || item.description}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                    {/* Goal */}
                    <div className="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div className="vm-card">
                            <div className="vm-icon">
                                <img src="/assets/img/icon/history_1_3.svg" alt="Goal" />
                            </div>
                            <h3 className="vm-title">{goal_title}</h3>
                            <p className="vm-text">{goal_text}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
