<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cfgsys;
use App\Models\TransaksiCio;
use App\Models\TransaksiCioModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiCioSyncController extends Controller
{
    public function receive(Request $request): JsonResponse
    {
        if (!$this->hasValidSyncKey($request)) {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'items' => ['required', 'array'],

            'items.*.kodeljk' => ['nullable', 'string', 'max:6'],
            'items.*.sandicabang' => ['nullable', 'string', 'max:3'],
            'items.*.tgltrx' => ['nullable', 'date'],

            'items.*.userid' => ['nullable', 'integer'],
            'items.*.username' => ['nullable', 'string', 'max:20'],
            'items.*.kodeao' => ['nullable', 'string', 'max:10'],

            'items.*.trxid' => ['required', 'integer'],

            'items.*.nopkg' => ['nullable', 'string', 'max:20'],
            'items.*.kodetrn' => ['nullable', 'string', 'max:4'],
            'items.*.noacc' => ['nullable', 'string', 'max:14'],
            'items.*.dokumen' => ['nullable', 'string', 'max:20'],

            'items.*.nominal' => ['nullable', 'numeric'],
            'items.*.keterangan' => ['nullable', 'string', 'max:100'],

            'items.*.ststrx' => ['nullable', 'integer'],
            'items.*.stsbar' => ['nullable', 'integer'],

            'items.*.cd_trx1' => ['nullable', 'string', 'max:3'],
            'items.*.cd_trx2' => ['nullable', 'string', 'max:3'],

            'items.*.caller' => ['nullable', 'string', 'max:20'],
            'items.*.flag' => ['nullable', 'string', 'max:3'],

            'items.*.param1' => ['nullable', 'string', 'max:20'],
            'items.*.param2' => ['nullable', 'string', 'max:20'],

            'items.*.delete_reason' => ['nullable', 'string', 'max:100'],

            'items.*.oto_date' => ['nullable', 'date'],
            'items.*.oto_user' => ['nullable', 'string', 'max:20'],

            'items.*.create_date' => ['nullable', 'date'],
            'items.*.create_user' => ['nullable', 'string', 'max:20'],

            'items.*.update_date' => ['nullable', 'date'],
            'items.*.update_user' => ['nullable', 'string', 'max:20'],

            'items.*.delete_date' => ['nullable', 'date'],
            'items.*.delete_user' => ['nullable', 'string', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $items = $validator->validated()['items'];

        foreach ($items as $item) {
            TransaksiCioModel::query()->updateOrCreate(
                [
                    'trxid' => $item['trxid'],
                ],
                $item
            );
        }

        return response()->json([
            'message' => 'Transaksi berhasil diterima.',
            'received' => count($items),
        ]);
    }

    private function hasValidSyncKey(Request $request): bool
    {
        $configuredKey = (string) (
            Cfgsys::current()?->api_key
            ?: env('SYNC_API_KEY', '')
        );

        $requestKey = (string) (
            $request->header('X-Sync-Key')
            ?: $request->bearerToken()
        );

        return $configuredKey !== ''
            && $requestKey !== ''
            && hash_equals($configuredKey, $requestKey);
    }
}
