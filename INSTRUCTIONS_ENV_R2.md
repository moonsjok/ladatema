# Instructions pour configurer les variables R2 dans .env

## Problème actuel
L'erreur "bucket must be of type string, null given" signifie que la variable R2_BUCKET n'est pas définie dans votre fichier .env.

## Solution immédiate

Ajoutez ces lignes à la fin de votre fichier .env (ouvrez-le avec votre éditeur) :

```env
# Configuration Cloudflare R2
R2_ACCESS_KEY_ID=d177e5fb753e76a2751a1414e56e2c5b
R2_SECRET_ACCESS_KEY=8b635c8eff25b23748a2d39f8140c6a7717ca80ffa24e8071e2ebaa7dafdf6cf
R2_DEFAULT_REGION=auto
R2_BUCKET=ladatema-media
R2_URL=https://ed51a58fc5221b991a2a0d5688d9f1e9.r2.cloudflarestorage.com
R2_ENDPOINT=https://ed51a58fc5221b991a2a0d5688d9f1e9.r2.cloudflarestorage.com
FILESYSTEM_DISK=r2
```

## Étapes à suivre

1. **Ouvrez le fichier .env** à la racine de votre projet
2. **Copiez-collez** les variables ci-dessus à la fin du fichier
3. **Créez le bucket** "ladatema-media" sur le dashboard Cloudflare R2
4. **Videz les caches** : `php artisan config:clear`
5. **Testez l'upload**

## Vérification

Après avoir ajouté les variables, vérifiez avec :
```bash
php artisan tinker
>>> env('R2_BUCKET');
>>> env('R2_ACCESS_KEY_ID');
```

## Important

- Le bucket "ladatema-media" doit exister sur Cloudflare R2
- Les clés d'API doivent être valides
- L'endpoint doit être correct
