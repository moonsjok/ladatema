# Instructions pour vérifier la date/heure du serveur en ligne

## Étape 1: Configuration du script

1. **Ouvrez le fichier** `check_server_time_online.php`
2. **Modifiez les identifiants de connexion** (lignes 13-17) :

```php
$dbConfig = [
    'host' => 'localhost', // ou l'IP de votre base de données
    'dbname' => 'c1745713c_ladatema', // nom de la base en ligne
    'username' => 'votre_utilisateur_db', // utilisateur de la base en ligne
    'password' => 'votre_mot_de_passe_db' // mot de passe de la base en ligne
];
```

## Étape 2: Upload sur le serveur

1. **Upload le fichier** `check_server_time_online.php` sur votre serveur en ligne
2. **Placez-le** à la racine de votre site ou dans un dossier temporaire
3. **Donnez les permissions** d'exécution si nécessaire

## Étape 3: Accès au script

1. **Ouvrez votre navigateur**
2. **Accédez à l'URL**: `https://votresite.com/check_server_time_online.php`
3. **Le script s'exécutera** et affichera les résultats

## Étape 4: Analyse des résultats

Le script affichera :

### Informations PHP
- Date/heure actuelle
- Fuseau horaire
- Version PHP
- Timestamp

### Informations MySQL
- Version MySQL
- Date/heure NOW()
- Fuseaux horaires
- Timestamp UNIX

### Comparaison
- Différence PHP vs MySQL
- Test d'insertion avec NOW()
- Analyse des données subscriptions

### Problèmes identifiés
- Durées négatives
- Incohérences
- Statistiques

## Étape 5: Actions recommandées

Selon les résultats :

### Si PHP et MySQL ne sont pas synchronisés
```sql
-- Configurer MySQL pour utiliser UTC
SET GLOBAL time_zone = '+00:00';
```

### Si des durées négatives sont trouvées
```sql
-- Corriger les durées négatives
UPDATE subscriptions 
SET duration_in_days = GREATEST(1, DATEDIFF(expires_at, created_at))
WHERE duration_in_days < 0;
```

### Si des doublons existent
```sql
-- Identifier les doublons
SELECT user_id, formation_id, COUNT(*) as count
FROM subscriptions 
GROUP BY user_id, formation_id 
HAVING count > 1;
```

## Étape 6: Nettoyage

**IMPORTANT**: Supprimez le script après utilisation :
```bash
rm check_server_time_online.php
```

## Alternative: Script en ligne de commande

Si vous avez accès SSH au serveur :

```bash
# Créer le script
cat > check_time_online.php << 'EOF'
<?php
echo "Date PHP: " . date('Y-m-d H:i:s') . "\n";
echo "Timezone: " . date_default_timezone_get() . "\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=c1745713c_ladatema", "username", "password");
    $stmt = $pdo->query("SELECT NOW() as mysql_now");
    $result = $stmt->fetch();
    echo "Date MySQL: " . $result['mysql_now'] . "\n";
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
?>
EOF

# Exécuter
php check_time_online.php

# Nettoyer
rm check_time_online.php
```

## Sécurité

- **Ne laissez jamais** le script sur le serveur en production
- **Utilisez des permissions** restrictives (600)
- **Supprimez immédiatement** après vérification
- **Changez les mots de passe** si vous les avez codés en dur

## Contact

Si vous rencontrez des problèmes :
1. Vérifiez les identifiants de base de données
2. Vérifiez que la base de données est accessible
3. Contactez votre hébergeur si nécessaire
