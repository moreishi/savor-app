<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ingredient::withCount('recipes')->with('category');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $ingredients = $query->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('admin.ingredients.index', compact('ingredients', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.ingredients.form', ['ingredient' => new Ingredient, 'categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'default_unit' => ['required', 'string', 'max:50'],
        ]);

        Ingredient::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(4),
            'category_id' => $validated['category_id'],
            'default_unit' => $validated['default_unit'],
        ]);

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient created successfully.');
    }

    public function edit(Ingredient $ingredient): View
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.ingredients.form', compact('ingredient', 'categories'));
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'default_unit' => ['required', 'string', 'max:50'],
        ]);

        $ingredient->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'default_unit' => $validated['default_unit'],
        ]);

        if ($ingredient->wasChanged('name')) {
            $ingredient->update(['slug' => Str::slug($validated['name']) . '-' . Str::random(4)]);
        }

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        if ($ingredient->recipes()->count() > 0) {
            return redirect()->route('admin.ingredients.index')
                ->with('error', 'Cannot delete ingredient used in recipes.');
        }

        $ingredient->delete();

        return redirect()->route('admin.ingredients.index')
            ->with('success', 'Ingredient deleted successfully.');
    }
}
