<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            if (!Schema::hasColumn('feedbacks', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('contractID')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('feedbacks', 'usability_comprehension')) {
                $table->unsignedTinyInteger('usability_comprehension')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('feedbacks', 'usability_learning')) {
                $table->unsignedTinyInteger('usability_learning')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'usability_effort')) {
                $table->unsignedTinyInteger('usability_effort')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'usability_interface')) {
                $table->unsignedTinyInteger('usability_interface')->nullable();
            }

            if (!Schema::hasColumn('feedbacks', 'functionality_registration')) {
                $table->unsignedTinyInteger('functionality_registration')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'functionality_tasks')) {
                $table->unsignedTinyInteger('functionality_tasks')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'functionality_results')) {
                $table->unsignedTinyInteger('functionality_results')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'functionality_security')) {
                $table->unsignedTinyInteger('functionality_security')->nullable();
            }

            if (!Schema::hasColumn('feedbacks', 'reliability_error_handling')) {
                $table->unsignedTinyInteger('reliability_error_handling')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'reliability_command_tolerance')) {
                $table->unsignedTinyInteger('reliability_command_tolerance')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'reliability_recovery')) {
                $table->unsignedTinyInteger('reliability_recovery')->nullable();
            }

            if (!Schema::hasColumn('feedbacks', 'comments')) {
                $table->text('comments')->nullable();
            }
            if (!Schema::hasColumn('feedbacks', 'archived_at')) {
                $table->timestamp('archived_at')->nullable();
            }
        });

        if (Schema::hasColumn('feedbacks', 'responses')) {
            Schema::table('feedbacks', function (Blueprint $table) {
                $table->dropColumn('responses');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('feedbacks', 'responses')) {
            Schema::table('feedbacks', function (Blueprint $table) {
                $table->json('responses')->nullable();
            });
        }

        if (Schema::hasColumn('feedbacks', 'user_id')) {
            Schema::table('feedbacks', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        $columnsToDrop = [
            'user_id',
            'usability_comprehension',
            'usability_learning',
            'usability_effort',
            'usability_interface',
            'functionality_registration',
            'functionality_tasks',
            'functionality_results',
            'functionality_security',
            'reliability_error_handling',
            'reliability_command_tolerance',
            'reliability_recovery',
            'comments',
            'archived_at',
        ];
        $columnsToDrop = array_values(array_filter($columnsToDrop, function ($column) {
            return Schema::hasColumn('feedbacks', $column);
        }));

        if (!empty($columnsToDrop)) {
            Schema::table('feedbacks', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }
};


