<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            // Drop existing foreign key if present
            try {
                $table->dropForeign(['storeID']);
            } catch (\Throwable $e) {
                // ignore if it doesn't exist
            }

            if (Schema::hasColumn('stalls', 'storeID')) {
                $table->renameColumn('storeID', 'userID');
            }
        });

        Schema::table('stalls', function (Blueprint $table) {
            // Add foreign key to users(id)
            if (!Schema::hasColumn('stalls', 'userID')) {
                $table->unsignedBigInteger('userID')->nullable()->after('marketplaceID');
            }
            $table->foreign('userID')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            try {
                $table->dropForeign(['userID']);
            } catch (\Throwable $e) {
                // ignore
            }
            if (Schema::hasColumn('stalls', 'userID')) {
                $table->renameColumn('userID', 'storeID');
            }
        });
        Schema::table('stalls', function (Blueprint $table) {
            // Optionally restore old foreign key if needed
            // $table->foreign('storeID')->references('storeID')->on('stores')->nullOnDelete();
        });
    }
};


