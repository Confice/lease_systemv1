<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id('contractID');
            $table->unsignedBigInteger('stallID');
            $table->unsignedBigInteger('userID');
            $table->timestamp('startDate')->useCurrent();
            $table->timestamp('endDate')->nullable();
            $table->enum('contractStatus', ['Active', 'Terminated', 'Expiring'])->default('Active');
            $table->enum('expiringStatus', ['Unconfirmed', 'Withdrawn', 'For Review', 'Approved', 'Rejected'])->nullable();
            $table->text('customReason')->nullable(); // Reason for terminating contract or rejecting renewal request
            $table->unsignedBigInteger('renewedFrom')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('stallID')
                  ->references('stallID')
                  ->on('stalls')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreign('userID')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
                  
            $table->foreign('renewedFrom')
                  ->references('contractID')
                  ->on('contracts')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contracts');
    }
};