// video/videoUtils.js
export function getEmbedUrl(url) {
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

export function getVideoEmbedCode(
    src,
    type = null,
    alignment = "center",
    isDailymotion = false
) {
    let style = `style="max-width: 100%; height: auto; ${
        alignment === "center"
            ? "margin-left: auto; margin-right: auto; display: block;"
            : "float: " + alignment + "; clear: both;"
    }"`;
    if (isDailymotion) {
        return `<br><div ${style}><div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;"><iframe src="${src}" style="width:100%;height:100%;position:absolute;left:0;top:0;overflow:hidden;border:none;" allowfullscreen title="Dailymotion Video Player" allow="web-share"></iframe></div></div><br>`;
    } else if (type) {
        return `<br><div ${style}><video controls><source src="${src}" type="video/${type}"></video></div><br>`;
    } else {
        return `<br><div ${style}><div class="video-container"><iframe width="560" height="315" src="${src}" frameborder="0" allowfullscreen></iframe></div></div><br>`;
    }
}

export function insertMedia(content, editor, savedRange) {
    const selection = window.getSelection();

    if (savedRange && editor.contains(savedRange.startContainer)) {
        selection.removeAllRanges();
        selection.addRange(savedRange);
        const range = selection.getRangeAt(0);
        range.insertNode(range.createContextualFragment(content));

        // Sélectionner la vidéo après insertion
        const videoElement = editor.querySelector("video, iframe");
        if (videoElement) {
            const range = document.createRange();
            range.selectNode(videoElement);
            selection.removeAllRanges();
            selection.addRange(range);
        }

        // Ajustement pour éviter l'erreur
        range.collapse(false);
        const textNode = document.createTextNode(" ");
        range.insertNode(textNode);
        if (textNode.previousSibling) {
            range.setStartAfter(textNode.previousSibling);
        } else {
            range.setStartBefore(textNode);
        }
        selection.removeAllRanges();
        selection.addRange(range);
    } else {
        editor.innerHTML += content;
    }
}
