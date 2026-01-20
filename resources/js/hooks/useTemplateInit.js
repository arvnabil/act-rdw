import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import $ from "jquery";
import Swiper from "swiper/bundle";
// import 'swiper/css/bundle';
// import "wowjs"; // Removed to fix Production Build WeakMap error (Replaced with CDN)
import Isotope from "isotope-layout";
import imagesLoaded from "imagesloaded";
import jQueryBridget from "jquery-bridget";
import Lenis from "lenis";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
// import './SplitText';

gsap.registerPlugin(ScrollTrigger);

export function useTemplateInit() {
    const { url } = usePage();

    useEffect(() => {
        // Expose Global jQuery
        window.jQuery = window.$ = $;

        // Register jQuery Plugins
        jQueryBridget("isotope", Isotope, $);
        imagesLoaded.makeJQueryPlugin($);

        // --- Lenis Smooth Scroll ---
        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // --- 01. Preloader & Mobile Menu ---
        // --- 01. Preloader & Smart Loading (Hero First) ---
        const handleLoad = () => {
            $(".preloader").fadeOut();

            // Init WOW.js safely after preloader fades
            if (typeof window.WOW !== "undefined") {
                const WOWClass = window.WOW.WOW || window.WOW;
                try {
                    new WOWClass({
                        boxClass: "wow",
                        animateClass: "animated",
                        offset: 0,
                        mobile: true,
                        live: true,
                    }).init();
                } catch (e) {
                    console.warn("WOW.js initialization failed", e);
                }
            }
        };

        // Strategy: "Hero First" - Unblock view as soon as Hero is ready
        // Max wait time: 1.5 seconds (prevents getting stuck)
        const maxWait = setTimeout(handleLoad, 1500);

        // Check if Hero Section exists and has images
        const $hero = $(".hero-slider-2, .hero-wrapper");
        if ($hero.length > 0) {
            // Wait ONLY for hero images
            $hero.imagesLoaded(function () {
                clearTimeout(maxWait); // Cancel timeout if hero loads fast
                handleLoad();
            });
        } else {
            // If no hero (internal page), utilize DOMContentLoaded (Fastest)
            if (document.readyState === "complete") {
                handleLoad();
            } else {
                window.addEventListener("load", handleLoad);
                // Fallback if load event takes too long
                setTimeout(handleLoad, 1000);
            }
        }

        $(document).on("click", ".preloaderCls", function (e) {
            e.preventDefault();
            $(".preloader").css("display", "none");
        });

        // Mobile Menu Plugin
        $.fn.thmobilemenu = function (options) {
            var opt = $.extend(
                {
                    menuToggleBtn: ".th-menu-toggle",
                    bodyToggleClass: "th-body-visible",
                    subMenuClass: "th-submenu",
                    subMenuParent: "menu-item-has-children",
                    thSubMenuParent: "th-item-has-children",
                    subMenuParentToggle: "th-active",
                    meanExpandClass: "th-mean-expand",
                    appendElement: '<span class="th-mean-expand"></span>',
                    subMenuToggleClass: "th-open",
                    toggleSpeed: 400,
                },
                options,
            );

            return this.each(function () {
                var menu = $(this);
                function menuToggle() {
                    menu.toggleClass(opt.bodyToggleClass);
                    var subMenu = "." + opt.subMenuClass;
                    $(subMenu).each(function () {
                        if ($(this).hasClass(opt.subMenuToggleClass)) {
                            $(this).removeClass(opt.subMenuToggleClass);
                            $(this).css("display", "none");
                            $(this)
                                .parent()
                                .removeClass(opt.subMenuParentToggle);
                        }
                    });
                }
                menu.find("." + opt.subMenuParent).each(function () {
                    var submenu = $(this).find("ul");
                    submenu.addClass(opt.subMenuClass);
                    submenu.css("display", "none");
                    $(this).addClass(opt.subMenuParent);
                    $(this).addClass(opt.thSubMenuParent);
                    if (
                        $(this).children("a").find(".th-mean-expand").length ===
                        0
                    ) {
                        $(this).children("a").append(opt.appendElement);
                    }
                    $(this)
                        .children("a")
                        .off("click")
                        .on("click", function (e) {
                            e.preventDefault();
                            toggleDropDown($(this).parent());
                        });
                });
                function toggleDropDown($element) {
                    var submenu = $element.children("ul");
                    if (submenu.length > 0) {
                        $element.toggleClass(opt.subMenuParentToggle);
                        submenu.slideToggle(opt.toggleSpeed);
                        submenu.toggleClass(opt.subMenuToggleClass);
                    }
                }
                // Removed document level listener as it's now handled directly on elements
                $(document)
                    .off("click", opt.menuToggleBtn)
                    .on("click", opt.menuToggleBtn, function () {
                        menuToggle();
                    });
                menu.off("click").on("click", function (e) {
                    e.stopPropagation();
                    menuToggle();
                });
                menu.find("div")
                    .off("click")
                    .on("click", function (e) {
                        if (
                            $(e.target).closest(".th-menu-toggle").length === 0
                        ) {
                            e.stopPropagation();
                        }
                    });
            });
        };
        $(".th-menu-wrapper").thmobilemenu();

        // --- Nice Select ---
        if ($.fn.niceSelect) {
            $(".nice-select").each(function () {
                if (!$(this).next().hasClass("nice-select")) {
                    $(this).niceSelect();
                }
            });
        }

        // Sticky Header
        $(window).on("scroll", function () {
            if ($(this).scrollTop() > 500) {
                $(".sticky-wrapper").addClass("sticky");
            } else {
                $(".sticky-wrapper").removeClass("sticky");
            }
        });

        // --- Scroll To Top (SVG Logic) ---
        if ($(".scroll-top").length > 0) {
            var scrollTopbtn = $(".scroll-top")[0];
            var progressPath = $(".scroll-top path")[0];
            var pathLength = progressPath.getTotalLength();

            progressPath.style.transition =
                progressPath.style.WebkitTransition = "none";
            progressPath.style.strokeDasharray = pathLength + " " + pathLength;
            progressPath.style.strokeDashoffset = pathLength;
            progressPath.getBoundingClientRect();
            progressPath.style.transition =
                progressPath.style.WebkitTransition =
                    "stroke-dashoffset 10ms linear";

            var updateProgress = function () {
                var scroll = $(window).scrollTop();
                var height = $(document).height() - $(window).height();
                var progress = pathLength - (scroll * pathLength) / height;
                progressPath.style.strokeDashoffset = progress;
            };
            updateProgress();
            $(window).on("scroll", updateProgress);

            var offset = 50;
            var duration = 750;
            $(window).on("scroll", function () {
                if ($(this).scrollTop() > offset) {
                    $(scrollTopbtn).addClass("show");
                } else {
                    $(scrollTopbtn).removeClass("show");
                }
            });
            $(scrollTopbtn).on("click", function (event) {
                event.preventDefault();
                $("html, body").animate({ scrollTop: 0 }, duration);
                return false;
            });
        }

        // Bg Src
        $("[data-bg-src]").each(function () {
            var src = $(this).attr("data-bg-src");
            $(this).css("background-image", "url(" + src + ")");
            $(this).removeAttr("data-bg-src").addClass("background-image");
        });

        // Animation Properties Helper
        function animationProperties() {
            $("[data-ani]").each(function () {
                var animationName = $(this).data("ani");
                $(this).addClass(animationName);
            });
            $("[data-ani-delay]").each(function () {
                var delayTime = $(this).data("ani-delay");
                $(this).css("animation-delay", delayTime);
            });
        }
        animationProperties();

        // --- 07. Swipers ---
        if ($(".hero-slider-2").length) {
            var thumbs = null;
            if ($(".heroThumbs").length) {
                thumbs = new Swiper(".heroThumbs", {
                    spaceBetween: 10,
                    slidesPerView: 1,
                    loop: true,
                    watchSlidesProgress: true,
                    slideToClickedSlide: true,
                    navigation: {
                        nextEl: ".slider-next",
                        prevEl: ".slider-prev",
                    },
                });
            }

            var heroOptions = {
                spaceBetween: 10,
                effect: "fade",
                pagination: { el: ".swiper-pagination", clickable: true },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                autoplay: { delay: 6000, disableOnInteraction: false },
                loop: true,
                watchSlidesProgress: true,
                on: {
                    slideChangeTransitionStart: function () {
                        // Remove animation classes
                        $(this.slides)
                            .find("[data-ani]")
                            .each(function () {
                                var ani = $(this).data("ani");
                                $(this).removeClass(ani);
                            });
                    },
                    slideChangeTransitionEnd: function () {
                        // Add animation classes back to active slide
                        $(this.slides[this.activeIndex])
                            .find("[data-ani]")
                            .each(function () {
                                var ani = $(this).data("ani");
                                $(this).addClass(ani);
                            });
                    },
                },
            };

            if (thumbs) {
                heroOptions.thumbs = { swiper: thumbs };
            }

            new Swiper(".hero-slider-2", heroOptions);
        }

        // Brands / Other Sliders
        $(".th-slider").each(function () {
            if (this.swiper) return;
            var $this = $(this);
            var settings = $this.data("slider-options") || {};
            new Swiper(this, {
                loop: true,
                spaceBetween: 24,
                autoplay: { delay: 3000, disableOnInteraction: false },
                speed: 1000,
                ...settings,
            });
        });

        // External Slider Navigation (Custom Arrows)
        $(document).on(
            "click",
            "[data-slider-prev], [data-slider-next]",
            function () {
                var sliderSelector =
                    $(this).data("slider-prev") || $(this).data("slider-next");
                var targetSlider = $(sliderSelector);
                if (targetSlider.length) {
                    var swiper = targetSlider[0].swiper;
                    if (swiper) {
                        if ($(this).data("slider-prev")) {
                            swiper.slidePrev();
                        } else {
                            swiper.slideNext();
                        }
                    }
                }
            },
        );

        // --- Interactions (Hover Items) ---
        // Service Hover
        $(document).on("mouseover", ".hover-item", function () {
            $(this).addClass("item-active");
            $(".hover-item").removeClass("item-active");
            $(this).addClass("item-active");
            const index = $(this).index(".hover-item");
            $(".service-card-thumb")
                .removeClass("active")
                .eq(index)
                .addClass("active");
        });

        // Process/Feature Hover
        $(document).on("mouseover", ".hover-item2", function () {
            $(this).addClass("item-active2");
            $(".hover-item2").removeClass("item-active2");
            $(this).addClass("item-active2");
            const index = $(this).index(".hover-item2");
            $(".process-box-img")
                .removeClass("active-img")
                .eq(index)
                .addClass("active-img");
        });

        // Service List Click Interaction
        $(document).on("click", ".service7-list", function () {
            $(this).addClass("active").siblings().removeClass("active");
        });

        // --- Popup Search Box ---
        function popupSarchBox(
            $searchBox,
            $searchOpen,
            $searchCls,
            $toggleCls,
        ) {
            $(document).on("click", $searchOpen, function (e) {
                e.preventDefault();
                $($searchBox).addClass($toggleCls);
            });
            $(document).on("click", function (e) {
                if (
                    !$(e.target).closest($searchBox).length &&
                    !$(e.target).closest($searchOpen).length
                ) {
                    $($searchBox).removeClass($toggleCls);
                }
            });
            $(document).on("click", $searchCls, function (e) {
                e.preventDefault();
                e.stopPropagation();
                $($searchBox).removeClass($toggleCls);
            });
        }
        popupSarchBox(
            ".popup-search-box",
            ".searchBoxToggler",
            ".searchClose",
            "show",
        );

        // --- Magnific Popup ---
        // --- Magnific Popup ---
        if ($.fn.magnificPopup) {
            $(".popup-video").magnificPopup({ type: "iframe" });
            $(".popup-image").magnificPopup({
                type: "image",
                gallery: { enabled: true },
            });
            $(".popup-login-register").magnificPopup({
                type: "inline",
                fixedContentPos: false,
                focus: "#name",
            });
        }

        // --- Isotope ---
        $(".filter-active, .masonry-active").imagesLoaded(function () {
            var $grid = $(".filter-active").isotope({
                itemSelector: ".filter-item",
                layoutMode: "fitRows",
            });

            $(".filter-menu-active").on("click", "button", function () {
                var filterValue = $(this).attr("data-filter");
                $grid.isotope({ filter: filterValue });
                $(this).addClass("active").siblings().removeClass("active");
            });

            $(".masonry-active").isotope({
                itemSelector: ".filter-item",
                percentPosition: true,
                masonry: { columnWidth: 1 },
            });
        });

        // --- GSAP Animations ---
        // Reveal Animation
        if ($(".reveal").length) {
            let revealContainers = document.querySelectorAll(".reveal");
            revealContainers.forEach((container) => {
                let image = container.querySelector("img");
                if (image) {
                    let tl = gsap.timeline({
                        scrollTrigger: {
                            trigger: container,
                            toggleActions: "play none none none",
                        },
                    });
                    tl.set(container, { autoAlpha: 1 });
                    tl.from(container, {
                        duration: 1.5,
                        xPercent: -100,
                        ease: "power2.out",
                    });
                    tl.from(image, {
                        duration: 1.5,
                        xPercent: 100,
                        scale: 1.3,
                        delay: -1.5,
                        ease: "power2.out",
                    });
                }
            });
        }

        // Scroll Text Animation (using SplitText if available for anime styles)
        const SplitText = window.SplitText;
        if (SplitText && $(".text-anime-style-2").length) {
            // Text Anime Style 2 (Subtitles) - Char Reveal
            let animatedTextElements = document.querySelectorAll(
                ".text-anime-style-2",
            );
            animatedTextElements.forEach((element) => {
                gsap.set(element, { autoAlpha: 1 });
                let animationSplitText = new SplitText(element, {
                    type: "chars, words",
                });
                gsap.from(animationSplitText.chars, {
                    duration: 1,
                    delay: 0.1,
                    x: 20,
                    autoAlpha: 0,
                    stagger: 0.05,
                    scrollTrigger: {
                        trigger: element,
                        start: "top 85%",
                        toggleActions: "play none none none",
                    },
                });
            });
        }

        // Isotope Grid (Partners Page - .grid)
        if ($(".grid").length > 0) {
            $(".grid").imagesLoaded(function () {
                var grid = $(".grid").isotope({
                    itemSelector: ".grid-item",
                    percentPosition: true,
                    masonry: {
                        columnWidth: ".grid-item",
                    },
                });

                $(".case-menu").on("click", "button", function () {
                    var filterValue = $(this).attr("data-filter");
                    grid.isotope({ filter: filterValue });
                    $(this).siblings(".active").removeClass("active");
                    $(this).addClass("active");
                });
            });
        }

        // --- Shape Mockup ---
        $.fn.shapeMockup = function () {
            var $shape = $(this);
            $shape.each(function () {
                var $currentShape = $(this),
                    shapeTop = $currentShape.data("top"),
                    shapeRight = $currentShape.data("right"),
                    shapeBottom = $currentShape.data("bottom"),
                    shapeLeft = $currentShape.data("left");
                $currentShape
                    .css({
                        top: shapeTop,
                        right: shapeRight,
                        bottom: shapeBottom,
                        left: shapeLeft,
                    })
                    .removeAttr("data-top")
                    .removeAttr("data-right")
                    .removeAttr("data-bottom")
                    .removeAttr("data-left")
                    .parent()
                    .addClass("shape-mockup-wrap");
            });
        };

        if ($(".shape-mockup").length > 0) {
            $(".shape-mockup").shapeMockup();
        }

        // Main Titles (scroll-text-ani) - Gradient Fill Animation (Legacy)
        if ($(".scroll-text-ani").length > 0) {
            document.querySelectorAll(".scroll-text-ani").forEach((line) => {
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
        }

        // --- Counter Up (if needed, currently no jQuery.counterUp loaded, but could be added) ---
    }, [url]);
}
