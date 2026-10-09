<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $guarded = [];

    public function mahasiswas()
    {
        return $this->hasMany(Mahasiswa::class);
    }

    public function jadwalKuliahs()
    {
        return $this->hasMany(JadwalKuliah::class);
    }
}
