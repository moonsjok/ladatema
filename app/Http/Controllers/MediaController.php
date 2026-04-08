<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\User;

class MediaController extends Controller
{
    // Types de fichiers supportés
    private const FILE_TYPES = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'videos' => ['mp4', 'webm'], // Formats web compatibles
        'documents' => ['pdf', 'txt', 'md'], // Documents
        'pdfs' => ['pdf'],
        'txt_files' => ['txt', 'md']
    ];

    // Mapping des types vers les collections
    private const COLLECTION_MAPPING = [
        'images' => 'images',
        'videos' => 'videos',
        'pdfs' => 'pdfs',
        'txt_files' => 'txt_files',
        'documents' => 'documents'
    ];

    /**
     * Afficher la liste des fichiers par type
     */
    public function index($type)
    {
        if (!array_key_exists($type, self::FILE_TYPES)) {
            abort(404, 'Type de fichier non valide');
        }

        return view('authenticated.owners.media.index', compact('type'));
    }

    /**
     * Récupérer les données pour DataTables
     */
    public function getData(Request $request, $type)
    {
        if (!array_key_exists($type, self::FILE_TYPES)) {
            return response()->json(['error' => 'Type de fichier non valide'], 404);
        }

        if ($request->ajax()) {
            $mediaCollection = self::COLLECTION_MAPPING[$type];
            
            $query = Media::where('collection_name', $mediaCollection);
            
            // Filtrer par type de fichier exact
            if ($type === 'images') {
                $query->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
            } elseif ($type === 'videos') {
                $query->whereIn('mime_type', ['video/mp4', 'video/webm']);
            } elseif ($type === 'pdfs') {
                $query->where('mime_type', 'application/pdf');
            } elseif ($type === 'txt_files') {
                $query->whereIn('mime_type', ['text/plain', 'text/markdown']);
            }
            
            // Si l'utilisateur n'est pas admin, ne montrer que ses médias ou les publics
            if (auth()->user()->role !== 'dev' && auth()->user()->role !== 'owner') {
                $query->where(function($q) {
                    $q->where('model_type', User::class)
                      ->where('model_id', auth()->id())
                      ->orWhere('custom_properties->is_public', true);
                });
            }

            return DataTables::of($query)
                ->addColumn('preview', function ($media) use ($type) {
                    // Utiliser l'URL sécurisée pour tous les aperçus
                    $secureUrl = route('media.file.secure', $media->id);
                    
                    if ($type === 'images') {
                        return '
                        <div class="img-thumbnail text-center">
                        <img src="' . $secureUrl . '" alt="' . $media->name . '" class="-img-thumbnail" style=".max-width: 60px; max-height: 200px; cursor: pointer;" onclick="window.open(\'' . $secureUrl . '\', \'_blank\')">
                        </div>
                        ';
                    
                    } elseif ($type === 'videos') {
                        return '<video class="img-thumbnail bg-none" style=".max-width: 300px; max-height: 200px;" controls onclick="window.open(\'' . $secureUrl . '\', \'_blank\')">
                                    <source src="' . $secureUrl . '" type="video/mp4">
                                </video>';
                    } else {
                        return '<i class="bi bi-file-text fs-4 text-secondary" style="cursor: pointer;" onclick="window.open(\'' . $secureUrl . '\', \'_blank\')"></i>';
                    }
                })
                ->addColumn('name', function ($media) {
                    $modelName = $media->getCustomProperty('name', $media->name);
                    $description = $media->getCustomProperty('description', '');
                    $mediaSize = number_format($media->size / 1024, 2) . ' KB';
                    $mediaCreatedAt = $media->created_at->format('d/m/Y H:i');
                    return '<strong>' . $modelName . '</strong>
                    <br>
                    Taille : '. $mediaSize .'
                    <br>
                    Date : '.  $mediaCreatedAt  .'
                    <br>
                    <small class="text-muted">' . $description . '</small>';
                })
                // ->addColumn('size', function ($media) {
                //     return number_format($media->size / 1024, 2) . ' KB';
                // })
                // ->addColumn('created_at', function ($media) {
                //     return $media->created_at->format('d/m/Y H:i');
                // })
                ->addColumn('owner', function ($media) {
                    if ($media->model_type === User::class) {
                        $user = User::find($media->model_id);
                        return $user ? $user->name : 'Utilisateur supprimé';
                    }
                    return 'Système';
                })
                ->addColumn('actions', function ($media) {
                    $secureUrl = route('media.file.secure', $media->id);
                    $showBtn = '<a href="' . $secureUrl . '" 
                                   class="btn btn-sm btn-outline-primary me-1" 
                                   target="_blank"
                                   title="Voir">
                                   <i class="bi bi-eye"></i>
                               </a>';
                    
                    // Bouton copier URL
                    $copyBtn = '<button class="btn btn-sm btn-outline-info me-1" 
                                       onclick="copyUrl(\'' . $secureUrl . '\', \'' . $media->getCustomProperty('name', $media->name) . '\')" 
                                       title="Copier l\'URL">
                                       <i class="bi bi-clipboard"></i>
                                   </button>';
                    
                    // Actions de modification seulement pour le propriétaire ou admin
                    if (auth()->user()->role === 'dev' || auth()->user()->role === 'owner' || 
                        ($media->model_type === User::class && $media->model_id === auth()->id())) {
                        
                        $editBtn = '<a href="' . route('media.edit', [$media->collection_name, $media->id]) . '" 
                                       class="btn btn-sm btn-outline-warning me-1" 
                                       title="Modifier">
                                       <i class="bi bi-pencil"></i>
                                   </a>';
                        
                        $deleteBtn = '<form action="' . route('media.destroy', [$media->collection_name, $media->id]) . '" 
                                          method="POST" 
                                          style="display: inline-block;"
                                          onsubmit="return confirm(\'Êtes-vous sûr de vouloir supprimer ce média ?\')">
                                          ' . csrf_field() . method_field('DELETE') . '
                                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                              <i class="bi bi-trash"></i>
                                          </button>
                                      </form>';
                        
                        return $showBtn . $copyBtn . $editBtn . $deleteBtn;
                    }
                    
                    return $showBtn . $copyBtn;
                })
                ->rawColumns(['preview', 'name', 'actions'])
                ->make(true);
        }

        return response()->json(['error' => 'Requête non valide'], 400);
    }

    /**
     * Afficher le formulaire de création
     */
    public function create($type)
    {
        if (!array_key_exists($type, self::FILE_TYPES)) {
            abort(404, 'Type de fichier non valide');
        }

        return view('authenticated.owners.media.create', compact('type'));
    }

    /**
     * Stocker un nouveau fichier
     */
    public function store(Request $request, $type)
    {
        if (!array_key_exists($type, self::FILE_TYPES)) {
            abort(404, 'Type de fichier non valide');
        }

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:' . ($type === 'videos' ? '2048000' : '10240'), // 2GB pour vidéos, 10MB pour autres
                'mimes:' . implode(',', self::FILE_TYPES[$type])
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ]);

        $mediaCollection = self::COLLECTION_MAPPING[$type];
        //dd($mediaCollection);
        // Utiliser l'utilisateur connecté pour ajouter le média
        $user = auth()->user();
        
        // Ajouter le fichier avec les métadonnées
        $media = $user->addMediaFromRequest('file')
            ->withCustomProperties([
                'name' => $request->name,
                'description' => $request->description,
                'is_public' => $request->boolean('is_public', false)
            ])
            ->toMediaCollection($mediaCollection);

        return redirect()->route('media.index', $type)
            ->with('success', 'Fichier uploadé avec succès: ' . $request->name);
    }

    /**
     * Afficher un fichier spécifique
     */
    public function show($type, $mediaId)
    {
        if (!auth()->check()) {
            return response()->json([
                'error' => 'Accès non autorisé',
                'message' => 'Vous devez être connecté pour accéder à ce fichier.',
                'redirect' => route('login')
            ], 401);
        }

        $media = Media::findOrFail($mediaId);
        
        // Vérifier les permissions
        if (auth()->user()->role !== 'dev' && auth()->user()->role !== 'owner') {
            if ($media->model_type !== User::class || $media->model_id !== auth()->id()) {
                if (!$media->getCustomProperty('is_public', false)) {
                    return response()->json([
                        'error' => 'Accès non autorisé',
                        'message' => 'Ce fichier est privé ou ne vous appartient pas.'
                    ], 403);
                }
            }
        }

        return view('authenticated.owners.media.show', compact('media'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($type, $mediaId)
    {
        $media = Media::findOrFail($mediaId);
        
        // Vérifier les permissions
        if (auth()->user()->role !== 'dev' && auth()->user()->role !== 'owner') {
            if ($media->model_type !== User::class || $media->model_id !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
        }

        return view('authenticated.owners.media.edit', compact('media', 'type'));
    }

    /**
     * Mettre à jour un média
     */
    public function update(Request $request, $type, $mediaId)
    {
        $media = Media::findOrFail($mediaId);
        
        // Vérifier les permissions
        if (auth()->user()->role !== 'dev' && auth()->user()->role !== 'owner') {
            if ($media->model_type !== User::class || $media->model_id !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
        }

        // Valider les métadonnées
        $validationRules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
        ];

        // Ajouter la validation du fichier si présent
        if ($request->hasFile('file')) {
            $validationRules['file'] = [
                'file',
                'max:' . ($type === 'videos' ? '2048000' : '10240'), // 2GB pour vidéos, 10MB pour autres
                'mimes:' . implode(',', self::FILE_TYPES[$type])
            ];
        }

        $request->validate($validationRules);

        // Mettre à jour les métadonnées
        $media->setCustomProperty('name', $request->name);
        $media->setCustomProperty('description', $request->description);
        $media->setCustomProperty('is_public', $request->boolean('is_public', false));
        $media->save();

        // Remplacer le fichier si un nouveau est uploadé
        if ($request->hasFile('file')) {
            try {
                // Sauvegarder l'ID original
                $originalId = $media->id;
                
                // Ajouter le nouveau fichier
                $newMedia = auth()->user()->addMediaFromRequest('file')
                    ->withCustomProperties([
                        'name' => $request->name,
                        'description' => $request->description,
                        'is_public' => $request->boolean('is_public', false)
                    ])
                    ->toMediaCollection($media->collection_name);
                
                // Mettre à jour le média original avec les nouvelles informations du fichier
                // sans supprimer l'enregistrement original
                \DB::table('media')
                    ->where('id', $originalId)
                    ->update([
                        'file_name' => $newMedia->file_name,
                        'mime_type' => $newMedia->mime_type,
                        'size' => $newMedia->size,
                        'disk' => $newMedia->disk,
                        'conversions_disk' => $newMedia->conversions_disk,
                        'custom_properties' => $newMedia->custom_properties,
                        'generated_conversions' => $newMedia->generated_conversions,
                        'responsive_images' => $newMedia->responsive_images,
                        'updated_at' => now()
                    ]);
                
                // Supprimer le nouveau média en double
                $newMedia->delete();
                
                // Recharger le média original avec les nouvelles informations
                $media = Media::find($originalId);
                
            } catch (\Exception $e) {
                // Masquer les détails techniques pour des raisons de sécurité
                \Log::error('Erreur lors du remplacement du média: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'Une erreur est survenue lors du remplacement du fichier. Veuillez réessayer.')
                    ->withInput();
            }
        }

        return redirect()->route('media.index', $type)
            ->with('success', 'Média mis à jour avec succès!');
    }

    /**
     * Supprimer un média
     */
    public function destroy($type, $mediaId)
    {
        $media = Media::findOrFail($mediaId);
        
        // Vérifier les permissions
        if (auth()->user()->role !== 'dev' && auth()->user()->role !== 'owner') {
            if ($media->model_type !== User::class || $media->model_id !== auth()->id()) {
                abort(403, 'Accès non autorisé');
            }
        }

        // Supprimer uniquement ce média, pas toute la collection
        $media->delete();

        return redirect()->route('media.index', $type)
            ->with('success', 'Média supprimé avec succès!');
    }

    /**
     * Nettoyer le nom du fichier
     */
    private function sanitizeFileName($name)
    {
        // Supprimer les caractères spéciaux et les espaces
        $name = preg_replace('/[^a-zA-Z0-9-_]/', '_', $name);
        return trim($name, '_');
    }

    /**
     * Générer un nom de fichier unique si nécessaire
     */
    private function generateUniqueFileName($path, $fileName)
    {
        if (!Storage::exists($path . '/' . $fileName)) {
            return $fileName;
        }

        $pathInfo = pathinfo($fileName);
        $baseName = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        $counter = 1;

        do {
            $newFileName = $baseName . '_' . $counter . $extension;
            $counter++;
        } while (Storage::exists($path . '/' . $newFileName));

        return $newFileName;
    }

    // Ancienne méthode upload conservée pour compatibilité
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            $folder = '';
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $folder = 'images';
            } elseif (in_array($extension, ['mp4', 'webm'])) {
                $folder = 'videos';
            } elseif (in_array($extension, ['pdf'])) {
                $folder = 'pdfs';
            } elseif (in_array($extension, ['txt', 'md'])) {
                $folder = 'txt_files';
            }

            $pathInStorage = 'public/' . $folder;

            // Création du dossier si il n'existe pas
            if (!Storage::exists($pathInStorage)) {
                Storage::makeDirectory($pathInStorage);
                Log::debug('Directory created', ['path' => $pathInStorage]);
            } else {
                Log::debug('Directory already exists', ['path' => $pathInStorage]);
            }

            if (Storage::exists($pathInStorage)) {
                $fileName = $file->hashName(); // Génère un nom unique pour le fichier
                $filePath = $pathInStorage . '/' . $fileName;

                // Déplacer le fichier directement
                $file->move(storage_path('app/' . $pathInStorage), $fileName);
                Log::debug('File moved', ['path' => $filePath]);

                $url = Storage::url($filePath);
                Log::debug('File url', ['url' => $url]);
                return response()->json(['location' => $url]);
            } else {
                Log::error('Could not create or access directory', ['path' => $pathInStorage]);
                return response()->json(['error' => 'Impossible de créer ou d\'accéder au répertoire de stockage.'], 500);
            }
        }

        return response()->json(['error' => 'Aucun fichier n\'a été uploadé.'], 400);
    }
}
