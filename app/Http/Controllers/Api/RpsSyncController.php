<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cfgsys;
use App\Models\RpsSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RpsSyncController extends Controller
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
            'items.*.kodeljk' => ['required', 'string', 'size:6'],
            'items.*.sandicabang' => ['required', 'string', 'size:3'],
            'items.*.cif' => ['required', 'string', 'max:7'],
            'items.*.norekcrd' => ['required', 'string', 'max:14'],
            'items.*.periode' => ['required', 'integer'],
            'items.*.tglangsuran' => ['required', 'date'],
            'items.*.saldoawal' => ['nullable', 'numeric'],
            'items.*.saldoakhir' => ['nullable', 'numeric'],
            'items.*.tagpokok' => ['nullable', 'numeric'],
            'items.*.tagbunga' => ['nullable', 'numeric'],
            'items.*.totalangsuran' => ['nullable', 'numeric'],
            'items.*.tagdenda' => ['nullable', 'numeric'],
            'items.*.byrpokok' => ['nullable', 'numeric'],
            'items.*.byrbunga' => ['nullable', 'numeric'],
            'items.*.byrdenda' => ['nullable', 'numeric'],
            'items.*.tglbyr' => ['nullable', 'date'],
            'items.*.sukubunga' => ['nullable', 'numeric'],
            'items.*.noakad' => ['nullable', 'string', 'max:50'],
            'items.*.jmlharidenda' => ['nullable', 'integer'],
            'items.*.tglbyrdenda' => ['nullable', 'date'],
            'items.*.tglbyrbunga' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $items = $validator->validated()['items'];
        $syncedAt = now();
        $saved = 0;
        $updated = 0;

        DB::transaction(function () use ($items, $syncedAt, &$saved, &$updated) {
            foreach ($items as $item) {
                $item['synced_at'] = $syncedAt;
                $item['tglangsuran'] = Carbon::parse($item['tglangsuran'])->toDateString();
                $item['tglbyrdenda'] = isset($item['tglbyrdenda'])
                    ? Carbon::parse($item['tglbyrdenda'])->toDateString()
                    : null;
                $item['tglbyrbunga'] = isset($item['tglbyrbunga'])
                    ? Carbon::parse($item['tglbyrbunga'])->toDateString()
                    : null;
                $item['tglbyr'] = isset($item['tglbyr'])
                    ? Carbon::parse($item['tglbyr'])->toDateTimeString()
                    : null;

                $rps = RpsSync::query()->updateOrCreate(
                    [
                        'norekcrd' => $item['norekcrd'],
                        'tglangsuran' => $item['tglangsuran'],
                    ],
                    $item
                );

                if ($rps->wasRecentlyCreated) {
                    $saved++;
                } else {
                    $updated++;
                }
            }
        });

        return response()->json([
            'message' => 'Data RPS berhasil diterima.',
            'received' => count($items),
            'saved' => $saved,
            'updated' => $updated,
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
