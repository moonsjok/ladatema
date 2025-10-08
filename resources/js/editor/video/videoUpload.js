// video/videoUpload.js
import { getVideoEmbedCode, insertMedia } from "./videoUtils.js";

export function uploadVideo(
    file,
    alignment,
    statusElement,
    modalManagement,
    editor,
    savedRange
) {
    const formData = new FormData();
    formData.append("file", file);

    // Afficher l'indicateur d'activité
    statusElement.innerHTML =
        '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>';

    fetch("/upload-video", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error("Réponse du réseau non valide");
            return response.json();
        })
        .then((data) => {
            if (data.location) {
                const videoEmbedCode = getVideoEmbedCode(
                    data.location,
                    file.type.split("/")[1],
                    alignment
                );
                insertMedia(videoEmbedCode, editor, savedRange);
                modalManagement.hide();
            } else {
                throw new Error(
                    data.error || "Erreur lors de l'upload de la vidéo."
                );
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            statusElement.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        });
}
