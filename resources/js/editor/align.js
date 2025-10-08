// resources/js/editor/align.js
export function enableAlign(toolbar) {
    const alignTypes = [
        { command: "justifyLeft", icon: "bi-text-left", active: false },
        { command: "justifyCenter", icon: "bi-text-center", active: false },
        { command: "justifyRight", icon: "bi-text-right", active: false },
    ];

    alignTypes.forEach((type) => {
        const button = document.createElement("button");
        button.innerHTML = `<i class="${type.icon}"></i>`;
        button.classList.add("btn", "btn-outline-dark");
        button.addEventListener("click", () => {
            document.execCommand(type.command, false, null); // Toujours appliquer la commande
            // Désactiver tous les autres alignements
            alignTypes.forEach((t) => (t.active = false));
            type.active = true; // Marquer le type courant comme actif
        });
        toolbar.appendChild(button);
    });
}
