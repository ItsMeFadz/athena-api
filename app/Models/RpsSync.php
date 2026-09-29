<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RpsSync extends Model
{
    protected $table = 'sync_rps';

    protected $fillable = [
        'kodeljk',
        'sandicabang',
        'cif',
        'norekcrd',
        'periode',
        'tglangsuran',
        'saldoawal',
        'saldoakhir',
        'tagpokok',
        'tagbunga',
        'totalangsuran',
        'tagdenda',
        'byrpokok',
        'byrbunga',
        'byrdenda',
        'tglbyr',
        'sukubunga',
        'noakad',
        'jmlharidenda',
        'tglbyrdenda',
        'tglbyrbunga',
        'synced_at',
    ];

    protected $casts = [
        'periode' => 'integer',
        'tglangsuran' => 'date',
        'saldoawal' => 'decimal:2',
        'saldoakhir' => 'decimal:2',
        'tagpokok' => 'decimal:2',
        'tagbunga' => 'decimal:2',
        'totalangsuran' => 'decimal:2',
        'tagdenda' => 'decimal:2',
        'byrpokok' => 'decimal:2',
        'byrbunga' => 'decimal:2',
        'byrdenda' => 'decimal:2',
        'tglbyr' => 'datetime',
        'sukubunga' => 'decimal:2',
        'jmlharidenda' => 'integer',
        'tglbyrdenda' => 'date',
        'tglbyrbunga' => 'date',
        'synced_at' => 'datetime',
    ];
}
