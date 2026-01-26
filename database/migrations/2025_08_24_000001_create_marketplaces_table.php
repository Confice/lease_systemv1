<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('marketplaces', function (Blueprint $table) {
            $table->id('marketplaceID');
            $table->string('marketplace', 100);
            $table->string('marketplaceAddress', 255);
            $table->string('logoPath', 255)->nullable();
            $table->string('facebookLink', 255)->nullable();
            $table->string('telephoneNo', 20)->nullable();
            $table->string('viberNo', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('marketplaces');
    }
};