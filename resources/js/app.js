import "bootstrap";
import { Tooltip } from "bootstrap";
import $ from "jquery";
window.$ = $;
import swal from "sweetalert2";
window.Swal = swal;
import "datatables.net-bs5/css/dataTables.bootstrap5.min.css";
import dt from "datatables.net-bs5";
import Plyr from "plyr";
import "plyr/dist/plyr.css";
import { initializeEditor } from "./editor/index";
import "./editor/editor.css";

$(document).ready(() => {
  $("#dataTable").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json",
    },
  });
});

document.addEventListener("DOMContentLoaded", () => {
  // Tooltips Bootstrap
  const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]',
  );
  [...tooltipTriggerList].forEach(
    (tooltipTriggerEl) => new Tooltip(tooltipTriggerEl),
  );

  // Plyr - Configuration personnalisée
  const initializePlyr = () => {
    const videos = document.querySelectorAll("video");
    videos.forEach((video) => {
      if (!video.classList.contains("plyr-initialized")) {
        // Configuration Plyr pour une meilleure expérience
        const player = new Plyr(video, {
          controls: [
            "play-large",
            "play",
            "progress",
            "current-time",
            "mute",
            "volume",
            "captions",
            "settings",
            "pip",
            "airplay",
            "fullscreen",
          ],
          autoplay: false,
          autopause: true,
          clickToPlay: true,
          hideControls: true,
          resetOnEnd: false,
          keyboard: {
            focused: true,
            global: true,
          },
          tooltips: {
            controls: true,
            seek: true,
          },
          captions: {
            active: false,
            update: false,
            language: "fr",
          },
          fullscreen: {
            enabled: true,
            fallback: true,
            iosNative: false,
          },
          storage: {
            enabled: true,
            key: "plyr",
          },
        });
        video.classList.add("plyr-initialized");
      }
    });
  };
  if (!document.querySelector(".no-plyr")) {
    initializePlyr();
    const observer = new MutationObserver(() => initializePlyr());
    observer.observe(document.body, { childList: true, subtree: true });
  }

  // Initialisation des éditeurs WYSIWYG
  const selectors = ["#content", ".wysiwyg-editor"];
  selectors.forEach((selector) => {
    const elements = document.querySelectorAll(selector);
    if (elements.length === 0) {
      console.log(`Aucun élément trouvé pour le sélecteur : ${selector}`);
      return;
    }

    elements.forEach((element) => {
      if (!element.dataset.editorInitialized) {
        console.log(`Initialisation de l'éditeur pour :`, element);
        try {
          initializeEditor(element); // Passe un élément DOM valide
          element.dataset.editorInitialized = "true";
        } catch (error) {
          console.error(
            `Erreur lors de l'initialisation de l'éditeur pour ${selector} :`,
            error,
          );
        }
      } else {
        console.log(`Éditeur déjà initialisé pour :`, element);
      }
    });
  });
});

document.addEventListener("livewire:load", () => {
  Livewire.on("datatableUpdated", () => {
    $("#dataTable").DataTable().draw();
  });

  Livewire.on("editorsUpdated", () => {
    const selectors = ["#content", ".wysiwyg-editor"];
    selectors.forEach((selector) => {
      const elements = document.querySelectorAll(selector);
      elements.forEach((element) => {
        if (!element.dataset.editorInitialized) {
          console.log(`Réinitialisation de l'éditeur pour :`, element);
          try {
            initializeEditor(element);
            element.dataset.editorInitialized = "true";
          } catch (error) {
            console.error(
              `Erreur lors de la réinitialisation de l'éditeur pour ${selector} :`,
              error,
            );
          }
        }
      });
    });
  });
});
