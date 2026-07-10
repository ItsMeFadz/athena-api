<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LunasKreditSync extends Model
{
    protected $fillable = [
        'nama_bank',
        'alamat',
        'notelp',
        'nohp',
        'kodeljk',
        'sandicabang',
        'cif',
        'norekcrd',
        'plafon',
        'namalengkap',
        'noakad',
        'os',
        'denda',
        'penalty',
        'kodekondisi',
        'kondisi',
        'tglkondisi',
        'tgleff',
        'namaproduk',
        'ao',
        'kolektibilitas',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'plafon' => 'decimal:2',
            'os' => 'decimal:2',
            'denda' => 'decimal:2',
            'penalty' => 'decimal:2',
            'tglkondisi' => 'date',
            'tgleff' => 'date',
            'synced_at' => 'datetime',
        ];
    }
}
