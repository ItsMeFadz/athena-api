<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cfgsys;
use App\Models\TagihanKreditSync;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            'items.*.tglefektif' => [
                'nullable',
                'date',
            ],

            'items.*.tgljthtempo' => [
                'nullable',
                'date',
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
            'items.*.plafon' => [
                'nullable',
                'numeric',
            ],

            // Tagihan
            'items.*.jangkawaktu' => [
                'nullable',
                'integer',
            ],

            'items.*.tungpokok' => [
                'nullable',
                'numeric',
            ],

            'items.*.tungbunga' => [
                'nullable',
                'numeric',
            ],

            'items.*.kolektibilitas' => [
                'string',
                'max:10',
            ],

            'items.*.kodekondisi' => [
                'string',
                'max:2',
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

        if (!empty($items))
        {
            foreach ($items as &$item)
            {
                $item['synced_at'] = $syncedAt;
            }
            unset($item);

            $received = count($items);

            DB::transaction(function () use ($items, &$saved, &$updated)
            {
                $accountNumbers = array_column($items, 'norekcrd');

                // Cukup satu query untuk memeriksa rekening yang sudah ada.
                $existingAccounts = TagihanKreditSync::query()
                    ->whereIn('norekcrd', $accountNumbers)
                    ->pluck('norekcrd')
                    ->map(fn($value) => (string) $value)
                    ->all();

                $existingSet = array_fill_keys($existingAccounts, true);

                foreach ($accountNumbers as $accountNumber)
                {
                    if (isset($existingSet[(string) $accountNumber]))
                    {
                        $updated++;
                    }
                    else
                    {
                        $saved++;
                        $existingSet[(string) $accountNumber] = true;
                    }
                }

                // Satu operasi database untuk seluruh batch.
                $columns = array_keys($items[0]);

                $updateColumns = array_values(array_diff(
                    $columns,
                    ['norekcrd']
                ));

                TagihanKreditSync::upsert(
                    $items,
                    ['norekcrd'],
                    $updateColumns
                );
            });
        }
        else
        {
            $received = 0;
        }

        return response()->json([
            'message' => 'Data tagihan kredit berhasil diterima.',
            'received' => $received,
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
