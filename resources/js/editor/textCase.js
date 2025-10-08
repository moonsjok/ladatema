// resources/js/editor/textCase.js
export function enableTextCase(toolbar) {
    const button = document.createElement("button");

    // Utilisation de l'icône Bootstrap si disponible
    if (document.querySelector('link[href*="bootstrap"]')) {
        button.innerHTML = `<i class="bi bi-text-uppercase"></i>`;
    } else {
        // Alternative si Bootstrap n'est pas disponible
        button.innerHTML = "Aa";
    }

    button.classList.add("btn", "btn-outline-dark");

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const selection = window.getSelection();
        const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;

        if (range) {
            const selectedText = selection.toString();
            if (selectedText) {
                const isUpperCase = selectedText === selectedText.toUpperCase();
                const transformedText = isUpperCase
                    ? selectedText.toLowerCase()
                    : selectedText.toUpperCase();

                // Remplace le texte sélectionné par le texte transformé
                range.deleteContents();
                range.insertNode(document.createTextNode(transformedText));
            }
        }
    });

    toolbar.appendChild(button);
}
