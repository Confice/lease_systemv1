<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id('feedbackID');
            $table->unsignedBigInteger('contractID'); // feedback is tied to a contract
            $table->json('responses'); // all Q1–Q15 answers stored here
            $table->timestamps();

            $table->foreign('contractID')
                  ->references('contractID')
                  ->on('contracts')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
