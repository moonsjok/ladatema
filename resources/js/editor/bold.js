// resources/js/editor/bold.js
export function enableBold(toolbar) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-type-bold"></i>`;
    button.classList.add("btn", "btn-outline-dark");

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const selection = window.getSelection();
        if (!selection.rangeCount) return;
        const range = selection.getRangeAt(0);
        if (document.queryCommandState("bold")) {
            // Si le texte est déjà en gras, on le rend normal
            document.execCommand("removeFormat", false, null);
        } else {
            // Sinon, on le met en gras
            document.execCommand("bold", false, null);
        }
    });

    toolbar.appendChild(button);
}
