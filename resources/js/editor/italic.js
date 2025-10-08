// resources/js/editor/italic.js
export function enableItalic(editor) {
    const button = document.createElement("button");
    button.innerHTML = "I";
    button.classList.add("btn", "btn-outline-dark");

    button.addEventListener("click", (e) => {
        e.preventDefault(); // Empêcher le comportement par défaut (soumission du formulaire)
        document.execCommand("italic");
    });

    editor.appendChild(button);
}
