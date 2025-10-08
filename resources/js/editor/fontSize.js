// resources/js/editor/fontSize.js
export function enableFontSize(toolbar) {
    const inputGroup = document.createElement("div");
    inputGroup.classList.add("input-group", "input-group-sm", "w-auto");

    const input = document.createElement("input");
    input.type = "number";
    input.min = "1";
    input.value = "16"; // Valeur par défaut
    input.classList.add("form-control");

    const dropdown = document.createElement("select");
    dropdown.classList.add("form-select");

    // Générer les options de taille
    for (let size = 1; size <= 100; size += 1) {
        const option = document.createElement("option");
        option.value = `${size}px`;
        option.textContent = `${size}px`;
        dropdown.appendChild(option);
    }

    // Synchroniser la saisie directe et le menu déroulant
    input.addEventListener("input", () => {
        const value = Math.max(parseInt(input.value, 10) || 1, 1); // Valeur minimale de 1
        input.value = value;

        // Ajouter la valeur saisie à la liste déroulante si elle n'existe pas
        if (
            !Array.from(dropdown.options).some(
                (option) => option.value === `${value}px`
            )
        ) {
            const newOption = document.createElement("option");
            newOption.value = `${value}px`;
            newOption.textContent = `${value}px`;
            dropdown.appendChild(newOption);
        }

        dropdown.value = `${value}px`;
    });

    dropdown.addEventListener("change", () => {
        const value = parseInt(dropdown.value, 10);
        input.value = value;
        applyFontSize(value);
    });

    // Fonction pour appliquer la taille de police et ajuster les espaces
    function applyFontSize(size) {
        const selection = window.getSelection();
        const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;

        if (range && !selection.isCollapsed) {
            const span = document.createElement("span");
            span.style.fontSize = `${size}px`;
            span.style.marginLeft = `${Math.ceil(size / 4)}px`; // Espacement à gauche
            span.style.marginRight = `${Math.ceil(size / 4)}px`; // Espacement à droite
            span.style.marginTop = `${Math.ceil(size / 6)}px`; // Espacement au-dessus
            span.style.marginBottom = `${Math.ceil(size / 6)}px`; // Espacement en-dessous
            span.textContent = selection.toString();

            // Remplace le texte sélectionné par le texte transformé
            range.deleteContents();
            range.insertNode(span);

            // Réinitialiser la sélection pour inclure le texte inséré
            const newRange = document.createRange();
            newRange.selectNodeContents(span);
            selection.removeAllRanges();
            selection.addRange(newRange);
        }
    }

    // Appliquer la taille lors de la saisie manuelle (lorsque l'utilisateur quitte l'input)
    input.addEventListener("blur", () => {
        const value = Math.max(parseInt(input.value, 10) || 1, 1); // Valeur minimale de 1

        // Ajouter la valeur saisie à la liste déroulante si elle n'existe pas
        if (
            !Array.from(dropdown.options).some(
                (option) => option.value === `${value}px`
            )
        ) {
            const newOption = document.createElement("option");
            newOption.value = `${value}px`;
            newOption.textContent = `${value}px`;
            dropdown.appendChild(newOption);
        }

        dropdown.value = `${value}px`;
        applyFontSize(value);
    });

    inputGroup.appendChild(input);
    inputGroup.appendChild(dropdown);
    toolbar.appendChild(inputGroup);
}
