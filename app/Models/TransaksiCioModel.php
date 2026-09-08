<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiCioModel extends Model
{
    protected $table = 'transaksi_cio';

    protected $fillable = [
        'kodeljk',
        'sandicabang',
        'tgltrx',
        'userid',
        'username',
        'trxid',
        'nopkg',
        'kodetrn',
        'noacc',
        'dokumen',
        'nominal',
        'keterangan',
        'ststrx',
        'stsbar',
        'cd_trx1',
        'cd_trx2',
        'caller',
        'flag',
        'param1',
        'param2',
        'delete_reason',
        'oto_date',
        'oto_user',
        'create_date',
        'create_user',
        'update_date',
        'update_user',
        'delete_date',
        'delete_user',
    ];

    protected $casts = [
        'tgltrx' => 'date',

        'userid' => 'integer',
        'trxid' => 'integer',

        'nominal' => 'decimal:2',

        'ststrx' => 'integer',
        'stsbar' => 'integer',

        'oto_date' => 'datetime',
        'create_date' => 'datetime',
        'update_date' => 'datetime',
        'delete_date' => 'datetime',
        'delete_user' => 'datetime',
    ];
}
