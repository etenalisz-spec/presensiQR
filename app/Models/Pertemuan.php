<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_open' => 'boolean',
        'tanggal_jadwal' => 'date',
        'qr_expires_at' => 'datetime',
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class);
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }
}
