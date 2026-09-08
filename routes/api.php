<?php

use App\Http\Controllers\Api\LunasKreditSyncController;
use App\Http\Controllers\Api\TagihanKreditSyncController;
use App\Http\Controllers\Api\TransaksiCioSyncController;
use Illuminate\Support\Facades\Route;

Route::post('sync/lunas-kredit/receive', [LunasKreditSyncController::class, 'receive']);
Route::post('sync/tagihan-kredit/receive', [TagihanKreditSyncController::class, 'receive']);
Route::post('sync/transaksi-cio/receive', [TransaksiCioSyncController::class, 'receive']);
