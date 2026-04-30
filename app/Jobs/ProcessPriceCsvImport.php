<?php

namespace App\Jobs;

use App\Models\BranchPrice;
use App\Models\Ingredient;
use App\Models\PriceImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use League\Csv\Reader;

class ProcessPriceCsvImport implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public PriceImport $import
    ) {}

    public function handle(): void
    {
        $this->import->update(['status' => 'validating']);

        $path = storage_path('app/' . $this->import->filepath);

        if (!file_exists($path)) {
            $this->import->update([
                'status' => 'failed',
                'errors' => [['line' => 0, 'errors' => ['File not found']]],
            ]);
            return;
        }

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0);

            $errors = [];
            $valid = [];

            foreach ($csv->getRecords() as $index => $record) {
                $lineNumber = $index + 2;
                $lineErrors = [];

                // Validate ingredient_id exists
                $ingredientId = $record['ingredient_id'] ?? null;
                if (!$ingredientId || !Ingredient::find($ingredientId)) {
                    $lineErrors[] = "Ingredient ID '{$ingredientId}' not found";
                }

                // Validate price
                $price = $record['price'] ?? null;
                if (!is_numeric($price) || $price <= 0) {
                    $lineErrors[] = "Invalid price: {$price}";
                }

                // Validate duplicate (branch_id + ingredient_id + variant_label)
                if ($ingredientId && empty($lineErrors)) {
                    $exists = BranchPrice::where([
                        'branch_id' => $this->import->branch_id,
                        'ingredient_id' => $ingredientId,
                        'variant_label' => $record['variant_label'] ?? null,
                    ])->exists();

                    if ($exists) {
                        $lineErrors[] = 'Duplicate price record';
                    }
                }

                if (!empty($lineErrors)) {
                    $errors[] = [
                        'line' => $lineNumber,
                        'errors' => $lineErrors,
                        'data' => $record,
                    ];
                } else {
                    $valid[] = [
                        'ingredient_id' => $ingredientId,
                        'price' => $price,
                        'variant_label' => $record['variant_label'] ?? null,
                        'purchase_qty' => $record['purchase_qty'] ?? 1,
                        'purchase_unit' => $record['purchase_unit'] ?? 'pcs',
                    ];
                }
            }
        } catch (\Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'errors' => [['line' => 0, 'errors' => ['CSV parse error: ' . $e->getMessage()]]],
            ]);
            return;
        }

        $totalRows = count($csv->getRecords());

        $this->import->update([
            'status' => 'validated',
            'total_rows' => $totalRows,
            'valid_rows' => count($valid),
            'error_rows' => count($errors),
            'errors' => $errors,
        ]);

        // Cache valid rows for the confirm step
        Cache::put("import.{$this->import->id}.valid", $valid, now()->addHour());
    }
}
