// resources/js/editor/code.js

export function enableCode(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-code-slash"></i>`; // Icône de code de Bootstrap
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Insérer du code";

    // Création de la modal
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-code");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Insérer du code source</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="codeForm">
                        <div class="mb-3">
                            <label for="codeLanguage" class="form-label">Langage (optionnel)</label>
                            <input class="form-control" type="text" id="codeLanguage" name="codeLanguage" placeholder="Ex: JavaScript, Python...">
                        </div>
                        <div class="mb-3">
                            <label for="codeContent" class="form-label">Code source</label>
                            <textarea class="form-control" id="codeContent" name="codeContent" rows="5" placeholder="Entrez votre code ici..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeCodeModal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="submitCode">Insérer</button>
                </div>
            </div>
        </div>
    `;

    // Fonction générique pour gérer l'affichage et la fermeture des modals
    function manageModal(modalElement) {
        return {
            show: () => (modalElement.style.display = "block"),
            hide: () => {
                modalElement.style.display = "none";
                modalElement.querySelector("#codeLanguage").value = "";
                modalElement.querySelector("#codeContent").value = "";
            },
        };
    }

    const codeModal = manageModal(modal);

    // Ajout des écouteurs d'événements
    button.addEventListener("click", codeModal.show);

    // Fermer la modal
    modal.querySelector(".btn-close").addEventListener("click", codeModal.hide);
    modal
        .querySelector("#closeCodeModal")
        .addEventListener("click", codeModal.hide);

    // Fermer la modal si on clique en dehors
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            codeModal.hide();
        }
    });

    // Écouteur pour le bouton d'insertion de code
    modal.querySelector("#submitCode").addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const language =
            modal.querySelector("#codeLanguage").value.trim() || "plaintext";
        let code = modal.querySelector("#codeContent").value;

        if (code) {
            // Échapper les caractères HTML pour éviter l'interprétation
            code = code
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;");

            // Créer un bloc de code non interprété entouré de balises <pre><code>
            const codeBlock = document.createElement("pre");
            codeBlock.classList.add("code-block");
            codeBlock.style.backgroundColor = "#f8f9fa"; // Couleur de fond pour différencier
            codeBlock.style.border = "1px solid #dee2e6"; // Bordure légère
            codeBlock.style.padding = "10px"; // Espacement intérieur
            codeBlock.style.borderRadius = "4px"; // Coins arrondis
            codeBlock.style.overflowX = "auto"; // Scroll horizontal si nécessaire
            codeBlock.innerHTML = `<code class="language-${language.toLowerCase()}">${code}</code>`;

            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const range = selection.getRangeAt(0);
                if (!range.collapsed) {
                    // Remplacer la sélection existante par le bloc de code
                    range.deleteContents();
                }

                range.insertNode(codeBlock);
                range.collapse(false);

                // Ajouter un espace après le code pour éviter que l'utilisateur continue à taper dedans
                range.insertNode(document.createTextNode(" "));
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                // Si aucune sélection, ajouter le bloc de code à la fin du contenu de l'éditeur
                editor.appendChild(codeBlock);
            }

            codeModal.hide();
        } else {
            alert("Veuillez entrer du code avant de l'insérer.");
        }
    });

    document.body.appendChild(modal); // Ajouter la modal au body pour qu'elle soit visible
    toolbar.appendChild(button); // Ajouter le bouton à la barre d'outils
}
