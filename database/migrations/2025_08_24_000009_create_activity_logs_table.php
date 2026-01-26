<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('activityID');
            $table->enum('actionType', ['Create', 'Update', 'Delete', 'Login', 'Logout', 'Other']);
            $table->string('entity', 100);
            $table->unsignedBigInteger('entityID');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('userID');
            $table->timestamps();
            $table->index(['entity', 'entityID']);
            
            $table->foreign('userID')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activity_logs');
    }
};
