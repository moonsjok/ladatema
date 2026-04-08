# 📊 Schéma des Relations entre Modèles - Ladatema

## 🗂️ Vue d'Ensemble

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│                           USERS (Utilisateurs)                        │
│  ┌─────────────────────────────────────────────────────────────────────────┐   │
│  │                        PROFILES                             │   │
│  │  ┌──────────────────────────────────────────────────────┐   │   │
│  │  │                   SUBSCRIPTIONS                │   │   │
│  │  │  ┌─────────────────────────────────────────────┐ │   │   │
│  │  │  │            EVALUATIONS              │   │   │   │
│  │  │  └─────────────────────────────────────────────┘ │   │   │
│  │  │  ┌─────────────────────────────────────────────┐ │   │   │
│  │  │  │     FORMATIONS ←─────────────────────────────┐ │   │   │
│  │  │  │     └───────────────┬─────────────┘ │   │   │
│  │  │  │                    │ COURSES           │   │   │
│  │  │  │                    └───────────────────┘ │   │   │
│  │  │  │  ┌─────────────────────────────────────┐ │   │   │
│  │  │  │  │           CHAPTERS          │   │   │   │
│  │  │  │  └─────────────────────────────────────┘ │   │   │
│  │  │  │  ┌─────────────────────────────────────┐ │   │   │
│  │  │  │  │         QUESTIONS            │   │   │   │
│  │  │  │  │  ┌───────────────────────────┐ │   │   │   │
│  │  │  │  │  │      ANSWERS         │   │   │   │   │
│  │  │  │  │  └───────────────────────────┘ │   │   │   │   │
│  │  │  │  └─────────────────────────────────────┘ │   │   │   │   │
│  │  │  └─────────────────────────────────────────────┘ │   │   │   │   │
│  │  └─────────────────────────────────────────────────────┘ │   │   │   │   │
│  └─────────────────────────────────────────────────────────────────┘ │   │   │   │   │
└─────────────────────────────────────────────────────────────────────────┘ │   │   │   │   │
                                                                   │   │   │   │
┌─────────────────────────────────────────────────────────────────────────┐ │   │   │   │
│                        CATEGORIES                                │   │   │   │
│  ┌─────────────────────────────────────────────────────────────────┐   │   │   │   │
│  │                  SUB_CATEGORIES                        │   │   │   │   │
│  │  └─────────────────────────────────────────────────────────┘   │   │   │   │
│  │  ┌─────────────────────────────────────────────────────────┐   │   │   │   │
│  │  │                 FORMATIONS (référence)          │   │   │   │
│  │  └─────────────────────────────────────────────────────────┘   │   │   │   │
└─────────────────────────────────────────────────────────────────────────┘ │   │   │   │
                                                                   │   │   │   │
┌─────────────────────────────────────────────────────────────────────────┐ │   │   │   │
│                         PARTNERS                               │   │   │   │
└─────────────────────────────────────────────────────────────────────────┘ │   │   │   │
                                                                   │   │   │   │
