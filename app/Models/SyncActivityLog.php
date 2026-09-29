<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncActivityLog extends Model
{
    protected $table = 'sync_activity_logs';

    protected $fillable = [
        'endpoint',
        'method',
        'status_code',
        'status',
        'received_count',
        'saved_count',
        'updated_count',
        'error_details',
        'duration_ms',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'received_count' => 'integer',
        'saved_count' => 'integer',
        'updated_count' => 'integer',
        'duration_ms' => 'integer',
    ];
}
