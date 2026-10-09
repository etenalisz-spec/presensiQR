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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kelas')->unique(); // e.g. 05SIFE001
            $table->string('nama_kelas');           // e.g. 05SIFE001 / SI-5A
            $table->string('prodi')->default('Sistem Informasi');
            $table->integer('semester')->default(5);
            $table->string('tahun_ajaran')->default('GANJIL 2026/2027');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
