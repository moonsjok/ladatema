<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Formation;
use App\Models\Category;
use App\Models\SubCategory;

class FormationManager extends Component
{
    public $formations, $title, $description, $price, $category_id, $sub_category_id, $formation_id;
    public $isCreateFormVisible = false;
    public $isEditFormVisible = false;
    public $categories, $subCategories;
    public $selectedCategory = null;
    public $selectedSubCategory = null;

    public function mount()
    {
        $this->categories = Category::all();
        $this->subCategories = collect();
        $this->formations = collect();
    }

    public function showCreateForm()
    {
        $this->resetFields();
        $this->category_id = $this->selectedCategory;
        $this->sub_category_id = $this->selectedSubCategory;
        $this->isCreateFormVisible = true;
        $this->isEditFormVisible = false;
    }

    public function showEditForm($formationId)
    {
        $formation = Formation::with(['category', 'subcategory'])->find($formationId);
        if ($formation) {
            $this->formation_id = $formation->id;
            $this->category_id = $formation->category_id;
            $this->sub_category_id = $formation->sub_category_id;
            $this->title = $formation->title;
            $this->description = $formation->description;
            $this->price = $formation->price;
            $this->isEditFormVisible = true;
            $this->isCreateFormVisible = false;
        }
    }

    public function hideForm()
    {
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
    }

    public function loadSubCategories($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->subCategories = SubCategory::where('category_id', $categoryId)->get();
        $this->selectedSubCategory = null;
        $this->loadFormations();
    }

    public function selectSubCategory($subCategoryId)
    {
        $this->selectedSubCategory = $subCategoryId;
        $this->loadFormations();
    }

    public function loadFormations()
    {
        if ($this->selectedCategory && $this->selectedSubCategory) {
            $this->formations = Formation::where('category_id', $this->selectedCategory)
                ->where('sub_category_id', $this->selectedSubCategory)
                ->with(['category', 'subcategory'])
                ->get();
        } else {
            $this->formations = collect();
        }
    }

    public function addFormation()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        Formation::create([
            'category_id' => $this->selectedCategory,
            'sub_category_id' => $this->selectedSubCategory,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
        ]);

        $this->resetFields();
        $this->loadFormations();
        $this->hideForm();
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Success Title',
            'message' => 'Formation Added Successfully!'
        ]);
    }

    public function updateFormation()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        $formation = Formation::find($this->formation_id);
        if ($formation) {
            $formation->update([
                'category_id' => $this->category_id,
                'sub_category_id' => $this->sub_category_id,
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price,
            ]);

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Success Title',
                'message' => 'Formation Updated Successfully!'
            ]);
            $this->resetFields();
            $this->loadFormations();
            $this->hideForm();
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'title' => 'Error Title',
                'message' => 'Formation not found for update.'
            ]);
        }
    }

    public function deleteFormation($id)
    {
        $formation = Formation::find($id);

        if ($formation) {
            $formation->delete();
            $this->loadFormations();
            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Success Title',
                'message' => 'Formation Deleted Successfully!'
            ]);
        } else {
            session()->flash('alert', [
                'type' => 'error',
                'title' => 'Error Title',
                'message' => 'Formation not found.'
            ]);
        }
    }

    public function resetFields()
    {
        $this->title = '';
        $this->description = '';
        $this->price = '';
        $this->category_id = null;
        $this->sub_category_id = null;
        $this->formation_id = null;
    }

    public function render()
    {
        return view('livewire.formation-manager');
    }
}
