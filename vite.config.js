import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";

export default defineConfig({
  server: {
    cors: {
      origin: ["https://ladatema.kom", "https://ladatemaresearch.com"], // Autoriser les deux domaines
      methods: ["GET", "POST", "PUT", "DELETE"], // Méthodes autorisées
      allowedHeaders: ["Content-Type"], // En-têtes autorisés
    },
    host: "localhost", // Forcer localhost au lieu de [::]
    https: false, // Désactiver HTTPS en développement pour éviter les erreurs SSL
    port: 5173, // Forcer le port
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
  // Définir l'URL de base pour les assets en développement
  base: process.env.NODE_ENV === "production" ? "/build/" : "/",
});
