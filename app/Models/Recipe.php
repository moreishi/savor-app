<?php

namespace App\Models;

use App\Helpers\UnitConverter;
use App\Models\BranchPrice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
        protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description', 'servings',
        'prep_time', 'cook_time', 'difficulty', 'image_url',
        'instructions', 'tips', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'servings' => 'integer',
            'prep_time' => 'integer',
            'cook_time' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
            ->withPivot('quantity', 'unit', 'is_optional', 'notes', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'recipe_tag');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function promoHighlights(): BelongsToMany
    {
        return $this->belongsToMany(PromoHighlight::class);
    }

    /**
     * Get grocery list with prices for a specific branch.
     * Returns each ingredient with computed cost and promo flags.
     *
     * Prefers null variant_label (generic) prices, falls back to
     * any available variant_label if no generic price exists.
     */
    public function getGroceryList(int $branchId)
    {
        return $this->ingredients()
            ->select(
                'ingredients.id',
                'ingredients.name',
                'ingredients.default_unit',
                'recipe_ingredient.quantity',
                'recipe_ingredient.unit as recipe_unit',
                'recipe_ingredient.notes',
                'recipe_ingredient.is_optional',
                'recipe_ingredient.sort_order',
            )
            ->orderBy('recipe_ingredient.sort_order')
            ->get()
            ->map(function ($item) use ($branchId) {
                // Prefer null variant_label (generic) first
                $price = BranchPrice::where('branch_id', $branchId)
                    ->where('ingredient_id', $item->id)
                    ->whereNull('variant_label')
                    ->first();

                // Fallback to any variant_label
                if (! $price) {
                    $price = BranchPrice::where('branch_id', $branchId)
                        ->where('ingredient_id', $item->id)
                        ->first();
                }

                $item->purchase_price = null;
                $item->purchase_quantity = null;
                $item->purchase_unit = null;
                $item->price_per_unit = null;
                $item->computed_cost = null;
                $item->variant_label = null;
                $item->is_on_promo = false;
                $item->promo_price = null;
                $item->promo_label = null;
                $item->has_price = false;
                $item->price_is_stale = false;

                if ($price) {
                    $pricePerPurchaseUnit = $price->purchase_quantity > 0
                        ? (float) $price->price / (float) $price->purchase_quantity
                        : (float) $price->price;

                    $computed = UnitConverter::costForRecipeQuantity(
                        (float) $item->quantity,
                        $item->recipe_unit,
                        $pricePerPurchaseUnit,
                        $price->purchase_unit,
                    );

                    $item->purchase_price = (float) $price->price;
                    $item->purchase_quantity = (float) $price->purchase_quantity;
                    $item->purchase_unit = $price->purchase_unit;
                    $item->price_per_unit = $pricePerPurchaseUnit;
                    $item->computed_cost = $computed;
                    $item->variant_label = $price->variant_label;
                    $item->is_on_promo = (bool) $price->is_on_promo;
                    $item->promo_price = $price->promo_price ? (float) $price->promo_price : null;
                    $item->promo_label = $price->promo_label;
                    $item->has_price = true;
                    $item->price_is_stale = $price->last_verified_at
                        && $price->last_verified_at->diffInDays(now()) > 7;
                }

                return $item;
            });
    }
}
