// video/videoButton.js
import { videoModal } from "./videoModal.js";

let savedRange = null;

export function createVideoButton(toolbar, editor, savedRange) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-film"></i>`;
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Ajouter une vidéo";
    button.addEventListener("click", () => {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && editor.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0);
        }
        videoModal.show(); // Maintenant videoModal est accessible
    });
    toolbar.appendChild(button);
}
