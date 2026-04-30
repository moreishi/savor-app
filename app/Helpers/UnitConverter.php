<?php

namespace App\Helpers;

class UnitConverter
{
    /**
     * Base conversion factors between units.
     * Each entry: 'from' => ['to' => factor] (multiply amount by factor).
     */
    private const CONVERSIONS = [
        // Mass
        'kg'            => ['g' => 1000, 'mg' => 1_000_000, 'lb' => 2.20462],
        'g'             => ['kg' => 0.001, 'mg' => 1000, 'lb' => 0.00220462],
        // Volume
        'L'             => ['mL' => 1000],
        'mL'            => ['L' => 0.001],
        'tbsp'          => ['mL' => 15],
        'tsp'           => ['mL' => 5],
        'cup'           => ['mL' => 240],
        // Container sizes (Robinson's standard)
        'bottle'        => ['mL' => 500, 'L' => 0.5],
        'can'           => ['mL' => 370, 'L' => 0.37],
        // Produce count-to-weight
        'clove'         => ['g' => 5, 'kg' => 0.005],
        'bundle'        => ['g' => 250, 'kg' => 0.25],
        'bundles'       => ['g' => 250, 'kg' => 0.25],   // plural alias
        'stalks'        => ['g' => 25, 'kg' => 0.025],    // e.g. 2 stalks lemongrass
        'stalk'         => ['g' => 25, 'kg' => 0.025],
        'thumb-sized'   => ['g' => 15, 'kg' => 0.015],
        'whole head'    => ['g' => 50, 'kg' => 0.05],
        // Pack (default — overridden per ingredient)
        'pack'          => ['g' => 50, 'kg' => 0.05],
        // Dozen
        'dozen'         => ['pcs' => 12],
        // Items (synonym for pieces in some contexts)
        'items'         => ['pcs' => 1],
    ];

    /**
     * Per-piece weight in grams, keyed by ingredient name.
     * Used when converting pcs → g / pcs → kg.
     */
    private const PER_PIECE_WEIGHT_G = [
        // Proteins (whole fish)
        'Tilapia'           => 300,
        'Bangus (Milkfish)' => 500,
        // Eggs
        'Egg'               => 50,
        'Eggs'              => 50,
        // Vegetables (produce sold by piece)
        'Tomato'            => 150,
        'Onion'             => 150,
        'Potato'            => 200,
        'Carrot'            => 100,
        'Cabbage'           => 1000,
        'Sayote (Chayote)'  => 200,
        'Eggplant'          => 200,
        'Ampalaya (Bitter Gourd)' => 200,
        'Green Bell Pepper' => 150,
        'Okra'              => 10,
        'Sili'              => 5,
        'Calamansi'         => 10,
        'Lemon'             => 100,
        // Fruit
        'Banana'            => 120,
        'Saba'              => 80,
        'Ripe Plantain (Saba)' => 80,
        // Coconut
        'Coconut (Gata)'    => 400,
        'Coconut'           => 400,
        // Herbs / seasonings (per piece)
        'Bay Leaf (Laurel)' => 0.5,
    ];

    /**
     * Pack sizes in grams for specific ingredients.
     * Used when converting g ↔ pack for non-default pack sizes.
     */
    private const PACK_SIZE_G = [
        'Spaghetti Pasta'         => 500,
        'Pancit Canton Noodles'   => 500,
        'Pancit Bihon (Rice Noodles)' => 500,
        'Sotanghon (Glass Noodles)'  => 200,
        'Caldereta Sauce Mix'     => 50,
        'Sinigang Mix'           => 50,
        'Ground Black Pepper'     => 25,
        'Bay Leaf (Laurel)'      => 10,
    ];

    /**
     * Density approximations (g/mL) for converting volume → weight.
     * Keyed by ingredient name; 'default' used as fallback (~water).
     */
    private const DENSITY_G_ML = [
        'default'               => 1.0,   // water-like
        'Sugar (White)'         => 0.85,
        'Brown Sugar'           => 0.85,
        'Cornstarch'            => 0.53,
        'Salt'                  => 1.2,
        'Butter'                => 0.96,
        'All-Purpose Flour'     => 0.53,
        'Cheddar Cheese'        => 0.50,  // grated
        'Cooking Oil'           => 0.92,
        'White Rice'            => 0.85,
        'Glutinous Rice (Malagkit)' => 0.85,
        'Monggo Beans'          => 0.85,
        'Ground Black Pepper'   => 0.50,
    ];

