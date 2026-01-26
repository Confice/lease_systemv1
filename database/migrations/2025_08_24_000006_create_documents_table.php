<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id('documentID');
            $table->unsignedBigInteger('userID');
            $table->enum('documentType', ['Proposal', 'Tenancy']);
            $table->json('requirementConfig')->nullable(); // System-defined requirement list (editable JSON by admin)
            // Example:
            // {
            //   "Tenancy": ["Government ID", "Business Permit", "Barangay Clearance"],
            //   "Proposal": ["Company Profile", "Financial Statement"]
            // }

            $table->json('files')->nullable(); // Actual uploaded files (linked to requirementName)
            // Example:
            // [
            //   {"requirementName": "Government ID", "filePath": "storage/docs/id123.pdf", "originalName": "ID.pdf", "dateUploaded": "2025-09-12"},
            //   {"requirementName": "Business Permit", "filePath": "storage/docs/permit456.pdf", "originalName": "Permit.pdf", "dateUploaded": "2025-09-12"}
            // ]

            $table->enum('docStatus', ['Pending', 'Approved', 'Rejected', 'Needs Revision'])->default('Pending'); // Review status
            $table->text('revisionComment')->nullable();

            // Links (to either application or contract)
            $table->unsignedBigInteger('applicationID')->nullable();
            $table->unsignedBigInteger('contractID')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('userID')
                  ->references('id')
                  ->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->foreign('applicationID')
                  ->references('applicationID')
                  ->on('applications')
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
        Schema::dropIfExists('documents');
    }
};