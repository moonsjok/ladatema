<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            $folder = '';
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $folder = 'images';
            } elseif (in_array($extension, ['mp4', 'webm', 'ogg'])) {
                $folder = 'videos';
            } elseif (in_array($extension, ['pdf'])) {
                $folder = 'pdfs';
            } elseif (in_array($extension, ['txt'])) {
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

                // Génération d'un lien signé temporaire
                $expiration = now()->addMinutes(60); // Accès valide pour 60 minutes par exemple
                // $signedUrl = Storage::disk('local')->temporaryUrl($filePath, $expiration);

                $url = Storage::url($filePath);
                Log::debug('File url', ['url' => $url]);
                return response()->json(['location' => $url]);
                // return response()->json(['location' => $signedUrl]);
            } else {
                Log::error('Could not create or access directory', ['path' => $pathInStorage]);
                return response()->json(['error' => 'Impossible de créer ou d\'accéder au répertoire de stockage.'], 500);
            }
        }

        return response()->json(['error' => 'Aucun fichier n\'a été uploadé.'], 400);
    }
}
