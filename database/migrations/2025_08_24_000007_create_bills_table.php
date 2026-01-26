<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id('billID');
            $table->unsignedBigInteger('stallID');
            $table->unsignedBigInteger('contractID');
            $table->timestamp('dueDate');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamp('datePaid')->nullable();
            $table->enum('status', ['Pending', 'Paid', 'Invalid', 'Due'])->default('Pending');
            $table->string('paymentProof', 255)->nullable();
            $table->timestamp('dateUploaded')->nullable();
            $table->softDeletes();
            $table->timestamps();

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

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};