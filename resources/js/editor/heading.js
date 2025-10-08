// resources/js/editor/heading.js
export function enableHeading(toolbar) {
    const levels = ["H1", "H2", "H3", "H4", "H5", "H6"];
    levels.forEach((level) => {
        const button = document.createElement("button");
        button.innerHTML = `<i class="bi bi-type-${level.toLowerCase()}"></i>`;
        button.classList.add("btn", "btn-outline-dark");

        button.addEventListener("click", (e) => {
            e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
            const selection = window.getSelection();
            const range =
                selection.rangeCount > 0 ? selection.getRangeAt(0) : null;

            if (range) {
                const parentElement = selection.anchorNode.parentElement;

                // Vérifie si le texte est déjà dans le même niveau (H1, H2, etc.)
                if (
                    parentElement &&
                    parentElement.tagName === level.toUpperCase()
                ) {
                    // Si oui, on remplace par un paragraphe (annule le style)
                    document.execCommand("formatBlock", false, "<p>");
                } else {
                    // Sinon, on applique le style
                    document.execCommand(
                        "formatBlock",
                        false,
                        `<${level.toLowerCase()}>`
                    );
                }
            }
        });

        toolbar.appendChild(button);
    });
}
