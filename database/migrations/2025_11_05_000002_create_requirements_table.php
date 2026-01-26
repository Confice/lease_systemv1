<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('requirements')) {
            Schema::create('requirements', function (Blueprint $table) {
                $table->id();
                $table->string('requirement_name');
                $table->enum('document_type', ['Proposal', 'Tenancy']);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });
        } else {
            // Table exists, check if we need to add missing columns
            Schema::table('requirements', function (Blueprint $table) {
                if (!Schema::hasColumn('requirements', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('description');
                }
                if (!Schema::hasColumn('requirements', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('is_active');
                }
                if (!Schema::hasColumn('requirements', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('requirements');
    }
};

