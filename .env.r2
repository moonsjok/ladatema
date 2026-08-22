# Configuration Cloudflare R2
# Ajoutez ces variables à votre fichier .env

# Cloudflare R2 Configuration
R2_ACCESS_KEY_ID=votre_access_key_id
R2_SECRET_ACCESS_KEY=votre_secret_access_key
R2_DEFAULT_REGION=auto
R2_BUCKET=votre_bucket_name
R2_URL=https://votre-domaine-public.r2.cloudflarestorage.com
R2_ENDPOINT=https://ed51a58fc5221b991a2a0d5688d9f1e9.r2.cloudflarestorage.com

# Utiliser R2 comme disque par défaut pour les médias
FILESYSTEM_DISK=r2

# Conserver les anciennes variables AWS si nécessaire
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
# AWS_USE_PATH_STYLE_ENDPOINT=false
