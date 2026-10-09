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
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->constrained('pertemuans')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->enum('status', ['Hadir', 'Absen', 'Belum Presensi'])->default('Belum Presensi');
            $table->timestamp('waktu_presensi')->nullable();
            $table->enum('metode', ['QR Scan', 'Manual Dosen'])->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();

            $table->unique(['pertemuan_id', 'mahasiswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
