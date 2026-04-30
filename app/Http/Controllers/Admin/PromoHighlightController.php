<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Ingredient;
use App\Models\PromoHighlight;
use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromoHighlightController extends Controller
{
    public function index(Request $request): View
    {
        $query = PromoHighlight::with('branch', 'ingredient', 'recipe');

        if ($search = $request->get('search')) {
            $query->whereHas('ingredient', fn($q) => $q->where('name', 'like', "%{$search}%"))
                ->orWhere('promo_text', 'like', "%{$search}%");
        }

        if ($request->get('active') !== null) {
            $query->where('is_active', $request->boolean('active'));
        }

        $promos = $query->orderBy('valid_from', 'desc')->paginate(20);

        return view('admin.promos.index', compact('promos'));
    }

    public function create(): View
    {
        $branches = Branch::active()->orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $recipes = Recipe::orderBy('title')->get();

        return view('admin.promos.create', compact('branches', 'ingredients', 'recipes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id'     => ['required', 'exists:branches,id'],
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'recipe_id'     => ['nullable', 'exists:recipes,id'],
            'promo_text'    => ['required', 'string', 'max:255'],
            'valid_from'    => ['required', 'date'],
            'valid_until'   => ['required', 'date', 'after_or_equal:valid_from'],
            'is_active'     => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        PromoHighlight::create($validated);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo created successfully.');
    }

    public function show(PromoHighlight $promo): View
    {
        $promo->load('branch', 'ingredient', 'recipe');

        return view('admin.promos.show', compact('promo'));
    }

    public function edit(PromoHighlight $promo): View
    {
        $branches = Branch::active()->orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        $recipes = Recipe::orderBy('title')->get();

        return view('admin.promos.edit', compact('promo', 'branches', 'ingredients', 'recipes'));
    }

    public function update(Request $request, PromoHighlight $promo): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id'     => ['required', 'exists:branches,id'],
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'recipe_id'     => ['nullable', 'exists:recipes,id'],
            'promo_text'    => ['required', 'string', 'max:255'],
            'valid_from'    => ['required', 'date'],
            'valid_until'   => ['required', 'date', 'after_or_equal:valid_from'],
            'is_active'     => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $promo->update($validated);

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo updated successfully.');
    }

    public function destroy(PromoHighlight $promo): RedirectResponse
    {
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo deleted successfully.');
    }
}
