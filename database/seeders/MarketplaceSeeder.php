<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marketplaces = [
            [
                'marketplace' => 'Downtown Market Hub',
                'marketplaceAddress' => '123 Main Street, Downtown District, Metro Manila',
                'facebookLink' => 'https://facebook.com/downtownmarkethub',
                'telephoneNo' => '+63 2 1234 5678',
                'viberNo' => '+63 917 123 4567',
            ],
            [
                'marketplace' => 'Central Bazaar',
                'marketplaceAddress' => '456 Commerce Avenue, Central Business District, Quezon City',
                'facebookLink' => 'https://facebook.com/centralbazaar',
                'telephoneNo' => '+63 2 2345 6789',
                'viberNo' => '+63 918 234 5678',
            ],
            [
                'marketplace' => 'Riverside Plaza',
                'marketplaceAddress' => '789 River Road, Riverside Area, Pasig City',
                'facebookLink' => 'https://facebook.com/riversideplaza',
                'telephoneNo' => '+63 2 3456 7890',
                'viberNo' => '+63 919 345 6789',
            ],
        ];

        foreach ($marketplaces as $marketplace) {
            Marketplace::create($marketplace);
        }
    }
}