    // -----------------------------------------------------------------
    //  Public API
    // -----------------------------------------------------------------

    /**
     * Check if conversion between two units is possible.
     * Accepts an optional ingredient context for context-sensitive units.
     */
    public static function canConvert(string $from, string $to, ?string $context = null): bool
    {
        $from = self::normalizeUnit($from);
        $to   = self::normalizeUnit($to);

        if ($from === $to) return true;

        // Direct or reverse direct
        if (isset(self::CONVERSIONS[$from][$to])) return true;
        if (isset(self::CONVERSIONS[$to][$from])) return true;

        // pcs → anything reachable via g
        if ($from === 'pcs' && self::getPieceWeight($context) !== null) {
            // pcs → g always works; then g → anything in the graph
            return true;
        }
        // anything → pcs via g → pcs
        if ($to === 'pcs' && self::getPieceWeight($context) !== null) {
            return true;
        }

        // g ↔ pack with context
        if ($from === 'g' && $to === 'pack' && self::getPackSize($context) !== null) return true;
        if ($from === 'pack' && $to === 'g' && self::getPackSize($context) !== null) return true;

        // Volume → weight via density
        if (self::isVolumeUnit($from) && ($to === 'g' || $to === 'kg')) return true;
        if (($from === 'g' || $from === 'kg') && self::isVolumeUnit($to)) return true;

        // Volume → pack (via g intermediate)
        if (self::isVolumeUnit($from) && $to === 'pack') return true;
        // Pack → volume (via g intermediate)
        if ($from === 'pack' && self::isVolumeUnit($to)) return true;

        // BFS through conversion graph
        return self::findPath($from, $to) !== null;
    }

    /**
     * Convert an amount from one unit to another.
     *
     * @param  float       $amount  Quantity to convert
     * @param  string      $from    Source unit
     * @param  string      $to      Target unit
     * @param  string|null $context Ingredient name (for context-sensitive conversions)
     * @return float
     *
     * @throws \InvalidArgumentException
     */
    public static function convert(float $amount, string $from, string $to, ?string $context = null): float
    {
        $from = self::normalizeUnit($from);
        $to   = self::normalizeUnit($to);

        if ($from === $to) return $amount;

        // 1. g ↔ pack (context-sensitive — check before direct factors, as
        //    CONVERSIONS["pack"]["g"] = 50 is only a default)
        if ($from === 'g' && $to === 'pack') {
            return self::convertGramsToPacks($amount, $context);
        }
        if ($from === 'pack' && $to === 'g') {
            return self::convertPacksToGrams($amount, $context);
        }

        // 2. Volume ↔ weight via density (context-sensitive)
        if (self::isVolumeUnit($from) && ($to === 'g' || $to === 'kg')) {
            return self::convertVolumeToWeight($amount, $from, $to, $context);
        }
        if (($from === 'g' || $from === 'kg') && self::isVolumeUnit($to)) {
            return self::convertWeightToVolume($amount, $from, $to, $context);
        }

        // 2b. Volume → pack (via grams intermediate)
        if (self::isVolumeUnit($from) && $to === 'pack') {
            $grams = self::convertVolumeToWeight($amount, $from, 'g', $context);
            return self::convertGramsToPacks($grams, $context);
        }
        // 2c. Pack → volume (via grams intermediate)
        if ($from === 'pack' && self::isVolumeUnit($to)) {
            $grams = self::convertPacksToGrams($amount, $context);
            return self::convertWeightToVolume($grams, 'g', $to, $context);
        }

        // 3. Direct forward conversion
        if (isset(self::CONVERSIONS[$from][$to])) {
            return $amount * self::CONVERSIONS[$from][$to];
        }

        // 4. Direct reverse conversion (to → from: divide by reverse)
        if (isset(self::CONVERSIONS[$to][$from])) {
            return $amount / self::CONVERSIONS[$to][$from];
        }

        // 5. pcs → anything (via g as intermediate)
        if ($from === 'pcs') {
            $grams = self::convertPcsToWeight($amount, 'g', $context);
            if ($to === 'g') return $grams;
            return self::convert($grams, 'g', $to, $context);
        }

        // 6. anything → pcs (via g as intermediate)
        if ($to === 'pcs') {
            if ($from === 'g') return self::convertWeightToPcs($amount, 'g', $context);
            if ($from === 'kg') return self::convertWeightToPcs($amount * 1000, 'g', $context);
            $grams = self::convert($amount, $from, 'g', $context);
            return self::convertWeightToPcs($grams, 'g', $context);
        }

        // 7. BFS pathfinding through conversion graph
        $path = self::findPath($from, $to);
        if ($path !== null) {
            return self::traversePath($amount, $path);
        }

        throw new \InvalidArgumentException("Cannot convert from {$from} to {$to}" . ($context ? " (context: {$context})" : ''));
    }

