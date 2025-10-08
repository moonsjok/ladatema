export function enableImage(toolbar, editor) {
    const button = document.createElement("button");
    button.innerHTML = `<i class="bi bi-image"></i>`;
    button.classList.add("btn", "btn-outline-dark");
    button.title = "Ajouter une image";

    // Création de la modale
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-image");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une image</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="imageForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="imageUpload" class="form-label">Uploader une image</label>
                            <input class="form-control" type="file" id="imageUpload" name="file" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label for="imageEmbed" class="form-label">Ou intégrer une image via URL</label>
                            <input class="form-control" type="text" id="imageEmbed" name="imageEmbed" placeholder="Entrez l'URL de l'image">
                        </div>
                        <div class="mb-3">
                            <label for="imageSize" class="form-label">Dimensions (en pixels, optionnel)</label>
                            <div class="d-flex gap-2">
                                <input class="form-control" type="number" id="imageWidth" name="imageWidth" placeholder="Largeur (px)">
                                <input class="form-control" type="number" id="imageHeight" name="imageHeight" placeholder="Hauteur (px)">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="imageAlignment" class="form-label">Alignement</label>
                            <select class="form-select" id="imageAlignment">
                                <option value="center" selected>Centré</option>
                                <option value="left">Gauche</option>
                                <option value="right">Droite</option>
                            </select>
                        </div>
                        <div id="previewArea" class="mb-3"></div>
                        <div id="uploadStatus" class="mb-3"></div>
                    </form>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary w-100" id="previewImage">Prévisualiser</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeImageModal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="submitImage">Ajouter</button>
                </div>
            </div>
        </div>
    `;

    // Fonction générique pour gérer l'affichage et la fermeture des modales
    function manageModal(modalElement) {
        return {
            show: () => (modalElement.style.display = "block"),
            hide: () => {
                modalElement.style.display = "none";
                modal.querySelector("#imageEmbed").value = "";
                modal.querySelector("#imageWidth").value = "";
                modal.querySelector("#imageHeight").value = "";
                modal.querySelector("#imageAlignment").value = "center"; // Réinitialiser à centré
                modal.querySelector("#previewArea").innerHTML = "";
                modal.querySelector("#imageUpload").value = "";
                modal.querySelector("#uploadStatus").innerHTML = "";
            },
        };
    }

    const imageModal = manageModal(modal);

    let savedRange = null;

    // Sauvegarder la position du curseur
    button.addEventListener("click", () => {
        const selection = window.getSelection();
        if (selection.rangeCount > 0 && editor.contains(selection.anchorNode)) {
            savedRange = selection.getRangeAt(0);
        }
        imageModal.show();
    });

    // Fermer la modale
    modal
        .querySelector(".btn-close")
        .addEventListener("click", imageModal.hide);
    modal
        .querySelector("#closeImageModal")
        .addEventListener("click", imageModal.hide);

    // Fermer la modale si on clique en dehors
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            imageModal.hide();
        }
    });

    // Prévisualisation de l'image
    modal.querySelector("#previewImage").addEventListener("click", (e) => {
        e.preventDefault();
        const imageUpload = modal.querySelector("#imageUpload").files[0];
        const imageEmbed = modal.querySelector("#imageEmbed").value;
        const width = modal.querySelector("#imageWidth").value;
        const height = modal.querySelector("#imageHeight").value;
        const alignment = modal.querySelector("#imageAlignment").value;
        const previewArea = modal.querySelector("#previewArea");

        let style = "max-width: 100%; height: auto;";
        if (width) style += ` width: ${width}px;`;
        if (height) style += ` height: ${height}px;`;
        style += ` ${
            alignment === "center"
                ? "margin-left: auto; margin-right: auto; display: block;"
                : "float: " + alignment + "; clear: both;"
        }`;

        if (imageUpload) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewArea.innerHTML = `<img src="${e.target.result}" alt="Prévisualisation" style="${style}">`;
            };
            reader.readAsDataURL(imageUpload);
        } else if (imageEmbed) {
            previewArea.innerHTML = `<img src="${imageEmbed}" alt="Prévisualisation" style="${style}">`;
        } else {
            previewArea.innerHTML = "<p>Aucune image à prévisualiser.</p>";
        }
    });

    // Écouteur pour le bouton d'ajout d'image - mise à jour pour gérer l'upload
    modal.querySelector("#submitImage").addEventListener("click", (e) => {
        e.preventDefault();
        const imageUpload = modal.querySelector("#imageUpload").files[0];
        const imageEmbed = modal.querySelector("#imageEmbed").value;
        const width = modal.querySelector("#imageWidth").value;
        const height = modal.querySelector("#imageHeight").value;
        const alignment = modal.querySelector("#imageAlignment").value;
        const uploadStatus = modal.querySelector("#uploadStatus");

        if (imageUpload) {
            uploadStatus.innerHTML =
                '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>';

            const formData = new FormData();
            formData.append("file", imageUpload);

            fetch("/upload-image", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    uploadStatus.innerHTML = "";
                    if (data.location) {
                        let style = "max-width: 100%; height: auto;";
                        if (width) style += ` width: ${width}px;`;
                        if (height) style += ` height: ${height}px;`;
                        style += ` ${
                            alignment === "center"
                                ? "margin-left: auto; margin-right: auto; display: block;"
                                : "float: " + alignment + "; clear: both;"
                        }`;
                        const imageEmbedCode = `<br><img src="${data.location}" alt="Image insérée" style="${style}"><br>`;

                        insertImage(imageEmbedCode);
                        imageModal.hide();
                    } else {
                        uploadStatus.innerHTML = `<div class="alert alert-danger">${
                            data.error || "Erreur lors de l'upload de l'image."
                        }</div>`;
                    }
                })
                .catch((error) => {
                    uploadStatus.innerHTML = "";
                    console.error("Error:", error);
                    uploadStatus.innerHTML = `<div class="alert alert-danger">Une erreur est survenue lors de l'upload de l'image.</div>`;
                });
        } else if (imageEmbed) {
            let style = "max-width: 100%; height: auto;";
            if (width) style += ` width: ${width}px;`;
            if (height) style += ` height: ${height}px;`;
            style += ` ${
                alignment === "center"
                    ? "margin-left: auto; margin-right: auto; display: block;"
                    : "float: " + alignment + "; clear: both;"
            }`;
            const imageEmbedCode = `<br><img src="${imageEmbed}" alt="Image insérée" style="${style}"><br>`;

            insertImage(imageEmbedCode);
            imageModal.hide();
        } else {
            uploadStatus.innerHTML = `<div class="alert alert-warning">Veuillez soit uploader une image soit entrer une URL valide.</div>`;
        }
    });

    function insertImage(content) {
        const selection = window.getSelection();

        if (savedRange && editor.contains(savedRange.startContainer)) {
            selection.removeAllRanges();
            selection.addRange(savedRange);
            const range = selection.getRangeAt(0);
            range.insertNode(range.createContextualFragment(content));

            // Sélectionner l'image après insertion
            const imageElement = editor.querySelector("img");
            if (imageElement) {
                const range = document.createRange();
                range.selectNode(imageElement);
                selection.removeAllRanges();
                selection.addRange(range);
            }

            range.collapse(false);
            const textNode = document.createTextNode(" ");
            range.insertNode(textNode);
            if (textNode.previousSibling) {
                range.setStartAfter(textNode.previousSibling);
            } else {
                // Si textNode.previousSibling est null, on peut définir le début du range juste avant textNode
                range.setStartBefore(textNode);
            }
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            editor.innerHTML += content;
        }
    }

    document.body.appendChild(modal);
    toolbar.appendChild(button);
}
