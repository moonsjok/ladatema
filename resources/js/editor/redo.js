// resources/js/editor/redo.js
export function enableRedo(toolbar) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-arrow-clockwise"></i>`; // Icône pour redo
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Refaire (Redo)";

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        document.execCommand("redo", false, null);
    });

    toolbar.appendChild(button);
}