    /**
     * Calculate cost for a recipe ingredient quantity against a purchase price.
     *
     * @param  float       $neededQty    Recipe ingredient quantity (e.g. 6 cloves garlic)
     * @param  string      $neededUnit   Recipe ingredient unit (e.g. "cloves")
     * @param  float       $pricePerUnit Branch price per purchase unit (e.g. ₱185/kg)
     * @param  string      $priceUnit    Branch price unit (e.g. "kg")
     * @param  string|null $context      Ingredient name for context-sensitive conversions (e.g. "Garlic")
     * @return float|null                Computed cost, or null if units can't be converted
     */
    public static function costForRecipeQuantity(
        float $neededQty,
        string $neededUnit,
        float $pricePerUnit,
        string $priceUnit,
        ?string $context = null,
    ): ?float {
        if (!self::canConvert($neededUnit, $priceUnit, $context)) {
            return null;
        }

        $normalizedQty = self::convert($neededQty, $neededUnit, $priceUnit, $context);
        return round($normalizedQty * $pricePerUnit, 2);
    }

    // -----------------------------------------------------------------
    //  Internal helpers
    // -----------------------------------------------------------------

    /**
     * Normalize a unit string: strip whitespace, handle common plurals.
     */
    private static function normalizeUnit(string $unit): string
    {
        $unit = trim($unit);
        $lower = strtolower($unit);

        // Preserve case for canonical abbreviated units
        $canonicalMap = [
            'ml'   => 'mL',
            'l'    => 'L',
            'mls'  => 'mL',
            'ls'   => 'L',
            'kg'   => 'kg',
            'kgs'  => 'kg',
            'g'    => 'g',
            'gs'   => 'g',
            'mg'   => 'mg',
        ];

        // Known plural/synonym → singular
        $synonymMap = [
            'cloves'    => 'clove',
            'cups'      => 'cup',
            'packs'     => 'pack',
            'stalks'    => 'stalk',
            'bottles'   => 'bottle',
            'cans'      => 'can',
            'pieces'    => 'pcs',
            'piece'     => 'pcs',
            'item'      => 'pcs',
            'items'     => 'pcs',
            'tablespoon' => 'tbsp',
            'tablespoons' => 'tbsp',
            'teaspoon'  => 'tsp',
            'teaspoons' => 'tsp',
            'bunch'     => 'bundle',
            'bundles'   => 'bundle',
        ];

        // Check canonical first (preserves case)
        if (isset($canonicalMap[$lower])) {
            return $canonicalMap[$lower];
        }

        // Check synonyms on the lowercased version
        if (isset($synonymMap[$lower])) {
            return $synonymMap[$lower];
        }

        return $lower;
    }

    /**
     * Get per-piece weight in grams for an ingredient.
     */
    private static function getPieceWeight(?string $context): ?float
    {
        if ($context === null) return null;
        return self::PER_PIECE_WEIGHT_G[$context] ?? self::PER_PIECE_WEIGHT_G[ucfirst($context)] ?? null;
    }

    /**
     * Get pack size in grams for an ingredient.
     */
    private static function getPackSize(?string $context): ?float
    {
        if ($context === null) return null;
        return self::PACK_SIZE_G[$context] ?? self::PACK_SIZE_G[ucfirst($context)] ?? null;
    }

    /**
     * Check if a unit is a volume unit.
     */
    private static function isVolumeUnit(string $unit): bool
    {
        return in_array($unit, ['mL', 'L', 'tbsp', 'tsp', 'cup', 'bottle', 'can']);
    }

    /**
     * Check if a unit is a weight/mass unit.
     */
    private static function isWeightUnit(string $unit): bool
    {
        return in_array($unit, ['g', 'kg', 'mg', 'lb']);
    }

    /**
     * Convert pcs → weight unit using per-piece weight context.
     * Always converts through grams.
     */
    private static function convertPcsToWeight(float $amount, string $toUnit, ?string $context): float
    {
        $pieceGrams = self::getPieceWeight($context);
        if ($pieceGrams === null) {
            throw new \InvalidArgumentException("Cannot convert pcs: unknown per-piece weight for '{$context}'");
        }

        $grams = $amount * $pieceGrams;

        if ($toUnit === 'g') return $grams;
        return self::convert($grams, 'g', $toUnit);
    }

