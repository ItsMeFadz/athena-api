<?php

use App\Http\Controllers\Api\LunasKreditSyncController;
use App\Http\Controllers\Api\TagihanKreditSyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('sync/lunas-kredit')->group(function ()
{
    Route::post('send', [LunasKreditSyncController::class, 'send']);
    Route::post('receive', [LunasKreditSyncController::class, 'receive']);
});

Route::prefix('sync/tagihan-kredit')->group(function ()
{
    Route::post('receive', [TagihanKreditSyncController::class, 'receive']);
});
