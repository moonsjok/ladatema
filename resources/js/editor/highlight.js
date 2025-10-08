// resources/js/editor/highlight.js

export function enableHighlight(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-highlighter"></i>`; // Icône Bootstrap pour le surlignage
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Surligner";

    // Création de la modal
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-color-picker");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Surlignage</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="colorPicker" class="form-label">Choisir une couleur</label>
                        <div class="input-group">
                            <input class="form-control form-control-color" id="colorPicker" type="color" value="#FFFFFF">
                            <input class="form-control" type="text" id="colorCode" name="colorCode" placeholder="Ex: #FFFFFF" value="#FFFFFF" readonly>
                        </div>
                    </div>
                    <div class="mb-3 text-center" id="colorPreview" style="height: 30px; width: 100%; border: 1px solid #000; background-color: #FFFFFF;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeHighlightModal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="submitHighlight">Appliquer</button>
                </div>
            </div>
        </div>
    `;

    // Fonction générique pour gérer l'affichage et la fermeture des modals
    function manageModal(modalElement) {
        return {
            show: () => (modalElement.style.display = "block"),
            hide: () => (modalElement.style.display = "none"),
        };
    }

    const highlightModal = manageModal(modal);

    // Variable pour stocker la sélection actuelle
    let currentRange;

    // Ajout des écouteurs d'événements
    button.addEventListener("click", () => {
        const selection = window.getSelection();
        if (selection.rangeCount > 0) {
            currentRange = selection.getRangeAt(0).cloneRange(); // Cloner la sélection pour la conserver
        } else {
            currentRange = null;
        }
        highlightModal.show();
        // Mettre à jour la prévisualisation dès l'ouverture de la modal
        updatePreview();
    });

    // Fermer la modal
    modal
        .querySelector(".btn-close")
        .addEventListener("click", highlightModal.hide);
    modal
        .querySelector("#closeHighlightModal")
        .addEventListener("click", highlightModal.hide);

    // Fermer la modal si on clique en dehors
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            highlightModal.hide();
        }
    });

    // Prévisualisation de la couleur
    modal
        .querySelector("#colorPicker")
        .addEventListener("input", updatePreview);

    function updatePreview() {
        const color = modal.querySelector("#colorPicker").value;
        modal.querySelector("#colorPreview").style.backgroundColor = color;
        modal.querySelector("#colorCode").value = color; // Afficher le code couleur sélectionné
    }

    // Écouteur pour le bouton d'application du surlignage
    modal.querySelector("#submitHighlight").addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        const color = modal.querySelector("#colorPicker").value;

        if (color && currentRange) {
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(currentRange); // Réappliquer la sélection sauvegardée

            // Supprimer l'ancienne couleur de surlignage si elle existe
            const selectedNode = currentRange.commonAncestorContainer;
            if (selectedNode.nodeType === Node.TEXT_NODE) {
                const parent = selectedNode.parentNode;
                if (parent && parent.style.backgroundColor) {
                    parent.style.backgroundColor = ""; // Supprimer l'ancienne couleur
                    parent.outerHTML = parent.innerHTML; // Retirer le span si c'était le seul style
                }
            } else if (selectedNode.nodeType === Node.ELEMENT_NODE) {
                if (selectedNode.style.backgroundColor) {
                    selectedNode.style.backgroundColor = ""; // Supprimer l'ancienne couleur sur l'élément
                }
            }

            // Appliquer la nouvelle couleur de surlignage uniquement au texte sélectionné
            const span = document.createElement("span");
            span.style.backgroundColor = color;
            span.style.padding = "0 2px"; // Ajout d'un petit padding pour rendre le surlignage visible
            try {
                currentRange.surroundContents(span);
            } catch (e) {
                // Si la sélection ne peut pas être entourée (par exemple, si elle contient des éléments HTML), appliquer le style en ligne
                const text = currentRange.extractContents();
                span.appendChild(text);
                currentRange.insertNode(span);
            }

            // Restaurer la sélection pour permettre de continuer à éditer
            const newRange = document.createRange();
            newRange.selectNodeContents(span);
            selection.removeAllRanges();
            selection.addRange(newRange);
        } else if (!currentRange) {
            alert("Veuillez sélectionner du texte avant de surligner.");
        } else {
            alert("Veuillez choisir ou entrer une couleur valide.");
        }
        highlightModal.hide();
    });

    document.body.appendChild(modal); // Ajouter la modal au body pour qu'elle soit visible
    toolbar.appendChild(button); // Ajout du bouton à la barre d'outils
}
