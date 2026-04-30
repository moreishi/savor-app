<?php

namespace App\Services;

use App\Models\Recipe;
use Illuminate\Support\Collection;

class GroceryListService
{
    /**
     * Get the cart structure from session.
     */
    public function getCart(): array
    {
        return session('grocery_list', [
            'branch_id' => null,
            'recipes' => [],
            'items' => [],
        ]);
    }

    /**
     * Add a recipe's ingredients to the grocery list cart.
     */
    public function addRecipe(Recipe $recipe, int $branchId): void
    {
        $cart = $this->getCart();

        // Update branch
        $cart['branch_id'] = $branchId;

        // Skip if already in cart
        if (in_array($recipe->id, $cart['recipes'])) {
            session(['grocery_list' => $cart]);
            return;
        }

        // Resolve ingredients with prices
        $groceryItems = $recipe->getGroceryList($branchId);

        // Add recipe ID to cart
        $cart['recipes'][] = $recipe->id;

        // Merge items into cart
        foreach ($groceryItems as $item) {
            $ingredientId = $item->id;

            if (isset($cart['items'][$ingredientId])) {
                // Already in cart — aggregate quantities
                $existing = $cart['items'][$ingredientId];
                $existing['quantity'] += (float) $item->quantity;
                $existing['recipes'][] = $recipe->id;
                $existing['recipes'] = array_unique($existing['recipes']);

                // Recompute cost
                if ($existing['price_per_unit'] !== null) {
                    $existing['total_cost'] = round(
                        $existing['quantity'] * $existing['price_per_unit'],
                        2
                    );
                }

                $cart['items'][$ingredientId] = $existing;
            } else {
                // New item
                $cart['items'][$ingredientId] = [
                    'id' => $ingredientId,
                    'name' => $item->name,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->recipe_unit,
                    'price_per_unit' => $item->price_per_unit,
                    'price_unit' => $item->purchase_unit,
                    'purchase_price' => $item->purchase_price,
                    'total_cost' => $item->computed_cost,
                    'has_price' => $item->has_price,
                    'is_on_promo' => $item->is_on_promo,
                    'promo_price' => $item->promo_price,
                    'promo_label' => $item->promo_label,
                    'variant_label' => $item->variant_label,
                    'recipes' => [$recipe->id],
                ];
            }
        }

        $cart["total"] = round(array_reduce($cart["items"], fn($c, $i) => $c + ($i["total_cost"] ?? 0), 0), 2);
        session(['grocery_list' => $cart]);
    }

    /**
     * Remove a recipe and its ingredients from the cart.
     */
    public function removeRecipe(int $recipeId): void
    {
        $cart = $this->getCart();

        // Remove from recipes list
        $cart['recipes'] = array_values(
            array_filter($cart['recipes'], fn($id) => $id !== $recipeId)
        );

        // Remove or decrement items
        foreach ($cart['items'] as $ingredientId => &$item) {
            $item['recipes'] = array_values(
                array_filter($item['recipes'], fn($id) => $id !== $recipeId)
            );

            // If no recipes reference this item, remove it
            if (empty($item['recipes'])) {
                unset($cart['items'][$ingredientId]);
            }
        }
        unset($item);

        $cart["total"] = round(array_reduce($cart["items"], fn($c, $i) => $c + ($i["total_cost"] ?? 0), 0), 2);
        session(['grocery_list' => $cart]);
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): void
    {
        session()->forget('grocery_list');
    }

    /**
     * Set the branch ID.
     */
    public function setBranch(int $branchId): void
    {
        $cart = $this->getCart();
        $cart['branch_id'] = $branchId;
        session(['grocery_list' => $cart]);
    }

    /**
     * Get the aggregated grocery list for display.
     */
    public function getList(int $branchId): array
    {
        $cart = $this->getCart();
        $cart['branch_id'] = $branchId;

        // Build collection of items with computed costs for this branch
        $items = collect($cart['items']);

        if ($items->isEmpty()) {
            return [
                'items' => collect(),
                'total_cost' => null,
                'recipe_count' => 0,
                'recipe_ids' => [],
            ];
        }

        // Recalculate costs for the selected branch
        $items = $items->map(function ($item) use ($branchId) {
            $recipe = Recipe::find($item['recipes'][0] ?? null);
            if ($recipe) {
                $groceryList = $recipe->getGroceryList($branchId);
                $ingredient = $groceryList->firstWhere('id', $item['id']);
                if ($ingredient) {
                    // Recalculate total cost with the new branch's price
                    $item['price_per_unit'] = $ingredient->price_per_unit;
                    $item['purchase_price'] = $ingredient->purchase_price;
                    $item['price_unit'] = $ingredient->purchase_unit;
                    $item['has_price'] = $ingredient->has_price;
                    $item['is_on_promo'] = $ingredient->is_on_promo;
                    $item['promo_price'] = $ingredient->promo_price;
                    $item['promo_label'] = $ingredient->promo_label;
                    $item['variant_label'] = $ingredient->variant_label;

                    if ($ingredient->price_per_unit !== null) {
                        $item['total_cost'] = round(
                            $item['quantity'] * (float) $ingredient->price_per_unit,
                            2
                        );
                    }
                }
            }
            return $item;
        });

        $totalCost = $items->reduce(function ($carry, $item) {
            return $carry + ($item['total_cost'] ?? 0);
        }, 0.0);

        session(['grocery_list' => $cart]);
        $cart["total"] = $totalCost > 0 ? round($totalCost, 2) : 0;

        return [
            'items' => $items,
            'total_cost' => $totalCost > 0 ? round($totalCost, 2) : null,
            'recipe_count' => count($cart['recipes']),
            'recipe_ids' => $cart['recipes'],
        ];
    }

    /**
     * Get the list of recipe IDs in the cart.
     */
    public function getRecipes(): array
    {
        return $this->getCart()['recipes'];
    }
}
