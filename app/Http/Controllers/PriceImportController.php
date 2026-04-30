<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\ConfirmPriceImport;
use App\Jobs\ProcessPriceCsvImport;
use App\Models\Branch;
use App\Models\PriceImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PriceImportController extends Controller
{
    public function create()
    {
        $branches = Branch::active()->orderBy('name')->get();
        return view('prices.import', compact('branches'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $uuid = (string) Str::uuid();
        $file = $request->file('file');
        $filepath = $file->storeAs('imports', "{$uuid}.csv");

        $import = PriceImport::create([
            'user_id' => $request->user()->id,
            'branch_id' => $request->branch_id,
            'filename' => $file->getClientOriginalName(),
            'filepath' => $filepath,
            'status' => 'pending',
        ]);

        ProcessPriceCsvImport::dispatch($import);

        return response()->json([
            'id' => $import->id,
            'status' => 'pending',
        ]);
    }

    public function status(PriceImport $import): JsonResponse
    {
        return response()->json([
            'id' => $import->id,
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'valid_rows' => $import->valid_rows,
            'error_rows' => $import->error_rows,
            'errors' => $import->errors,
        ]);
    }

    public function confirm(PriceImport $import): JsonResponse
    {
        if ($import->status !== 'validated') {
            return response()->json([
                'message' => 'Import must be in "validated" status to confirm.',
            ], 422);
        }

        ConfirmPriceImport::dispatch($import);

        return response()->json([
            'id' => $import->id,
            'status' => 'importing',
            'message' => 'Import confirmed. Processing...',
        ]);
    }

    public function downloadTemplate()
    {
        $headers = ['ingredient_id', 'price', 'variant_label', 'purchase_qty', 'purchase_unit'];
        $sample = [
            ['1', '185.00', 'Magnolia / 1kg', '1', 'kg'],
            ['1', '155.00', 'Bounty Fresh / 500g', '500', 'g'],
            ['2', '320.00', '', '1', 'kg'],
            ['3', '25.00', '', '1', 'pcs'],
        ];

        $callback = function () use ($headers, $sample) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($sample as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, 'price-import-template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
