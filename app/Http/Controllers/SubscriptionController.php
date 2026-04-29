<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Formation;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use FedaPay\FedaPay;
use FedaPay\Transaction;
use FedaPay\Customer;

use App\Mail\SubscriptionReminder;




class SubscriptionController extends Controller
{

    public function __construct()
    {
        FedaPay::setApiKey(config('fedapay.live_secret_key'));
        FedaPay::setEnvironment(config('fedapay.mode')); // "sandbox" ou "live"
    }
    public function selectType()
    {
        // Afficher la page où l'utilisateur choisit la formation, le cours ou le chapitre
        $formations = Formation::all();
        $courses = Course::all();
        $chapters = Chapter::all();

        return view('subscriptions.select', compact('formations', 'courses', 'chapters'));
    }

    public function createAccount(Request $request)
    {
        // Si l'utilisateur n'est pas connecté, afficher le formulaire d'inscription
    if (!Auth::check()) {
            return view('subscriptions.create_account', ['request' => $request]);
        }

        // Si connecté, rediriger vers la validation de la souscription
        return redirect()->route('subscriptions.confirm', $request->all());
    }
    /**
     * Création du compte utilisateur et redirection vers la confirmation.
     */
    public function storeAccount(Request $request)
    {
        //dd($request);
        $validated = $request->validate([
            'prenoms' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'type' => 'required|in:formation,course,chapter',
            'typeid' => 'required|integer',
            'phone_call' => 'required|string|max:30',
            'phone_whatsapp' => 'nullable|string|max:30',
        ]);

        try {
            // Use the first token of prenoms as the short 'name' field
            $firstPrenom = null;
            if (!empty($validated['prenoms'])) {
                $parts = preg_split('/\s+/', trim($validated['prenoms']));
                $firstPrenom = $parts[0] ?? trim($validated['prenoms']);
            }
            $displayName = $firstPrenom ? $firstPrenom : trim(($validated['prenoms'] ?? '') . ' ' . ($validated['nom'] ?? ''));
            $user = User::create([
                'name' => $displayName,
                'prenoms' => $validated['prenoms'],
                'nom' => $validated['nom'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone_call' => $validated['phone_call'] ?? null,
                'phone_whatsapp' => $validated['phone_whatsapp'] ?? null,
            ]);

            // Create profile for the user and map prenoms/nom to first_name/last_name
            try {
                \App\Models\Profile::create([
                    'user_id' => $user->id,
                    'first_name' => $validated['prenoms'] ?? null,
                    'last_name' => $validated['nom'] ?? null,
                    'phone' => $validated['phone_call'] ?? null,
                    'address' => null,
                    'photo' => null,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create profile for subscription signup: ' . $e->getMessage());
            }

            // If a Profile model exists for the user and has a 'phone' column, sync phone_call to profile.phone
            try {
                if (method_exists($user, 'profile') || class_exists('\App\Models\Profile')) {
                    $profile = $user->profile ?? null;
                    if ($profile) {
                        $profile->phone = $validated['phone_call'] ?? $profile->phone;
                        $profile->save();
                    }
                }
            } catch (\Exception $e) {
                // Non-fatal: continue even if profile sync fails
                Log::warning('Failed to sync user phone to profile: ' . $e->getMessage());
            }

            Auth::login($user); // Connexion automatique de l'utilisateur

            return redirect()->route('subscriptions.confirm', [
                'type' => $validated['type'],
                'typeid' => $validated['typeid'],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la création du compte.');
        }
    }

    /**
     * Affichage de la page de confirmation de souscription.
     */
    /**
     * Affichage de la page de confirmation de souscription.
     */
    public function confirm(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour continuer.');
        }
        $fedapayTransactionId = $request->id;
        if ($fedapayTransactionId) {
            $transation = Transaction::retrieve($fedapayTransactionId);
            //dd($transation);
            //reference
            //status

            if ($transation->status === "approved") {
                //approved
                Subscription::create([
                    'user_id' => $user->id,
                    'formation_id' => $request->type === 'formation' ? $request->typeid : null,
                    'course_id' => $request->type === 'course' ? $request->typeid : null,
                    'chapter_id' => $request->type === 'chapter' ? $request->typeid : null,
                    'type' => $request->type,
                    'price' => $transation->amount,
                    'payment_reference' => $transation->reference,
                    'is_validated' => 1
                ]);

                $message = 'Votre souscription a été validée.';
                return redirect()->route('dashboard')->with('success', $message);
            }
        }

        $validated = $request->validate([
            'type' => 'required|in:formation,course,chapter',
            'typeid' => 'required|integer',
        ]);

        $type = $validated['type'];
        $typeid = $validated['typeid'];

        if ($this->hasActiveSubscription($user, $type, $typeid)) {
            $typeTitle = $this->getTypeTitle($type, $typeid);
            $message =  $user->name . ", vous avez déjà une souscription active pour : $type - $typeTitle";
            return redirect()->route('course-viewer', ['course' => $typeid])->with('success', $message);
        }

        // Si pas de souscription active, continuez avec le chargement de l'élément
        $item = null;
        if ($type === 'formation') {
            $item = Formation::findOrFail($typeid);
        } elseif ($type === 'course') {
            $item = Course::findOrFail($typeid);
        } elseif ($type === 'chapter') {
            // Note: Assurez-vous que 'course-viewer' est la route correcte pour les chapitres également
            $item = Chapter::findOrFail($typeid);
        }

        return view('subscriptions.confirm', compact('user', 'type', 'item'));
    }

    /**
     * Vérifie si l'utilisateur a une souscription active pour le type et l'id donnés.
     */
    private function hasActiveSubscription($user, $type, $typeid)
    {
        return Subscription::where('user_id', $user->id)
            ->where('type', $type)
            ->where(function ($query) use ($type, $typeid) {
                if ($type === 'formation') {
                    $query->where('formation_id', $typeid);
                } elseif ($type === 'course') {
                    $query->where('course_id', $typeid);
                } elseif ($type === 'chapter') {
                    $query->where('chapter_id', $typeid);
                }
            })
            ->where('is_validated', true)
            ->exists();
    }

    /**
     * Retourne le titre du type d'élément souscrit.
     */
    private function getTypeTitle($type, $typeid)
    {
        switch ($type) {
            case 'formation':
                return Formation::find($typeid)->title ?? 'Formation';
            case 'course':
                return Course::find($typeid)->title ?? 'Cours';
            case 'chapter':
                return Chapter::find($typeid)->title ?? 'Chapitre';
            default:
                return 'Type inconnu';
        }
    }
    /**
     * Traitement de la souscription et enregistrement en base.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour souscrire.');
        }

        // Vérifier l'existence de l'élément
        $item = null;
        $validated = $request->validate([
            'subscription_type' => 'required|in:formation,course,chapter',
            'subscription_typeid' => 'required|integer',
        ]);

        if ($validated['subscription_type'] === 'formation') {
            $item = Formation::findOrFail($validated['subscription_typeid']);
        } elseif ($validated['subscription_type'] === 'course') {
            $item = Course::findOrFail($validated['subscription_typeid']);
        } elseif ($validated['subscription_type'] === 'chapter') {
            $item = Chapter::findOrFail($validated['subscription_typeid']);
        }

        if (!$item) {
            return redirect()->back()->with('error', 'L\'élément sélectionné n\'existe pas.');
        }

        // Validation conditionnelle de payment_reference
        $paymentRules = ($item->price > 0) ? ['required', 'string', 'max:255', 'unique:subscriptions,payment_reference'] : ['nullable', 'string', 'max:255', 'unique:subscriptions,payment_reference'];

        $validated = $request->validate([
            'subscription_type' => 'required|in:formation,course,chapter',
            'subscription_typeid' => 'required|integer',
            'payment_reference' => $paymentRules,
        ]);

        // Vérification du prix
        $isValidated = $item->price == 0;
        $paymentReference = $isValidated ? null : $validated['payment_reference'];

        try {
            Subscription::create([
                'user_id' => $user->id,
                'formation_id' => $validated['subscription_type'] === 'formation' ? $validated['subscription_typeid'] : null,
                'course_id' => $validated['subscription_type'] === 'course' ? $validated['subscription_typeid'] : null,
                'chapter_id' => $validated['subscription_type'] === 'chapter' ? $validated['subscription_typeid'] : null,
                'type' => $validated['subscription_type'],
                'price' => $item->price,
                'payment_reference' => $paymentReference,
                'is_validated' => $isValidated, // Validation automatique si prix = 0
            ]);

            $message = $isValidated ? 'Votre souscription a été validée.' : 'Votre souscription a été envoyée pour validation.';
            return redirect()->route('dashboard')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la souscription, veuillez réessayer.');
        }
    }

    // use Yajra\DataTables\Facades\DataTables;

    public function listStudentSubscriptions()
    {
        // Récupération du nombre total d'étudiants sans souscription
        // Students that have no subscription record at all (ignores soft-delete status)
        // Only consider users that are not soft-deleted (deleted_at IS NULL)
        $studentsWithoutSubscriptions = User::where('role', 'student')
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                // Ensure we only consider non-deleted subscriptions when checking existence
                $query->select(DB::raw(1))
                    ->from('subscriptions')
                    ->whereRaw('subscriptions.user_id = users.id')
                    ->whereNull('subscriptions.deleted_at');
            });
        $data['countOf_studentsWithoutSubscriptions'] = $studentsWithoutSubscriptions->count();

        // Récupération du nombre total d'étudiants avec souscription
        // Only count subscriptions that are not soft-deleted and whose user is a non-deleted student
        $subscriptions = Subscription::whereNull('deleted_at')
            ->whereHas('user', function ($query) {
                $query->where('role', 'student')
                    ->whereNull('deleted_at');
            });
        $data['countOf_studentsWithSubscriptions'] = $subscriptions->count();

        // Vérification si la requête est AJAX
        if (request()->ajax()) {
            if (request()->query('type') === 'without_subscriptions') {
                // Select prenoms/nom/phones for non-deleted users and compute nom_complet and contacts
                $query = $studentsWithoutSubscriptions->select('id', 'name', 'email', 'prenoms', 'nom', 'phone_call', 'phone_whatsapp', 'updated_at');

                return DataTables::of($query)
                    ->addColumn('nom_complet', function ($user) {
                        $full = trim((($user->prenoms ?? '') . ' ' . ($user->nom ?? '')));
                        return $full !== '' ? $full : ($user->name ?? '');
                    })
                    ->filterColumn('nom_complet', function ($query, $keyword) {
                        $keyword = "%{$keyword}%";
                        $query->where(function ($q) use ($keyword) {
                            $q->whereRaw("CONCAT(COALESCE(prenoms,''), ' ', COALESCE(nom,'')) LIKE ?", [$keyword])
                              ->orWhere('name', 'like', $keyword);
                        });
                    })
                    ->addColumn('contacts', function ($user) {
                        $lines = [];
                        // Email (mailto)
                        if (!empty($user->email)) {
                            $lines[] = '<div><i class="bi bi-envelope-fill text-primary me-2"></i><a href="' . e('mailto:' . $user->email) . '">' . e($user->email) . '</a></div>';
                        }
                        // Phone call (tel)
                        if (!empty($user->phone_call)) {
                            $lines[] = '<div><i class="bi bi-telephone-fill text-primary me-2"></i><a href="' . e('tel:' . preg_replace('/\\s+/', '', $user->phone_call)) . '">' . e($user->phone_call) . '</a></div>';
                        }
                        // WhatsApp (wa.me) - strip non-digits for wa.me link
                        if (!empty($user->phone_whatsapp)) {
                            $digits = preg_replace('/\D+/', '', $user->phone_whatsapp);
                            $lines[] = '<div><i class="bi bi-whatsapp text-success me-2"></i><a href="' . e('https://wa.me/' . $digits) . '" target="_blank" rel="noopener noreferrer">' . e($user->phone_whatsapp) . '</a></div>';
                        }
                        return count($lines) ? implode('', $lines) : '';
                    })
                    ->filterColumn('contacts', function ($query, $keyword) {
                        $keyword = "%{$keyword}%";
                        $query->where(function ($q) use ($keyword) {
                            $q->where('email', 'like', $keyword)
                              ->orWhere('phone_call', 'like', $keyword)
                              ->orWhere('phone_whatsapp', 'like', $keyword);
                        });
                    })
                    ->rawColumns(['contacts'])
                    ->addColumn('last_login', function ($user) {
                        // use updated_at as a proxy for last activity if no dedicated column
                        return optional($user->updated_at)->format('Y-m-d H:i:s');
                    })
                    ->orderColumn('nom_complet', function ($query, $order) {
                        // order by prenoms then nom then name fallback
                        $query->orderBy('prenoms', $order)->orderBy('nom', $order)->orderBy('name', $order);
                    })
                    ->orderColumn('email', function ($query, $order) {
                        $query->orderBy('email', $order);
                    })
                    ->make(true);
            }

            if (request()->query('type') === 'with_subscriptions') {
            // Return one row per non-deleted subscription whose user is not soft-deleted
            // Use joins so searching and ordering can work on user fields and formation title
            $subQuery = Subscription::select(
                    'subscriptions.id',
                    'subscriptions.user_id',
                    'subscriptions.formation_id',
                    'subscriptions.payment_reference',
                    'subscriptions.created_at',
                    'subscriptions.is_validated',
                    'users.prenoms as user_prenoms',
                    'users.nom as user_nom',
                    'users.name as user_name',
                    'users.email as user_email',
                        'users.phone_call as user_phone_call',
                        'users.phone_whatsapp as user_phone_whatsapp',
                        'users.updated_at as user_updated_at',
                    'formations.title as formation_title'
                )
                ->join('users', 'users.id', '=', 'subscriptions.user_id')
                ->leftJoin('formations', 'formations.id', '=', 'subscriptions.formation_id')
                ->whereNull('subscriptions.deleted_at')
                ->where('users.role', 'student')
                ->whereNull('users.deleted_at');

            return DataTables::of($subQuery)
                        ->addColumn('payment_reference', fn($sub) => $sub->payment_reference)
                        ->addColumn('student', function ($sub) {
                            $full = trim((($sub->user_prenoms ?? '') . ' ' . ($sub->user_nom ?? '')));
                            $name = $full !== '' ? $full : ($sub->user_name ?? '');

                            $lines = [];
                            // name line
                            $lines[] = '<div><i class="bi bi-person-badge-fill text-primary me-2"></i><strong>' . e($name) . '</strong></div>';
                            // contacts (clickable)
                            if (!empty($sub->user_email)) {
                                $lines[] = '<div class="small text-muted"><i class="bi bi-envelope-fill text-primary me-1"></i><a href="' . e('mailto:' . $sub->user_email) . '">' . e($sub->user_email) . '</a></div>';
                            }
                            if (!empty($sub->user_phone_call)) {
                                $lines[] = '<div class="small text-muted"><i class="bi bi-telephone-fill text-primary me-1"></i><a href="' . e('tel:' . preg_replace('/\\s+/', '', $sub->user_phone_call)) . '">' . e($sub->user_phone_call) . '</a></div>';
                            }
                            if (!empty($sub->user_phone_whatsapp)) {
                                $digits = preg_replace('/\D+/', '', $sub->user_phone_whatsapp);
                                $lines[] = '<div class="small text-muted"><i class="bi bi-whatsapp text-success me-1"></i><a href="' . e('https://wa.me/' . $digits) . '" target="_blank" rel="noopener noreferrer">' . e($sub->user_phone_whatsapp) . '</a></div>';
                            }

                            // last login (from joined users.updated_at)
                            if (!empty($sub->user_updated_at)) {
                                $lines[] = '<div class="mt-1 small text-muted"><i class="bi bi-clock-fill me-1"></i>' . e(optional($sub->user_updated_at)->format('Y-m-d H:i:s')) . '</div>';
                            }
                            return implode('', $lines);
                        })
                        ->addColumn('formation_title', function ($sub) {
                            return $sub->formation_title ?? '';
                        })
                        ->filterColumn('formation_title', function ($query, $keyword) {
                            $keyword = "%{$keyword}%";
                            $query->where('formations.title', 'like', $keyword);
                        })
                        ->addColumn('status', function ($sub) {
                            return $sub->is_validated
                                ? '<span class="badge bg-success">Validée</span>'
                                : '<button class="btn btn-warning validate-subscription" data-id="' . $sub->id . '">Valider</button>';
                        })
                        ->filterColumn('status', function ($query, $keyword) {
                            // allow searching by validated/unvalidated text
                            $keyword = strtolower($keyword);
                            if (strpos('valid', $keyword) !== false || strpos('validée', $keyword) !== false || strpos('valide', $keyword) !== false) {
                                $query->where('subscriptions.is_validated', true);
                            } elseif (strpos('pending', $keyword) !== false || strpos('non', $keyword) !== false || strpos('attente', $keyword) !== false) {
                                $query->where('subscriptions.is_validated', false);
                            }
                        })
                        ->rawColumns(['student', 'status'])
                        ->filterColumn('student', function ($query, $keyword) {
                            $keyword = "%{$keyword}%";
                            $query->where(function ($q) use ($keyword) {
                                $q->whereRaw("CONCAT(COALESCE(users.prenoms,''),' ',COALESCE(users.nom,'')) LIKE ?", [$keyword])
                                  ->orWhere('users.name', 'like', $keyword)
                                  ->orWhere('users.email', 'like', $keyword)
                                  ->orWhere('users.phone_call', 'like', $keyword)
                                  ->orWhere('users.phone_whatsapp', 'like', $keyword);
                            });
                        })
                        ->filterColumn('last_login', function ($query, $keyword) {
                            // Allow searching date-like strings; convert to wildcard
                            $like = "%{$keyword}%";
                            $query->where('users.updated_at', 'like', $like);
                        })
                        ->filterColumn('payment_reference', function ($query, $keyword) {
                            $like = "%{$keyword}%";
                            $query->where('subscriptions.payment_reference', 'like', $like);
                        })
                        ->orderColumn('payment_reference', function ($query, $order) {
                            $query->orderBy('subscriptions.payment_reference', $order);
                        })
                        ->orderColumn('student', function ($query, $order) {
                            $query->orderBy('users.prenoms', $order)->orderBy('users.nom', $order)->orderBy('users.name', $order);
                        })
                        ->orderColumn('formation_title', function ($query, $order) {
                            $query->orderBy('formations.title', $order);
                        })
                        ->orderColumn('status', function ($query, $order) {
                            // order by is_validated boolean
                            $query->orderBy('subscriptions.is_validated', $order);
                        })
                        ->orderColumn('created_at', function ($query, $order) {
                            $query->orderBy('created_at', $order);
                        })
                        ->orderColumn('last_login', function ($query, $order) {
                            $query->orderBy('users.updated_at', $order);
                        })
                        ->make(true);
            }
        }

        return view('authenticated.owners.subscriptions.student-list', $data);
    }

    /**
     * Overview page showing totals and links to the two lists
     */
    public function subscriptionsOverview()
    {
        // reuse the same logic to compute counts
        $studentsWithoutSubscriptions = User::where('role', 'student')
            ->whereNull('deleted_at')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('subscriptions')
                    ->whereRaw('subscriptions.user_id = users.id')
                    ->whereNull('subscriptions.deleted_at');
            });

        $countWithout = $studentsWithoutSubscriptions->count();

        // Count distinct users who have at least one non-deleted subscription (any role)
        $countWith = Subscription::whereNull('subscriptions.deleted_at')
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->distinct('user_id')
            ->count('user_id');

        // Count total subscriptions (only those attached to a formation) to match the per-formation aggregation
        $totalSubscriptions = Subscription::whereNull('subscriptions.deleted_at')
            ->whereNotNull('subscriptions.formation_id')
            ->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->count();

        // Count users who have more than one non-deleted subscription
        // Count users (any role) who have more than one non-deleted subscription
        $countMultiple = Subscription::whereNull('subscriptions.deleted_at')
            ->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        // Aggregate subscriptions per formation (only non-deleted subscriptions and formations)
        // Aggregate subscriptions per formation (only non-deleted subscriptions and formations) across all users
        // Aggregate by formation using join to avoid N+1: total subscriptions and distinct users per formation
        $subsPerFormation = Subscription::join('formations', 'formations.id', '=', 'subscriptions.formation_id')
            ->whereNull('subscriptions.deleted_at')
            ->whereNotNull('subscriptions.formation_id')
            ->whereHas('user', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->groupBy('subscriptions.formation_id', 'formations.title')
            ->select(
                'subscriptions.formation_id',
                DB::raw('formations.title as formation_title'),
                DB::raw('COUNT(*) as total_subscriptions'),
                DB::raw('COUNT(DISTINCT subscriptions.user_id) as unique_users')
            )
            ->get()
            ->map(function ($row) {
                return [
                    'formation_id' => $row->formation_id,
                    'formation_title' => $row->formation_title ?? 'N/A',
                    'total_subscriptions' => $row->total_subscriptions,
                    'unique_users' => $row->unique_users,
                ];
            });

        return view('authenticated.owners.subscriptions.overview', [
            'countWithout' => $countWithout,
            'countWith' => $countWith,
            'totalSubscriptions' => $totalSubscriptions,
            'countMultiple' => $countMultiple,
            'subsPerFormation' => $subsPerFormation,
        ]);
    }

    /**
     * View for students without subscription (renders DataTable that calls listStudentSubscriptions?type=without_subscriptions)
     */
    public function studentsWithoutView()
    {
        return view('authenticated.owners.subscriptions.without-list');
    }

    /**
     * View for students with subscription (renders DataTable that calls listStudentSubscriptions?type=with_subscriptions)
     */
    public function studentsWithView()
    {
        return view('authenticated.owners.subscriptions.with-list');
    }



    public function validateSubscription(Subscription $subscription)
    {
        try {
            $subscription->update(['is_validated' => true]);

            return response()->json([
                'success' => true,
                'message' => "La souscription " . $subscription->type . " : " . $subscription->formation->title . " de " . $subscription->user->name . " a été validée.",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Une erreur est survenue lors de la validation.",
            ], 500);
        }
    }


    // Méthode Controller pour envoyer un mail de relance
    public function sendReminderEmail(Request $request, $student)
    {
        $student = User::findOrFail($student);

        $request->validate([
            'message' => 'required|string',
        ]);

        $content = $request->input('message'); // Changement de nom ici

        if (empty($student->email)) {
            Log::warning("Tentative d'envoi d'email à un étudiant sans adresse email.", ['student_id' => $student->id]);
            return redirect()->back()->with('error', "Impossible d'envoyer l'email : l'étudiant n'a pas d'adresse email.");
        }

        Log::info("Tentative d'envoi d'un email de relance.", [
            'student_id' => $student->id,
            'email' => $student->email,
            'message_preview' => substr($content, 0, 50) . '...', // Mise à jour du log
        ]);

        try {
            Mail::to($student->email)->send(new SubscriptionReminder($content, $student)); // Changement de nom ici
            Log::info("Email de relance envoyé avec succès.", ['student_id' => $student->id]);
            return redirect()->back()->with('success', "L'email de relance a été envoyé avec succès à {$student->email} !");
        } catch (\Exception $e) {
            Log::error("Échec de l'envoi de l'email de relance.", [
                'student_id' => $student->id,
                'email' => $student->email,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', "Une erreur est survenue lors de l'envoi de l'email : " . $e->getMessage());
        }
    }

    /**
     * Afficher la liste des souscriptions pour l'admin
     */
    public function index()
    {
               
        $subscriptions = Subscription::with(['user', 'formation', 'course', 'chapter'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('authenticated.owners.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Afficher le formulaire de création de souscription
     */
    public function create()
    {
        $users = User::where('role', 'student')->get();
        $formations = Formation::all();
        $courses = Course::all();
        $chapters = Chapter::all();

        return view('authenticated.owners.subscriptions.create', compact('users', 'formations', 'courses', 'chapters'));
    }

    /**
     * Enregistrer une nouvelle souscription
     */
    public function storeSubscription(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:formation,course,chapter',
            'formation_id' => 'nullable|required_if:type,formation|exists:formations,id',
            'course_id' => 'nullable|required_if:type,course|exists:courses,id',
            'chapter_id' => 'nullable|required_if:type,chapter|exists:chapters,id',
            'price' => 'required|integer|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'payment_reference' => 'nullable|string|max:255',
            'is_validated' => 'boolean',
        ]);

        try {
            $subscription = Subscription::create([
                'user_id' => $validated['user_id'],
                'formation_id' => $validated['type'] === 'formation' ? $validated['formation_id'] : null,
                'course_id' => $validated['type'] === 'course' ? $validated['course_id'] : null,
                'chapter_id' => $validated['type'] === 'chapter' ? $validated['chapter_id'] : null,
                'type' => $validated['type'],
                'price' => $validated['price'],
                'duration_in_days' => $validated['duration_in_days'],
                'expires_at' => now()->addDays($validated['duration_in_days']),
                'payment_reference' => $validated['payment_reference'],
                'is_validated' => $validated['is_validated'] ?? false,
            ]);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Souscription créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la souscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher le formulaire de modification de souscription
     */
    public function edit(Subscription $subscription)
    {
        $users = User::where('role', 'student')->get();
        $formations = Formation::all();
        $courses = Course::all();
        $chapters = Chapter::all();

        return view('authenticated.owners.subscriptions.edit', compact('subscription', 'users', 'formations', 'courses', 'chapters'));
    }

    /**
     * Mettre à jour une souscription
     */
    public function updateSubscription(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:formation,course,chapter',
            'formation_id' => 'nullable|required_if:type,formation|exists:formations,id',
            'course_id' => 'nullable|required_if:type,course|exists:courses,id',
            'chapter_id' => 'nullable|required_if:type,chapter|exists:chapters,id',
            'price' => 'required|integer|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'payment_reference' => 'nullable|string|max:255',
            'is_validated' => 'boolean',
        ]);

        try {
            $subscription->update([
                'user_id' => $validated['user_id'],
                'formation_id' => $validated['type'] === 'formation' ? $validated['formation_id'] : null,
                'course_id' => $validated['type'] === 'course' ? $validated['course_id'] : null,
                'chapter_id' => $validated['type'] === 'chapter' ? $validated['chapter_id'] : null,
                'type' => $validated['type'],
                'price' => $validated['price'],
                'duration_in_days' => $validated['duration_in_days'],
                'expires_at' => $subscription->created_at->addDays($validated['duration_in_days']),
                'payment_reference' => $validated['payment_reference'],
                'is_validated' => $validated['is_validated'] ?? false,
            ]);

            return redirect()->route('subscriptions.index')
                ->with('success', 'Souscription mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la souscription: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Soft delete d'une souscription
     */
    public function destroy(Subscription $subscription)
    {
        try {
            $subscription->delete();
            return redirect()->route('subscriptions.index')
                ->with('success', 'Souscription supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la souscription: ' . $e->getMessage());
        }
    }

    /**
     * Restaurer une souscription supprimée
     */
    public function restore($id)
    {
        try {
            $subscription = Subscription::withTrashed()->findOrFail($id);
            $subscription->restore();
            return redirect()->route('subscriptions.index')
                ->with('success', 'Souscription restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la restauration de la souscription: ' . $e->getMessage());
        }
    }

    /**
     * Étendre une souscription
     */
    public function extend(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'additional_days' => 'required|integer|min:1|max:365',
        ]);

        try {
            $subscription->extend($validated['additional_days']);
            return redirect()->back()
                ->with('success', "Souscription étendue de {$validated['additional_days']} jours.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'extension de la souscription: ' . $e->getMessage());
        }
    }

    /**
     * Définir une durée par défaut pour toutes les souscriptions
     */
    public function bulkUpdateDuration(Request $request)
    {
        $validated = $request->validate([
            'duration_in_days' => 'required|integer|min:1|max:365',
            'only_without_expiration' => 'boolean',
        ]);

        try {
            $query = Subscription::query();
            
            // Si coché, ne mettre à jour que les souscriptions sans date d'expiration
            if ($validated['only_without_expiration']) {
                $query->whereNull('expires_at');
            }

            $subscriptions = $query->get();
            $updatedCount = 0;

            foreach ($subscriptions as $subscription) {
                $subscription->duration_in_days = $validated['duration_in_days'];
                $subscription->expires_at = $subscription->created_at->addDays($validated['duration_in_days']);
                $subscription->save();
                $updatedCount++;
            }

            return redirect()->back()
                ->with('success', "{$updatedCount} souscription(s) mise(s) à jour avec une durée de {$validated['duration_in_days']} jours.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour groupée: ' . $e->getMessage());
        }
    }

    /**
     * Rechercher un étudiant par email 
     */
    public function searchStudent(Request $request)
    {

        $search = $request->get('search');
        
        if (empty($search)) {
            return response()->json(['users' => []]);
        }

        try {
            // Vérifier les permissions de l'utilisateur connecté
            $user = auth()->user();
            
            // Vérifier si l'utilisateur a les permissions de gérer les souscriptions
            if (!$user || !$user->hasPermissionTo('manage subscriptions')) {
                return response()->json(['error' => 'Permission non autorisée'], 403);
            }

            $users = User::where(function($query) use ($search) {
                    $query->where('email', 'LIKE', "%{$search}%");
                })
                ->with(['souscriptions' => function($query) {
                    $query->with(['formation', 'course', 'chapter']);
                }])
                ->limit(10)
                ->get();

            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la recherche'], 500);
        }
    }

    /*
     * Mettre à jour la durée de souscription d'un étudiant spécifique
     */
    public function updateStudentSubscriptionDuration(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subscription_id' => 'required|exists:subscriptions,id',
            'duration_in_days' => 'required|integer|min:1|max:365',
        ]);

        try {
            // Vérifier les permissions de l'utilisateur connecté
            $authUser = auth()->user();
            
            // Vérifier si l'utilisateur a les permissions de gérer les souscriptions
            if (!$authUser || !$authUser->hasPermissionTo('manage subscriptions')) {
                return response()->json(['error' => 'Permission non autorisée'], 403);
            }

            $user = User::findOrFail($validated['user_id']);
            $subscription = Subscription::findOrFail($validated['subscription_id']);

            // Vérifier que la souscription appartient bien à l'utilisateur
            if ($subscription->user_id != $user->id) {
                return response()->json(['error' => 'Cette souscription n\'appartient pas à cet utilisateur'], 400);
            }

            // Mettre à jour la durée et calculer la nouvelle expiration
            $subscription->duration_in_days = $validated['duration_in_days'];
            $subscription->expires_at = $subscription->created_at->addDays($validated['duration_in_days']);
            $subscription->save();

            return response()->json([
                'success' => true,
                'message' => "Souscription de {$user->name} mise à jour avec {$validated['duration_in_days']} jours",
                'subscription' => [
                    'id' => $subscription->id,
                    'duration_in_days' => $subscription->duration_in_days,
                    'expires_at' => $subscription->expires_at->format('d/m/Y H:i'),
                    'content' => $subscription->formation ? $subscription->formation->title : 
                              ($subscription->course ? $subscription->course->title : 
                              ($subscription->chapter ? $subscription->chapter->title : 'N/A'))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()], 500);
        }
    }

    
}
