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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_contractor_id')->constrained('project_contractors')->onDelete('cascade');
            $table->string('full_name');
            $table->string('nik');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('position');
            $table->string('photo')->nullable();
            $table->string('ktp_document')->nullable();
            $table->string('form_b_document')->nullable();
            $table->string('induction_card_number')->nullable();
            $table->string('security_card_number')->nullable();
            $table->string('age_justification_document')->comment('Surat keterangan umur')->nullable();
            $table->string('domicile')->comment('Alamat domisili pekerja, bisa berupa alamat KTP');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->default('laki-laki');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
