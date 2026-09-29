<?php

use App\Http\Controllers\Api\LunasKreditSyncController;
use App\Http\Controllers\Api\RpsSyncController;
use App\Http\Controllers\Api\TagihanKreditSyncController;
use App\Http\Controllers\Api\TransaksiCioSyncController;
use App\Http\Middleware\LogSyncActivity;
use Illuminate\Support\Facades\Route;

Route::middleware(LogSyncActivity::class)->group(function () {
    Route::post('sync/lunas-kredit/receive', [LunasKreditSyncController::class, 'receive']);
    Route::post('sync/tagihan-kredit/receive', [TagihanKreditSyncController::class, 'receive']);
    Route::post('sync/transaksi-cio/receive', [TransaksiCioSyncController::class, 'receive']);
    Route::post('sync/rps/receive', [RpsSyncController::class, 'receive']);
});
