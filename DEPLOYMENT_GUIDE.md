# **DEPLOYMENT GUIDE - Ladatema Production Update**

## **Récapitulatif complet des modifications depuis début de session**

---

## **1. PAQUETS AJOUTÉS**

### **Composer (PHP)**
```json
{
    "require": {
        "artesaos/seotools": "^1.3",           // SEO Tools
        "fedapay/fedapay-php": "^0.4.5",       // Paiement FedaPay  
        "livewire/livewire": "^3.5",           // Composants dynamiques
        "realrashid/sweet-alert": "^7.2",      // SweetAlert Laravel
        "spatie/laravel-medialibrary": "^11.12", // Gestion médias
        "spatie/laravel-sitemap": "^7.3",       // Génération sitemap
        "yajra/laravel-datatables-oracle": "^11.1" // DataTables Laravel
    }
}
```

### **NPM (JavaScript)**
```json
{
    "dependencies": {
        "@ckeditor/ckeditor5-build-classic": "^44.1.0", // Éditeur CKEditor 5
        "autoprefixer": "^10.4.20",                    // CSS autoprefixer
        "bootstrap": "^5.3.3",                         // Bootstrap 5
        "bootstrap-icons": "^1.11.3",                   // Icons Bootstrap
        "ckeditor4": "^4.22.1",                        // CKEditor 4 (legacy)
        "datatables.net-bs5": "^2.2.0",               // DataTables Bootstrap 5
        "jquery": "^3.7.1",                           // jQuery
        "plyr": "^3.8.4",                             // Lecteur vidéo Plyr
        "summernote": "^0.9.1",                       // Éditeur Summernote
        "sweetalert2": "^11.15.10"                    // SweetAlert 2
    }
}
```

---

## **2. FICHIERS MODIFIÉS**

### **Base de données**
```
database/migrations/0001_01_01_000000_create_users_table.php
database/migrations/2025_10_08_000001_add_search_indexes_for_subscriptions_and_users.php  
database/migrations/2025_10_08_000002_add_composite_and_fulltext_indexes.php

database/seeders/DatabaseSeeder.php
database/seeders/CategorySeeder.php
database/seeders/SubCategorySeeder.php
database/seeders/PartnerSeeder.php
database/seeders/UserSeeder.php
database/seeders/FormationSeeder.php
database/seeders/CourseSeeder.php
database/seeders/ChapterSeeder.php
database/seeders/QuestionSeeder.php
database/seeders/AnswerSeeder.php
database/seeders/SubscriptionSeeder.php
database/seeders/EvaluationSeeder.php
```

### **Controllers**
```
app/Http/Controllers/SubscriptionController.php
app/Http/Controllers/MediaController.php
```

### **Models**
```
app/models/Evaluation.php
app/models/Partner.php
```

### **Views**
```
resources/views/authenticated/owners/subscriptions/with-list.blade.php
resources/views/authenticated/owners/subscriptions/without-list.blade.php
resources/views/authenticated/students/courses/view.blade.php
resources/views/errors/ (media-forbidden.blade.php, media-unauthorized.blade.php)
resources/views/layouts/ (authenticated/owners/index.blade.php, authenticated/students/index.blade.php, guest/index.blade.php)
```

### **Configuration**
```
vite.config.js
config/vite.php
.env.example
```

### **JavaScript**
```
resources/js/app.js
```

### **Nouveaux fichiers**
```
ASSETS_BUILD.md
DATABASE_SCHEMA.md
BROWSER_DETECTION.md
IP_AUTO_CONFIG.md
database/seeders/README.md
```

---

## **3. ÉTAPES DE DÉPLOIEMENT**

### **Étape 1: Backup**
```bash
# Backup base de données
mysqldump -u username -p ladatema > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup fichiers
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz public/ storage/
```

