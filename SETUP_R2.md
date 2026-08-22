# Configuration Cloudflare R2 pour le stockage des médias

## Étapes de configuration

### 1. Créer un bucket R2 sur Cloudflare

1. Allez sur le dashboard Cloudflare
2. Accédez à R2 Object Storage
3. Créez un nouveau bucket
4. Notez le nom du bucket

### 2. Générer les clés d'API

1. Dans le dashboard Cloudflare, allez dans "Manage R2 API tokens"
2. Créez un nouveau token avec les permissions suivantes:
   - Object Read & Write
   - List & Search Buckets
3. Notez l'Access Key ID et Secret Access Key

### 3. Configurer les variables d'environnement

Ajoutez ces variables à votre fichier `.env`:

```env
# Configuration Cloudflare R2
R2_ACCESS_KEY_ID=votre_access_key_id
R2_SECRET_ACCESS_KEY=votre_secret_access_key
R2_DEFAULT_REGION=auto
R2_BUCKET=nom_de_votre_bucket
R2_URL=https://votre-domaine-public.r2.cloudflarestorage.com
R2_ENDPOINT=https://ed51a58fc5221b991a2a0d5688d9f1e9.r2.cloudflarestorage.com

# Utiliser R2 comme disque par défaut pour les médias
FILESYSTEM_DISK=r2
```

### 4. Configurer le domaine public (optionnel)

Pour servir les fichiers avec votre propre domaine:

1. Dans le dashboard Cloudflare, allez dans "R2" → "Custom Domains"
2. Ajoutez votre domaine personnalisé
3. Mettez à jour `R2_URL` avec votre domaine personnalisé

### 5. Tester la configuration

```bash
# Vider les caches
php artisan optimize:clear
php artisan config:clear

# Tester l'upload
# Allez sur /media/images/create et uploadez une image
```

## Configuration déjà effectuée

### ✅ Filesystème Laravel
- Configuration du disque `r2` dans `config/filesystems.php`
- Endpoint Cloudflare R2 configuré
- Path style endpoint activé

### ✅ MediaController
- Méthode `store()` mise à jour pour utiliser R2
- Méthode `update()` mise à jour pour utiliser R2
- `->usingDisk('r2')` ajouté aux uploads

### ✅ Configuration spatie/laravel-medialibrary
- Fichier de configuration créé pour R2
- Disque par défaut défini sur `r2`

## Vérification

Après configuration, vérifiez que:

1. Les fichiers uploadés apparaissent dans votre bucket R2
2. Les URLs générées pointent vers R2
3. Les conversions d'images fonctionnent
4. Les permissions public/privé sont respectées

## Dépannage

### Erreur: "Access Denied"
- Vérifiez que les clés d'API sont correctes
- Assurez-vous que le bucket existe
- Vérifiez les permissions du token

### Erreur: "Endpoint not found"
- Vérifiez que l'endpoint R2 est correct
- Assurez-vous que `use_path_style_endpoint` est à `true`

### Erreur: "Bucket not found"
- Vérifiez le nom du bucket dans les variables d'environnement
- Assurez-vous que le bucket existe dans R2
