# **CORRECTION UPLOAD MÉDIA - Taille de fichier**

## **Problème identifié**

### **Erreur serveur**
```
Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
File `/tmp/phpjlDvkb` has a size of 40.75 MB which is greater than maximum allowed 10 MB
```

### **Cause**
- **Fichier uploadé** : 40.75 MB
- **Limite MediaLibrary** : 10 MB (config par défaut)
- **Limite validation Laravel** : 2GB pour vidéos (déjà correcte)

---

## **Solution appliquée**

### **1. Publication configuration MediaLibrary**
```bash
php artisan vendor:publish --tag=medialibrary-config
```

### **2. Modification de la limite**
```php
// config/media-library.php
'max_file_size' => 1024 * 1024 * 2048, // 2GB (2048MB)
```

### **3. Cache configuration**
```bash
php artisan config:cache
```

---

## **Configuration finale**

### **Validation Laravel (MediaController.php)**
```php
$request->validate([
    'file' => [
        'required',
        'file',
        'max:' . ($type === 'videos' ? '2048000' : '10240'), // 2GB pour vidéos, 10MB pour autres
        'mimes:' . implode(',', self::FILE_TYPES[$type])
    ],
]);
```

### **Configuration MediaLibrary (config/media-library.php)**
```php
'max_file_size' => 1024 * 1024 * 2048, // 2GB (2048MB)
```

---

## **Limites par type de fichier**

| Type | Validation Laravel | MediaLibrary | Limite finale |
|-------|------------------|---------------|----------------|
| Vidéos | 2GB | 2GB | **2GB** |
| Images | 10MB | 2GB | **10MB** |
| Documents | 10MB | 2GB | **10MB** |
| Audio | 10MB | 2GB | **10MB** |

---

## **Pour déployer en production**

### **1. Upload des fichiers modifiés**
```bash
# Upload config/media-library.php
scp config/media-library.php user@serveur:/var/www/ladatema/config/
```

### **2. Vider cache sur serveur**
```bash
php artisan config:cache
php artisan optimize:clear
php artisan optimize
```

### **3. Vérifier permissions**
```bash
# Vérifier que le dossier storage est accessible
ls -la storage/app/public/media/
```

---

## **Test de validation**

### **Upload de test**
- **Vidéo 40MB** : ✅ Devrait fonctionner
- **Vidéo 1GB** : ✅ Devrait fonctionner  
- **Vidéo 2.5GB** : ❌ Erreur (dépasse 2GB)
- **Image 15MB** : ❌ Erreur (dépasse 10MB)

---

## **Notes importantes**

### **Performance**
- **Upload de gros fichiers** : Peut nécessiter timeout PHP
- **Mémoire PHP** : Augmenter `memory_limit` si nécessaire
- **Upload progress** : Considérer chunk upload pour très gros fichiers

### **Configuration PHP recommandée**
```ini
upload_max_filesize = 2048M
post_max_size = 2048M
max_execution_time = 300
memory_limit = 512M
```

### **Configuration Nginx/Apache**
```nginx
# Nginx
client_max_body_size 2048M;

# Apache
LimitRequestBody 2147483648
```

---

## **Résultat**

**L'upload de vidéos jusqu'à 2GB est maintenant supporté !** 

Le fichier de 40.75 MB devrait maintenant s'uploader sans erreur.
