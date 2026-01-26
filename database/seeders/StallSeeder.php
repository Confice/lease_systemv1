<?php

namespace Database\Seeders;

use App\Models\Stall;
use App\Models\Marketplace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get marketplaces
        $marketplaces = Marketplace::all();
        if ($marketplaces->isEmpty()) {
            $this->command->warn('No marketplaces found. Please run MarketplaceSeeder first.');
            return;
        }

        $stalls = [
            // Downtown Market Hub stalls
            [
                'stallNo' => 'DMH-001',
                'marketplaceID' => $marketplaces[0]->marketplaceID,
                'size' => '10 sqm',
                'rentalFee' => 5000.00,
                'stallStatus' => 'Occupied',
                'applicationDeadline' => null,
            ],
            [
                'stallNo' => 'DMH-002',
                'marketplaceID' => $marketplaces[0]->marketplaceID,
                'size' => '15 sqm',
                'rentalFee' => 7500.00,
                'stallStatus' => 'Occupied',
                'applicationDeadline' => null,
            ],
            [
                'stallNo' => 'DMH-003',
                'marketplaceID' => $marketplaces[0]->marketplaceID,
                'size' => '10 sqm',
                'rentalFee' => 5000.00,
                'stallStatus' => 'Vacant',
                'applicationDeadline' => now()->addDays(30),
            ],
            [
                'stallNo' => 'DMH-004',
                'marketplaceID' => $marketplaces[0]->marketplaceID,
                'size' => '12 sqm',
                'rentalFee' => 6000.00,
                'stallStatus' => 'Vacant',
                'applicationDeadline' => now()->addDays(15),
            ],
            // Central Bazaar stalls
            [
                'stallNo' => 'CBZ-001',
                'marketplaceID' => $marketplaces[1]->marketplaceID,
                'size' => '20 sqm',
                'rentalFee' => 10000.00,
                'stallStatus' => 'Occupied',
                'applicationDeadline' => null,
            ],
            [
                'stallNo' => 'CBZ-002',
                'marketplaceID' => $marketplaces[1]->marketplaceID,
                'size' => '10 sqm',
                'rentalFee' => 5500.00,
                'stallStatus' => 'Vacant',
                'applicationDeadline' => now()->addDays(45),
            ],
            [
                'stallNo' => 'CBZ-003',
                'marketplaceID' => $marketplaces[1]->marketplaceID,
                'size' => '15 sqm',
                'rentalFee' => 7000.00,
                'stallStatus' => 'Occupied',
                'applicationDeadline' => null,
            ],
            // Riverside Plaza stalls
            [
                'stallNo' => 'RSP-001',
                'marketplaceID' => $marketplaces[2]->marketplaceID,
                'size' => '25 sqm',
                'rentalFee' => 12000.00,
                'stallStatus' => 'Occupied',
                'applicationDeadline' => null,
            ],
            [
                'stallNo' => 'RSP-002',
                'marketplaceID' => $marketplaces[2]->marketplaceID,
                'size' => '10 sqm',
                'rentalFee' => 5000.00,
                'stallStatus' => 'Vacant',
                'applicationDeadline' => now()->addDays(20),
            ],
            [
                'stallNo' => 'RSP-003',
                'marketplaceID' => $marketplaces[2]->marketplaceID,
                'size' => '12 sqm',
                'rentalFee' => 6000.00,
                'stallStatus' => 'Vacant',
                'applicationDeadline' => now()->addDays(60),
            ],
        ];

        foreach ($stalls as $stall) {
            Stall::create($stall);
        }
    }
}
