// resources/js/editor/link.js

export function enableLink(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-link-45deg"></i>`; // Icône Bootstrap pour le lien
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Ajouter ou Éditer un lien";

    // Création de la modal
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-link");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter ou Éditer un lien</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="linkForm">
                        <div class="mb-3">
                            <label for="linkUrl" class="form-label">URL du lien</label>
                            <input class="form-control" type="text" id="linkUrl" name="linkUrl" placeholder="Entrez l'URL du lien">
                        </div>
                        <div class="mb-3">
                            <label for="linkText" class="form-label">Texte du lien (optionnel)</label>
                            <input class="form-control" type="text" id="linkText" name="linkText" placeholder="Texte à afficher">
                        </div>
                    </form>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary w-100" id="previewLink">Prévisualiser</button>
                    </div>
                    <div id="previewArea" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeLinkModal">Fermer</button>
                    <button type="button" class="btn btn-danger" id="removeLink" style="display: none;">Supprimer le lien</button>
                    <button type="button" class="btn btn-primary" id="submitLink">Ajouter</button>
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
                modalElement.querySelector("#linkUrl").value = "";
                modalElement.querySelector("#linkText").value = "";
                modalElement.querySelector("#previewArea").innerHTML = "";
                modalElement.querySelector("#removeLink").style.display =
                    "none"; // Réinitialiser l'affichage
            },
        };
    }

    const linkModal = manageModal(modal);

    let savedRange = null; // Sauvegarde de la sélection dans l'éditeur

    // Ajout des écouteurs d'événements
    button.addEventListener("click", () => {
        // Sauvegarder la sélection avant d'ouvrir la modal
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            savedRange = selection.getRangeAt(0);
            const selectedText = selection.toString();

            if (savedRange.startContainer.parentNode.tagName === "A") {
                const linkElement = savedRange.startContainer.parentNode;
                modal.querySelector("#linkUrl").value = linkElement.href;
                modal.querySelector("#linkText").value =
                    linkElement.textContent;
                modal.querySelector("#removeLink").style.display = "block"; // Afficher le bouton de suppression
            } else if (selectedText) {
                modal.querySelector("#linkText").value = selectedText;
            }
        }
        linkModal.show();
    });

    // Fermer la modal
    modal.querySelector(".btn-close").addEventListener("click", linkModal.hide);
    modal
        .querySelector("#closeLinkModal")
        .addEventListener("click", linkModal.hide);

    // Fermer la modal si on clique en dehors
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            linkModal.hide();
        }
    });

    // Prévisualisation du lien
    modal.querySelector("#previewLink").addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const linkUrl = modal.querySelector("#linkUrl").value;
        const linkText = modal.querySelector("#linkText").value || linkUrl; // Si pas de texte, utiliser l'URL
        const previewArea = modal.querySelector("#previewArea");
        previewArea.innerHTML = `<a href="${linkUrl}" target="_blank">${linkText}</a>`;
    });

    // Écouteur pour le bouton d'ajout de lien
    modal.querySelector("#submitLink").addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const linkUrl = modal.querySelector("#linkUrl").value;
        const linkText = modal.querySelector("#linkText").value;

        if (linkUrl) {
            if (savedRange) {
                const selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(savedRange);

                // Vérifier si la sélection est déjà un lien
                if (savedRange.startContainer.parentNode.tagName === "A") {
                    const linkElement = savedRange.startContainer.parentNode;
                    linkElement.href = linkUrl;
                    linkElement.textContent = linkText || savedRange.toString();
                } else {
                    // Sinon, créer un nouveau lien
                    const link = document.createElement("a");
                    link.href = linkUrl;
                    link.target = "_blank";
                    link.textContent = linkText || savedRange.toString();
                    savedRange.deleteContents();
                    savedRange.insertNode(link);
                }

                savedRange = null; // Réinitialiser la sélection sauvegardée
            } else {
                // Si aucune sélection, insérer un lien à la fin du contenu de l'éditeur
                const linkTextToUse = linkText || linkUrl;
                editor.innerHTML += `<a href="${linkUrl}" target="_blank">${linkTextToUse}</a>`;
            }
            linkModal.hide();
        } else {
            alert("Veuillez entrer une URL valide.");
        }
    });

    // Écouteur pour le bouton de suppression de lien
    modal.querySelector("#removeLink").addEventListener("click", () => {
        if (
            savedRange &&
            savedRange.startContainer.parentNode.tagName === "A"
        ) {
            const linkElement = savedRange.startContainer.parentNode;
            const textNode = document.createTextNode(linkElement.textContent);

            // Remplacer le lien par un texte brut
            linkElement.parentNode.replaceChild(textNode, linkElement);

            linkModal.hide();
        } else {
            alert("Aucun lien sélectionné à supprimer.");
        }
    });

    document.body.appendChild(modal); // Ajouter la modal au body pour qu'elle soit visible
    toolbar.appendChild(button); // Ajout du bouton à la barre d'outils
}
