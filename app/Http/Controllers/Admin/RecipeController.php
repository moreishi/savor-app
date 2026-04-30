<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(Request $request): View
    {
        $query = Recipe::with(['category', 'tags']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $recipes = $query->latest()->paginate(20);
        $categories = Category::orderBy('name')->get();

        return view('admin.recipes.index', compact('recipes', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $ingredients = Ingredient::with('category')->orderBy('name')->get();

        return view('admin.recipes.form', [
            'recipe' => new Recipe,
            'categories' => $categories,
            'tags' => $tags,
            'ingredients' => $ingredients,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'prep_time' => ['nullable', 'integer', 'min:0'],
            'cook_time' => ['nullable', 'integer', 'min:0'],
            'servings' => ['required', 'integer', 'min:1'],
            'is_featured' => ['boolean'],
            'instructions' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['required', 'string', 'max:50'],
            'ingredients.*.notes' => ['nullable', 'string', 'max:255'],
            'ingredients.*.is_optional' => ['boolean'],
            'ingredients.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $recipe = Recipe::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(4),
            'description' => $validated['description'] ?? null,
            'difficulty' => $validated['difficulty'],
            'prep_time' => $validated['prep_time'] ?? null,
            'cook_time' => $validated['cook_time'] ?? null,
            'servings' => $validated['servings'],
            'is_featured' => $validated['is_featured'] ?? false,
            'instructions' => $validated['instructions'],
        ]);

        if (!empty($validated['tags'])) {
            $recipe->tags()->sync($validated['tags']);
        }

        $ingredientData = [];
        foreach ($validated['ingredients'] as $item) {
            $ingredientData[$item['ingredient_id']] = [
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'notes' => $item['notes'] ?? null,
                'is_optional' => $item['is_optional'] ?? false,
                'sort_order' => $item['sort_order'] ?? 0,
            ];
        }
        $recipe->ingredients()->sync($ingredientData);

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe created successfully.');
    }

    public function edit(Recipe $recipe): View
    {
        $recipe->load(['ingredients', 'tags']);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $ingredients = Ingredient::with('category')->orderBy('name')->get();

        return view('admin.recipes.form', compact('recipe', 'categories', 'tags', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'prep_time' => ['nullable', 'integer', 'min:0'],
            'cook_time' => ['nullable', 'integer', 'min:0'],
            'servings' => ['required', 'integer', 'min:1'],
            'is_featured' => ['boolean'],
            'instructions' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'ingredients' => ['required', 'array', 'min:1'],
            'ingredients.*.ingredient_id' => ['required', 'exists:ingredients,id'],
            'ingredients.*.quantity' => ['required', 'numeric', 'min:0'],
            'ingredients.*.unit' => ['required', 'string', 'max:50'],
            'ingredients.*.notes' => ['nullable', 'string', 'max:255'],
            'ingredients.*.is_optional' => ['boolean'],
            'ingredients.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $recipe->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'difficulty' => $validated['difficulty'],
            'prep_time' => $validated['prep_time'] ?? null,
            'cook_time' => $validated['cook_time'] ?? null,
            'servings' => $validated['servings'],
            'is_featured' => $validated['is_featured'] ?? false,
            'instructions' => $validated['instructions'],
        ]);

        // Only update slug if title changed
        if ($recipe->wasChanged('title')) {
            $recipe->update(['slug' => Str::slug($validated['title']) . '-' . Str::random(4)]);
        }

        if (!empty($validated['tags'])) {
            $recipe->tags()->sync($validated['tags']);
        } else {
            $recipe->tags()->sync([]);
        }

        $ingredientData = [];
        foreach ($validated['ingredients'] as $item) {
            $ingredientData[$item['ingredient_id']] = [
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'notes' => $item['notes'] ?? null,
                'is_optional' => $item['is_optional'] ?? false,
                'sort_order' => $item['sort_order'] ?? 0,
            ];
        }
        $recipe->ingredients()->sync($ingredientData);

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe updated successfully.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->ingredients()->detach();
        $recipe->tags()->detach();
        $recipe->delete();

        return redirect()->route('admin.recipes.index')
            ->with('success', 'Recipe deleted successfully.');
    }
}
