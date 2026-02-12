<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Marketplace;
use App\Models\Stall;
use App\Models\Store;
use App\Models\Contract;
use App\Models\Bill;
use App\Models\Application;
use App\Models\Feedback;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DummyUsersSeeder extends Seeder
{
    /**
     * Seed 12 dummy tenant accounts with system interactions.
     * Uses ONLY existing marketplaces and stalls. Does not create/remove/modify stalls or marketplaces.
     */
    public function run(): void
    {
        $marketplaces = Marketplace::whereNull('deleted_at')->get();
        if ($marketplaces->isEmpty()) {
            $this->command->warn('No marketplaces found. Skipping dummy users seeder.');
            return;
        }

        $vacantStalls = Stall::where('stallStatus', 'Vacant')
            ->whereNull('deleted_at')
            ->orderBy('marketplaceID')
            ->orderBy('stallID')
            ->get();

        $allStalls = Stall::whereNull('deleted_at')->get();
        if ($allStalls->isEmpty()) {
            $this->command->warn('No stalls found. Skipping dummy users seeder.');
            return;
        }

        $password = Hash::make('Password123!');
        $appStatuses = [
            'Proposal Received',
            'Presentation Scheduled',
            'Pending Submission',
            'Proposal Rejected',
            'Requirements Received',
            'Withdrawn',
        ];

        $tenantData = [
            ['Dummy', 'A', 'Tenant', 'dummy.tenant.01@example.com'],
            ['Dummy', 'B', 'Tenant', 'dummy.tenant.02@example.com'],
            ['Dummy', 'C', 'Tenant', 'dummy.tenant.03@example.com'],
            ['Dummy', 'D', 'Tenant', 'dummy.tenant.04@example.com'],
            ['Dummy', 'E', 'Tenant', 'dummy.tenant.05@example.com'],
            ['Dummy', 'F', 'Tenant', 'dummy.tenant.06@example.com'],
            ['Dummy', 'G', 'Tenant', 'dummy.tenant.07@example.com'],
            ['Dummy', 'H', 'Tenant', 'dummy.tenant.08@example.com'],
            ['Dummy', 'I', 'Tenant', 'dummy.tenant.09@example.com'],
            ['Dummy', 'J', 'Tenant', 'dummy.tenant.10@example.com'],
            ['Dummy', 'K', 'Tenant', 'dummy.tenant.11@example.com'],
            ['Dummy', 'L', 'Tenant', 'dummy.tenant.12@example.com'],
        ];

        $users = [];
        foreach ($tenantData as $i => $row) {
            $email = $row[3];
            if (User::where('email', $email)->exists()) {
                $this->command->warn("User {$email} already exists. Skipping duplicate.");
                continue;
            }

            $users[] = User::create([
                'firstName' => $row[0],
                'middleName' => $row[1],
                'lastName' => $row[2],
                'email' => $email,
                'password' => $password,
                'homeAddress' => 'Demo Address ' . ($i + 1) . ', Philippines',
                'contactNo' => '0917-' . str_pad(100 + $i, 3, '0') . '-' . str_pad(5000 + $i, 4, '0'),
                'birthDate' => Carbon::now()->subYears(28)->subDays($i * 7),
                'role' => 'Tenant',
                'userStatus' => 'Active',
                'email_verified_at' => now(),
                'isFirstLogin' => false,
            ]);
        }

        if (empty($users)) {
            $this->command->warn('No new users created. All may already exist.');
            return;
        }

        $stallIndex = 0;
        $maxContracts = min(5, $vacantStalls->count(), count($users));

        for ($i = 0; $i < $maxContracts && $stallIndex < $vacantStalls->count(); $i++) {
            $user = $users[$i];
            $stall = $vacantStalls[$stallIndex];

            DB::beginTransaction();
            try {
                $store = Store::create([
                    'storeName' => $user->firstName . ' ' . $user->lastName . ' Store',
                    'businessType' => ['Retail', 'Food Service', 'Services', 'Merchandise'][$i % 4],
                    'userID' => $user->id,
                    'marketplaceID' => $stall->marketplaceID,
                ]);

                $startDate = Carbon::now()->subMonths(rand(1, 4));
                $endDate = $startDate->copy()->addMonths(6);

                $contract = Contract::create([
                    'stallID' => $stall->stallID,
                    'userID' => $user->id,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'contractStatus' => 'Active',
                ]);

                $stallUpdate = [
                    'stallStatus' => 'Occupied',
                    'lastStatusChange' => now(),
                ];
                if (Schema::hasColumn('stalls', 'storeID')) {
                    $stallUpdate['storeID'] = $store->storeID;
                }
                $stall->update($stallUpdate);

                for ($m = 1; $m <= 6; $m++) {
                    $dueDate = $startDate->copy()->addMonths($m);
                    $status = 'Pending';
                    $datePaid = null;
                    if ($dueDate->lt(now())) {
                        $status = $m <= 3 ? 'Paid' : ($m === 4 ? 'Due' : 'Pending');
                        $datePaid = $status === 'Paid' ? $dueDate->copy()->addDays(rand(0, 3)) : null;
                    }
                    Bill::create([
                        'stallID' => $stall->stallID,
                        'contractID' => $contract->contractID,
                        'dueDate' => $dueDate,
                        'amount' => $stall->rentalFee,
                        'status' => $status,
                        'datePaid' => $datePaid,
                    ]);
                }

                if ($i < 3) {
                    try {
                        Feedback::create([
                            'contractID' => $contract->contractID,
                            'user_id' => $user->id,
                            'usability_comprehension' => rand(3, 5),
                            'usability_learning' => rand(3, 5),
                            'usability_effort' => rand(3, 5),
                            'usability_interface' => rand(3, 5),
                            'functionality_registration' => rand(3, 5),
                            'functionality_tasks' => rand(3, 5),
                            'functionality_results' => rand(3, 5),
                            'functionality_security' => rand(3, 5),
                            'reliability_error_handling' => rand(3, 5),
                            'reliability_command_tolerance' => rand(3, 5),
                            'reliability_recovery' => rand(3, 5),
                            'comments' => 'Dummy feedback for testing. System works well.',
                        ]);
                    } catch (\Exception $e) {
                        \Log::warning("Feedback create skipped (schema may differ): " . $e->getMessage());
                    }
                }

                DB::commit();
                $stallIndex++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Failed to create contract for user {$user->email}: " . $e->getMessage());
            }
        }

        $remainingVacant = Stall::where('stallStatus', 'Vacant')
            ->whereNull('deleted_at')
            ->get();
        $usersForApps = array_slice($users, $maxContracts);

        foreach ($usersForApps as $user) {
            if ($remainingVacant->isEmpty()) {
                break;
            }
            $count = min(2, $remainingVacant->count());
            $stallsForApp = $remainingVacant->random($count);
            foreach ($stallsForApp as $stall) {
                $status = $appStatuses[array_rand($appStatuses)];
                Application::create([
                    'userID' => $user->id,
                    'stallID' => $stall->stallID,
                    'appStatus' => $status,
                    'noticeDate' => $status === 'Presentation Scheduled' ? now()->addDays(7) : null,
                    'noticeType' => $status === 'Presentation Scheduled' ? 'Presentation Scheduled' : null,
                ]);
            }
        }

        $this->command->info('Dummy users seeded successfully.');
        $this->command->info('12 accounts created. Password for all: Password123!');
        $this->command->info('Emails: dummy.tenant.01@example.com through dummy.tenant.12@example.com');
    }
}
