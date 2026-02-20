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
                        // Group core React and related libraries to avoid circular dependency
                        if (
                            id.includes("react") || 
                            id.includes("scheduler") || 
                            id.includes("inertiajs") || 
                            id.includes("react-is") ||
                            id.includes("object-assign")
                        ) {
                            return "vendor-react";
                        }
                        
                        // Heavy UI/Utility libraries that can be separate
                        if (id.includes("jspdf") || id.includes("html2canvas")) {
                            return "vendor-pdf";
                        }
                        
                        if (id.includes("swiper") || id.includes("jquery")) {
                            return "vendor-ui";
                        }

                        if (id.includes("gsap") || id.includes("lenis") || id.includes("wowjs")) {
                            return "vendor-animation";
                        }

                        // Everything else goes to a general vendor chunk
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
