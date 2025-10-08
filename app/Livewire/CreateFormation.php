<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Formation;
use App\Models\Course;
use App\Models\Chapter;
use Livewire\Component;

class CreateFormation extends Component
{
    public $categories = [];
    public $subcategories = [];
    public $courses = [];
    public $step = 1;

    public $category_id, $category_name, $category_description;
    public $subcategory_id, $subcategory_name, $subcategory_description;
    public $formation_title, $formation_description, $formation_price;
    public $course_title, $course_content;
    public $chapter_title, $chapter_content;

    public function mount($subcategory_id = null) // Ajout du paramètre optionnel
    {
        // Initialisation des catégories
        $this->categories = Category::all();

        // Si un $subcategory_id est fourni, charge la sous-catégorie associée
        if ($subcategory_id) {
            $this->subcategory_id = $subcategory_id;
            $this->subcategories = SubCategory::where('category_id', $this->category_id)->get();
        }
    }

    public function render()
    {
        return view('livewire.create-formation');
    }

    public function selectCategory($categoryId)
    {
        $this->category_id = $categoryId;
        $this->subcategories = SubCategory::where('category_id', $categoryId)->get();
    }

    public function saveCategory()
    {
        if (!$this->category_id && !$this->category_name) {
            $this->addError('category_id', 'Veuillez sélectionner ou créer une catégorie.');
            return;
        }

        if ($this->category_name) {
            $category = Category::create([
                'name' => $this->category_name,
                'description' => $this->category_description,
            ]);
            $this->categories = Category::all();
            $this->category_id = $category->id;
        }

        $this->step = 2;
    }

    public function selectSubcategory($subcategoryId)
    {
        $this->subcategory_id = $subcategoryId;
    }

    public function saveSubcategory()
    {
        if (!$this->subcategory_id && !$this->subcategory_name) {
            $this->addError('subcategory_id', 'Veuillez sélectionner ou créer une sous-catégorie.');
            return;
        }

        if ($this->subcategory_name) {
            $subcategory = SubCategory::create([
                'category_id' => $this->category_id,
                'name' => $this->subcategory_name,
                'description' => $this->subcategory_description,
            ]);
            $this->subcategories = SubCategory::where('category_id', $this->category_id)->get();
            $this->subcategory_id = $subcategory->id;
        }

        $this->step = 3;
    }

    public function saveFormation()
    {
        $this->validate([
            'formation_title' => 'required|string|max:255',
            'formation_description' => 'required|string',
            'formation_price' => 'required|numeric',
        ]);

        $formation = Formation::create([
            'category_id' => $this->category_id,
            'sub_category_id' => $this->subcategory_id,
            'title' => $this->formation_title,
            'description' => $this->formation_description,
            'price' => $this->formation_price,
        ]);

        $this->step = 4;
    }

    public function addCourse()
    {
        $this->validate([
            'course_title' => 'required|string|max:255',
            'course_content' => 'required|string',
        ]);

        $this->courses[] = [
            'title' => $this->course_title,
            'content' => $this->course_content,
            'chapters' => [],
        ];

        $this->course_title = '';
        $this->course_content = '';
    }

    public function addChapter($courseIndex)
    {
        $this->validate([
            'chapter_title' => 'required|string|max:255',
            'chapter_content' => 'required|string',
        ]);

        $this->courses[$courseIndex]['chapters'][] = [
            'title' => $this->chapter_title,
            'content' => $this->chapter_content,
        ];

        $this->chapter_title = '';
        $this->chapter_content = '';
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function saveAll()
    {
        $formation = Formation::latest()->first();

        foreach ($this->courses as $courseData) {
            $course = Course::create([
                'formation_id' => $formation->id,
                'title' => $courseData['title'],
                'content' => $courseData['content'],
            ]);

            foreach ($courseData['chapters'] as $chapterData) {
                Chapter::create([
                    'course_id' => $course->id,
                    'title' => $chapterData['title'],
                    'content' => $chapterData['content'],
                ]);
            }
        }

        session()->flash('success', 'Formation créée avec succès.');
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Formation créée!',
            'text' => 'Toutes les informations ont été enregistrées avec succès.',
        ]);
        return redirect()->to('/');
    }
}
