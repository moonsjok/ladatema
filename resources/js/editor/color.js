// resources/js/editor/color.js

export function enableColor(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-palette"></i>`; // Icône Bootstrap pour la couleur
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Changer la couleur du texte";

    // Création de la modal
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-color-picker");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Changer la couleur du texte</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="colorForm">
                        <div class="mb-3">
                            <label for="colorPicker" class="form-label">Choisir une couleur</label>
                            <input class="form-control form-control-color" id="colorPicker" type="color" value="#000000">
                        </div>
                        <div class="mb-3">
                            <label for="colorCode" class="form-label">Ou entrer un code couleur</label>
                            <input class="form-control" type="text" id="colorCode" name="colorCode" placeholder="Ex: #FF0000">
                        </div>
                    </form>
                    <div class="mb-3 text-center" id="colorPreview" style="height: 30px; width: 100%; border: 1px solid #000; color: #000000; background-color: #FFFFFF;">Aperçu de la couleur</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeColorModal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="submitColor">Appliquer</button>
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
                modalElement.querySelector("#colorPicker").value = "#000000";
                modalElement.querySelector("#colorCode").value = "";
                modalElement.querySelector("#colorPreview").style.color =
                    "#000000";
            },
        };
    }

    const colorModal = manageModal(modal);

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
        colorModal.show();
        // Mettre à jour la prévisualisation dès l'ouverture de la modal
        updatePreview();
    });

    // Fermer la modal
    modal
        .querySelector(".btn-close")
        .addEventListener("click", colorModal.hide);
    modal
        .querySelector("#closeColorModal")
        .addEventListener("click", colorModal.hide);

    // Fermer la modal si on clique en dehors
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            colorModal.hide();
        }
    });

    // Prévisualisation de la couleur
    modal
        .querySelector("#colorPicker")
        .addEventListener("input", updatePreview);
    modal.querySelector("#colorCode").addEventListener("input", updatePreview);

    function updatePreview() {
        let color = modal.querySelector("#colorPicker").value;
        if (modal.querySelector("#colorCode").value) {
            color = modal.querySelector("#colorCode").value;
        }
        modal.querySelector("#colorPreview").style.color = color;
    }

    // Écouteur pour le bouton d'application de la couleur
    modal.querySelector("#submitColor").addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        let color = modal.querySelector("#colorPicker").value;
        if (modal.querySelector("#colorCode").value) {
            color = modal.querySelector("#colorCode").value;
        }

        if (color && currentRange) {
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(currentRange); // Réappliquer la sélection sauvegardée

            // Appliquer la nouvelle couleur au texte sélectionné
            const span = document.createElement("span");
            span.style.color = color;
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
            alert(
                "Veuillez sélectionner du texte avant de changer sa couleur."
            );
        } else {
            alert("Veuillez choisir ou entrer une couleur valide.");
        }
        colorModal.hide();
    });

    document.body.appendChild(modal); // Ajouter la modal au body pour qu'elle soit visible
    toolbar.appendChild(button); // Ajout du bouton à la barre d'outils
}
