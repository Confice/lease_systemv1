<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('applications', function (Blueprint $table) {
            $table->id('applicationID');
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('stallID');
            $table->timestamp('dateApplied')->useCurrent();
            $table->enum('appStatus', ['Proposal Received', 'Presentation Scheduled', 'Pending Submission', 'Proposal Rejected', 'Requirements Received', 'Withdrawn']);
            $table->string('remarks', 255)->nullable();
            $table->string('noticeType', 50)->nullable(); // for notices
            $table->timestamp('noticeDate')->nullable(); // for notices
            $table->unsignedBigInteger('contractID')->nullable(); // link to contract once approved
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('userID')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreign('stallID')
                  ->references('stallID')
                  ->on('stalls')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
                  
            $table->foreign('contractID')
                  ->references('contractID')
                  ->on('contracts')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('applications');
    }
};