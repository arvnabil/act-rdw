import React, { useEffect, useRef } from "react";
import SectionPreview from "./SectionPreview";

/**
 * BuilderCanvas
 *
 * The main stage for the page builder.
 * - Renders all sections in order.
 * - Handles viewport emulation (Desktop/Tablet/Mobile).
 * - Manages auto-scrolling to selected sections.
 * - Provides the scroll container for the preview.
 */
export default function BuilderCanvas({
    sections,
    selectedSectionId,
    onSelectSection,
    previewDevice,
    allServices,
    allProjects,
}) {
    const scrollContainerRef = useRef(null);

    // Auto-scroll to selected section
    useEffect(() => {
        if (selectedSectionId && scrollContainerRef.current) {
            const element = document.getElementById(
                `section-${selectedSectionId}`,
            );

            if (element) {
                // We use scrollIntoView with block: 'center' to nicely frame the section
                element.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                    inline: "nearest",
                });
            }
        }
    }, [selectedSectionId]);

    // Calculate width class for viewport emulation
    // The canvas container centers itself and constrains width based on this.
    // Transition classes ensure smooth resizing.
    const getViewportClass = () => {
        switch (previewDevice) {
            case "mobile":
                return "w-[375px] shadow-2xl my-8 border-x border-gray-200";
            case "tablet":
                return "w-[768px] shadow-2xl my-8 border-x border-gray-200";
            case "desktop":
            default:
                return "w-full shadow-lg";
        }
    };

    const renderSection = (section, index) => (
        <SectionPreview
            key={section.id || `temp-${index}`}
            section={section}
            isSelected={selectedSectionId === section.id}
            onSelect={onSelectSection}
            previewDevice={previewDevice}
            allServices={allServices}
            allProjects={allProjects}
        />
    );

    return (
        <div
            ref={scrollContainerRef}
            className="flex-1 overflow-y-auto bg-gray-100/50 bg-[url('/assets/img/patterns/grid.svg')] flex justify-center custom-scrollbar scroll-smooth relative"
            id="builder-canvas-scroll-container"
            onClick={(e) => {
                // Deselect if clicking the empty canvas background logic
                // We check if the click target is the scroll container logic itself
                if (e.target === scrollContainerRef.current) {
                    onSelectSection(null);
                }
            }}
        >
            {/* The actual content "Body" simulation */}
            <div
                className={`
                    min-h-full bg-white transition-all duration-500 ease-in-out relative flex flex-col origin-top mx-auto
                    ${getViewportClass()}
                    ${previewDevice !== "desktop" ? "rounded-lg overflow-hidden ring-4 ring-gray-900/5" : ""}
                `}
                style={{
                    paddingBottom: "0",
                    minHeight: previewDevice === "desktop" ? "100%" : "auto", // Allow mobile to grow naturally
                }}
            >
                {/* Scroll Spacer for Device Mode */}
                <div
                    className={`flex-1 flex flex-col ${previewDevice !== "desktop" ? "overflow-hidden bg-white" : ""}`}
                >
                    {sections.length === 0 ? (
                        <div className="flex-1 flex flex-col items-center justify-center p-12 text-gray-400 border-2 border-dashed border-gray-200 m-8 rounded-xl h-[50vh] animate-fade-in-up">
                            <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner text-act-primary/50">
                                <i className="fa-regular fa-layer-group text-3xl"></i>
                            </div>
                            <h3 className="text-xl font-bold text-gray-700">
                                Start Building
                            </h3>
                            <p className="text-sm mt-2 text-gray-500 max-w-xs text-center leading-relaxed">
                                Drag sections from the{" "}
                                <strong>Left Sidebar</strong> or click{" "}
                                <strong>Add</strong> to construct your page.
                            </p>
                        </div>
                    ) : (
                        // Render ALL sections in order
                        sections.map(renderSection)
                    )}
                </div>
            </div>
        </div>
    );
}
