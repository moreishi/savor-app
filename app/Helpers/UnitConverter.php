<?php

namespace App\Helpers;

class UnitConverter
{
    private const CONVERSIONS = [
        'kg'   => ['g' => 1000, 'mg' => 1_000_000, 'lb' => 2.20462],
        'g'    => ['kg' => 0.001, 'mg' => 1000, 'lb' => 0.00220462],
        'L'    => ['mL' => 1000],
        'mL'   => ['L' => 0.001],
        'tbsp' => ['mL' => 15],
        'tsp'  => ['mL' => 5],
        'cup'  => ['mL' => 240],
        'pcs'  => [], // pieces — no conversion, same unit only
    ];

    /**
     * Check if conversion between two units is possible.
     */
    public static function canConvert(string $from, string $to): bool
    {
        if ($from === $to) return true;
        return isset(self::CONVERSIONS[$from][$to]);
    }

    /**
     * Convert an amount from one unit to another.
     */
    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) return $amount;

        $conversion = self::CONVERSIONS[$from][$to] ?? null;

        if ($conversion === null) {
            throw new \InvalidArgumentException("Cannot convert from {$from} to {$to}");
        }

        return $amount * $conversion;
    }

    /**
     * Calculate cost for a recipe ingredient quantity against a purchase price.
     *
     * @param  float  $neededQty    Recipe ingredient quantity (e.g. 500g of chicken)
     * @param  string $neededUnit   Recipe ingredient unit (e.g. "g")
     * @param  float  $pricePerUnit Branch price per purchase unit (e.g. ₱185/kg)
     * @param  string $priceUnit    Branch price unit (e.g. "kg")
     * @return float|null           Computed cost, or null if units can't be converted
     */
    public static function costForRecipeQuantity(
        float $neededQty,
        string $neededUnit,
        float $pricePerUnit,
        string $priceUnit
    ): ?float {
        if (!self::canConvert($neededUnit, $priceUnit)) {
            return null;
        }

        $normalizedQty = self::convert($neededQty, $neededUnit, $priceUnit);
        return round($normalizedQty * $pricePerUnit, 2);
    }
}
