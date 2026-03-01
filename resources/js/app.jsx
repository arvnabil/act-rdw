import React from "react";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import "../css/app.css";
import "../css/tailwind-output.css";
import "./bootstrap";

const appName = import.meta.env.VITE_APP_NAME || "ACTiV";

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob([
            "./Pages/**/*.jsx",
            "../../Modules/**/Resources/assets/js/Pages/**/*.jsx",
        ]);
        let pagePath = `./Pages/${name}.jsx`;

        if (!pages[pagePath]) {
            const modulePage = Object.keys(pages).find((path) =>
                path.endsWith(`/${name}.jsx`),
            );
            if (modulePage) {
                pagePath = modulePage;
            }
        }

        return resolvePageComponent(pagePath, pages);
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
