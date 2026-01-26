<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillController;
use Illuminate\Console\Command;

class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for all active contracts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating monthly bills...');

        try {
            // Create a request object to simulate the HTTP request
            $request = new \Illuminate\Http\Request();
            
            // Get the BillController instance
            $controller = app(BillController::class);
            
            // Call the generateMonthlyBills method (skip auth check for scheduled task)
            $response = $controller->generateMonthlyBills(true);
            
            // Get the response data
            $data = json_decode($response->getContent(), true);
            
            if ($data['success']) {
                $this->info("Successfully generated {$data['billsGenerated']} bill(s).");
                
                if (!empty($data['errors'])) {
                    $this->warn('Some errors occurred:');
                    foreach ($data['errors'] as $error) {
                        $this->error("  - {$error}");
                    }
                }
                
                return Command::SUCCESS;
            } else {
                $this->error('Failed to generate bills: ' . ($data['message'] ?? 'Unknown error'));
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Error generating bills: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
