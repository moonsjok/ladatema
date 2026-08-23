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

    public function mount($courseId, $chapterId = null)
    {
        $this->selectCourse($courseId, $chapterId);
    }

    public function selectCourse($courseId, $preferredChapterId = null)
    {
        $this->selectedCourse = Course::findOrFail($courseId);
        $this->chapters = $this->selectedCourse->chapters()->orderBy('numero', 'asc')->get();

        if ($this->chapters->isNotEmpty()) {
            if ($preferredChapterId && $this->chapters->contains('id', $preferredChapterId)) {
                $this->selectChapter($preferredChapterId);
            } else {
                $this->selectChapter($this->chapters->first()->id);
            }
        } else {
            $this->selectedChapter = null;
        }

        $this->loadAdjacentCourses();
    }

    public function loadAdjacentCourses()
    {
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
        $this->selectedChapter = Chapter::find($chapterId);
        $this->dispatch('chapter-changed');
    }

    public function nextChapter()
    {
        if (!$this->selectedChapter) {
            if ($this->chapters->isNotEmpty()) {
                $this->selectChapter($this->chapters->first()->id);
            }
            return;
        }

        $nextChapter = $this->chapters
            ->where('numero', '>', $this->selectedChapter->numero)
            ->sortBy('numero')
            ->first();

        if ($nextChapter) {
            $this->selectChapter($nextChapter->id);
        } else if ($this->nextCourse) {
            $this->goToNextCourse();
        }
    }

    public function previousChapter()
    {
        if (!$this->selectedChapter) {
            return;
        }

        $previousChapter = $this->chapters
            ->where('numero', '<', $this->selectedChapter->numero)
            ->sortByDesc('numero')
            ->first();

        if ($previousChapter) {
            $this->selectChapter($previousChapter->id);
        } else if ($this->previousCourse) {
            $this->goToPreviousCourse();
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
        $currentIndex = 0;
        $totalChapters = $this->chapters->count();
        $progressPercent = 0;

        if ($this->selectedChapter && $totalChapters > 0) {
            $sortedList = $this->chapters->pluck('id')->values()->all();
            $pos = array_search($this->selectedChapter->id, $sortedList);
            $currentIndex = ($pos !== false) ? $pos + 1 : 1;
            $progressPercent = round(($currentIndex / $totalChapters) * 100);
        }

        return view('livewire.authenticated.course-viewer', [
            'chapters' => $this->chapters,
            'selectedChapter' => $this->selectedChapter,
            'selectedCourse' => $this->selectedCourse,
            'nextCourse' => $this->nextCourse,
            'previousCourse' => $this->previousCourse,
            'currentIndex' => $currentIndex,
            'totalChapters' => $totalChapters,
            'progressPercent' => $progressPercent,
        ]);
    }
}
