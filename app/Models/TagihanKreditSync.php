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
        'tglangsuran',
        'tglefektif',
        'tgljthtempo',
        'graceperiod',
        'statusrek',
        'plafon',
        'jangkawaktu',
        'tungpokok',
        'tungbunga',
        'kolektibilitas',
        'kodekondisi',
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

        'haritunggakkan' => 'integer',

        'saldotab' => 'decimal:2',
        'saldotabactual' => 'decimal:2',

        'synced_at' => 'datetime',
    ];
}
