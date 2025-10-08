<?php

// app/Http/Controllers/CategoryController.php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('subcategories')->get();
        return view('authenticated.owners.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('authenticated.owners.categories.create');
    }


    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        Category::create($request->all());
        return redirect()->route('authenticated.owners.categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back();
    }
}
