<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cfgsys;
use App\Models\TagihanKreditSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagihanKreditSyncController extends Controller
{
    /**
     * Menerima data tagihan kredit dari Hermes.
     */
    public function receive(Request $request): JsonResponse
    {
        // Validasi API Key
        if (!$this->hasValidSyncKey($request))
        {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'source' => ['nullable', 'array'],

            'items' => ['required', 'array'],

            'items.*.norekcrd' => [
                'required',
                'string',
                'max:50',
            ],

            // Debitur
            'items.*.namalengkap' => [
                'nullable',
                'string',
                'max:150',
            ],

            'items.*.alamatktp' => [
                'nullable',
                'string',
            ],

            'items.*.alamatdomisili' => [
                'nullable',
                'string',
            ],

            'items.*.notelp' => [
                'nullable',
                'string',
                'max:50',
            ],

            'items.*.nohp' => [
                'nullable',
                'string',
                'max:50',
            ],

            // Kredit
            'items.*.noakad' => [
                'nullable',
                'string',
                'max:100',
            ],

            'items.*.bakidebet' => [
                'nullable',
                'numeric',
            ],

            // Jadwal
            'items.*.tgltempo' => [
                'nullable',
                'integer',
                'between:1,31',
            ],

            'items.*.tglefektif' => [
                'nullable',
                'integer',
                'between:1,31',
            ],

            'items.*.graceperiod' => [
                'nullable',
                'integer',
            ],

            // Status
            'items.*.statusrek' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Tagihan
            'items.*.tagpokok' => [
                'nullable',
                'numeric',
            ],

            'items.*.tagbunga' => [
                'nullable',
                'numeric',
            ],

            'items.*.tagdenda' => [
                'nullable',
                'numeric',
            ],

            'items.*.totalangsuran' => [
                'nullable',
                'numeric',
            ],

            'items.*.haritunggakkan' => [
                'nullable',
                'integer',
            ],

            // Tabungan
            'items.*.norekpembayaran' => [
                'nullable',
                'string',
                'max:50',
            ],

            'items.*.saldotab' => [
                'nullable',
                'numeric',
            ],

            'items.*.saldotabactual' => [
                'nullable',
                'numeric',
            ],

            // AO
            'items.*.kodeao' => [
                'nullable',
                'string',
                'max:50',
            ],

            'items.*.ao' => [
                'nullable',
                'string',
                'max:150',
            ],

            // Instansi
            'items.*.ketinstansi' => [
                'nullable',
                'string',
                'max:150',
            ],
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $items = $validator->validated()['items'];

        $syncedAt = now();

        $saved = 0;
        $updated = 0;

        foreach ($items as $item)
        {

            $item['synced_at'] = $syncedAt;

            /*
             * Gunakan kombinasi:
             * kodeljk
             * sandicabang
             * norekcrd
             * tglangsuran
             *
             * karena satu rekening kredit mempunyai
             * banyak jadwal angsuran.
             */
            $existing = TagihanKreditSync::query()
                ->where('norekcrd', $item['norekcrd'])
                ->first();

            TagihanKreditSync::query()->updateOrCreate(
                [
                    'norekcrd' => $item['norekcrd'],
                ],
                $item
            );

            if ($existing)
            {
                $updated++;
            }
            else
            {
                $saved++;
            }
        }

        return response()->json([
            'message' => 'Data tagihan kredit berhasil diterima.',
            'received' => count($items),
            'saved' => $saved,
            'updated' => $updated,
        ]);
    }

    /**
     * Validasi API Key sinkronisasi.
     */
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
