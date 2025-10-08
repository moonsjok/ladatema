import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
    server: {
        cors: {
            origin: "https://ladatemaresearch.com",
            // origin: "https://ladatema.kom",
            methods: ["GET", "POST", "PUT", "DELETE"], // Méthodes autorisées
            allowedHeaders: ["Content-Type"], // En-têtes autorisés
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            $: "jquery",
            "~bootstrap": path.resolve(__dirname, "node_modules/bootstrap"),
        },
    },
    build: {
        manifest: "manifest.json", // ✅ Correction du chemin du manifest.json
        outDir: "public/build",
        rollupOptions: {
            output: {
                assetFileNames: "assets/[name]-[hash][extname]",
                entryFileNames: "assets/[name]-[hash].js",
            },
        },
    },
});
