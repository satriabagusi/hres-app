<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade');
            $table->foreignId('reviewed_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['on_review','approved', 'rejected']);
            $table->enum('status_mcu', ['fit', 'fit_with_note', 'unfit', 'follow_up']);
            $table->text('notes')->nullable();
            $table->enum('risk_notes', ['low_risk', 'medium_risk', 'high_risk']);
            $table->string('mcu_document')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_reviews');
    }
};
