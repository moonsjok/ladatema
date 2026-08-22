# Correction de l'erreur PostTooLargeException

## 🚨 Problème identifié

L'erreur `PostTooLargeException` se produit même avec des fichiers de 164MB car les limites PHP sur le serveur de production sont plus restrictives que sur votre environnement local.

## 🔍 Analyse des limites actuelles

### Serveur de production (probable) :
- **upload_max_filesize**: 8M (défaut)
- **post_max_size**: 8M (défaut)
- **max_execution_time**: 30s (défaut)

### Votre fichier : 164MB
- **Taille**: 163.97 MB
- **Dépasse**: Les limites par défaut de PHP

## 🛠️ Solutions à implémenter

### Option 1: Fichier .htaccess (Recommandé pour Apache)
Ajoutez ce contenu à votre `.htaccess` :

```apache
<IfModule mod_php.c>
    php_value upload_max_filesize 2G
    php_value post_max_size 2.5G
    php_value max_execution_time 300
    php_value max_input_time 300
    php_value memory_limit 512M
</IfModule>
```

### Option 2: Fichier user.ini
Créez un fichier `user.ini` à la racine :

```ini
upload_max_filesize = 2G
post_max_size = 2.5G
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

### Option 3: php.ini (accès serveur requis)
Modifiez le `php.ini` du serveur :

```ini
upload_max_filesize = 2G
post_max_size = 2.5G
max_execution_time = 300
max_input_time = 300
memory_limit = 512M
```

## 📋 Fichiers créés pour vous

1. **`.htaccess`** - Configuration Apache
2. **`user.ini`** - Configuration PHP utilisateur
3. **`check_upload_limits.php`** - Script de diagnostic

## 🚀 Instructions de déploiement

### Étape 1: Uploader les fichiers
```bash
# Uploader sur le serveur de production
- .htaccess
- user.ini
```

### Étape 2: Vérifier les limites
```bash
php check_upload_limits.php
```

### Étape 3: Redémarrer le serveur web
```bash
# Apache
sudo systemctl restart apache2
# ou
sudo service httpd restart

# Nginx avec PHP-FPM
sudo systemctl restart php-fpm
sudo systemctl restart nginx
```

### Étape 4: Tester l'upload
1. Allez sur `/media/videos/create`
2. Uploadez votre vidéo de 164MB
3. Vérifiez que l'indicateur de progression fonctionne

## ⚠️ Notes importantes

### Hébergement mutualisé
- Certains hébergeurs bloquent la modification des limites PHP
- Contactez votre support si les fichiers .htaccess/user.ini ne fonctionnent pas

### Serveur dédié/VPS
- Vous avez probablement accès au php.ini
- C'est la meilleure solution pour les gros fichiers

### Limites recommandées pour vidéos 2GB
```ini
upload_max_filesize = 2G      # Taille max d'un fichier
post_max_size = 2.5G          # Taille max des données POST
max_execution_time = 300      # 5 minutes pour l'upload
max_input_time = 300          # 5 minutes pour le traitement
memory_limit = 512M           # Mémoire PHP
```

## 🧪 Test de validation

Après déploiement, testez avec :
```bash
# Vérifier les nouvelles limites
php -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"
php -r "echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;"
```

## 📞 Si ça ne fonctionne pas

1. **Contactez votre hébergeur** pour augmenter les limites
2. **Vérifiez les logs** du serveur web
3. **Testez avec un fichier plus petit** (ex: 50MB) pour confirmer

## 🎯 Résultat attendu

Après correction, vous devriez pouvoir uploader :
- ✅ Vidéos jusqu'à 2GB
- ✅ Avec indicateur de progression
- ✅ Stockage sur Cloudflare R2
- ✅ URLs sécurisées Laravel
