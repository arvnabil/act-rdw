import React from "react";
import { createRoot } from "react-dom/client";
import SerpPreview from "./Components/Seo/SerpPreview";

// Function to mount the React component
const mountSerpPreview = (rootElement) => {
    if (rootElement && !rootElement._reactRoot) {
        // console.log('Mounting SERP Preview React Component...');
        const root = createRoot(rootElement);
        rootElement._reactRoot = root;

        // Initial render with default/empty props or data attributes if we had them
        const render = (props = {}) => {
            root.render(<SerpPreview {...props} />);
        };

        // Initial render
        render();

        // Listen for custom event from Blade/Alpine
        const updateHandler = (event) => {
            // console.log('SERP Data Updated:', event.detail);
            render(event.detail);
        };

        window.addEventListener("serp-data-updated", updateHandler);

        // Store cleanup function
        rootElement._cleanup = () => {
            window.removeEventListener("serp-data-updated", updateHandler);
            root.unmount();
            delete rootElement._reactRoot;
        };
    }
};

// Global Observer to handle Livewire DOM updates
const observerCallback = (mutationsList, observer) => {
    const rootElement = document.getElementById("react-serp-root");
    if (rootElement && !rootElement._reactRoot) {
        mountSerpPreview(rootElement);
    }
};

// Start Observing
const observer = new MutationObserver(observerCallback);
observer.observe(document.body, { childList: true, subtree: true });

// Also check immediately in case it's already there
const initialCheck = document.getElementById("react-serp-root");
if (initialCheck) {
    mountSerpPreview(initialCheck);
}

// console.log('Filament SERP Preview Script Loaded');
