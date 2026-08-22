<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

use App\Models\User;
use App\Models\Profile;
use App\Models\Formation;
use App\Models\Course;
use App\Models\Subscription;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SecureController extends Controller
{
    /**
     * Display the dashboard page based on user role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $data = [];

        // Notifications non lues destinées à l'utilisateur connecté
        $topNotifications = \App\Models\AppNotification::with('sender')
            ->where(function ($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhere('target_type', $user->role)
                      ->orWhere(function ($q) use ($user) {
                          $q->where('target_type', 'user')
                            ->where('target_user_id', $user->id);
                      });
            })
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->get();

        // Récupération des données selon le rôle de l'utilisateur
        if ($user->role === 'dev' || $user->role === 'owner') {
            // Données spécifiques aux administrateurs
            $data = [
                'totalStudents' => User::where('role', 'student')->count(), // Compter uniquement les étudiants
                'totalFormations' => Formation::count(),
                'totalCourses' => Course::count(),
                'totalPendingSubscriptions' => Subscription::where('is_validated', 0)
                    ->whereHas('user', function ($query) {
                        $query->where('role', 'student');
                    })->count(),

                'totalSubscriptions' => Subscription::where('is_validated', 1)
                    ->whereHas('user', function ($query) {
                        $query->where('role', 'student');
                    })->count(),

                'latestFormations' => Formation::withCount('courses')->latest()->take(5)->get(),
                'latestUsers' => User::where('role', '!=', 'dev')->latest()->take(5)->get(),
                'topNotifications' => $topNotifications,
            ];
            return view('authenticated.owners.dashboard', $data);
        }

        // Récupération des données du tableau de bord pour l'étudiant
        if ($user->role === 'student') {
            // 1. Souscriptions actives (validées et non expirées)
            $activeSubscriptions = $user->souscriptions()
                ->where('is_validated', 1)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                })
                ->with(['formation.courses.chapters', 'course', 'chapter'])
                ->get();

            // 2. Souscriptions expirées ou en attente
            $expiredSubscriptions = $user->souscriptions()
                ->where(function ($query) {
                    $query->where('is_validated', 0)
                          ->orWhere(function ($q) {
                              $q->whereNotNull('expires_at')
                                ->where('expires_at', '<', now());
                          });
                })
                ->with(['formation', 'course', 'chapter'])
                ->get();

            $formations = $activeSubscriptions->pluck('formation')->filter()->unique();

            // 3. Journal des activités de l'étudiant
            $activityLog = collect();

            // a. Tentatives d'évaluations
            $attempts = \App\Models\Attempt::where('user_id', $user->id)
                ->with('evaluation')
                ->latest()
                ->take(10)
                ->get();

            foreach ($attempts as $attempt) {
                $evalTitle = $attempt->evaluation ? $attempt->evaluation->title : 'Évaluation';
                $activityLog->push([
                    'type' => 'attempt',
                    'icon' => 'bi-journal-check',
                    'color' => $attempt->passed ? 'success' : 'danger',
                    'title' => 'Évaluation : ' . $evalTitle,
                    'description' => "Score : {$attempt->score}/{$attempt->total_points} ({$attempt->pourcentage}%) - " . ($attempt->passed ? 'Réussi' : 'Échoué'),
                    'date' => $attempt->created_at,
                ]);
            }

            // b. Souscriptions enregistrées, validées & mises à jour
            $allSubs = $user->souscriptions()->with(['formation', 'course', 'chapter'])->latest()->get();
            foreach ($allSubs as $sub) {
                $itemTitle = $sub->formation ? $sub->formation->title : ($sub->course ? $sub->course->title : ($sub->chapter ? $sub->chapter->title : 'Souscription'));

                if ($sub->is_validated) {
                    $activityLog->push([
                        'type' => 'subscription_validated',
                        'icon' => 'bi-check-circle-fill',
                        'color' => 'success',
                        'title' => 'Souscription validée',
                        'description' => "Accès activé pour : {$itemTitle}",
                        'date' => $sub->created_at,
                    ]);

                    // Si la souscription a été mise à jour/prolongée après sa création
                    if ($sub->updated_at && $sub->created_at && $sub->updated_at->diffInMinutes($sub->created_at) > 1) {
                        $expiresDateText = $sub->expires_at ? $sub->expires_at->format('d/m/Y') : 'N/A';
                        $activityLog->push([
                            'type' => 'subscription_updated',
                            'icon' => 'bi-arrow-repeat',
                            'color' => 'primary',
                            'title' => 'Souscription mise à jour',
                            'description' => "Validité modifiée : {$sub->duration_in_days} jours (Expiration : {$expiresDateText}) - {$itemTitle}",
                            'date' => $sub->updated_at,
                        ]);
                    }
                } else {
                    $activityLog->push([
                        'type' => 'subscription_created',
                        'icon' => 'bi-credit-card-fill',
                        'color' => 'warning',
                        'title' => 'Souscription enregistrée',
                        'description' => "Demande pour : {$itemTitle}",
                        'date' => $sub->created_at,
                    ]);
                }
            }

            // c. Activité de confirmation e-mail
            if ($user->email_verified_at) {
                $activityLog->push([
                    'type' => 'email_verified',
                    'icon' => 'bi-envelope-check-fill',
                    'color' => 'info',
                    'title' => 'Compte Vérifié',
                    'description' => 'Adresse e-mail vérifiée avec succès',
                    'date' => $user->email_verified_at,
                ]);
            }

            // Trier toutes les activités chronologiquement
            $activityLog = $activityLog->sortByDesc('date')->take(15);

            $data = [
                'profile' => $user->profile,
                'souscriptions' => $activeSubscriptions,
                'expiredSubscriptions' => $expiredSubscriptions,
                'formations' => $formations,
                'activityLog' => $activityLog,
                'topNotifications' => $topNotifications,
            ];

            return view('authenticated.students.dashboard', $data);
        }

        // Si aucun rôle défini, redirection vers une page d'erreur ou autre
        return abort(403, "Accès non autorisé.");
    }

    public function listVideos()
    {
        // Chemin absolu vers le dossier des vidéos
        $path = public_path('storage/videos');

        // Récupérer tous les fichiers dans le dossier videos
        $videos = [];
        if (File::exists($path)) {
            $videos = File::files($path);
        }

        // Passer les vidéos à la vue
        return view('authenticated.owners.videos.list', compact('videos'));
    }

    public function showVideo($videoName)
    {
        // Chemin absolu vers le fichier vidéo
        $videoPath = public_path("storage/videos/{$videoName}");

        // Vérifier si la vidéo existe
        if (File::exists($videoPath)) {
            // Générer l'URL pour la vidéo
            $videoUrl = asset("storage/videos/{$videoName}");
            return view('authenticated.owners.videos.show', compact('videoUrl', 'videoName'));
        } else {
            // Gérer le cas où la vidéo n'existe pas
            return redirect()->route('videos.list')->with('error', 'Cette vidéo n\'existe pas.');
        }
    }

    public function deleteVideo(Request $request, $videoName)
    {
        // Chemin absolu vers le fichier vidéo
        $videoPath = public_path("storage/videos/{$videoName}");

        // Vérifier si la vidéo existe
        if (File::exists($videoPath)) {
            // Demander confirmation avant de supprimer
            if ($request->has('confirm') && $request->input('confirm') == 'true') {
                // Supprimer la vidéo
                File::delete($videoPath);
                return redirect()->route('videos.list')->with('success', 'La vidéo a été supprimée avec succès.');
            } else {
                // Demander confirmation
                return view('authenticated.owners.videos.confirm_delete', compact('videoName'));
            }
        } else {
            // Gérer le cas où la vidéo n'existe pas
            return redirect()->route('videos.list')->with('error', 'Cette vidéo n\'existe pas.');
        }
    }

    public function create()
    {
        return view('authenticated.owners.videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:102400', // 100MB max
        ]);

        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $fileName = time() . '_' . $video->getClientOriginalName();
            $video->storeAs('public/videos', $fileName);

            return redirect()->route('videos.list')->with('success', 'Vidéo ajoutée avec succès.');
        }

        return redirect()->route('videos.list')->with('error', 'Une erreur s\'est produite lors de l\'ajout de la vidéo.');
    }
}
