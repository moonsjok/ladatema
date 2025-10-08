// resources/js/editor/strikethrough.js
export function enableStrikethrough(toolbar) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-type-strikethrough"></i>`; // Icône pour texte barré
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Texte barré";

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        if (document.queryCommandState("strikethrough")) {
            // Si le texte est déjà barré, on le rend normal
            document.execCommand("removeFormat", false, null);
        } else {
            // Sinon, on le barre
            document.execCommand("strikethrough", false, null);
        }
    });

    toolbar.appendChild(button);
}
