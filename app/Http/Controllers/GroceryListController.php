<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Recipe;
use App\Services\GroceryListService;
use Illuminate\Http\Request;

class GroceryListController extends Controller
{
    public function __construct(
        protected GroceryListService $groceryList
    ) {}

    public function index()
    {
        $branches = Branch::active()->orderBy('name')->get();
        $cart = $this->groceryList->getCart();

        $list = null;
        $selectedBranch = null;
        if ($cart['branch_id']) {
            $selectedBranch = $branches->firstWhere('id', $cart['branch_id']);
            $list = $this->groceryList->getList($cart['branch_id']);
        }

        $cartRecipes = [];
        if (! empty($cart['recipes'])) {
            $cartRecipes = Recipe::whereIn('id', $cart['recipes'])->get()->keyBy('id');
        }

        return view('grocery-list.index', compact(
            'branches',
            'cart',
            'list',
            'selectedBranch',
            'cartRecipes',
        ));
    }

    public function addRecipe(Request $request, Recipe $recipe)
    {
        $cart = $this->groceryList->getCart();

        // Allow branch_id from request (recipe detail page includes hidden field)
        $branchId = $request->input('branch_id') ?: $cart['branch_id'];

        if (! $branchId) {
            return redirect()->route('grocery-list.index')
                ->with('error', 'Please select a store/branch first before adding items to your grocery list.');
        }

        $this->groceryList->addRecipe($recipe, $branchId);
        $this->groceryList->setBranch($branchId);

        return redirect()->route('grocery-list.index')
            ->with('success', "Added {$recipe->title} to your grocery list!");
    }

    public function removeRecipe(Recipe $recipe)
    {
        $this->groceryList->removeRecipe($recipe->id);

        return redirect()->route('grocery-list.index')
            ->with('success', "Removed \"{$recipe->title}\" from your grocery list.");
    }

    public function clear()
    {
        $this->groceryList->clear();

        return redirect()->route('grocery-list.index')
            ->with('success', 'Grocery list cleared.');
    }

    public function setBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $this->groceryList->setBranch($request->branch_id);

        return redirect()->route('grocery-list.index');
    }
}
