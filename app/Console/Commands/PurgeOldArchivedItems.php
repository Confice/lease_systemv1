<?php

namespace App\Console\Commands;

use App\Http\Controllers\ArchivedItemsController;
use Illuminate\Console\Command;

class PurgeOldArchivedItems extends Command
{
    protected $signature = 'archive:purge-old {--years=5 : Number of years after which to permanently delete archived items}';

    protected $description = 'Permanently delete archived items older than the specified years (data retention policy)';

    public function handle()
    {
        $years = (int) $this->option('years');
        if ($years < 1) {
            $this->error('Years must be at least 1.');
            return Command::FAILURE;
        }

        $this->info("Purging archived items older than {$years} year(s)...");

        try {
            $controller = app(ArchivedItemsController::class);
            $counts = $controller->purgeOldArchivedItems($years);

            $total = array_sum($counts);
            if ($total === 0) {
                $this->info('No archived items found to purge.');
                return Command::SUCCESS;
            }

            $this->info("Purged {$total} archived item(s):");
            foreach ($counts as $type => $count) {
                if ($count > 0) {
                    $this->line("  - {$type}: {$count}");
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error purging archived items: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
