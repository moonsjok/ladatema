# Instructions pour vérifier la date/heure avec Tinker et MySQL

## Option 1: Avec Laravel Tinker (Recommandé)

### Étape 1: Connexion SSH
```bash
ssh votre_utilisateur@votreserveur.com
cd /chemin/vers/votre/projet/laravel
```

### Étape 2: Exécuter Tinker
```bash
php artisan tinker
```

### Étape 3: Copier et coller le script
Ouvrez le fichier `tinker_time_check.php` et copiez tout le code après "Code à copier dans Tinker"

### Étape 4: Résultats attendus
Le script affichera :
- Date/heure PHP
- Date/heure Laravel/Carbon
- Date/heure MySQL
- Comparaison entre les trois
- Test d'insertion
- Analyse des subscriptions

### Étape 5: Quitter Tinker
```bash
> exit
```

---

## Option 2: Avec MySQL Direct (Plus rapide)

### Étape 1: Via phpMyAdmin
1. Connectez-vous à phpMyAdmin sur votre hébergement
2. Sélectionnez votre base de données `c1745713c_ladatema`
3. Cliquez sur l'onglet "SQL"
4. Copiez et collez le contenu de `mysql_time_check.sql`
5. Cliquez sur "Exécuter"

### Étape 2: Via ligne de commande MySQL
```bash
# Connexion MySQL
mysql -h localhost -u votre_utilisateur -p c1745713c_ladatema

# Exécuter le script
source /chemin/vers/mysql_time_check.sql;

# Ou copier-coller directement le contenu
```

### Étape 3: Via SSH avec MySQL
```bash
ssh votre_utilisateur@votreserveur.com
mysql -u votre_utilisateur -p c1745713c_ladatema < mysql_time_check.sql
```

---

## Option 3: Script PHP rapide (Alternative)

### Créer un script simple
```bash
cat > quick_time_check.php << 'EOF'
<?php
require_once 'vendor/autoload.php';

echo "PHP Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Timezone: " . date_default_timezone_get() . "\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=c1745713c_ladatema", "username", "password");
    $stmt = $pdo->query("SELECT NOW() as mysql_now, @@time_zone as timezone");
    $result = $stmt->fetch();
    echo "MySQL Time: " . $result['mysql_now'] . "\n";
    echo "MySQL Timezone: " . $result['timezone'] . "\n";
    
    // Test subscriptions
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(CASE WHEN duration_in_days < 0 THEN 1 ELSE 0 END) as negative FROM subscriptions");
    $stats = $stmt->fetch();
    echo "Subscriptions - Total: " . $stats['total'] . ", Negative: " . $stats['negative'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
EOF

php quick_time_check.php
rm quick_time_check.php
```

---

## Analyse des résultats

### Si PHP et MySQL sont synchronisés
```
PHP Time: 2026-04-30 14:30:00
MySQL Time: 2026-04-30 14:30:00
```
=> OK, les serveurs sont synchronisés

### Si PHP et MySQL ne sont pas synchronisés
```
PHP Time: 2026-04-30 14:30:00
MySQL Time: 2026-04-30 12:30:00
```
=> Problème de fuseau horaire

### Si des durées négatives sont trouvées
```
Subscriptions - Total: 109, Negative: 1
```
=> Il y a des données corrompues

---

## Actions correctives

### 1. Synchroniser les fuseaux horaires MySQL
```sql
-- Dans MySQL
SET GLOBAL time_zone = '+00:00';
SET time_zone = '+00:00';
```

### 2. Corriger les durées négatives
```sql
UPDATE subscriptions 
SET duration_in_days = GREATEST(1, DATEDIFF(expires_at, created_at))
WHERE duration_in_days < 0;
```

### 3. Vérifier un cas spécifique
```sql
SELECT id, created_at, expires_at, duration_in_days, 
       DATEDIFF(expires_at, created_at) as expected_duration
FROM subscriptions 
WHERE id = 92;
```

---

## Sécurité

- **Ne laissez jamais** les scripts sur le serveur
- **Utilisez des connexions sécurisées** (SSH/HTTPS)
- **Supprimez** les fichiers temporaires après usage
- **Changez les mots de passe** si nécessaire

---

## Support

Si vous rencontrez des problèmes :
1. Vérifiez les identifiants de base de données
2. Assurez-vous que Laravel est bien installé
3. Contactez votre hébergeur pour l'accès MySQL

## Résumé rapide

**Pour une vérification rapide :**
```bash
php artisan tinker
# Copier le script Tinker
```

**Pour une analyse complète :**
```sql
-- Exécuter mysql_time_check.sql dans phpMyAdmin
```
