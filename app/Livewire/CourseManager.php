<?php
// app/livewire/CourseManager.php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use App\Models\Formation;

class CourseManager extends Component
{
    public $courses, $formations, $title, $description, $formation_id, $course_id;
    public $isCreateFormVisible = false;
    public $isEditFormVisible = false;
    public $selectedFormation = null;

    public function mount()
    {
        $this->formations = Formation::all();
        $this->courses = collect();
    }

    public function selectFormation($formationId)
    {
        $this->selectedFormation = $formationId;
        $this->loadCourses();
    }

    public function loadCourses()
    {
        if ($this->selectedFormation) {
            $this->courses = Course::where('formation_id', $this->selectedFormation)->with(['formation', 'chapters'])->get();
        } else {
            $this->courses = collect();
        }
    }

    public function showCreateForm()
    {
        if ($this->selectedFormation) {
            $this->resetFields();
            $this->isCreateFormVisible = true;
            $this->isEditFormVisible = false;
            $this->formation_id = $this->selectedFormation;
        }
    }

    public function showEditForm($courseId)
    {
        if ($this->selectedFormation) {
            $course = Course::find($courseId);
            if ($course && $course->formation_id == $this->selectedFormation) {
                $this->course_id = $course->id;
                $this->formation_id = $course->formation_id;
                $this->title = $course->title;
                $this->description = $course->description;

                $this->isEditFormVisible = true;
                $this->isCreateFormVisible = false;
            }
        }
    }

    public function hideForm()
    {
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
    }

    public function addCourse()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Course::create([
            'formation_id' => $this->formation_id,
            'title' => $this->title,
            'description' => $this->description,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Cours ajouté avec succès !'
        ]);

        $this->resetFields();
        $this->hideForm();
        $this->loadCourses();
    }

    public function updateCourse()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $course = Course::find($this->course_id);
        if ($course) {
            $course->update([
                'title' => $this->title,
                'description' => $this->description,
            ]);

            session()->flash('alert', [
                'type' => 'success',
                'message' => 'Cours mis à jour avec succès !'
            ]);

            $this->resetFields();
            $this->hideForm();
            $this->loadCourses();
        }
    }

    public function deleteCourse($id)
    {
        if ($this->selectedFormation) {
            $course = Course::find($id);

            if ($course && $course->formation_id == $this->selectedFormation) {
                $course->delete();
                session()->flash('alert', [
                    'type' => 'success',
                    'message' => 'Cours supprimé avec succès !'
                ]);
                $this->loadCourses();
            } else {
                session()->flash('alert', [
                    'type' => 'error',
                    'message' => 'Cours introuvable ou ne correspond pas à la formation sélectionnée.'
                ]);
            }
        }
    }

    public function resetFields()
    {
        $this->course_id = null;
        $this->title = '';
        $this->description = '';
    }

    public function render()
    {
        return view('livewire.course-manager');
    }
}
