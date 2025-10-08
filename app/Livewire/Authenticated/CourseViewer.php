<?php

namespace App\Livewire\Authenticated;

use Livewire\Component;
use App\Models\Course;
use App\Models\Chapter;

class CourseViewer extends Component
{
    public $selectedCourse = null;
    public $chapters = [];
    public $selectedChapter = null;
    public $nextCourse = null;
    public $previousCourse = null;

    public function mount($courseId)
    {
        $this->selectCourse($courseId);
    }

    public function selectCourse($courseId)
    {
        $this->selectedCourse = Course::findOrFail($courseId);
        $this->chapters = $this->selectedCourse->chapters()->orderBy('numero')->get(); // Ordonner les chapitres par numéro

        // Vérifier s'il y a des chapitres avant de sélectionner le premier
        if ($this->chapters->isNotEmpty()) {
            $this->selectChapter($this->chapters->first()->id);
        } else {
            $this->selectedChapter = null; // Aucun chapitre disponible
        }
        $this->loadAdjacentCourses();
    }

    public function loadAdjacentCourses()
    {
        // Charger le cours suivant et précédent
        $this->nextCourse = Course::where('formation_id', $this->selectedCourse->formation_id)
            ->where('id', '>', $this->selectedCourse->id)
            ->orderBy('id', 'asc')
            ->first();

        $this->previousCourse = Course::where('formation_id', $this->selectedCourse->formation_id)
            ->where('id', '<', $this->selectedCourse->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function selectChapter($chapterId)
    {
        $this->selectedChapter = Chapter::findOrFail($chapterId);
    }

    public function nextChapter()
    {
        // Sélectionner le chapitre suivant
        $nextChapter = $this->chapters->where('numero', '>', $this->selectedChapter->numero)->first();

        if ($nextChapter) {
            $this->selectChapter($nextChapter->id);
        } else {
            // Si c'est le dernier chapitre, naviguer vers le prochain cours
            $this->goToNextCourse();
        }
    }

    public function previousChapter()
    {
        // Sélectionner le chapitre précédent
        $previousChapter = $this->chapters->where('numero', '<', $this->selectedChapter->numero)->first();

        if ($previousChapter) {
            $this->selectChapter($previousChapter->id);
        }
    }

    public function goToNextCourse()
    {
        if ($this->nextCourse) {
            $this->selectCourse($this->nextCourse->id);
        }
    }

    public function goToPreviousCourse()
    {
        if ($this->previousCourse) {
            $this->selectCourse($this->previousCourse->id);
        }
    }

    public function render()
    {
        return view('livewire.authenticated.course-viewer', [
            'chapters' => $this->chapters,
            'selectedChapter' => $this->selectedChapter,
            'selectedCourse' => $this->selectedCourse,
            'nextCourse' => $this->nextCourse,
            'previousCourse' => $this->previousCourse,
        ]);
    }
}
