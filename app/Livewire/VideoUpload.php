<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class VideoUpload extends Component
{
    use WithFileUploads;

    public $video;
    public $videoPath = null;  // Pour stocker le chemin de la vidéo téléchargée
    public $videoPreview = null; // Pour stocker l'aperçu de la vidéo (avant upload)

    // Règles de validation
    protected $rules = [
        'video' => 'required|mimes:mp4,webm,ogg,avi,mkv,flv|max:1024000', // 1 Go = 1024000 Ko
    ];

    public function updatedVideo()
    {
        $this->validate(); // Validation dès que la vidéo est sélectionnée
        // Générer un aperçu de la vidéo si possible
        if ($this->video) {
            $this->videoPreview = $this->video->temporaryUrl();
        }
    }

    public function upload()
    {
        $this->validate(); // Validation avant de traiter le téléchargement

        // Enregistrer la vidéo et obtenir son chemin
        $this->videoPath = $this->video->store('videos', 'public');

        session()->flash('message', 'Vidéo téléchargée avec succès!');
    }

    public function render()
    {
        return view('livewire.video-upload');
    }
}
