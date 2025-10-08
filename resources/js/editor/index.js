// resources/js/editor/index.js

// Importations pour le formatage de texte
import { enableBold } from "./bold";
import { enableItalic } from "./italic";
import { enableHeading } from "./heading";
import { enableParagraphs } from "./utils";
import { enableStrikethrough } from "./strikethrough";
import { enableUnderline } from "./underline";
import { enableTextCase } from "./textCase";
import { enableFontSize } from "./fontSize";

// Importations pour les listes et l'alignement
import { enableList } from "./list";
import { enableAlign } from "./align";
import { enableJustify } from "./justify";

// Importations pour les médias
import { enableImage } from "./image";
import { enableLink } from "./link";
import { enableVideo } from "./video";

// Importations pour les fonctionnalités de couleur
import { enableHighlight } from "./highlight";
import { enableColor } from "./color";

// Importations pour les fonctionnalités d'annulation/répétition
import { enableUndo } from "./undo";
import { enableRedo } from "./redo";

// Importation pour le code
import { enableCode } from "./code";

export function initializeEditor(element) {
    // Vérifier que l'élément est valide
    if (
        !(element instanceof HTMLElement) &&
        !(element instanceof HTMLTextAreaElement)
    ) {
        console.warn(`Élément invalide fourni à initializeEditor:`, element);
        return;
    }

    // Remplacer l'élément original par un div contenteditable
    const editor = document.createElement("div");
    editor.innerHTML = element.value || element.innerHTML; // Copie le contenu initial
    editor.setAttribute("contenteditable", true);
    editor.classList.add("form-control", "editor");

    // Copie les attributs importants
    if (element.name) {
        editor.setAttribute("data-name", element.name); // Stocker le nom pour la soumission du formulaire
    }
    if (element.id) {
        editor.id = element.id; // Garder l'id pour les références
    }

    // Remplacer l'élément original par l'éditeur
    element.parentNode.replaceChild(editor, element);

    // Création de la barre d'outils
    const toolbar = document.createElement("div");
    toolbar.id = "editor-toolbar";
    toolbar.classList.add(
        "editor-toolbar",
        "d-flex",
        "flex-wrap",
        "mb-2",
        "sticky-toolbar"
    );

    // Groupes d'outils
    const textFormattingGroup = document.createElement("div");
    textFormattingGroup.classList.add("btn-group", "me-2");
    enableBold(textFormattingGroup);
    enableItalic(textFormattingGroup);
    enableStrikethrough(textFormattingGroup);
    enableUnderline(textFormattingGroup, editor);
    enableTextCase(textFormattingGroup);
    toolbar.appendChild(textFormattingGroup);

    const headingGroup = document.createElement("div");
    headingGroup.classList.add("btn-group", "me-2");
    enableHeading(headingGroup);
    toolbar.appendChild(headingGroup);

    const listAndAlignGroup = document.createElement("div");
    listAndAlignGroup.classList.add("btn-group", "me-2");
    enableList(listAndAlignGroup);
    enableAlign(listAndAlignGroup);
    enableJustify(listAndAlignGroup);
    toolbar.appendChild(listAndAlignGroup);

    const mediaGroup = document.createElement("div");
    mediaGroup.classList.add("btn-group", "me-2");
    enableLink(mediaGroup, editor);
    enableImage(mediaGroup, editor);
    enableVideo(mediaGroup, editor);
    toolbar.appendChild(mediaGroup);

    const colorGroup = document.createElement("div");
    colorGroup.classList.add("btn-group", "me-2");
    enableHighlight(colorGroup, editor);
    enableColor(colorGroup, editor);
    toolbar.appendChild(colorGroup);

    const fontSizeGroup = document.createElement("div");
    fontSizeGroup.classList.add("btn-group", "me-2");
    enableFontSize(fontSizeGroup);
    toolbar.appendChild(fontSizeGroup);

    const codeGroup = document.createElement("div");
    codeGroup.classList.add("btn-group", "me-2");
    enableCode(codeGroup, editor);
    toolbar.appendChild(codeGroup);

    const undoRedoGroup = document.createElement("div");
    undoRedoGroup.classList.add("btn-group", "me-2");
    enableUndo(undoRedoGroup);
    enableRedo(undoRedoGroup);
    toolbar.appendChild(undoRedoGroup);

    const paragraphGroup = document.createElement("div");
    paragraphGroup.classList.add("btn-group", "me-2");
    enableParagraphs(paragraphGroup);
    toolbar.appendChild(paragraphGroup);

    // Insérer la barre d'outils avant l'éditeur
    editor.parentNode.insertBefore(toolbar, editor);

    // Ajout des fonctionnalités undo/redo via clavier
    document.addEventListener("keydown", function (e) {
        if (e.ctrlKey && e.key === "z") {
            document.execCommand("undo", false, null);
        } else if (e.ctrlKey && e.key === "y") {
            document.execCommand("redo", false, null);
        }
    });

    // Ajouter un écouteur pour la mise à jour des valeurs lors de la soumission du formulaire
    const form = editor.closest("form");
    if (form) {
        form.addEventListener("submit", function (event) {
            if (event.submitter && event.submitter.closest("#editor-toolbar")) {
                event.preventDefault();
            } else {
                const content = editor.innerHTML;
                const hiddenInput = document.createElement("input");
                hiddenInput.type = "hidden";
                hiddenInput.name =
                    editor.getAttribute("data-name") || "content";
                hiddenInput.value = content;
                form.appendChild(hiddenInput);
            }
        });
    }

    // Vérification de l'initialisation
    if (
        editor.hasAttribute("contenteditable") &&
        editor.classList.contains("form-control") &&
        editor.classList.contains("editor")
    ) {
        console.log(
            `L'éditeur pour ${
                editor.id || editor.className
            } a été initialisé avec succès.`
        );
    } else {
        console.error(
            `Échec de l'initialisation de l'éditeur pour ${
                editor.id || editor.className
            }.`
        );
    }
}
