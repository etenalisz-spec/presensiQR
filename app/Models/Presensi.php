<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $guarded = [];

    protected $casts = [
        'waktu_presensi' => 'datetime',
    ];

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
