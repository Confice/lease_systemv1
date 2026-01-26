<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stores', function (Blueprint $table) {
            $table->id('storeID');
            $table->string('storeName', 100);
            $table->string('businessType', 100);
            $table->unsignedBigInteger('userID');
            $table->unsignedBigInteger('marketplaceID');
            $table->timestamps();

            $table->foreign('userID')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
                  
            $table->foreign('marketplaceID')
                  ->references('marketplaceID')
                  ->on('marketplaces')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stores');
    }
};