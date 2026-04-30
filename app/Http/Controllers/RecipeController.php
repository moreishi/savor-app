<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $promos = \App\Models\PromoHighlight::with('branch', 'ingredient')
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->inRandomOrder()
            ->take(6)
            ->get();

        $query = Recipe::with('category', 'tags')
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', '%' . $search . '%'));
            });
        }

        $featured = Recipe::with('category')
            ->where('is_featured', true)
            ->inRandomOrder()
            ->take(8)
            ->get();

        $categories = Category::orderBy('sort_order')->get();

        $recipes = $query->paginate(12);

        return view('recipes.index', compact('featured', 'categories', 'recipes', 'promos'));
    }

    public function show($slug)
    {
        $recipe = Recipe::with(['category', 'tags', 'ingredients'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Parse instructions into numbered steps, strip the original numbering
        $steps = array_values(array_filter(
            array_map(function($s) {
                $s = trim($s);
                // Remove leading numbering like "1. " or "1) "
                return preg_replace('/^\d+[\.\)]\s*/', '', $s);
            }, explode("\n", $recipe->instructions)),
            fn($s) => !empty($s)
        ));

        $branches = \App\Models\Branch::active()->orderBy('name')->get();

        return view('recipes.show', compact('recipe', 'steps', 'branches'));
    }
}
