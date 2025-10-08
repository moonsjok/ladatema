// resources/js/editor/undo.js
export function enableUndo(toolbar) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-arrow-counterclockwise"></i>`; // Icône pour undo
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Annuler (Undo)";

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        document.execCommand("undo", false, null);
    });

    toolbar.appendChild(button);
}
