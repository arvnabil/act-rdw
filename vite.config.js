import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

import legacy from "@vitejs/plugin-legacy";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.jsx",
                "resources/js/filament-serp.jsx",
            ],
            refresh: true,
        }),
        react(),
        legacy({
            targets: ["defaults", "not IE 11"],
        }),
    ],
    build: {
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        if (id.includes("jspdf") || id.includes("html2canvas")) {
                            return "utils-pdf";
                        }
                        if (id.includes("gsap") || id.includes("lenis") || id.includes("wowjs")) {
                            return "animations";
                        }
                        if (id.includes("swiper")) {
                            return "ui-swiper";
                        }
                        if (id.includes("jquery")) {
                            return "ui-jquery";
                        }
                        if (id.includes("react") || id.includes("scheduler") || id.includes("inertiajs") || id.includes("react-is")) {
                            return "react-core";
                        }
                        return "vendor";
                    }
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    resolve: {
        alias: {
            "@": "/resources/js",
        },
    },
});