### **Étape 2: Mise à jour des paquets**
```bash
# Mettre à jour Composer
composer update --no-dev --optimize-autoloader

# Installer nouveaux paquets si nécessaire
composer require artesaos/seotools fedapay/fedapay-php livewire/livewire realrashid/sweet-alert spatie/laravel-medialibrary spatie/laravel-sitemap yajra/laravel-datatables-oracle

# Mettre à jour NPM
npm install
npm run build
```

### **Étape 3: Upload des fichiers**
```bash
# Upload des fichiers modifiés
rsync -avz --exclude='node_modules' --exclude='.git' \
  --exclude='storage/app/*' --exclude='storage/logs/*' \
  ./ user@serveur:/var/www/ladatema/

# Upload du build
rsync -avz public/build/ user@serveur:/var/www/ladatema/public/build/
```

### **Étape 4: Migrations**
```bash
# Exécuter les migrations
php artisan migrate --force

# Si besoin de réinitialiser les seeders
php artisan db:seed --force
```

### **Étape 5: Configuration**
```bash
# Publier les packages
php artisan vendor:publish --tag=medialibrary-config
php artisan vendor:publish --tag=medialibrary-migrations
php artisan vendor:publish --tag=seotools-config
php artisan vendor:publish --tag=sweet-alert-config

# Optimiser
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### **Étape 6: Permissions**
```bash
# Permissions Laravel
sudo chown -R www-data:www-data /var/www/ladatema
sudo chmod -R 755 /var/www/ladatema
sudo chmod -R 777 /var/www/ladatema/storage
sudo chmod -R 777 /var/www/ladatema/bootstrap/cache
```

---

## **4. VÉRIFICATIONS POST-DÉPLOIEMENT**

### **Fonctionnalités à tester**
- [ ] **Souscriptions** : Validation avec SweetAlert
- [ ] **DataTables** : Chargement et recherche
- [ ] **Médias** : Upload et affichage
- [ ] **Navigateur** : Détection et alertes
- [ ] **Seeders** : Données de test
- [ ] **Paiement** : FedaPay (si utilisé)
- [ ] **SEO** : Meta tags et sitemap

### **Logs à vérifier**
```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs erreurs
tail -f /var/log/nginx/error.log
```

---

## **5. COMMANDES UTILES**

### **Maintenance**
```bash
# Vider cache
php artisan optimize:clear

# Recréer cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Maintenance mode
php artisan down
php artisan up
```

### **Seeders**
```bash
# Exécuter tous les seeders
php artisan db:seed

# Seeder spécifique
php artisan db:seed --class=UserSeeder

# Réinitialiser base
php artisan migrate:fresh --seed
```

---

## **6. POINTS D'ATTENTION**

### **Variables d'environnement**
```env
# Vérifier ces variables en production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ladatemaresearch.com

# FedaPay
FEDAPAY_MODE=live
FEDAPAY_LIVE_SECRET_KEY=votre_cle_live

# Base de données
DB_DATABASE=ladatema_prod
DB_USERNAME=ladatema_user
DB_PASSWORD=password_secure
```

### **Performance**
- Activer OPCache
- Configurer Redis si possible
- Optimiser les requêtes SQL
- Configurer CDN pour assets

---

## **7. ROLLBACK PLAN**

Si problème après déploiement :
```bash
# 1. Maintenance mode
php artisan down

# 2. Restaurer backup base de données
mysql -u username -p ladatema < backup_YYYYMMDD_HHMMSS.sql

# 3. Restaurer fichiers
tar -xzf backup_files_YYYYMMDD_HHMMSS.tar.gz

# 4. Rollback migrations si nécessaire
php artisan migrate:rollback --step=5

# 5. Remettre en ligne
php artisan up
```

---

## **8. CONTACT SUPPORT**

En cas de problème :
- Vérifier les logs Laravel
- Tester en environnement local
- Contacter le support technique

---

**Ce guide couvre toutes les modifications apportées et les étapes nécessaires pour un déploiement réussi en production.**
