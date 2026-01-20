import React from "react";

export default function VisionMissionSection({
    vision_title = "Our Vision",
    vision_text = "To be the leading national company in providing services, sales, and rentals in the field of ICT (Information Communication Technology) and Education in Indonesia.",
    mission_title = "Our Mission",
    mission_items = [
        {
            text: "To provide the best consultation services to customers to help resolve issues or enhance customer business processes to be more productive using ICT (Information Communication Technology).",
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
        <div
            className="history-sec1 overflow-hidden space-bottom"
            id="story-sec"
        >
            <div className="container">
                <div className="row gy-4 justify-content-center">
                    {/* Vision */}
                    <div className="col-xl-4 col-md-6">
                        <div className="story-box inner-style">
                            <span className="story-box_icon">
                                <img
                                    src="/assets/img/icon/history_1_2.svg"
                                    alt=""
                                />
                            </span>
                            <h3 className="box-title">{vision_title}</h3>
                            <div className="about-item style-16">
                                <div className="about-content">
                                    <div className="about-featured-box">
                                        <div className="about-feature">
                                            <ul>
                                                <li
                                                    className="wow fadeInUp"
                                                    data-wow-delay=".1s"
                                                >
                                                    <p className="story-box_text mb-0">
                                                        {vision_text}
                                                    </p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Mission */}
                    <div className="col-xl-4 col-md-6">
                        <div className="story-box inner-style">
                            <span className="story-box_icon">
                                <img
                                    src="/assets/img/icon/history_1_1.svg"
                                    alt=""
                                />
                            </span>
                            <h3 className="box-title">{mission_title}</h3>
                            <div className="about-item style-16">
                                <div className="about-content">
                                    <div className="about-featured-box">
                                        <div className="about-feature">
                                            <ul>
                                                {mission_items.map(
                                                    (item, index) => (
                                                        <li
                                                            key={index}
                                                            className="wow fadeInUp"
                                                            data-wow-delay={`.${index + 1}s`}
                                                        >
                                                            <p className="story-box_text mb-0">
                                                                {item.text ||
                                                                    item.description}
                                                            </p>
                                                        </li>
                                                    ),
                                                )}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Goal */}
                    <div className="col-xl-4 col-md-6">
                        <div className="story-box inner-style">
                            <span className="story-box_icon">
                                <img
                                    src="/assets/img/icon/history_1_3.svg"
                                    alt=""
                                />
                            </span>
                            <h3 className="box-title">{goal_title}</h3>
                            <div className="about-item style-16">
                                <div className="about-content">
                                    <div className="about-featured-box">
                                        <div className="about-feature">
                                            <ul>
                                                <li
                                                    className="wow fadeInUp"
                                                    data-wow-delay=".1s"
                                                >
                                                    <p className="story-box_text mb-0">
                                                        {goal_text}
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
    );
}
