// resources/js/editor/utils.js
export function enableParagraphs(editor) {
    const button = document.createElement("button");
    button.innerHTML = "P";
    button.classList.add("btn", "btn-outline-dark");

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        document.execCommand("formatBlock", false, "p");
    });

    editor.appendChild(button);
}
