# **EMPLACEMENT DE LA DÉTECTION NAVIGATEUR ET NOTIFICATION**

## **Fichier principal**

```
resources/views/authenticated/students/courses/view.blade.php
```

---

## **Structure du code**

### **1. Détection du navigateur (lignes 59-144)**
```javascript
document.addEventListener("DOMContentLoaded", function() {
    const userAgent = navigator.userAgent.toLowerCase();
    
    // Détection améliorée et plus précise des navigateurs
    const isChrome = /chrome/.test(userAgent) && !/edge|opr|brave|firefox|safari/.test(userAgent);
    const isFirefox = /firefox/.test(userAgent) && !/seamonkey/.test(userAgent);
    const isEdge = /edg/.test(userAgent) || /edge/.test(userAgent);
    const isSafari = /safari/.test(userAgent) && !/chrome/.test(userAgent);
    const isOpera = /opera/.test(userAgent) || /opr/.test(userAgent);
    const isBrave = /brave/.test(userAgent);
    const isVivaldi = /vivaldi/.test(userAgent);
    const isTor = /tor/.test(userAgent);
    const isIE = /msie/.test(userAgent) || /trident/.test(userAgent);
    const isChromium = /chromium/.test(userAgent);
    
    // Détection mobile
    const isMobile = /mobile|android|iphone|ipad|phone/.test(userAgent);
    
    // Extraire les versions
    let browserVersion = 'Inconnue';
    let browserName = 'Navigateur inconnu';
    
    if (isChrome) {
        browserName = 'Google Chrome';
        const chromeMatch = userAgent.match(/chrome\/([0-9.]+)/);
        browserVersion = chromeMatch ? chromeMatch[1] : 'Inconnue';
    }
    // ... etc pour tous les navigateurs
    
    // Vérifier si c'est un navigateur moderne
    const isModernBrowser = isChrome || isFirefox || isEdge || isBrave || isVivaldi || isChromium || 
        (isSafari && parseFloat(browserVersion) >= 14);
});
```

### **2. Bandeau d'alerte (lignes 8-14)**
```html
{{-- Bandeau haut si pas Chrome --}}
<div id="browser-banner"
    style="display:none; background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; padding:10px 20px; margin-bottom: 15px; border-radius: 5px;">
    <img src="https://www.google.com/chrome/static/images/favicons/favicon-96x96.png" width="24" class="me-2" alt="Chrome">
    Pour une meilleure expérience, nous vous recommandons d'utiliser <strong>Google Chrome</strong>.
    <a href="https://www.google.com/chrome/" target="_blank" class="text-decoration-underline">Télécharger</a>
</div>
```

### **3. Affichage du bandeau (ligne 148)**
```javascript
if (!isModernBrowser) {
    // Affiche le bandeau pour navigateurs non supportés
    document.getElementById('browser-banner').style.display = 'block';
}
```

### **4. Notification SweetAlert (lignes 166-183)**
```javascript
// Vérifie si l'utilisateur n'a pas déjà dismissé l'alerte
if (!localStorage.getItem('browserWarningDismissed')) {
    Swal.fire({
        icon: isIE ? 'error' : 'warning',
        title: isIE ? 'Navigateur obsolète' : 'Compatibilité navigateur',
        html: `${recommendationMessage}<br><br>
               <a href="https://www.google.com/chrome/" target="_blank" class="btn btn-primary btn-sm me-2">
                   <i class="bi bi-google"></i> Télécharger Chrome
               </a>`,
        confirmButtonText: 'J\'ai compris',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: true,
        cancelButtonText: 'Continuer avec ' + browserName,
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.setItem('browserWarningDismissed', true);
        }
    });
}
```

---

## **Comment ça fonctionne**

### **1. Détection**
- Au chargement de la page (`DOMContentLoaded`)
- Analyse du `navigator.userAgent.toLowerCase()`
- Tests regex pour chaque navigateur
- Extraction de la version avec `.match()`

### **2. Classification**
```javascript
const isModernBrowser = isChrome || isFirefox || isEdge || isBrave || isVivaldi || isChromium || 
    (isSafari && parseFloat(browserVersion) >= 14);
```

### **3. Affichage**
#### **Si navigateur moderne**
- Rien n'est affiché
- localStorage nettoyé
- Log console positif

#### **Si navigateur non moderne**
- **Bandeau** : `display: 'block'`
- **SweetAlert** : Affiché si pas déjà dismissé

---

## **Messages personnalisés**

### **Navigateurs avec alerte**
```javascript
// Messages spécifiques pour certains navigateurs
if (isIE) {
    recommendationMessage = `<b>Internet Explorer n'est plus supporté</b> et présente des risques de sécurité...`;
} else if (isSafari && parseFloat(browserVersion) < 14) {
    recommendationMessage = `Votre version de Safari est trop ancienne...`;
} else {
    recommendationMessage = `Pour une meilleure expérience, nous vous recommandons d'utiliser <b>Google Chrome</b>...`;
}
```

### **Icônes SweetAlert**
```javascript
icon: isIE ? 'error' : 'warning',  // Rouge pour IE, Orange pour autres
title: isIE ? 'Navigateur obsolète' : 'Compatibilité navigateur',
```

---

## **LocalStorage pour éviter le spam**

### **Mémorisation**
```javascript
// Vérifie si déjà dismissé
if (!localStorage.getItem('browserWarningDismissed')) {
    // Affiche SweetAlert
}

// Sauvegarde la décision
localStorage.setItem('browserWarningDismissed', true);
```

### **Nettoyage**
```javascript
// Si navigateur moderne, nettoie le localStorage
localStorage.removeItem('browserWarningDismissed');
```

---

## **Débogage**

### **Console log complet**
```javascript
console.log('Détection navigateur améliorée:', {
    userAgent: userAgent,
    browserName: browserName,
    browserVersion: browserVersion,
    isMobile: isMobile,
    isModernBrowser: isModernBrowser
});
```

---

## **Pour modifier la détection**

1. **Ouvrir** : `resources/views/authenticated/students/courses/view.blade.php`
2. **Chercher** : Lignes 59-144 pour la détection
3. **Modifier** : Les regex ou les conditions `isModernBrowser`
4. **Tester** : Vérifier la console pour les logs

---

## **Pour modifier la notification**

1. **Bandeau** : Lignes 8-14 (HTML)
2. **SweetAlert** : Lignes 167-183 (JavaScript)
3. **Messages** : Lignes 151-163 (texte personnalisé)

**Le système est centralisé dans un seul fichier pour une maintenance facile !**
