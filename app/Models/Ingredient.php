<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'default_unit', 'image_url'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(BranchPrice::class);
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_ingredient')
            ->withPivot('quantity', 'unit', 'is_optional', 'notes', 'sort_order')
            ->orderByPivot('sort_order');
    }

    public function promoHighlights(): HasMany
    {
        return $this->hasMany(PromoHighlight::class);
    }
}
