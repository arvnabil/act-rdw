import React, { useState, useEffect, memo } from "react";
import { sectionRegistry } from "./utils/sectionRegistry";
import ErrorBoundary from "./Components/ErrorBoundary";

/**
 * SectionPreview
 *
 * Wraps the actual CMS components for the builder context.
 * - Provides the ID anchor for scrolling.
 * - Handles selection overlay and interaction locking.
 * - Disables dangerous actions (links/submissions) while keeping reading experience alive.
 * - Wrapped in ErrorBoundary for safety.
 */
const SectionPreview = memo(function SectionPreview({
    section,
    isSelected,
    onSelect,
    previewDevice,
    allServices,
    allProjects,
}) {
    const registryItem = sectionRegistry[section.section_key];
    const [isHovered, setIsHovered] = useState(false);

    // Component Identification
    const Component = registryItem?.component;

    // determine props based on key
    const extraProps = {};
    if (section.section_key === "services") {
        extraProps.services = allServices;
    }
    if (section.section_key === "projects") {
        extraProps.projects = allProjects;
    }
    // Add logic for News etc if needed later

    // Interaction Components
    const handleClick = (e) => {
        // Stop bubbling so we don't trigger canvas valid/deselect handlers
        e.stopPropagation();
        // Invoke selection
        onSelect(section.id);
    };

    // If unknown section type, render a safe placeholder
    if (!registryItem || !Component) {
        return (
            <div
                id={`section-${section.id}`}
                className="p-8 border-2 border-dashed border-red-200 bg-red-50 text-red-600 rounded-lg m-4 flex items-center gap-4 transition-all"
                onClick={handleClick}
            >
                <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i className="fa-solid fa-triangle-exclamation text-xl"></i>
                </div>
                <div>
                    <h4 className="font-bold text-sm uppercase tracking-wide">
                        Missing Component
                    </h4>
                    <p className="text-xs mt-1 text-red-500">
                        Key:{" "}
                        <code className="bg-red-100 px-1 py-0.5 rounded text-red-700 font-mono">
                            {section.section_key}
                        </code>
                    </p>
                    <p className="text-xs mt-1 opacity-75">
                        Please register this component in{" "}
                        <code>sectionRegistry.js</code>.
                    </p>
                </div>
            </div>
        );
    }

    return (
        <div
            id={`section-${section.id}`}
            onClick={handleClick}
            onMouseEnter={() => setIsHovered(true)}
            onMouseLeave={() => setIsHovered(false)}
            className={`
                group relative transition-all duration-300 ease-out border-2 mx-auto
                ${
                    isSelected
                        ? "z-10 border-act-primary ring-4 ring-act-primary/10 shadow-xl"
                        : isHovered
                          ? "z-0 border-act-primary/40 shadow-md"
                          : "z-0 border-transparent hover:border-act-primary/20"
                }
            `}
        >
            {/*
                INTERACTION LOCK OVERLAY
                REMOVED: To allow hover interactions (accordion effects etc).
                Safety is handled by onClickCapture on the wrapper.
            */}
            {/* <div className="absolute inset-0 z-10 bg-transparent cursor-default" /> */}

            {/*
                CONTENT RENDERER
                - Wrapped in ErrorBoundary
                - onClickCapture prevents link navigation
            */}
            {/*
                CONTENT RENDERER
            */}
            <div
                className="section-content-wrapper"
                onClickCapture={(e) => e.preventDefault()}
            >
                <ErrorBoundary key={section.id}>
                    {/* Unified component rendering */}
                    <Component
                        {...(section.config || {})}
                        {...extraProps}
                        preview={true}
                        isBuilder={true}
                        previewDevice={previewDevice}
                        allServices={allServices}
                    />
                </ErrorBoundary>
            </div>

            {/*
                LABEL / CONTROLS
                Only visible when selected or builder-hovered
            */}
            <div
                className={`
                absolute top-0 right-0 z-20 flex items-center gap-1 p-0 transition-opacity duration-200 pointer-events-none
                ${isSelected || isHovered ? "opacity-100" : "opacity-0"}
            `}
            >
                <div
                    className={`
                    text-[10px] px-3 py-1 font-bold text-white shadow-md uppercase tracking-wider rounded-bl-lg backdrop-blur-sm
                    ${isSelected ? "bg-act-primary" : "bg-gray-500/80"}
                `}
                >
                    {registryItem.label}
                </div>
            </div>
        </div>
    );
});

export default SectionPreview;
