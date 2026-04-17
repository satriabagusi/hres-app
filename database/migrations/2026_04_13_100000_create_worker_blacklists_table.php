<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 32)->index();
            $table->string('full_name');
            $table->boolean('is_blacklisted')->default(true)->index();
            $table->enum('blacklist_type', ['temporary', 'permanent'])->default('permanent');
            $table->date('blacklisted_until')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('blacklisted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['nik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_blacklists');
    }
};
