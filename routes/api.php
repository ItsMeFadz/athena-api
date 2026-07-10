<?php

use App\Http\Controllers\Api\LunasKreditSyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('sync/lunas-kredit')->group(function () {
    Route::post('send', [LunasKreditSyncController::class, 'send']);
    Route::post('receive', [LunasKreditSyncController::class, 'receive']);
});
