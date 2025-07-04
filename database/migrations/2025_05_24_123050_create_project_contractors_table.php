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
        Schema::create('project_contractors', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('memo_number')->unique();
            $table->string('memo_document')->nullable();
            $table->date('start_date');
            $table->date('end_date');

            $table->unsignedBigInteger('contractor_id');
            $table->foreign('contractor_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_contractors');
    }
};
