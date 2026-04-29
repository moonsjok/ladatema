<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UnsecureController;
use App\Http\Controllers\SecureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\StudentEvaluationController;
use App\Http\Controllers\AttemptController;


use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Auth\VerificationController;


use App\Http\Controllers\PartnerController;

// ✅ Routes publiques (accessibles sans authentification)

Route::get('artisan-clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    return redirect()->back()->with('Cache cleared successfuly');
})->name('artisan-clear-cache');

Route::get('/sitemap.xml', [SitemapController::class, 'generate'])->name('sitemap');

Route::get('/', [UnsecureController::class, 'index'])->name("welcome");
Route::get('/nos/services', [UnsecureController::class, 'services'])->name("nos.services");
Route::get('/contact', [UnsecureController::class, 'showContactForm'])->name('contact.form');
Route::post('/contact', [UnsecureController::class, 'sendContactForm'])->name('contact.send');

Route::get('/catalogs/formations', [UnsecureController::class, 'formationsList'])->name("guest.formationsList");
Route::get('/nos/formations', [UnsecureController::class, 'formations'])->name("guest.formations");
Route::get('/la/formation/{formation}/{slug?}', [UnsecureController::class, 'formationShow'])->name('guest.formations.show');

// ✅ Routes d'authentification (Connexion / Inscription)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/complete/profile', [VerificationController::class, 'completeprofile'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.completeprofile');

Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ✅ Mot de passe oublié / Réinitialisation
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// ✅ Souscriptions (public)
Route::get('/subscriptions/select', [SubscriptionController::class, 'selectType'])->name('subscriptions.select');
Route::get('/subscriptions/create-account', [SubscriptionController::class, 'createAccount'])->name('subscriptions.createAccount');
Route::post('/subscriptions/store-account', [SubscriptionController::class, 'storeAccount'])->name('subscriptions.storeAccount');
Route::get('/subscriptions/confirm', [SubscriptionController::class, 'confirm'])->name('subscriptions.confirm');
Route::post('/subscriptions/store', [SubscriptionController::class, 'store'])->name('subscriptions.store');




