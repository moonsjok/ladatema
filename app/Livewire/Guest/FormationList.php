<?php
//app/Livewire/guest/formationList.php 
namespace App\Livewire\Guest;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Formation;
use App\Models\Category;
use App\Models\SubCategory;

class FormationList extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $selectedSubCategory = null;
    public $categories;
    public $subCategories = [];
    public $categoryDetails = null;
    public $loading = false; // Indicateur de chargement
    public $showCategoryList = false; // Propriété pour gérer l’affichage de la liste

    public function mount()
    {
        // Récupérer la catégorie sélectionnée depuis l'URL
        $this->selectedCategory = request()->query('selectedCategory', null);

        // Charger toutes les catégories
        $this->categories = Category::orderBy('id', 'desc')->get();

        // Si une catégorie est sélectionnée, charger ses sous-catégories
        if ($this->selectedCategory) {
            $this->selectCategory($this->selectedCategory);
        } else {
            // Sélectionner automatiquement la première catégorie si aucune catégorie sélectionnée
            $this->selectCategory($this->categories->first()->id ?? null);
            $this->toggleCategoryList(); // masque la liste sur mobile
        }
    }


    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->selectedSubCategory = null;
        $this->subCategories = SubCategory::where('category_id', $categoryId)->get();
        $this->categoryDetails = Category::find($categoryId);
        $this->resetPage(); // Réinitialiser la pagination
        $this->toggleCategoryList(); // masque la liste sur mobile
    }

    public function selectSubCategory($subCategoryId)
    {
        $this->selectedSubCategory = $subCategoryId;
        $this->resetPage(); // Réinitialiser la pagination

    }

    public function toggleCategoryList()
    {
        $this->showCategoryList = !$this->showCategoryList;
    }

    public function render()
    {
        $formations = Formation::query()
            ->when($this->selectedCategory, function ($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedSubCategory, function ($query) {
                $query->where('sub_category_id', $this->selectedSubCategory);
            })
            ->orderBy('created_at', 'desc') // Trier par ordre décroissant
            ->paginate(10); // Paginer par 10 formations par page

        return view('livewire.guest.formation-list', [
            'formations' => $formations,
        ]);
    }
}
