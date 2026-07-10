<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cfgsys extends Model
{
    protected $table = 'cfgsys';

    protected $fillable = [
        'api_key',
        'api_url',
        'kodeljk',
    ];

    public static function current(): ?self
    {
        return static::query()
            ->latest('id')
            ->first();
    }
}
