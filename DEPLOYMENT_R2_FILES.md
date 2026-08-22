# Fichiers à uploader pour la mise en production R2

## 📋 Fichiers modifiés à uploader

### 🔧 Configuration principale
- **`config/filesystems.php`**
  - Ajout de la configuration du disque `r2`
  - Configuration S3 pour Cloudflare R2

- **`config/media-library.php`**
  - Modification du disque par défaut vers `r2`
  - Support pour R2 dans spatie/laravel-medialibrary

### 🎬 Contrôleurs
- **`app/Http/Controllers/MediaController.php`**
  - Mise à jour des méthodes `store()` et `update()`
  - Utilisation de `->toMediaCollection($collection, 'r2')`
  - Limites de taille par type de fichier (vidéos 2GB, images 10MB, PDFs 5MB, TXT 5MB)

### 🛣️ Routes
- **`routes/web.php`**
  - Modification de la route `/media-file/{media}`
  - Redirection vers URLs temporaires R2 avec protection Laravel

## 📦 Dépendances à installer

Exécutez cette commande sur le serveur de production :
```bash
composer require league/flysystem-aws-s3-v3
```

## 🔧 Variables d'environnement à ajouter

Ajoutez ces variables dans votre fichier `.env` de production :
```env
# Configuration Cloudflare R2
R2_ACCESS_KEY_ID=d177e5fb753e76a2751a1414e56e2c5b
R2_SECRET_ACCESS_KEY=8b635c8eff25b23748a2d39f8140c6a7717ca80ffa24e8071e2ebaa7dafdf6cf
R2_DEFAULT_REGION=auto
R2_BUCKET=ladatema
R2_URL=https://pub-ac9b39c934f44945a4a0871dd96f57fd.r2.dev
R2_ENDPOINT=https://ed51a58fc5221b991a2a0d5688d9f1e9.r2.cloudflarestorage.com
FILESYSTEM_DISK=r2
```

## 🚀 Commandes à exécuter après déploiement

```bash
# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier la configuration
php artisan tinker --execute="echo 'R2 Bucket: ' . env('R2_BUCKET');"
```

## ⚠️ Notes importantes

### 📁 Configuration Cloudflare R2
1. **Bucket R2** : Le bucket `ladatema` doit exister sur Cloudflare R2
2. **Public Development URL** : Doit être activée pour le bucket
3. **Permissions** : Les clés API doivent avoir les permissions nécessaires

### 🔄 Migration des fichiers existants
- Les fichiers uploadés avant la mise à jour resteront accessibles
- Les nouveaux fichiers seront uploadés directement sur R2
- Aucune migration de fichiers nécessaire

### 🎯 Résultat attendu
- Upload des médias sur Cloudflare R2
- URLs sécurisées Laravel avec redirection R2
- Protection contre le téléchargement non autorisé
- Performance améliorée avec CDN Cloudflare

## 📋 Checklist de déploiement

- [ ] Backup du code actuel
- [ ] Upload des fichiers modifiés
- [ ] Installation des dépendances composer
- [ ] Configuration des variables d'environnement
- [ ] Vérification du bucket R2
- [ ] Exécution des commandes artisan
- [ ] Test d'upload d'un fichier
- [ ] Vérification des URLs sécurisées

## 🧪 Tests de validation

Après déploiement, testez :
1. Upload d'une nouvelle vidéo
2. Accès via `/media/videos`
3. Accès direct via `/media-file/{id}`
4. Protection contre les accès non autorisés
