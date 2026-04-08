import {
  getEmbedUrl,
  getVideoEmbedCode,
  insertMedia,
  isLocalVideo,
  normalizeLocalVideoUrl,
} from "./video/videoUtils.js";

export function enableVideo(toolbar, editor) {
  const button = document.createElement("button");
  button.innerHTML = `<i class="bi bi-film"></i>`;
  button.classList.add("btn", "btn-outline-dark");
  button.title = "Ajouter une vidéo";

  // Création de la modal
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
                <input class="form-control" type="text" id="videoEmbed" name="videoEmbed" placeholder="Entrez l'URL de la vidéo (ex: https://ladatema.kom/media-file/21)">
            </div> 
          
            <div id="previewArea" class="mb-3 text-center"></div>
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

  // Fonction générique pour gérer la modal
  function manageModal(modalElement) {
    return {
      show: () => (modalElement.style.display = "block"),
      hide: () => {
        modalElement.style.display = "none";
        modal.querySelector("#videoEmbed").value = "";

        modal.querySelector("#previewArea").innerHTML = "";
        modal.querySelector("#videoUpload").value = "";
        modal.querySelector("#uploadStatus").innerHTML = "";
      },
    };
  }

  const videoModal = manageModal(modal);
  let savedRange = null;

  // Sauvegarde de la position du curseur
  button.addEventListener("click", () => {
    const selection = window.getSelection();
    if (selection.rangeCount > 0 && editor.contains(selection.anchorNode)) {
      savedRange = selection.getRangeAt(0);
    }
    videoModal.show();
  });

  // Fonction pour convertir l'URL vidéo en URL d'intégration
  function getEmbedUrl(url) {
    const youtubeRegex =
      /(?:youtube\.com\/(?:[^/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?/\s]{11})/;
    const tiktokRegex = /tiktok\.com\/([^/]+)\/video\/(\d+)/;
    const facebookRegex = /facebook\.com\/([^/]+)\/videos\/([^/]+)/;
    const vimeoRegex = /vimeo\.com\/(\d+)/;
    const dailymotionRegexFull = /dailymotion\.com\/video\/([a-zA-Z0-9]+)/;
    const dailymotionRegexShort = /dai\.ly\/([a-zA-Z0-9]+)/;

    if (youtubeRegex.test(url)) {
      const match = url.match(youtubeRegex);
      return `https://www.youtube.com/embed/${match[1]}`;
    } else if (tiktokRegex.test(url)) {
      const match = url.match(tiktokRegex);
      return `https://www.tiktok.com/embed/v2/${match[2]}`;
    } else if (facebookRegex.test(url)) {
      const match = url.match(facebookRegex);
      return `https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2F${match[1]}%2Fvideos%2F${match[2]}%2F`;
    } else if (vimeoRegex.test(url)) {
      const match = url.match(vimeoRegex);
      return `https://player.vimeo.com/video/${match[1]}`;
    } else if (
      dailymotionRegexFull.test(url) ||
      dailymotionRegexShort.test(url)
    ) {
      const match =
        url.match(dailymotionRegexFull) || url.match(dailymotionRegexShort);
      return `https://geo.dailymotion.com/player.html?video=${match[1]}`;
    }
    return url; // Si aucun match, retourne l'URL telle quelle
  }

  // Fermer la modal
  modal.querySelector(".btn-close").addEventListener("click", videoModal.hide);
  modal
    .querySelector("#closeVideoModal")
    .addEventListener("click", videoModal.hide);

  // Fermer la modal si clic à l'extérieur
  window.addEventListener("click", (event) => {
    if (event.target == modal) {
      videoModal.hide();
    }
  });

  // Prévisualisation de la vidéo
  modal.querySelector("#previewVideo").addEventListener("click", (e) => {
    e.preventDefault();
    const videoEmbed = modal.querySelector("#videoEmbed").value;
    const videoUpload = modal.querySelector("#videoUpload").files[0];
    const previewArea = modal.querySelector("#previewArea");

    if (videoUpload) {
      const fileURL = URL.createObjectURL(videoUpload);
      const videoElement = document.createElement("video");
      videoElement.controls = true;
      videoElement.style.maxWidth = "calc(100% - 20px)"; // Largeur maximale avec marge
      videoElement.style.height = "auto";

      const sourceElement = document.createElement("source");
      sourceElement.src = fileURL;
      sourceElement.type = videoUpload.type;
      videoElement.appendChild(sourceElement);
      previewArea.innerHTML = "";
      previewArea.appendChild(videoElement);
    } else if (videoEmbed) {
      const embedUrl = getEmbedUrl(videoEmbed);

      // Si c'est une vidéo locale, utiliser la balise video directement
      if (isLocalVideo(videoEmbed)) {
        const normalizedUrl = normalizeLocalVideoUrl(videoEmbed);
        previewArea.innerHTML = `
          <video controls style="max-width: 100%; height: auto;">
            <source src="${normalizedUrl}" type="video/mp4">
            Votre navigateur ne supporte pas les vidéos.
          </video>
        `;
      } else if (embedUrl.includes("dailymotion.com")) {
        previewArea.innerHTML = `<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"><iframe src="${embedUrl}" style="width:calc(100% - 20px);height:100%;position:absolute;left:0;top:0;overflow:hidden;border:none;" allowfullscreen title="Dailymotion Video Player" allow="web-share"></iframe></div>`;
      } else {
        const iframeElement = document.createElement("iframe");
        iframeElement.src = embedUrl;
        iframeElement.style.maxWidth = "calc(100% - 20px)"; // Largeur maximale avec marge
        iframeElement.style.height = "auto";

        iframeElement.frameBorder = "0";
        iframeElement.allowFullscreen = true;
        previewArea.innerHTML = "";
        previewArea.appendChild(iframeElement);
      }
    } else {
      previewArea.innerHTML = "<p>Aucune vidéo à prévisualiser.</p>";
    }
  });

  // Ajout de la vidéo dans l'éditeur ou upload de la vidéo
  modal.querySelector("#submitVideo").addEventListener("click", (e) => {
    e.preventDefault();
    const videoUpload = modal.querySelector("#videoUpload").files[0];
    const videoEmbed = modal.querySelector("#videoEmbed").value;

    const uploadStatus = modal.querySelector("#uploadStatus");

    if (videoUpload) {
      uploadStatus.innerHTML =
        '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>';

      const formData = new FormData();
      formData.append("file", videoUpload);

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
          if (!response.ok) {
            throw new Error("Réponse du réseau non valide");
          }
          return response.json();
        })
        .then((data) => {
          uploadStatus.innerHTML = "";
          if (data.location) {
            const videoEmbedCode = getVideoEmbedCode(
              data.location,
              videoUpload.type.split("/")[1],
            );
            insertMedia(videoEmbedCode);
            videoModal.hide(); // Fermer automatiquement le modal après l'insertion réussie
          } else {
            throw new Error(
              data.error || "Erreur lors de l'upload de la vidéo.",
            );
          }
        })
        .catch((error) => {
          uploadStatus.innerHTML = "";
          console.error("Error:", error);
          uploadStatus.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
        });
    } else if (videoEmbed) {
      const embedUrl = getEmbedUrl(videoEmbed);
      const videoEmbedCode = getVideoEmbedCode(
        embedUrl,
        null,
        false, // Pas Dailymotion par défaut
      );
      insertMedia(videoEmbedCode, editor, savedRange);
      videoModal.hide(); // Fermer automatiquement le modal après l'insertion réussie
    } else {
      uploadStatus.innerHTML = `<div class="alert alert-warning">Veuillez soit uploader une vidéo soit entrer une URL valide.</div>`;
    }
  });

  document.body.appendChild(modal);
  toolbar.appendChild(button);
}