    /**
     * Convert grams → pcs using per-piece weight context.
     */
    private static function convertWeightToPcs(float $grams, string $fromUnit, ?string $context): float
    {
        $pieceGrams = self::getPieceWeight($context);
        if ($pieceGrams === null) {
            throw new \InvalidArgumentException("Cannot convert to pcs: unknown per-piece weight for '{$context}'");
        }

        return $grams / $pieceGrams;
    }

    /**
     * Convert grams to packs using context-sensitive pack size.
     */
    private static function convertGramsToPacks(float $grams, ?string $context): float
    {
        $packSize = self::getPackSize($context) ?? 50; // default 50g
        return $grams / $packSize;
    }

    /**
     * Convert packs to grams using context-sensitive pack size.
     */
    private static function convertPacksToGrams(float $packs, ?string $context): float
    {
        $packSize = self::getPackSize($context) ?? 50; // default 50g
        return $packs * $packSize;
    }

    /**
     * Convert a volume amount to weight (g or kg) using density approximation.
     */
    private static function convertVolumeToWeight(float $amount, string $fromUnit, string $toUnit, ?string $context): float
    {
        // First convert volume to mL
        $mL = $fromUnit === 'mL' ? $amount : self::convert($amount, $fromUnit, 'mL');

        // Look up density (g/mL)
        $density = self::DENSITY_G_ML[$context ?? 'default'] ?? self::DENSITY_G_ML['default'];

        $grams = $mL * $density;

        if ($toUnit === 'g') return $grams;
        if ($toUnit === 'kg') return $grams * 0.001;

        return self::convert($grams, 'g', $toUnit);
    }

    /**
     * Convert a weight amount (g or kg) to volume using density approximation.
     */
    private static function convertWeightToVolume(float $amount, string $fromUnit, string $toUnit, ?string $context): float
    {
        // First convert weight to grams
        $grams = $fromUnit === 'kg' ? $amount * 1000 : $amount;

        // Look up density (g/mL)
        $density = self::DENSITY_G_ML[$context ?? 'default'] ?? self::DENSITY_G_ML['default'];

        $mL = $grams / $density;

        if ($toUnit === 'mL') return $mL;
        return self::convert($mL, 'mL', $toUnit);
    }

    /**
     * BFS to find a conversion path through the graph.
     * Walks edges in both directions (forward and reverse).
     *
     * @return array{units: string[], factors: float[]}|null
     */
    private static function findPath(string $from, string $to): ?array
    {
        if ($from === $to) return ['units' => [$from], 'factors' => []];

        $visited = [$from => true];
        $queue = [[
            'unit' => $from,
            'path' => ['units' => [$from], 'factors' => []],
        ]];

        while (!empty($queue)) {
            $current = array_shift($queue);

            // Try forward edges from current unit
            if (isset(self::CONVERSIONS[$current['unit']])) {
                foreach (self::CONVERSIONS[$current['unit']] as $nextUnit => $factor) {
                    if (!isset($visited[$nextUnit])) {
                        $newPath = [
                            'units'     => array_merge($current['path']['units'], [$nextUnit]),
                            'factors'   => array_merge($current['path']['factors'], [$factor]),
                        ];

                        if ($nextUnit === $to) {
                            return $newPath;
                        }

                        $visited[$nextUnit] = true;
                        $queue[] = ['unit' => $nextUnit, 'path' => $newPath];
                    }
                }
            }

            // Try reverse edges (other units that can convert to current unit)
            foreach (self::CONVERSIONS as $src => $targets) {
                if (isset($targets[$current['unit']]) && !isset($visited[$src])) {
                    // Reverse: $current → $src = 1 / ($src → $current)
                    $reverseFactor = 1 / $targets[$current['unit']];
                    $newPath = [
                        'units'   => array_merge($current['path']['units'], [$src]),
                        'factors' => array_merge($current['path']['factors'], [$reverseFactor]),
                    ];

                    if ($src === $to) {
                        return $newPath;
                    }

                    $visited[$src] = true;
                    $queue[] = ['unit' => $src, 'path' => $newPath];
                }
            }
        }

        return null; // no path found
    }

    /**
     * Apply conversion factors along a found path.
     */
    private static function traversePath(float $amount, array $path): float
    {
        $result = $amount;
        foreach ($path['factors'] as $factor) {
            $result *= $factor;
        }
        return $result;
    }
}
