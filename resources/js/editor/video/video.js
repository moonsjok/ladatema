//video.js
import { createVideoButton } from "./videoButton.js";
import { videoModal } from "./videoModal.js";
import { getEmbedUrl, getVideoEmbedCode, insertMedia } from "./videoUtils.js";
import { uploadVideo } from "./videoUpload.js";

export let savedRange = null; // Déclaration de savedRange ici

export function enableVideo(toolbar, editor) {
    createVideoButton(toolbar, editor, savedRange);

    // Assurez-vous que videoModal est accessible ici, il est déjà initialisé dans videoModal.js
    // Vous n'avez donc pas besoin de le recréer ici

    // Vous pouvez utiliser ces fonctions directement ici si nécessaire
    // Par exemple, si vous avez besoin d'utiliser une fonction de videoUtils directement dans ce contexte
    // getEmbedUrl, getVideoEmbedCode, insertMedia sont accessibles ici

    // Si vous avez besoin d'ajouter des fonctionnalités supplémentaires ou de gérer d'autres interactions ici, vous pouvez le faire.
}
