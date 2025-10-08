// video/videoModal.js

import { getEmbedUrl, getVideoEmbedCode, insertMedia } from "./videoUtils.js";
import { uploadVideo } from "./videoUpload.js";

export function createVideoModal(editor, savedRange) {
    const modal = document.createElement("div");
    modal.classList.add("modal-custom", "modal-video");
    modal.style.display = "none";
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une vidéo</h5>
                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="videoForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="videoUpload" class="form-label">Uploader une vidéo</label>
                            <input class="form-control" type="file" id="videoUpload" name="file" accept="video/*">
                        </div>
                        <div class="mb-3">
                            <label for="videoEmbed" class="form-label">Ou intégrer une vidéo via URL</label>
                            <input class="form-control" type="text" id="videoEmbed" name="videoEmbed" placeholder="Entrez l'URL de la vidéo">
                        </div>
                        <div class="mb-3">
                            <label for="videoAlignment" class="form-label">Alignement</label>
                            <select class="form-select" id="videoAlignment">
                                <option value="center" selected>Centré</option>
                                <option value="left">Gauche</option>
                                <option value="right">Droite</option>
                            </select>
                        </div>
                        <div id="previewArea" class="mb-3"></div>
                        <div id="uploadStatus" class="mb-3 text-center"></div>
                    </form>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary w-100" id="previewVideo">Prévisualiser</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeVideoModal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="submitVideo">Ajouter</button>
                </div>
            </div>
        </div>
    `;

    function manageModal(modalElement) {
        return {
            show: () => (modalElement.style.display = "block"),
            hide: () => {
                modalElement.style.display = "none";
                const videoEmbed = modal.querySelector("#videoEmbed");
                const videoAlignment = modal.querySelector("#videoAlignment");
                const previewArea = modal.querySelector("#previewArea");
                const videoUpload = modal.querySelector("#videoUpload");
                const uploadStatus = modal.querySelector("#uploadStatus");

                if (videoEmbed) videoEmbed.value = "";
                if (videoAlignment) videoAlignment.value = "center";
                if (previewArea) previewArea.innerHTML = "";
                if (videoUpload) videoUpload.value = "";
                if (uploadStatus) uploadStatus.innerHTML = "";
            },
        };
    }

    const videoModal = manageModal(modal);

    // Fermer la modal
    modal
        .querySelector(".btn-close")
        ?.addEventListener("click", videoModal.hide);
    modal
        .querySelector("#closeVideoModal")
        ?.addEventListener("click", videoModal.hide);

    // Fermer la modal si clic à l'extérieur
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            videoModal.hide();
        }
    });

    // Prévisualisation de la vidéo
    modal.querySelector("#previewVideo")?.addEventListener("click", (e) => {
        e.preventDefault();
        const videoEmbed = modal.querySelector("#videoEmbed")?.value;
        const videoUpload = modal.querySelector("#videoUpload")?.files[0];
        const previewArea = modal.querySelector("#previewArea");

        if (previewArea) {
            if (videoUpload) {
                const fileURL = URL.createObjectURL(videoUpload);
                previewArea.innerHTML = `<video controls style="max-width:100%; height: auto;"><source src="${fileURL}" type="${videoUpload.type}"></video>`;
            } else if (videoEmbed) {
                const embedUrl = getEmbedUrl(videoEmbed);
                if (embedUrl.includes("dailymotion.com")) {
                    previewArea.innerHTML = `<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"><iframe src="${embedUrl}" style="width:100%;height:100%;position:absolute;left:0;top:0;overflow:hidden;border:none;" allowfullscreen title="Dailymotion Video Player" allow="web-share"></iframe></div>`;
                } else {
                    previewArea.innerHTML = `<div class="video-container"><iframe width="560" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe></div>`;
                }
            } else {
                previewArea.innerHTML = "<p>Aucune vidéo à prévisualiser.</p>";
            }
        }
    });

    // Ajout de la vidéo dans l'éditeur ou upload de la vidéo
    modal.querySelector("#submitVideo")?.addEventListener("click", (e) => {
        e.preventDefault();
        const videoUpload = modal.querySelector("#videoUpload")?.files[0];
        const videoEmbed = modal.querySelector("#videoEmbed")?.value;
        const alignment = modal.querySelector("#videoAlignment")?.value;
        const uploadStatus = modal.querySelector("#uploadStatus");

        if (uploadStatus) {
            if (videoUpload) {
                uploadStatus.innerHTML =
                    '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>';
                uploadVideo(
                    videoUpload,
                    alignment,
                    uploadStatus,
                    videoModal,
                    editor
                );
            } else if (videoEmbed) {
                const embedUrl = getEmbedUrl(videoEmbed);
                const videoEmbedCode = getVideoEmbedCode(
                    embedUrl,
                    null,
                    alignment,
                    embedUrl.includes("dailymotion.com")
                );
                insertMedia(videoEmbedCode, editor, savedRange);
                videoModal.hide();
            } else {
                uploadStatus.innerHTML =
                    '<div class="alert alert-warning text-center">Veuillez soit uploader une vidéo soit entrer une URL valide.</div>';
            }
        } else {
            console.error("L'élément #uploadStatus n'est pas trouvé.");
        }
    });

    document.body.appendChild(modal);
    return videoModal;
}

export const videoModal = createVideoModal();
