// resources/js/editor/underline.js

export function enableUnderline(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-type-underline"></i>`; // Icône pour soulignement
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Souligner le texte";

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            const range = selection.getRangeAt(0);
            // Vérifier si le texte est déjà souligné
            const isUnderlined = document.queryCommandState("underline");

            if (isUnderlined) {
                // Si le texte est déjà souligné, on le rend normal
                document.execCommand("removeFormat", false, null);
            } else {
                // Sinon, on le souligne
                document.execCommand("underline", false, null);
            }
        } else {
            // Si aucun texte n'est sélectionné, on pourrait vouloir souligner tout le contenu
            // ou informer l'utilisateur de sélectionner du texte
            alert(
                "Veuillez sélectionner du texte pour appliquer le soulignement."
            );
        }
    });

    toolbar.appendChild(button);
}
