<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cfgsys;
use App\Models\LunasKreditSync;
use App\Services\LunasKreditSyncService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class LunasKreditSyncController extends Controller
{
    public function __construct(private readonly LunasKreditSyncService $service)
    {
    }

    public function receive(Request $request): JsonResponse
    {
        if (!$this->hasValidSyncKey($request))
        {
            return response()->json([
                'message' => 'API key tidak valid.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'items' => ['required', 'array'],
            'items.*.nama_bank' => ['nullable', 'string', 'max:150'],
            'items.*.alamat' => ['nullable', 'string'],
            'items.*.notelp' => ['nullable', 'string', 'max:50'],
            'items.*.nohp' => ['nullable', 'string', 'max:50'],
            'items.*.kodeljk' => ['required', 'string', 'size:6'],
            'items.*.sandicabang' => ['required', 'string', 'max:3'],
            'items.*.cif' => ['required', 'string', 'max:50'],
            'items.*.norekcrd' => ['required', 'string', 'max:50'],
            'items.*.plafon' => ['nullable', 'numeric'],
            'items.*.namalengkap' => ['nullable', 'string', 'max:150'],
            'items.*.noakad' => ['nullable', 'string', 'max:100'],
            'items.*.os' => ['nullable', 'numeric'],
            'items.*.denda' => ['nullable', 'numeric'],
            'items.*.penalty' => ['nullable', 'numeric'],
            'items.*.kodekondisi' => ['required', 'string', 'in:02'],
            'items.*.kondisi' => ['nullable', 'string', 'max:100'],
            'items.*.tglkondisi' => ['required', 'date'],
            'items.*.tgleff' => ['nullable', 'date'],
            'items.*.namaproduk' => ['nullable', 'string', 'max:150'],
            'items.*.ao' => ['nullable', 'string', 'max:150'],
            'items.*.kolektibilitas' => ['nullable', 'string', 'max:10'],
        ]);

        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Payload tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $syncedAt = now();
        $saved = 0;

        foreach ($validator->validated()['items'] as $item)
        {
            $item['tglkondisi'] = Carbon::parse($item['tglkondisi'])->toDateString();
            $item['tgleff'] = isset($item['tgleff']) ? Carbon::parse($item['tgleff'])->toDateString() : null;
            $item['synced_at'] = $syncedAt;

            LunasKreditSync::query()->updateOrCreate([
                'kodeljk' => $item['kodeljk'],
                'sandicabang' => $item['sandicabang'],
                'norekcrd' => $item['norekcrd'],
                'noakad' => $item['noakad'] ?? null,
                'tglkondisi' => $item['tglkondisi'],
            ], $item);

            $saved++;
        }

        return response()->json([
            'message' => 'Data lunas kredit diterima.',
            'received' => $saved,
        ]);
    }

    private function hasValidSyncKey(Request $request): bool
    {
        $configuredKey = (string) (Cfgsys::current()?->api_key ?: env('SYNC_API_KEY', ''));
        $requestKey = (string) ($request->header('X-Sync-Key') ?: $request->bearerToken());

        return $configuredKey !== '' && $requestKey !== '' && hash_equals($configuredKey, $requestKey);
    }
}
