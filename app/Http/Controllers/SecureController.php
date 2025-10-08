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

            ];
            return view('authenticated.owners.dashboard', $data);
        }

        // Récupération des formations souscrites pour les étudiants
        if ($user->role === 'student') {
            $souscriptions = $user->souscriptions()
                ->where('is_validated', 1)
                ->with('formation.courses')
                ->get();

            $formations = $souscriptions->pluck('formation')->unique();

            $data = [
                'profile' => $user->profile,
                'souscriptions' => $souscriptions,
                'formations' => $formations,
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
