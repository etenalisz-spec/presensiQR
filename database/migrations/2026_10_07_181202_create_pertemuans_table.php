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
        Schema::create('pertemuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliahs')->onDelete('cascade');
            $table->integer('pertemuan_ke'); // 1 s/d 14
            $table->date('tanggal_jadwal')->nullable();
            $table->string('tema')->nullable();
            $table->enum('jenis_pertemuan', ['Tatap Muka', 'E-learning'])->default('Tatap Muka');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_open')->default(false);
            $table->string('qr_token')->nullable()->unique();
            $table->timestamp('qr_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertemuans');
    }
};
