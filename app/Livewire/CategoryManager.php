<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Alert;

class CategoryManager extends Component
{
    public $categories, $name, $description, $category_id, $selectedCategory;
    public $isCreateFormVisible = false;
    public $isEditFormVisible = false;
    public $isEdit = false;

    public function mount()
    {
        $this->categories = Category::all();
        alert()->success('Title', 'Lorem Lorem Lorem');
    }

    public function showCreateForm()
    {
        $this->isCreateFormVisible = true;
        $this->isEditFormVisible = false;
    }

    public function showEditForm($categoryId)
    {
        $category = Category::find($categoryId);
        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->isEditFormVisible = true;
        $this->isCreateFormVisible = false;
    }

    public function hideForm()
    {
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
    }

    public function addCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);
        $this->isCreateFormVisible = false;
        $this->isEditFormVisible = false;
        session()->flash('alert', [
            'type' => 'success',
            'title' => 'Success Title',
            'message' => 'Category Added Successfully!'
        ]);
        $this->resetFields();
        $this->hideForm();
    }

    public function editCategory($id)
    {
        $category = Category::find($id);
        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->isEdit = true;
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($this->category_id);
        $category->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Category Updated Successfully!');
        $this->resetFields();
        $this->hideForm();
    }

    public function deleteCategory($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();

            // // Rafraîchir la liste des catégories
            $this->categories = Category::all();

            session()->flash('message', 'Category Deleted Successfully!');
        } else {
            session()->flash('error', 'Category not found.');
        }
        $this->hideForm();
    }


    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->category_id = null;
        $this->isEdit = false;
        $this->mount();
    }


    public function render()
    {
        return view('livewire.category-manager', []);
    }
}
