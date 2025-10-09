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
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Auth\VerificationController;


use App\Http\Controllers\PartnerController;

// ✅ Routes publiques (accessibles sans authentification)
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

    // 🔒 Routes réservées aux rôles "dev" et "owner"
    Route::middleware([CheckRole::class . ':dev,owner'])->group(function () {


        Route::resource('partners', PartnerController::class);


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
        Route::put('evaluations/question/{question}/update', [EvaluationController::class, 'questionUpdate'])->name("evaluation.question.update");
        Route::put('evaluations/answer/{answer}/update', [EvaluationController::class, 'answerUpdate'])->name("evaluation.answer.update");

        Route::resource('questions', QuestionController::class);
        Route::resource('answers', AnswerController::class);

        // ✅ Gestion des vidéos
        Route::get('/videos', [SecureController::class, 'listVideos'])->name('videos.list');
        Route::get('/video/{videoName}', [SecureController::class, 'showVideo'])->name('video.show');
        Route::get('/video/{videoName}/delete', [SecureController::class, 'deleteVideo'])->name('video.delete');
        Route::post('/video/{videoName}/delete', [SecureController::class, 'deleteVideo']);
        Route::get('/ajouter/une/create', [SecureController::class, 'create'])->name('video.create');
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
