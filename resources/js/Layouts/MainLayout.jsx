import React, { useEffect, useState } from "react";
import { Link, usePage, router } from "@inertiajs/react";
import { useTemplateInit } from "@/hooks/useTemplateInit";
import Toast from "@/Components/Common/Toast";
import { getWhatsAppLink } from "@/Utils/whatsapp";
import { getImageUrl } from "@/Utils/image";

export default function MainLayout({ children }) {
    useTemplateInit();
    const { auth, flash, menus, settings } = usePage().props;
    console.log("MainLayout menus:", menus);
    const [toast, setToast] = useState(null);
    const [isScrolled, setIsScrolled] = useState(false);
    const [mobileLangOpen, setMobileLangOpen] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            setIsScrolled(window.scrollY > 50);
        };
        window.addEventListener("scroll", handleScroll);

        // Safety: Force unlock scroll on mount/URL change
        document.body.style.overflow = "auto";
        document.documentElement.style.overflow = "auto";
        
        return () => window.removeEventListener("scroll", handleScroll);
    }, [usePage().url]);

    useEffect(() => {
        // 1. Flash messages handling
        if (flash?.success) {
            setToast({ message: flash.success, type: "success" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: "danger" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const [selectedLang, setSelectedLang] = useState({
        code: 'id',
        label: 'Indonesian',
        flag: 'https://cdn.gtranslate.net/flags/svg/id.svg'
    });

    useEffect(() => {
        // 2. GTranslate Initialization logic
        const script = document.createElement("script");
        script.src = "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2";
        script.async = true;
        document.body.appendChild(script);

        window.googleTranslateElementInit2 = () => {
            new window.google.translate.TranslateElement({
                pageLanguage: 'id',
                autoDisplay: false
            }, 'google_translate_element2');
        };

        window.doGTranslate = (lang_pair) => {
            if (lang_pair.value) lang_pair = lang_pair.value;
            if (lang_pair == '') return;
            var lang = lang_pair.split('|')[1];
            var teCombo = document.querySelector('.goog-te-combo');
            if (teCombo) {
                teCombo.value = lang;
                teCombo.dispatchEvent(new Event('change'));

                // Update UI state
                const langMap = {
                    'en': { label: 'English', flag: 'https://cdn.gtranslate.net/flags/svg/en.svg' },
                    'id': { label: 'Indonesian', flag: 'https://cdn.gtranslate.net/flags/svg/id.svg' },
                    'ar': { label: 'Arabic', flag: 'https://cdn.gtranslate.net/flags/svg/countries/sa.svg' },
                    'zh-CN': { label: 'Chinese', flag: 'https://cdn.gtranslate.net/flags/svg/zh-CN.svg' },
                    'nl': { label: 'Dutch', flag: 'https://cdn.gtranslate.net/flags/svg/nl.svg' },
                    'fr': { label: 'French', flag: 'https://cdn.gtranslate.net/flags/svg/fr.svg' },
                    'de': { label: 'German', flag: 'https://cdn.gtranslate.net/flags/svg/de.svg' },
                    'hi': { label: 'Hindi', flag: 'https://cdn.gtranslate.net/flags/svg/hi.svg' },
                    'it': { label: 'Italian', flag: 'https://cdn.gtranslate.net/flags/svg/it.svg' },
                    'ja': { label: 'Japanese', flag: 'https://cdn.gtranslate.net/flags/svg/ja.svg' },
                    'ko': { label: 'Korean', flag: 'https://cdn.gtranslate.net/flags/svg/ko.svg' },
                    'pt': { label: 'Portuguese', flag: 'https://cdn.gtranslate.net/flags/svg/pt.svg' },
                    'ru': { label: 'Russian', flag: 'https://cdn.gtranslate.net/flags/svg/ru.svg' },
                    'es': { label: 'Spanish', flag: 'https://cdn.gtranslate.net/flags/svg/es.svg' },
                    'th': { label: 'Thai', flag: 'https://cdn.gtranslate.net/flags/svg/th.svg' }
                };
                if (langMap[lang]) {
                    setSelectedLang({ code: lang, ...langMap[lang] });
                }
            }
        };
    }, []);

    const closeMobileMenu = (e) => {
        // If the click is on the expander icon (+) or a '#' link, don't close the menu
        if (e && (e.target.classList.contains("th-mean-expand") || e.currentTarget.getAttribute("href") === "#")) {
            return;
        }

        const menuWrapper = document.querySelector(".th-menu-wrapper");
        if (menuWrapper) {
            menuWrapper.classList.remove("th-body-visible");
        }
    };

    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    };

    const isExternalUrl = (url) => {
        if (!url) return false;
        return url.startsWith("http://") || url.startsWith("https://");
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        const query = formData.get('q');

        if (query) {
            // Force close all search-related UI
            document.querySelectorAll(".popup-search-box").forEach(el => el.classList.remove("show"));
            document.querySelectorAll(".th-menu-wrapper").forEach(el => el.classList.remove("th-body-visible"));
            document.body.classList.remove("th-body-visible");

            router.get('/search', { q: query });
        }
    };

    // Helper to render menu items recursively
    const renderMenuItems = (items) => {
        return items.map((item, index) => {
            const hasChildren = item.children && item.children.length > 0;
            const liClass = hasChildren ? "menu-item-has-children" : "";

            const isExternal = isExternalUrl(item.url) || item.target === "_blank";

            return (
                <li key={index} className={liClass}>
                    {isExternal ? (
                        <a
                            href={item.url || "#"}
                            target={item.target || "_self"}
                            onClick={closeMobileMenu}
                            rel={item.target === "_blank" ? "noopener" : ""}
                        >
                            {item.title}
                        </a>
                    ) : (
                        <Link
                            href={item.url || "#"}
                            target={item.target || "_self"}
                            onClick={closeMobileMenu}
                        >
                            {item.title}
                        </Link>
                    )}

                    {hasChildren && (
                        <ul className="sub-menu">
                            {renderMenuItems(item.children)}
                        </ul>
                    )}
                </li>
            );
        });
    };

    // Helper for footer links (simple list)
    const renderFooterLinks = (items) => {
        return items.map((item, index) => {
            const isExternal = isExternalUrl(item.url) || item.target === "_blank";
            return (
                <li key={index}>
                    {isExternal ? (
                        <a
                            href={item.url || "#"}
                            style={{ color: "#fff" }}
                            target={item.target || "_self"}
                            rel={item.target === "_blank" ? "noopener" : ""}
                        >
                            {item.title}
                        </a>
                    ) : (
                        <Link
                            href={item.url || "#"}
                            style={{ color: "#fff" }}
                            target={item.target || "_self"}
                        >
                            {item.title}
                        </Link>
                    )}
                </li>
            );
        });
    };

    return (
        <>
            <style>
                {`
                /* GTranslate Switcher Styling */
                #google_translate_element2 { display: none !important; }
                .skiptranslate { display: none !important; }
                body { top: 0px !important; }
                .goog-te-banner-frame { display: none !important; }
                iframe.goog-te-banner-frame { display: none !important; }
                body > .skiptranslate { display: none !important; height: 0 !important; overflow: hidden !important; }
                iframe[name="votingFrame"] { display: none !important; }

                .gt_switcher_wrapper {
                    display: inline-block;
                    font-family: Arial, sans-serif;
                    z-index: 999;
                    vertical-align: middle;
                }

                /* When page is scrolled, make it float at the top */
                .gt_switcher_wrapper.gt_sticky {
                    position: fixed;
                    top: 0;
                    right: 8%;
                    z-index: 999999;
                }

                .gt_switcher {
                    background: #fff;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                    padding: 4px 8px;
                    cursor: pointer;
                    display: inline-block;
                    position: relative;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    line-height: 1.2;
                }

                .gt_switcher .gt_selected {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 13px;
                    color: #333;
                    white-space: nowrap;
                }

                .gt_switcher .gt_option {
                    display: none;
                    position: absolute;
                    top: 100%;
                    left: 0;
                    width: 140px;
                    background: #fff;
                    border: 1px solid #ccc;
                    border-radius: 0 0 4px 4px;
                    overflow-y: auto;
                    max-height: 200px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }

                .gt_switcher:hover .gt_option {
                    display: block;
                }

                .gt_switcher .gt_option a {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 8px 12px;
                    text-decoration: none;
                    color: #444;
                    font-size: 13px;
                    transition: background 0.2s;
                    text-align: left;
                }

                .gt_switcher .gt_option a:hover {
                    background: #f5f5f5;
                }

                .gt_switcher img {
                    width: 18px;
                    height: 12px;
                    object-fit: cover;
                }

                /* Hide Google Translate top bar & tooltips */
                #goog-gt-tt, .goog-te-balloon-frame { display: none !important; }
                .goog-text-highlight { background-color: transparent !important; box-shadow: none !important; }

                /* Hide reCAPTCHA v3 badge - it blocks scroll-to-top button clicks */
                .grecaptcha-badge { visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; }

                /* Fix Qontak webchat position and z-index */
                #qontak-webchat-widget {
                    bottom: 55px !important;
                    right: 15px !important;
                }

                /* Ensure scroll-top button stays below Qontak but above other content */
                .scroll-top { pointer-events: auto !important; z-index: 9999999 !important; }

                @media (max-width: 991px) {
                    .header-logo {
                        margin-left: 10px !important;
                    }
                }
            `}
            </style>

            <div id="google_translate_element2"></div>
            <div className="slider-drag-cursor d-flex align-items-center justify-content-between">
                <span className="drag-icon-left">
                    <img src="/assets/img/icon/drag-arrow-left.svg" alt="" />
                </span>
                DRAG
                <span className="drag-icon-right">
                    <img src="/assets/img/icon/drag-arrow-right.svg" alt="" />
                </span>
            </div>

            <div className="preloader">
                <button className="th-btn preloaderCls">
                    Batalkan Preloader
                </button>
                <div className="preloader-inner">
                    <img src={getImageUrl(settings?.site_logo_preloader, "/assets/img/logo-icon3.svg")} alt="ACTiV Logo Icon" />
                </div>
            </div>

            <div className="popup-search-box">
                <button className="searchClose">
                    <i className="fal fa-times"></i>
                </button>
                <form action="/search" method="GET" onSubmit={handleSearchSubmit}>
                    <input
                        type="text"
                        name="q"
                        placeholder="Apa yang Anda cari?"
                        autoComplete="off"
                    />
                    <button type="submit">
                        <i className="fal fa-search"></i>
                    </button>
                </form>
            </div>

            <div className="th-menu-wrapper onepage-nav">
                <div className="th-menu-area text-center">
                    <button className="th-menu-toggle">
                        <i className="fal fa-times"></i>
                    </button>
                    <div className="mobile-logo" style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', padding: '40px 10px' }}>
                        <Link href="/" style={{ display: 'inline-block' }}>
                            <img 
                                src={getImageUrl(settings?.site_logo_icon, "/assets/img/logo2.svg")} 
                                alt="ACTiV" 
                                style={{ maxHeight: '50px', width: 'auto', display: 'block', margin: '0 auto' }}
                            />
                        </Link>
                    </div>
                    <div className="th-mobile-menu allow-natural-scroll">
                        <div
                            className="mobile-search-box position-relative mb-4"
                            style={{ padding: "0 20px" }}
                        >
                            <form action="/search" method="GET" onSubmit={handleSearchSubmit}>
                                <input
                                    type="text"
                                    name="q"
                                    placeholder="Cari..."
                                    className="form-control"
                                    autoComplete="off"
                                    style={{
                                        height: "50px",
                                        paddingRight: "50px",
                                        borderRadius: "5px",
                                    }}
                                />
                                <button
                                    type="submit"
                                    style={{
                                        position: "absolute",
                                        right: "35px",
                                        top: "50%",
                                        transform: "translateY(-50%)",
                                        border: "none",
                                        background: "none",
                                        color: "var(--theme-color)",
                                    }}
                                >
                                    <i className="fal fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <ul>
                            {menus?.primary ? (
                                renderMenuItems(menus.primary)
                            ) : (
                                // Fallback static menu if dynamic fails (or empty)
                                <>
                                    <li>
                                        <Link href="/">Beranda</Link>
                                    </li>
                                </>
                            )}
                        </ul>
                        {/* Mobile Header Info */}
                        <div className="mobile-header-info mt-30 text-center">
                            <div className="header-links justify-content-center">
                                <ul>
                                    {menus?.top_header?.map((item, index) => (
                                        <li key={index}>
                                            <Link 
                                                href={item.url} 
                                                target={item.target || "_self"}
                                                rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                                                onClick={closeMobileMenu}
                                            >
                                                {item.title}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            {settings?.header_button_visible !== '0' && (() => {
                                const btnUrl = settings?.header_button_url || "whatsapp";
                                const isWa = btnUrl.startsWith('whatsapp');

                                if (isWa) {
                                    // Extract static params if any (e.g. whatsapp?utm_source=header)
                                    const staticParams = {};
                                    if (btnUrl.includes('?')) {
                                        const search = btnUrl.split('?')[1];
                                        new URLSearchParams(search).forEach((v, k) => { staticParams[k] = v; });
                                    }

                                    return (
                                        <div className="text-center mt-3">
                                            <a
                                                href={getWhatsAppLink(settings?.whatsapp_number, {
                                                    cta_position: 'mobile_menu',
                                                    cta_label: 'Header Mobile',
                                                    ...staticParams
                                                })}
                                                target="_blank"
                                                rel="noopener"
                                                className="th-btn th-radius th-icon"
                                                style={{ whiteSpace: 'nowrap' }}
                                            >
                                                {settings?.header_button_text || 'Hubungi Kami'}{" "}
                                                <i className="fa-light fa-arrow-right-long"></i>
                                            </a>
                                        </div>
                                    );
                                }

                                return (
                                    <div className="text-center mt-3">
                                        <Link
                                            href={btnUrl || "/contact"}
                                            className="th-btn th-radius th-icon"
                                            style={{ whiteSpace: 'nowrap' }}
                                        >
                                            {settings?.header_button_text || 'Hubungi Kami'}{" "}
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                );
                            })()}
                        </div>
                    </div>
                </div>
            </div>

            <header className="th-header header-layout1 header-layout2">
                <div className="header-top">
                    <div className="container">
                        <div className="row justify-content-center justify-content-xl-between align-items-center">
                            <div className="col-auto d-none d-xl-block">
                                <div className="header-links">
                                    <ul>
                                        <li className="d-none d-xl-inline-block">
                                            <i className="fa-brands fa-instagram"></i>
                                            <span>
                                                <a
                                                    href={settings?.instagram_url || "https://www.instagram.com/activ_teknologi/"}
                                                    target="__BLANK"
                                                >
                                                    {settings?.instagram_username || "@activ_teknologi"}
                                                </a>
                                            </span>
                                        </li>
                                        <li className="d-none d-xl-inline-block">
                                            <i className="fa-regular fa-clock"></i>
                                            <span>
                                                {settings?.office_hours || "Senin - Jumat: 8.30 am - 05.30 pm"}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div className="col-auto d-none d-xl-block">
                                <div className="header-right">
                                    <div className="header-links">
                                        <ul className="d-flex align-items-center">
                                            {menus?.top_header?.map((item, index) => (
                                                <li key={index}>
                                                    <Link 
                                                        href={item.url} 
                                                        target={item.target || "_self"}
                                                        rel={item.target === "_blank" ? "noopener noreferrer" : undefined}
                                                    >
                                                        {item.title}
                                                    </Link>
                                                </li>
                                            ))}
                                            <li className="ms-3 d-none d-xl-inline-block">
                                                <div className={`gt_switcher_wrapper notranslate ${isScrolled ? 'gt_sticky' : ''}`}>
                                                    <div className="gt_switcher">
                                                        <div className="gt_selected">
                                                            <img src={selectedLang.flag} alt={selectedLang.code} />
                                                            {selectedLang.label}
                                                            <i className="fa-light fa-angle-down ms-1"></i>
                                                        </div>
                                                        <div className="gt_option">
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|en'); }} title="English">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/en.svg" alt="en" /> English
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|id'); }} title="Indonesian">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/id.svg" alt="id" /> Indonesian
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|ar'); }} title="Arabic">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/countries/sa.svg" alt="ar" /> Arabic
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|zh-CN'); }} title="Chinese">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/zh-CN.svg" alt="zh-CN" /> Chinese
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|nl'); }} title="Dutch">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/nl.svg" alt="nl" /> Dutch
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|fr'); }} title="French">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/fr.svg" alt="fr" /> French
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|de'); }} title="German">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/de.svg" alt="de" /> German
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|hi'); }} title="Hindi">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/hi.svg" alt="hi" /> Hindi
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|it'); }} title="Italian">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/it.svg" alt="it" /> Italian
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|ja'); }} title="Japanese">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/ja.svg" alt="ja" /> Japanese
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|ko'); }} title="Korean">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/ko.svg" alt="ko" /> Korean
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|pt'); }} title="Portuguese">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/pt.svg" alt="pt" /> Portuguese
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|ru'); }} title="Russian">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/ru.svg" alt="ru" /> Russian
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|es'); }} title="Spanish">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/es.svg" alt="es" /> Spanish
                                                            </a>
                                                            <a href="#" onClick={(e) => { e.preventDefault(); window.doGTranslate('id|th'); }} title="Thai">
                                                                <img src="https://cdn.gtranslate.net/flags/svg/th.svg" alt="th" /> Thai
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="sticky-wrapper">
                    <div
                        className="menu-area"
                        data-bg-src="/assets/img/bg/line-pattern.png"
                    >
                        <div className="container">
                            <div className="row align-items-center justify-content-between">
                                <div className="col-xl-2 col-xxl-2 col-auto">
                                    <div className="header-logo">
                                        <Link href="/">
                                            <img
                                                src={getImageUrl(settings?.site_logo_header, "/assets/img/logo2.svg")}
                                                alt="ACTiV"
                                            />
                                        </Link>
                                    </div>
                                </div>
                                <div className="col-xl-8 col-xxl-7 col-auto text-start">
                                    <nav className="main-menu d-none d-lg-inline-block">
                                        <ul>
                                            {menus?.primary ? (
                                                renderMenuItems(menus.primary)
                                            ) : (
                                                <li>
                                                    <Link href="/">Beranda</Link>
                                                </li>
                                            )}
                                        </ul>
                                    </nav>
                                    <div className="d-flex align-items-center gap-3 d-lg-none justify-content-end">
                                        <div className="gt_switcher_wrapper notranslate" style={{ zIndex: 9 }}>
                                            <div className="gt_switcher border-0 p-0 shadow-none bg-transparent">
                                                <div
                                                    className="gt_selected p-0 d-flex align-items-center gap-1"
                                                    role="button"
                                                    onClick={(e) => { e.stopPropagation(); setMobileLangOpen(!mobileLangOpen); }}
                                                >
                                                    <img src={selectedLang.flag} alt={selectedLang.code} style={{ width: '22px', borderRadius: '3px', cursor: 'pointer' }} />
                                                    <i className={`fa-light fa-angle-${mobileLangOpen ? 'up' : 'down'}`} style={{ fontSize: '12px', color: 'var(--title-color)' }}></i>
                                                </div>
                                                <div
                                                    className={`gt_option shadow-sm ${mobileLangOpen ? 'd-block' : 'd-none'}`}
                                                    style={{ width: '150px', left: 'auto', right: '-10px', top: '35px', borderRadius: '6px' }}
                                                >
                                                    {[
                                                        { code: 'en', label: 'English', flag: 'https://cdn.gtranslate.net/flags/svg/en.svg' },
                                                        { code: 'id', label: 'Indonesian', flag: 'https://cdn.gtranslate.net/flags/svg/id.svg' },
                                                        { code: 'ar', label: 'Arabic', flag: 'https://cdn.gtranslate.net/flags/svg/countries/sa.svg' },
                                                        { code: 'zh-CN', label: 'Chinese', flag: 'https://cdn.gtranslate.net/flags/svg/zh-CN.svg' },
                                                        { code: 'nl', label: 'Dutch', flag: 'https://cdn.gtranslate.net/flags/svg/nl.svg' },
                                                        { code: 'fr', label: 'French', flag: 'https://cdn.gtranslate.net/flags/svg/fr.svg' },
                                                        { code: 'de', label: 'German', flag: 'https://cdn.gtranslate.net/flags/svg/de.svg' },
                                                        { code: 'hi', label: 'Hindi', flag: 'https://cdn.gtranslate.net/flags/svg/hi.svg' },
                                                        { code: 'it', label: 'Italian', flag: 'https://cdn.gtranslate.net/flags/svg/it.svg' },
                                                        { code: 'ja', label: 'Japanese', flag: 'https://cdn.gtranslate.net/flags/svg/ja.svg' },
                                                        { code: 'ko', label: 'Korean', flag: 'https://cdn.gtranslate.net/flags/svg/ko.svg' },
                                                        { code: 'pt', label: 'Portuguese', flag: 'https://cdn.gtranslate.net/flags/svg/pt.svg' },
                                                        { code: 'ru', label: 'Russian', flag: 'https://cdn.gtranslate.net/flags/svg/ru.svg' },
                                                        { code: 'es', label: 'Spanish', flag: 'https://cdn.gtranslate.net/flags/svg/es.svg' },
                                                        { code: 'th', label: 'Thai', flag: 'https://cdn.gtranslate.net/flags/svg/th.svg' }
                                                    ].map(lang => (
                                                        <a
                                                            key={lang.code}
                                                            href="#"
                                                            onClick={(e) => {
                                                                e.preventDefault();
                                                                window.doGTranslate(`id|${lang.code}`);
                                                                setMobileLangOpen(false);
                                                            }}
                                                            title={lang.label}
                                                        >
                                                            <img src={lang.flag} alt={lang.code} /> {lang.label}
                                                        </a>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            className="th-menu-toggle"
                                        >
                                            <i className="far fa-bars"></i>
                                        </button>
                                    </div>
                                </div>
                                <div className="col-xl-2 col-xxl-3 col-auto d-none d-lg-block">
                                    <div className="header-button d-flex align-items-center justify-content-end gap-2">
                                        <button
                                            type="button"
                                            className="icon-btn searchBoxToggler d-flex justify-content-center align-items-center flex-shrink-0"
                                        >
                                            <img
                                                src="/assets/img/icon/search.svg"
                                                alt="icon"
                                            />
                                        </button>
                                        {settings?.header_button_visible !== '0' && (() => {
                                            const btnUrl = settings?.header_button_url || "whatsapp";
                                            const isWa = btnUrl.startsWith('whatsapp');

                                            if (isWa) {
                                                const staticParams = {};
                                                if (btnUrl.includes('?')) {
                                                    const search = btnUrl.split('?')[1];
                                                    new URLSearchParams(search).forEach((v, k) => { staticParams[k] = v; });
                                                }

                                                return (
                                                    <a
                                                        href={getWhatsAppLink(settings?.whatsapp_number, {
                                                            cta_position: 'header',
                                                            cta_label: 'Header Desktop',
                                                            ...staticParams
                                                        })}
                                                        target="_blank"
                                                        rel="noopener"
                                                        className="th-btn th-radius th-icon flex-shrink-0"
                                                        style={{ whiteSpace: 'nowrap' }}
                                                    >
                                                        {settings?.header_button_text || 'Hubungi Kami'}{" "}
                                                        <i className="fa-light fa-arrow-right-long"></i>
                                                    </a>
                                                );
                                            }

                                            return (
                                                <Link
                                                    href={btnUrl || "/abouts"}
                                                    className="th-btn th-radius th-icon flex-shrink-0"
                                                    style={{ whiteSpace: 'nowrap' }}
                                                >
                                                    {settings?.header_button_text || 'Hubungi Kami'}{" "}
                                                    <i className="fa-light fa-arrow-right-long"></i>
                                                </Link>
                                            );
                                        })()}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {children}

            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            <footer className="footer-wrapper bg-title footer-layout2 space-top">
                <div className="widget-area">
                    <div className="container">
                        <div className="row justify-content-between">
                            <div className="col-md-6 col-xl-3">
                                <div className="widget footer-widget">
                                    <div className="th-widget-about">
                                        <div className="about-logo">
                                            <Link href="/">
                                                <img
                                                    src={getImageUrl(settings?.site_logo_footer, "/assets/img/logo3.svg")}
                                                    alt="ACTiV"
                                                />
                                            </Link>
                                        </div>
                                        <p className="about-text">
                                            kami berdedikasi untuk memberikan solusi
                                            teknologi komprehensif terbaik kepada
                                            klien kami.
                                        </p>
                                        <div className="th-social">
                                            {settings?.facebook_url && (
                                                <a href={settings.facebook_url} target="_blank" rel={settings.facebook_url_rel || "noopener noreferrer"}>
                                                    <i className="fab fa-facebook-f"></i>
                                                </a>
                                            )}
                                            {settings?.twitter_url && (
                                                <a href={settings.twitter_url} target="_blank" rel={settings.twitter_url_rel || "noopener noreferrer"}>
                                                    <i className="fab fa-twitter"></i>
                                                </a>
                                            )}
                                            {settings?.linkedin_url && (
                                                <a href={settings.linkedin_url} target="_blank" rel={settings.linkedin_url_rel || "noopener noreferrer"}>
                                                    <i className="fab fa-linkedin-in"></i>
                                                </a>
                                            )}
                                            {settings?.instagram_url && (
                                                <a href={settings.instagram_url} target="_blank" rel={settings.instagram_url_rel || "noopener noreferrer"}>
                                                    <i className="fab fa-instagram"></i>
                                                </a>
                                            )}
                                            {settings?.youtube_url && (
                                                <a href={settings.youtube_url} target="_blank" rel={settings.youtube_url_rel || "noopener noreferrer"}>
                                                    <i className="fab fa-youtube"></i>
                                                </a>
                                            )}
                                            <a
                                                href={getWhatsAppLink(settings?.whatsapp_number, {
                                                    cta_position: 'footer_social',
                                                    cta_label: 'Footer Social'
                                                }) || "https://www.whatsapp.com/"}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                <i className="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget widget_nav_menu footer-widget">
                                    <h3 className="widget_title">
                                        Tautan Penting
                                    </h3>
                                    <div className="menu-all-pages-container">
                                        <ul className="menu">
                                            {menus?.footer ? (
                                                renderFooterLinks(menus.footer)
                                            ) : (
                                                <li>
                                                    <Link
                                                        href="/"
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                    >
                                                        Beranda
                                                    </Link>
                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget footer-widget">
                                    <h3 className="widget_title">
                                        Hubungi Kami
                                    </h3>
                                    <div className="th-widget-contact">
                                        <div className="info-box_text">
                                            <div className="icon d-flex justify-content-center align-items-center">
                                                <img
                                                    src="/assets/img/icon/phone.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="tel:+622150110987"
                                                        className="info-box_link"
                                                    >
                                                        Tel: (+62) 2150110987
                                                    </a>
                                                </p>
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href={getWhatsAppLink(settings?.whatsapp_number || '6285162994602', {
                                                            cta_position: 'footer_info',
                                                            cta_label: 'Footer Support'
                                                        })}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="info-box_link"
                                                    >
                                                        WA: (+62) 851-6299-4602
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="info-box_text">
                                            <div className="icon d-flex justify-content-center align-items-center">
                                                <img
                                                    src="/assets/img/icon/envelope.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="mailto:info@activ.co.id"
                                                        className="info-box_link"
                                                    >
                                                        info@activ.co.id
                                                    </a>
                                                </p>
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="mailto:sales@activ.co.id"
                                                        className="info-box_link"
                                                    >
                                                        sales@activ.co.id
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="info-box_text">
                                            <div className="icon d-flex justify-content-center align-items-center">
                                                <img
                                                    src="/assets/img/icon/location-dot.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href={settings?.map_url || "https://maps.app.goo.gl/td2c6mkExW9zmY7C8"}
                                                        target="_blank"
                                                        rel={settings?.map_url_rel || "noopener noreferrer"}
                                                    >
                                                        Infinity Office, Belleza
                                                        BSA 1st Floor Unit 106,
                                                        Jl. Letjen Soepeno, Keb.
                                                        Lama Jakarta Selatan
                                                        12210
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget footer-widget footer-newsletter-style3">
                                    <h4 className="widget_title">
                                        Dapatkan buletin terbaru kami
                                    </h4>
                                    <div className="newsletter-widget">
                                        <div className="footer-search-contact">
                                            <h4 className="newsletter-title">
                                                Alamat Email
                                            </h4>
                                            <form action="#">
                                                <input
                                                    className="form-control"
                                                    type="email"
                                                    placeholder="Masukkan alamat email Anda...."
                                                />
                                                <button
                                                    type="submit"
                                                    className="icon-btn style2"
                                                >
                                                    <i className="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="copyright-wrap">
                    <div className="container">
                        <div className="row justify-content-between align-items-center">
                            <div className="col-lg-6">
                                <p className="copyright-text">
                                    Copyright © 2025 <Link href="/">ACTiV</Link>
                                    . Seluruh hak cipta dilindungi undang-undang.
                                </p>
                                <p className="copyright-text" style={{ fontSize: '11px', opacity: 0.7, marginTop: '4px' }}>
                                    Situs ini dilindungi oleh reCAPTCHA dan{' '}
                                    <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" style={{ color: 'inherit', textDecoration: 'underline' }}>
                                        Kebijakan Privasi
                                    </a>{' '}
                                    serta{' '}
                                    <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" style={{ color: 'inherit', textDecoration: 'underline' }}>
                                        Ketentuan Layanan
                                    </a>{' '}
                                    Google berlaku.
                                </p>
                            </div>
                            <div className="col-lg-6 text-lg-end text-center">
                                <div className="footer-links">
                                    <ul>
                                        <li>
                                            <Link href="/syarat-ketentuan">
                                                Syarat & Ketentuan
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/kemitraan">
                                                Kemitraan
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/kebijakan-privasi">
                                                Kebijakan Privasi
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <div 
                className={`scroll-top ${isScrolled ? "show" : ""}`}
                onClick={scrollToTop}
                style={{ cursor: "pointer" }}
            >
                <svg
                    className="progress-circle svg-content"
                    width="100%"
                    height="100%"
                    viewBox="-1 -1 102 102"
                >
                    <path
                        d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                        style={{
                            transition: "stroke-dashoffset 10ms linear 0s",
                            strokeDasharray: "307.919, 307.919",
                            strokeDashoffset: "307.919",
                        }}
                    ></path>
                </svg>
            </div>

            {/* Modal Area */}
            <div id="login-form" className="popup-login-register mfp-hide">
                <ul className="nav" id="pills-tab" role="tablist">
                    <li className="nav-item" role="presentation">
                        <button
                            className="nav-menu"
                            id="pills-home-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-home"
                            type="button"
                            role="tab"
                            aria-controls="pills-home"
                            aria-selected="false"
                        >
                            Login
                        </button>
                    </li>
                    <li className="nav-item" role="presentation">
                        <button
                            className="nav-menu active"
                            id="pills-profile-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-profile"
                            type="button"
                            role="tab"
                            aria-controls="pills-profile"
                            aria-selected="true"
                        >
                            Register
                        </button>
                    </li>
                </ul>
                <div className="tab-content" id="pills-tabContent">
                    <div
                        className="tab-pane fade"
                        id="pills-home"
                        role="tabpanel"
                        aria-labelledby="pills-home-tab"
                    >
                        <h3 className="box-title mb-30">
                            Sign in to your account
                        </h3>
                        <div className="th-login-form">
                            <form
                                action="#"
                                method="POST"
                                className="login-form ajax-contact"
                            >
                                <div className="row">
                                    <div className="form-group col-12">
                                        <label>Username or email</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            name="email"
                                            id="login_email"
                                            required
                                        />
                                    </div>
                                    <div className="form-group col-12">
                                        <label>Password</label>
                                        <input
                                            type="password"
                                            className="form-control"
                                            name="pasword"
                                            id="pasword"
                                            required
                                        />
                                    </div>
                                    <div className="form-btn mb-20 col-12">
                                        <button className="th-btn btn-fw th-radius2">
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                                <div id="forgot_url">
                                    <Link href="/my-account">
                                        Forgot password?
                                    </Link>
                                </div>
                                <p className="form-messages mb-0 mt-3"></p>
                            </form>
                        </div>
                    </div>
                    <div
                        className="tab-pane fade active show"
                        id="pills-profile"
                        role="tabpanel"
                        aria-labelledby="pills-profile-tab"
                    >
                        <h3 className="th-form-title mb-30">
                            Sign in to your account
                        </h3>
                        <form
                            action="#"
                            method="POST"
                            className="login-form ajax-contact"
                        >
                            <div className="row">
                                <div className="form-group col-12">
                                    <label>Username*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="usename"
                                        id="usename"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label>First name*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="firstname"
                                        id="firstname"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label>Last name*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="lastname"
                                        id="lastname"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label htmlFor="new_email">
                                        Your email*
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="new_email"
                                        id="new_email"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label htmlFor="new_email_confirm">
                                        Confirm email*
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="new_email_confirm"
                                        id="new_email_confirm"
                                        required
                                    />
                                </div>
                                <div className="statement">
                                    <span className="register-notes">
                                        A password will be emailed to you.
                                    </span>
                                </div>
                                <div className="form-btn mt-20 col-12">
                                    <button className="th-btn btn-fw th-radius2">
                                        Sign up
                                    </button>
                                </div>
                            </div>
                            <p className="form-messages mb-0 mt-3"></p>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
