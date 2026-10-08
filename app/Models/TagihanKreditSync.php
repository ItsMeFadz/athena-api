<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagihanKreditSync extends Model
{
    protected $table = 'sync_tagihan_kredit';

    protected $fillable = [
        'kodeljk',
        'sandicabang',
        'norekcrd',
        'namalengkap',
        'alamatktp',
        'alamatdomisili',
        'notelp',
        'nohp',
        'noakad',
        'bakidebet',
        'tgltempo',
        'tglangsuran',
        'tglefektif',
        'tgljthtempo',
        'graceperiod',
        'statusrek',
        'plafon',
        'jangkawaktu',
        'tagpokok',
        'tagbunga',
        'tagdenda',
        'tungpokok',
        'tungbunga',
        'kolektibilitas',
        'kodekondisi',
        'totalangsuran',
        'haritunggakkan',
        'norekpembayaran',
        'saldotab',
        'saldotabactual',
        'kodeao',
        'ao',
        'ketinstansi',
        'synced_at',
    ];

    protected $casts = [
        'bakidebet' => 'decimal:2',

        'tgltempo' => 'integer',
        'tglangsuran' => 'date',
        'tglefektif' => 'date',
        'tgljthtempo' => 'date',
        'graceperiod' => 'integer',

        'tagpokok' => 'decimal:2',
        'tagbunga' => 'decimal:2',
        'tagdenda' => 'decimal:2',
        'totalangsuran' => 'decimal:2',

        'haritunggakkan' => 'integer',

        'saldotab' => 'decimal:2',
        'saldotabactual' => 'decimal:2',

        'synced_at' => 'datetime',
    ];
}
