<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PriceImport;
use App\Jobs\ProcessPriceCsvImport;
use Illuminate\Console\Command;

class ImportBranchPrices extends Command
{
    protected $signature = 'import:prices {file : Path to CSV file} {--branch= : Branch ID} {--user=1 : User ID}';

    protected $description = 'Import branch prices from a CSV file via the queue';

    public function handle()
    {
        $file = $this->argument('file');
        $branchId = $this->option('branch');
        $userId = $this->option('user');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return Command::FAILURE;
        }

        if (!$branchId) {
            $this->error('Branch ID is required (--branch=1)');
            return Command::FAILURE;
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error("User ID {$userId} not found");
            return Command::FAILURE;
        }

        $import = PriceImport::create([
            'user_id' => $userId,
            'branch_id' => $branchId,
            'filename' => basename($file),
            'filepath' => 'imports/' . basename($file),
            'status' => 'pending',
        ]);

        // Copy file to storage
        $storagePath = storage_path("app/imports/" . basename($file));
        copy($file, $storagePath);

        ProcessPriceCsvImport::dispatch($import);

        $this->info("Import #{$import->id} dispatched. Processing...");

        return Command::SUCCESS;
    }
}
