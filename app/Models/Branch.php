<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $fillable = [
        'brand', 'name', 'address', 'city',
        'latitude', 'longitude', 'contact_number', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(BranchPrice::class);
    }

    public function promoHighlights(): HasMany
    {
        return $this->hasMany(PromoHighlight::class);
    }

    public function priceImports(): HasMany
    {
        return $this->hasMany(PriceImport::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearest($query, float $lat, float $lng, float $radiusKm = 50)
    {
        $select = <<<SQL
            *, (6371 * acos(cos(radians(?)) * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?)) * sin(radians(latitude)))) AS distance
        SQL;

        return $query
            ->selectRaw($select, [$lat, $lng, $lat])
            ->having('distance', '<', $radiusKm)
            ->orderBy('distance');
    }
}
