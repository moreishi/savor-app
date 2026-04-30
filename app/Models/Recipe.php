<?php

namespace App\Models;

use App\Helpers\UnitConverter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'servings',
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

    public function promoHighlights(): BelongsToMany
    {
        return $this->belongsToMany(PromoHighlight::class);
    }

    /**
     * Get grocery list with prices for a specific branch.
     * Returns each ingredient with computed cost and promo flags.
     */
    public function getGroceryList(int $branchId): mixed
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
            ->leftJoin('branch_prices', function ($join) use ($branchId) {
                $join->on('branch_prices.ingredient_id', '=', 'ingredients.id')
                    ->where('branch_prices.branch_id', $branchId);
            })
            ->addSelect(
                'branch_prices.price',
                'branch_prices.variant_label',
                'branch_prices.purchase_quantity',
                'branch_prices.purchase_unit',
                'branch_prices.is_on_promo',
                'branch_prices.promo_price',
                'branch_prices.promo_label',
                'branch_prices.last_verified_at',
            )
            ->orderBy('recipe_ingredient.sort_order')
            ->get()
            ->map(function ($item) {
                $item->computed_cost = $item->price && $item->purchase_unit
                    ? UnitConverter::costForRecipeQuantity(
                        $item->quantity,
                        $item->recipe_unit,
                        $item->purchase_unit === $item->recipe_unit
                            ? $item->price
                            : $item->price / $item->purchase_quantity,
                        $item->purchase_unit
                    )
                    : null;

                $item->has_price = !is_null($item->price);
                $item->price_is_stale = $item->last_verified_at
                    && $item->last_verified_at->diffInDays(now()) > 7;

                return $item;
            });
    }
}
