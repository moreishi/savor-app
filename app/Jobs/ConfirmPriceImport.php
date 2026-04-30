<?php

namespace App\Jobs;

use App\Models\BranchPrice;
use App\Models\PriceImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ConfirmPriceImport implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public PriceImport $import
    ) {}

    public function handle(): void
    {
        $this->import->update(['status' => 'importing']);

        $valid = Cache::pull("import.{$this->import->id}.valid", []);

        if (empty($valid)) {
            $this->import->update([
                'status' => 'failed',
                'errors' => array_merge($this->import->errors ?? [], [
                    ['line' => 0, 'errors' => ['Valid data not found or expired']],
                ]),
            ]);
            return;
        }

        try {
            collect($valid)->chunk(500)->each(function ($chunk) {
                $records = $chunk->map(fn ($row) => [
                    'branch_id' => $this->import->branch_id,
                    'ingredient_id' => $row['ingredient_id'],
                    'price' => $row['price'],
                    'variant_label' => $row['variant_label'] ?? null,
                    'purchase_quantity' => $row['purchase_qty'] ?? 1,
                    'purchase_unit' => $row['purchase_unit'] ?? 'pcs',
                    'last_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                // Upsert — update price on duplicate key
                BranchPrice::upsert(
                    $records,
                    uniqueBy: ['branch_id', 'ingredient_id', 'variant_label'],
                    update: ['price', 'variant_label', 'purchase_quantity', 'purchase_unit', 'last_verified_at', 'updated_at']
                );
            });

            $this->import->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        } catch (\Exception $e) {
            $this->import->update([
                'status' => 'failed',
                'errors' => array_merge($this->import->errors ?? [], [
                    ['line' => 0, 'errors' => ['Import error: ' . $e->getMessage()]],
                ]),
            ]);
        }
    }
}
