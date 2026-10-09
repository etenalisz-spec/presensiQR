<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKuliah extends Model
{
    protected $guarded = [];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function pertemuans()
    {
        return $this->hasMany(Pertemuan::class)->orderBy('pertemuan_ke');
    }
}
