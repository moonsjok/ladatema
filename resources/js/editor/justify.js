// resources/js/editor/justify.js
export function enableJustify(toolbar) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-justify"></i>`; // Icône pour justification
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Justifier le texte";
    let isJustified = false;

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        if (isJustified) {
            document.execCommand("justifyLeft", false, null); // Annule la justification
        } else {
            document.execCommand("justifyFull", false, null); // Applique la justification
        }
        isJustified = !isJustified; // Inverse l'état pour le prochain clic
    });

    toolbar.appendChild(button);
}