// ✅ Paiement (public)
Route::post('/process/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

// 🔒 Routes protégées (nécessitent une authentification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [SecureController::class, 'dashboard'])->name('dashboard');

    // ✅ Uploads (images et vidéos)
    Route::post('/upload-image', [MediaController::class, 'upload']);
    Route::post('/upload-video', [MediaController::class, 'upload']);

    // ✅ Accès sécurisé aux médias (uniquement pour utilisateurs connectés)
    Route::get('/media-file/{media}', function ($mediaId) {
        if (!auth()->check()) {
            return response()->view('errors.media-unauthorized', [
                'message' => 'Vous devez être connecté pour accéder à ce fichier.',
                'redirect' => route('login')
            ], 401);
        }

        try {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
            
            // Vérifier si le média est public ou appartient à l'utilisateur
            if (!$media->getCustomProperty('is_public', false) && 
                ($media->model_type !== \App\Models\User::class || $media->model_id !== auth()->id())) {
                
                return response()->view('errors.media-forbidden', [
                    'message' => 'Ce fichier est privé ou ne vous appartient pas.'
                ], 403);
            }

            return response()->file($media->getPath());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->view('errors.media-not-found', [
                'message' => 'Le fichier demandé n\'existe pas ou a été supprimé.'
            ], 404);
        } catch (\Illuminate\Filesystem\FileNotFoundException $e) {
            return response()->view('errors.media-not-found', [
                'message' => 'Le fichier physique n\'existe plus. Le média a peut-être été remplacé récemment.'
            ], 404);
        } catch (\Exception $e) {
            // Masquer les détails techniques pour des raisons de sécurité
            \Log::error('Erreur accès média: ' . $e->getMessage());
            return response()->view('errors.media-not-found', [
                'message' => 'Une erreur est survenue lors de l\'accès au fichier. Veuillez réessayer plus tard.'
            ], 404);
        }
    })->name('media.file.secure');

    // ✅ CRUD complet pour les médias par type
    Route::get('/media/{type}', [MediaController::class, 'index'])->name('media.index');
    Route::get('/media/{type}/data', [MediaController::class, 'getData'])->name('media.data');
    Route::get('/media/{type}/create', [MediaController::class, 'create'])->name('media.create');
    Route::post('/media/{type}', [MediaController::class, 'store'])->name('media.store');
    Route::get('/media/{type}/{mediaId}', [MediaController::class, 'show'])->name('media.show');
    Route::get('/media/{type}/{mediaId}/edit', [MediaController::class, 'edit'])->name('media.edit');
    Route::put('/media/{type}/{mediaId}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{type}/{mediaId}', [MediaController::class, 'destroy'])->name('media.destroy');

    // ✅ Accès aux fichiers stockés
    Route::get('/file/{file}', function ($file) {
        if (Storage::exists('public/' . $file)) {
            return response()->file(storage_path('app/public/' . $file));
        }
        return response('File not found.', 404);
    });

    // ✅ Gestion du profil pour mettre ajour les informations utilisateur
    Route::get('/profile/complete', [ProfileController::class, 'showForm'])->name('profile.complete');
    Route::post('/profile/complete', [ProfileController::class, 'submitForm'])->name('profile.complete.submit');


    // ✅ Consultation de cours (pour tous les utilisateurs connectés)
    Route::get('course/{course}/viewer/{chapterId?}', [CourseController::class, 'courseViewer'])->name('course-viewer');

    // ✅ Routes de test pour le débogage
    Route::get('/test-collections', function () {
        if (!auth()->check()) return redirect('/login');
        
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::all();
        $collections = $media->groupBy('collection_name');
        
        $output = "<h1>Collections de médias</h1>";
        foreach ($collections as $collection => $items) {
            $output .= "<h2>Collection: $collection (" . $items->count() . " fichiers)</h2>";
            $output .= "<ul>";
            foreach ($items as $item) {
                $output .= "<li>ID: {$item->id} - Nom: {$item->name} - MIME: {$item->mime_type} - Taille: " . number_format($item->size / 1024 / 1024, 2) . "MB</li>";
            }
            $output .= "</ul>";
        }
        
        return $output;
    })->name('test.collections');

    // ✅ Gestion des évaluations pour étudiants
    Route::prefix('student/evaluations')->name('student.evaluations.')->group(function () {
        Route::get('/', [StudentEvaluationController::class, 'index'])->name('index');
        Route::get('/{evaluation}', [StudentEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluation}/start', [StudentEvaluationController::class, 'start'])->name('start');
        Route::post('/{evaluation}/submit', [StudentEvaluationController::class, 'submit'])->name('submit');
        Route::get('/{evaluation}/results/{attempt}', [StudentEvaluationController::class, 'results'])->name('results');
    });

    // 🔒 Routes réservées aux rôles "dev" et "owner"
    Route::middleware([CheckRole::class . ':dev,owner'])->group(function () {


        Route::resource('partners', PartnerController::class);

        // ✅ Gestion des souscriptions (CRUD complet)
        Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('subscriptions', [SubscriptionController::class, 'storeSubscription'])->name('subscriptions.store');
        Route::get('subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'updateSubscription'])->name('subscriptions.update');
        Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
        Route::post('subscriptions/{subscription}/restore', [SubscriptionController::class, 'restore'])->name('subscriptions.restore');
        Route::post('subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('subscriptions/bulk-update-duration', [SubscriptionController::class, 'bulkUpdateDuration'])->name('subscriptions.bulkUpdateDuration');
        Route::get('subscriptions/search-student', [SubscriptionController::class, 'searchStudent'])->name('subscriptions.searchStudent');
        Route::post('subscriptions/update-student-duration', [SubscriptionController::class, 'updateStudentSubscriptionDuration'])->name('subscriptions.updateStudentDuration');


        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubCategoryController::class);
        Route::resource('formations', FormationController::class);
        Route::get('formation/quick/create', [FormationController::class, 'quickCreate'])->name('formation.quickCreate');
        Route::resource('courses', CourseController::class);
        Route::resource('chapters', ChapterController::class);

        // ✅ Gestion des modèles (Formation, Course, Chapter)
        Route::get('/get-models', function (Request $request) {
            $allowedModels = [
                'App\\Models\\Formation' => App\Models\Formation::class,
                'App\\Models\\Course' => App\Models\Course::class,
                'App\\Models\\Chapter' => App\Models\Chapter::class,
            ];

            $type = $request->query('type');
            if (!isset($allowedModels[$type])) {
                return response()->json(['error' => 'Type de modèle non valide ou non autorisé.'], 400);
            }

            $modelClass = $allowedModels[$type];
            try {
                $data = $modelClass::select('id', 'title')->get();
                return response()->json($data, 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erreur lors de la récupération des modèles.'], 500);
            }
        });

        // ✅ Gestion des évaluations et questions/réponses
        Route::resource('evaluations', EvaluationController::class);
        
        // Processus de création d'évaluation par étapes
        Route::get('evaluations/create/step-1', [EvaluationController::class, 'createStep1'])->name('evaluations.create.step1');
        Route::post('evaluations/create/step-1', [EvaluationController::class, 'storeStep1'])->name('evaluations.store.step1');
        Route::get('evaluations/create/step-2', [EvaluationController::class, 'createStep2'])->name('evaluations.create.step2');
        Route::post('evaluations/create/step-2', [EvaluationController::class, 'storeStep2'])->name('evaluations.store.step2');
        Route::get('evaluations/create/step-3', [EvaluationController::class, 'createStep3'])->name('evaluations.create.step3');
        Route::post('evaluations/create/step-3', [EvaluationController::class, 'storeStep3'])->name('evaluations.store.step3');
        
        // Processus d'édition d'évaluation par étapes
        Route::get('evaluations/{evaluation}/edit/step-1', [EvaluationController::class, 'editStep1'])->name('evaluations.edit.step1');
        Route::put('evaluations/{evaluation}/edit/step-1', [EvaluationController::class, 'updateStep1'])->name('evaluations.update.step1');
        Route::get('evaluations/{evaluation}/edit/step-2', [EvaluationController::class, 'editStep2'])->name('evaluations.edit.step2');
        Route::put('evaluations/{evaluation}/edit/step-2', [EvaluationController::class, 'updateStep2'])->name('evaluations.update.step2');
        Route::get('evaluations/{evaluation}/edit/step-3', [EvaluationController::class, 'editStep3'])->name('evaluations.edit.step3');
        Route::put('evaluations/{evaluation}/edit/step-3', [EvaluationController::class, 'updateStep3'])->name('evaluations.update.step3');
        Route::put('evaluations/question/{question}/update', [EvaluationController::class, 'questionUpdate'])->name("evaluation.question.update");
        Route::put('evaluations/answer/{answer}/update', [EvaluationController::class, 'answerUpdate'])->name("evaluation.answer.update");
        
        
        Route::resource('questions', QuestionController::class);
        Route::resource('answers', AnswerController::class);
//Gestion des tentatives

Route::resource('attempts', AttemptController::class);
        



        // ✅ Gestion des vidéos
        Route::get('/videos', [SecureController::class, 'listVideos'])->name('videos.list');
        Route::get('/video/{videoName}', [SecureController::class, 'showVideo'])->name('video.show');
        Route::get('/video/{videoName}/delete', [SecureController::class, 'deleteVideo'])->name('video.delete');
        Route::post('/video/{videoName}/delete', [SecureController::class, 'deleteVideo']);
        Route::get('/ajouter/une/video', [SecureController::class, 'create'])->name('video.create');
        Route::post('/video', [SecureController::class, 'store'])->name('video.store');

        // ✅ Gestion des des souscriptions
        Route::get('/subscriptions/students', [SubscriptionController::class, 'listStudentSubscriptions'])->name('subscriptions.students');
        // New views: overview and separate lists
        Route::get('/subscriptions/overview', [SubscriptionController::class, 'subscriptionsOverview'])->name('subscriptions.overview');
        Route::get('/subscriptions/students/without', [SubscriptionController::class, 'studentsWithoutView'])->name('subscriptions.students.without');
        Route::get('/subscriptions/students/with', [SubscriptionController::class, 'studentsWithView'])->name('subscriptions.students.with');
        Route::put('/subscriptions/{subscription}/validate', [SubscriptionController::class, 'validateSubscription'])->name('subscriptions.validate');
        Route::post('/subscriptions/reminder/for/{user}', [SubscriptionController::class, 'sendReminderEmail'])->name('send.reminder.email');
    });

    // 🔒 Routes spécifiques aux étudiants
    Route::middleware([CheckRole::class . ':student'])->group(function () {
        // Ici tu peux ajouter des routes spécifiques aux étudiants
    });
});
