<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stalls', function (Blueprint $table) {
            $table->id('stallID');
            $table->string('stallNo', 20);
            $table->unsignedBigInteger('storeID')->nullable();
            $table->unsignedBigInteger('marketplaceID');
            $table->string('size', 50)->nullable();
            $table->decimal('rentalFee', 10, 2)->default(0);
            $table->timestamp('applicationDeadline')->nullable();
            $table->enum('stallStatus', ['Vacant', 'Occupied'])->default('Vacant');
            $table->timestamp('lastStatusChange')->useCurrent(); // merged from stall_histories
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('marketplaceID')
                  ->references('marketplaceID')
                  ->on('marketplaces')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
                  
            $table->foreign('storeID')
                  ->references('storeID')
                  ->on('stores')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stalls');
    }
};