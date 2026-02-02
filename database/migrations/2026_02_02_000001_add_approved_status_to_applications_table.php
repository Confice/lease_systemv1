<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY appStatus ENUM('Proposal Received','Presentation Scheduled','Pending Submission','Proposal Rejected','Requirements Received','Withdrawn','Approved') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE applications MODIFY appStatus ENUM('Proposal Received','Presentation Scheduled','Pending Submission','Proposal Rejected','Requirements Received','Withdrawn') NOT NULL");
        }
    }
};
