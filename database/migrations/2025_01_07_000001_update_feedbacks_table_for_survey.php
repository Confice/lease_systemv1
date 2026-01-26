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
            $table->foreignId('user_id')->nullable()->after('contractID')->constrained()->nullOnDelete();

            $table->unsignedTinyInteger('usability_comprehension')->nullable()->after('user_id');
            $table->unsignedTinyInteger('usability_learning')->nullable();
            $table->unsignedTinyInteger('usability_effort')->nullable();
            $table->unsignedTinyInteger('usability_interface')->nullable();

            $table->unsignedTinyInteger('functionality_registration')->nullable();
            $table->unsignedTinyInteger('functionality_tasks')->nullable();
            $table->unsignedTinyInteger('functionality_results')->nullable();
            $table->unsignedTinyInteger('functionality_security')->nullable();

            $table->unsignedTinyInteger('reliability_error_handling')->nullable();
            $table->unsignedTinyInteger('reliability_command_tolerance')->nullable();
            $table->unsignedTinyInteger('reliability_recovery')->nullable();

            $table->text('comments')->nullable();
            $table->timestamp('archived_at')->nullable();
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
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->json('responses')->nullable();

            $table->dropForeign(['user_id']);
            $table->dropColumn([
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
            ]);
        });
    }
};


