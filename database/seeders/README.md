# 📊 Database Seeders - Documentation

## 🗂️ Structure des Seeders

Les seeders sont maintenant séparés par modèle pour une meilleure organisation et maintenabilité.

### 📁 Fichiers de Seeders

| Seeder | Modèle | Description |
|--------|----------|-------------|
| `CategorySeeder` | Category | Crée les catégories principales |
| `SubCategorySeeder` | SubCategory | Crée les sous-catégories |
| `PartnerSeeder` | Partner | Crée les partenaires commerciaux |
| `UserSeeder` | User + Profile | Crée les utilisateurs avec leurs profils |
| `FormationSeeder` | Formation | Crée les formations complètes |
| `CourseSeeder` | Course | Crée les cours individuels |
| `ChapterSeeder` | Chapter | Crée les chapitres pédagogiques |
| `QuestionSeeder` | Question | Crée les questions QCM |
| `AnswerSeeder` | Answer | Crée les réponses aux questions |
| `EvaluationSeeder` | Evaluation | Crée les évaluations polymorphiques |
| `SubscriptionSeeder` | Subscription | Crée les souscriptions utilisateurs |

## 🚀 Utilisation

### Exécuter tous les seeders
```bash
php artisan db:seed
```

### Exécuter un seeder spécifique
```bash
# Seulement les catégories
php artisan db:seed --class=CategorySeeder

# Plusieurs seeders spécifiques
php artisan db:seed --class=CategorySeeder --class=SubCategorySeeder --class=UserSeeder
```

### Réinitialiser et peupler
```bash
# Migration fraîche + tous les seeders
php artisan migrate:fresh --seed

# Rafraîchir + tous les seeders
php artisan migrate:refresh --seed
```

## 📋 Ordre d'Exécution

Le `DatabaseSeeder.php` principal exécute les seeders dans l'ordre suivant pour respecter les dépendances :

1. **CategorySeeder** - Catégories de base
2. **SubCategorySeeder** - Sous-catégories (dépend des catégories)
3. **PartnerSeeder** - Partenaires (indépendant)
4. **UserSeeder** - Utilisateurs et profils
5. **FormationSeeder** - Formations (dépend catégories/sous-catégories)
6. **CourseSeeder** - Cours (dépend des formations)
7. **ChapterSeeder** - Chapitres (dépend des cours)
8. **QuestionSeeder** - Questions (dépend des chapitres)
9. **AnswerSeeder** - Réponses (dépend des questions)
10. **EvaluationSeeder** - Évaluations (polymorphique)
11. **SubscriptionSeeder** - Souscriptions (dépend utilisateurs/formations/cours/chapitres)

## 🔧 Personnalisation

Chaque seeder peut être personnalisé indépendamment :

### Ajouter des données
```php
// Dans CategorySeeder.php
$categories = [
    ['name' => 'Nouvelle Catégorie', 'description' => 'Description...'],
    // ... autres catégories
];
```

### Modifier les relations
```php
// Dans UserSeeder.php
$user = User::create($userData);
Profile::create([
    'user_id' => $user->id,
    'bio' => 'Nouvelle bio...',
]);
```

## 📊 Statistiques

Après exécution complète :
- ✅ **5 Catégories**
- ✅ **10 Sous-catégories** 
- ✅ **5 Partenaires**
- ✅ **5 Utilisateurs** avec profils
- ✅ **4 Formations**
- ✅ **5 Cours**
- ✅ **5 Chapitres**
- ✅ **4 Évaluations**
- ✅ **2 Questions**
- ✅ **2 Réponses**
- ✅ **3 Souscriptions**

**Total : 50 enregistrements**

## 🎯 Avantages

1. **Organisation** : Chaque modèle a son propre seeder
2. **Maintenabilité** : Facile à modifier un type de données
3. **Réutilisabilité** : Seeders peuvent être utilisés dans les tests
4. **Performance** : Exécution plus rapide avec des seeders ciblés
5. **Dépendances** : Ordre respecté pour éviter les erreurs de clés étrangères

## 🔍 Débogage

Pour activer le mode verbeux :
```bash
php artisan db:seed --verbose
```

Pour vérifier l'état :
```bash
php artisan db:seed --dry-run
```
