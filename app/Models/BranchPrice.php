<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchPrice extends Model
{
    protected $fillable = [
        'branch_id', 'ingredient_id', 'price',
        'variant_label', 'purchase_quantity', 'purchase_unit',
        'is_on_promo', 'promo_price', 'promo_label', 'last_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'purchase_quantity' => 'decimal:2',
            'promo_price' => 'decimal:2',
            'is_on_promo' => 'boolean',
            'last_verified_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
