# Instructions pour vérifier la date/heure en ligne

## Option 1: Script PHP Terminal (Copier-coller direct)

### Étape 1: Connexion SSH
```bash
ssh votre_utilisateur@votreserveur.com
cd /chemin/vers/votre/projet
```

### Étape 2: Modifier les identifiants (si nécessaire)
Ouvrez `terminal_time_check.php` et modifiez les lignes 32-34:
```php
"mysql:host=localhost;dbname=c1745713c_ladatema;charset=utf8mb4",
"c1745713c_ladatema", // Remplacez si nécessaire
"votre_mot_de_passe", // Remplacez avec le vrai mot de passe
```

### Étape 3: Exécuter le script
```bash
php terminal_time_check.php
```

### Étape 4: Copier les résultats
Sélectionnez tout le résultat et copiez-le pour me le partager.

---

## Option 2: Script phpMyAdmin (Copier-coller direct)

### Étape 1: Accéder à phpMyAdmin
1. Connectez-vous à votre hébergement web
2. Accédez à phpMyAdmin
3. Sélectionnez votre base de données `c1745713c_ladatema`

### Étape 2: Exécuter le script
1. Cliquez sur l'onglet "SQL"
2. Copiez tout le contenu de `phpmyadmin_time_check.sql`
3. Collez dans la zone SQL
4. Cliquez sur "Exécuter"

### Étape 3: Copier les résultats
Sélectionnez tout le tableau de résultats et copiez-le.

---

## Ce que les scripts vérifient

### Script PHP Terminal:
- Date/heure PHP
- Fuseau horaire
- Connexion base de données
- Informations MySQL
- Comparaison PHP/MySQL
- Test d'insertion
- Analyse des subscriptions
- Cas spécifique ID 92

### Script phpMyAdmin:
- Version MySQL
- Fuseaux horaires
- Date/heure actuelles
- Test de calcul de durée
- Statistiques subscriptions
- Enregistrements problématiques
- Cas ID 92
- Doublons
- Souscriptions expirées

---

## Résultats attendus

### Si tout est normal:
```
PHP: 2026-04-30 14:35:00
MySQL: 2026-04-30 14:35:00
Statut: SYNCHRONISÉ
Subscriptions - Total: 109, Negative: 0
```

### S'il y a des problèmes:
```
PHP: 2026-04-30 14:35:00
MySQL: 2026-04-30 12:35:00
Statut: DIFFÉRENCE DE 02:00:00
Subscriptions - Total: 109, Negative: 1
```

---

## Partage des résultats

Copiez simplement les résultats complets et partagez-les avec moi. J'analyserai :
- La synchronisation PHP/MySQL
- Les fuseaux horaires
- Les problèmes de données
- Les actions correctives nécessaires

---

## Commandes alternatives rapides

### Si PHP ne fonctionne pas:
```bash
# Vérifier PHP
php -v

# Vérifier la date système
date
```

### Si MySQL ne fonctionne pas:
```bash
# Vérifier MySQL
mysql --version

# Test simple
mysql -u utilisateur -p -e "SELECT NOW();"
```
