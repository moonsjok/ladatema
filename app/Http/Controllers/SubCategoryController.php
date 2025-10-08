<?php
// app/Http/Controllers/SubCategoryController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('category')->get();
        return view('authenticated.owners.subcategories.index', compact('subcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);
        SubCategory::create($request->all());
        return redirect()->route('authenticated.owners.subcategories');
    }

    public function destroy(SubCategory $subcategory)
    {
        $subcategory->courses()->each(function ($course) {
            $course->chapters()->delete(); // Supprime tous les chapitres liés (soft delete)
            $course->delete(); // Supprime le cours (soft delete)
        });

        $subcategory->delete(); // Supprime la sous-catégorie (soft delete)

        return back()->with('success', 'Sous-catégorie et éléments liés supprimés avec succès.');
    }
}