```

## 📋 Détail des Relations

### 👤 **User (Utilisateur)**
**Table** : `users`
**Relations** :
- **Profile** (1:1) : `hasOne(Profile::class)`
- **Subscriptions** (1:N) : `hasMany(Subscription::class)`
- **Media Collections** : Spatie MediaLibrary
  - `avatar` : Image de profil (single file)
  - `images` : Images multiples
  - `videos` : Vidéos MP4/WebM
  - `pdfs` : Documents PDF
  - `txt_files` : Fichiers texte/markdown
  - `documents` : Documents variés

### 👤 **Profile (Profil)**
**Table** : `profiles`
**Relations** :
- **User** (N:1) : `belongsTo(User::class)`

### 📁 **Category (Catégorie)**
**Table** : `categories`
**Relations** :
- **SubCategories** (1:N) : `hasMany(SubCategory::class)`
- **Formations** (1:N) : `hasMany(Formation::class)`

### 📂 **SubCategory (Sous-Catégorie)**
**Table** : `sub_categories`
**Relations** :
- **Category** (N:1) : `belongsTo(Category::class)`
- **Formations** (1:N) : `hasMany(Formation::class)`

### 🎓 **Formation (Formation)**
**Table** : `formations`
**Relations** :
- **Category** (N:1) : `belongsTo(Category::class)`
- **SubCategory** (N:1) : `belongsTo(SubCategory::class)`
- **Courses** (1:N) : `hasMany(Course::class)`
- **Subscriptions** (1:N) : `hasMany(Subscription::class)`
- **Evaluations** (1:1) : `morphOne(Evaluation::class, 'evaluatable')`

### 📚 **Course (Cours)**
**Table** : `courses`
**Relations** :
- **Formation** (N:1) : `belongsTo(Formation::class)`
- **Chapters** (1:N) : `hasMany(Chapter::class)`
- **Subscriptions** (1:N) : `hasMany(Subscription::class)`
- **Evaluations** (1:1) : `morphOne(Evaluation::class, 'evaluatable')`

### 📖 **Chapter (Chapitre)**
**Table** : `chapters`
**Relations** :
- **Course** (N:1) : `belongsTo(Course::class)`
- **Subscriptions** (1:N) : `hasMany(Subscription::class)`
- **Evaluations** (1:1) : `morphOne(Evaluation::class, 'evaluatable')`

### 💳 **Subscription (Souscription)**
**Table** : `subscriptions`
**Relations** :
- **User** (N:1) : `belongsTo(User::class)`
- **Formation** (N:1) : `belongsTo(Formation::class)`
- **Course** (N:1) : `belongsTo(Course::class)`
- **Chapter** (N:1) : `belongsTo(Chapter::class)`

### 📝 **Evaluation (Évaluation)**
**Table** : `evaluations`
**Relations** :
- **Polymorphic** : `morphTo()` vers Course, Chapter, Formation
- **User** : Évaluateur (via la souscription)

### ❓ **Question (Question)**
**Table** : `questions`
**Relations** :
- **Chapter** (N:1) : `belongsTo(Chapter::class)`
- **Answers** (1:N) : `hasMany(Answer::class)`

### 💡 **Answer (Réponse)**
**Table** : `answers`
**Relations** :
- **Question** (N:1) : `belongsTo(Question::class)`

### 🤝 **Partner (Partenaire)**
**Table** : `partners`
**Relations** : Aucune relation définie (modèle indépendant)

## 🔧 **Fonctionnalités Spéciales**

### 🗑️ **Soft Deletes**
Tous les modèles utilisent `SoftDeletes` sauf Partner :
- `deleted_at` timestamp pour suppression logique
- `withTrashed()` / `withoutTrashed()` pour inclure/exclure les enregistrements supprimés

### 🔄 **Cascading Deletes**
- **Formation → Courses** : Soft delete automatique des cours lors de la suppression d'une formation
- **Course → Chapters** : Soft delete automatique des chapitres lors de la suppression d'un cours
- **SubCategory → Formations** : Soft delete automatique des formations lors de la suppression d'une sous-catégorie

### 🎯 **Polymorphic Relations**
- **Evaluations** : Peut être liée à Course, Chapter, ou Formation via `evaluatable_type` et `evaluatable_id`

### 📎 **Media Collections**
Chaque utilisateur peut stocker différents types de médias :
- **avatar** : Photo de profil (unique)
- **images** : Gallery d'images
- **videos** : Vidéos éducatives
- **pdfs** : Documents PDF
- **txt_files** : Fichiers texte
- **documents** : Documents variés (PDF + texte)

## 🚀 **Seeders Complètes**

Le `DatabaseSeeder.php` contient maintenant :
- ✅ **5 Catégories** avec descriptions
- ✅ **10 Sous-Catégories** couvrant tous les domaines
- ✅ **4 Formations** avec prix et descriptions
- ✅ **5 Cours** avec contenu pédagogique
- ✅ **5 Chapitres** avec numéros et contenus
- ✅ **5 Utilisateurs** avec profils complets
- ✅ **4 Partenaires** avec descriptions
- ✅ **4 Évaluations** avec feedbacks
- ✅ **2 Questions** avec types et options
- ✅ **2 Réponses** avec validation
- ✅ **3 Souscriptions** avec paiements

## 📊 **Statistiques du Seeder**
- **Catégories** : 5
- **Sous-Catégories** : 10
- **Formations** : 4
- **Cours** : 5
- **Chapitres** : 5
- **Utilisateurs** : 5
- **Profils** : 5
- **Partenaires** : 5
- **Évaluations** : 4
- **Questions** : 2
- **Réponses** : 2
- **Souscriptions** : 3

**Total** : 50 enregistrements créés

## 🔍 **Commandes Utiles**

```bash
# Exécuter le seeder complet
php artisan db:seed

# Exécuter avec migration fraîche
php artisan migrate:fresh --seed

# Vider la base et repeupler
php artisan migrate:refresh --seed
```

Ce schéma complet permet de comprendre l'architecture de données et les relations entre toutes les entités de l'application Ladatema.
