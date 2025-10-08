// resources/js/editor/list.js
export function enableList(toolbar) {
    const listTypes = [
        {
            command: "insertUnorderedList",
            icon: "bi-list-ul",
            text: "UL",
            active: false,
        },
        {
            command: "insertOrderedList",
            icon: "bi-list-ol",
            text: "OL",
            active: false,
        },
    ];

    listTypes.forEach((type) => {
        const button = document.createElement("button");
        button.innerHTML = `<i class="${type.icon}"></i>`;
        button.classList.add("btn", "btn-outline-dark");
        button.addEventListener("click", () => {
            document.execCommand(type.command, false, null); // Toujours appliquer la commande
            type.active = !type.active; // Toggle l'état actif
            if (type.active) {
                // Si on vient d'activer, on désactive l'autre type de liste
                listTypes.find((t) => t !== type).active = false;
            }
        });
        toolbar.appendChild(button);
    });
}
