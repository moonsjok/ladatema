<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryManager extends Component
{
    public $subCategories, $name, $description, $subCategory_id, $category_id, $selectedCategory = null;
    public $isCreateFormVisible = false;
    public $isEditFormVisible = false;
    public $categories;

    public function mount()
    {
        $this->categories = Category::all();
        $this->subCategories = collect([]);
    }

    public function loadSubCategories()
    {
        if ($this->selectedCategory) {
            $this->subCategories = SubCategory::where('category_id', $this->selectedCategory)->with('category')->get();
        } else {
            $this->subCategories = collect([]);
        }
    }

    public function showCreateForm()
    {
        if ($this->selectedCategory) {
            $this->resetFields();
            $this->isCreateFormVisible = true;
            $this->isEditFormVisible = false;
        }
    }

    public function showEditForm($subCategoryId)
    {
        if ($this->selectedCategory) {
            $subCategory = SubCategory::with('category')->find($subCategoryId);
            $this->subCategory_id = $subCategory->id;
            $this->category_id = $subCategory->category_id;
            $this->name = $subCategory->name;
            $this->description = $subCategory->description;
            $this->isEditFormVisible = true;
            $this->isCreateFormVisible = false;
        }
    }

    public function hideForm()
    {
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
    }

    public function addSubCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        SubCategory::create([
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        $this->resetFields();
        $this->isCreateFormVisible = false;
        $this->loadSubCategories();
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Success Title',
            'message' => 'SubCategory Added Successfully!'
        ]);
    }

    public function updateSubCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $subCategory = SubCategory::find($this->subCategory_id);
        $subCategory->update([
            'category_id' => $this->category_id,
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Success Title',
            'message' => 'SubCategory Updated Successfully!'
        ]);
        $this->resetFields();
        $this->loadSubCategories();
        $this->hideForm();
    }

    public function deleteSubCategory($id)
    {
        if ($this->selectedCategory) {
            $subCategory = SubCategory::find($id);

            if ($subCategory) {
                $subCategory->delete();
                $this->loadSubCategories();
                session()->flash('alert', [
                    'type' => 'success',
                    'title' => 'Success Title',
                    'message' => 'SubCategory Deleted Successfully!'
                ]);
            } else {
                session()->flash('alert', [
                    'type' => 'error',
                    'title' => 'Error Title',
                    'message' => 'SubCategory not found.'
                ]);
            }
        }
    }

    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->category_id = $this->selectedCategory;
        $this->subCategory_id = null;
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->loadSubCategories();
    }

    public function clearSelection()
    {
        $this->selectedCategory = null;
        $this->subCategories = collect([]);
        $this->hideForm();
    }

    public function render()
    {
        return view('livewire.sub-category-manager');
    }
}
