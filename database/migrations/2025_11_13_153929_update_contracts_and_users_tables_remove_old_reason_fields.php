<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =====================================================
        // CONTRACTS TABLE CHANGES
        // =====================================================
        
        // Step 1: Migrate existing data to customReason (if columns exist)
        if (Schema::hasColumn('contracts', 'terminationReason') || 
            Schema::hasColumn('contracts', 'terCustomReason') || 
            Schema::hasColumn('contracts', 'expCustomReason')) {
            
            // Add customReason column if it doesn't exist
            if (!Schema::hasColumn('contracts', 'customReason')) {
                Schema::table('contracts', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->after('expiringStatus');
                });
            }
            
            // Migrate data: prioritize terCustomReason, then expCustomReason, then terminationReason
            DB::statement("
                UPDATE contracts 
                SET customReason = CASE
                    WHEN terCustomReason IS NOT NULL AND terCustomReason != '' THEN terCustomReason
                    WHEN expCustomReason IS NOT NULL AND expCustomReason != '' THEN expCustomReason
                    WHEN terminationReason IS NOT NULL AND terminationReason != '' THEN terminationReason
                    ELSE NULL
                END
                WHERE (terminationReason IS NOT NULL OR terCustomReason IS NOT NULL OR expCustomReason IS NOT NULL)
                  AND (customReason IS NULL OR customReason = '')
            ");
            
            // Drop old columns
            Schema::table('contracts', function (Blueprint $table) {
                if (Schema::hasColumn('contracts', 'terminationReason')) {
                    $table->dropColumn('terminationReason');
                }
                if (Schema::hasColumn('contracts', 'terCustomReason')) {
                    $table->dropColumn('terCustomReason');
                }
                if (Schema::hasColumn('contracts', 'expCustomReason')) {
                    $table->dropColumn('expCustomReason');
                }
            });
        } else {
            // If old columns don't exist, just ensure customReason exists
            if (!Schema::hasColumn('contracts', 'customReason')) {
                Schema::table('contracts', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->after('expiringStatus');
                });
            }
        }
        
        // =====================================================
        // USERS TABLE CHANGES
        // =====================================================
        
        // Step 1: Update any existing 'Locked' status to 'Deactivated'
        if (Schema::hasColumn('users', 'userStatus')) {
            DB::table('users')
                ->where('userStatus', 'Locked')
                ->update(['userStatus' => 'Deactivated']);
        }
        
        // Step 2: Migrate deactivationReason to customReason if needed
        if (Schema::hasColumn('users', 'deactivationReason')) {
            // Ensure customReason column exists and is TEXT type
            if (!Schema::hasColumn('users', 'customReason')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->after('userStatus');
                });
            } else {
                // Change customReason from VARCHAR to TEXT if needed
                Schema::table('users', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->change();
                });
            }
            
            // Migrate data from deactivationReason to customReason
            DB::statement("
                UPDATE users 
                SET customReason = deactivationReason 
                WHERE deactivationReason IS NOT NULL 
                  AND deactivationReason != '' 
                  AND (customReason IS NULL OR customReason = '')
            ");
            
            // Drop deactivationReason column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('deactivationReason');
            });
        } else {
            // If deactivationReason doesn't exist, just ensure customReason is TEXT
            if (Schema::hasColumn('users', 'customReason')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->change();
                });
            } else {
                Schema::table('users', function (Blueprint $table) {
                    $table->text('customReason')->nullable()->after('userStatus');
                });
            }
        }
        
        // Step 3: Modify userStatus enum to remove 'Locked'
        // MySQL doesn't support removing enum values directly, so we use raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN userStatus ENUM('Active', 'Pending', 'Deactivated') DEFAULT 'Pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert contracts table changes
        if (Schema::hasColumn('contracts', 'customReason')) {
            Schema::table('contracts', function (Blueprint $table) {
                $table->enum('terminationReason', ['Breach of Contract', 'Late Payment', 'Others'])->nullable()->after('contractStatus');
                $table->string('terCustomReason', 150)->nullable()->after('terminationReason');
                $table->string('expCustomReason', 150)->nullable()->after('expiringStatus');
            });
            
            // Note: We can't restore the migrated data automatically
            // The customReason data would need to be manually restored if needed
            
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('customReason');
            });
        }
        
        // Revert users table changes
        DB::statement("ALTER TABLE users MODIFY COLUMN userStatus ENUM('Active', 'Pending', 'Deactivated', 'Locked') DEFAULT 'Pending'");
        
        if (Schema::hasColumn('users', 'customReason')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('deactivationReason', [
                    'Suspicious or Unusual Activity', 
                    'Violation of Terms or Policies', 
                    'Unpaid Bills or Payment Problems', 
                    'Inactivity for an Extended Period', 
                    'Requested by Account Owner', 
                    'Other'
                ])->nullable()->after('userStatus');
                $table->string('customReason', 150)->nullable()->change();
            });
            
            // Note: We can't restore the migrated data automatically
            // The customReason data would need to be manually restored if needed
        }
    }
};
