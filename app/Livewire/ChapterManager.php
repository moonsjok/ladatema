<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Chapter;
use App\Models\Course;

class ChapterManager extends Component
{
    public $chapters, $courses, $title, $content, $course_id, $chapter_id;
    public $isCreateFormVisible = false;
    public $isEditFormVisible = false;

    public function mount()
    {
        $this->chapters = Chapter::with('course')->get();
        $this->courses = Course::all();
    }

    public function showCreateForm()
    {
        $this->resetFields();
        $this->isCreateFormVisible = true;
        $this->isEditFormVisible = false;
    }

    public function showEditForm($chapterId)
    {
        $chapter = Chapter::find($chapterId);
        $this->chapter_id = $chapter->id;
        $this->course_id = $chapter->course_id;
        $this->title = $chapter->title;
        $this->content = $chapter->content;

        $this->isEditFormVisible = true;
        $this->isCreateFormVisible = false;
    }

    public function hideForm()
    {
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
    }

    public function addChapter()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        Chapter::create([
            'course_id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Chapitre ajouté avec succès !'
        ]);

        $this->resetFields();
        $this->hideForm();
        $this->mount();
    }

    public function updateChapter()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $chapter = Chapter::find($this->chapter_id);
        $chapter->update([
            'course_id' => $this->course_id,
            'title' => $this->title,
            'content' => $this->content,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Chapitre mis à jour avec succès !'
        ]);

        $this->resetFields();
        $this->hideForm();
        $this->mount();
    }

    public function deleteChapter($id)
    {
        $chapter = Chapter::find($id);

        if ($chapter) {
            $chapter->delete();
            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Chapitre supprimé avec succès !'
            ]);
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'message' => 'Chapitre introuvable.'
            ]);
        }

        $this->mount();
    }

    public function resetFields()
    {
        $this->chapter_id = null;
        $this->course_id = null;
        $this->title = '';
        $this->content = '';
    }

    public function render()
    {
        return view('livewire.chapter-manager');
    }
}
