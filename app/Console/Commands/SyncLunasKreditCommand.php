<?php

namespace App\Console\Commands;

use App\Models\Cfgsys;
use App\Services\LunasKreditSyncService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SyncLunasKreditCommand extends Command
{
    protected $signature = 'sync:lunas-kredit
        {--bln= : Bulan data yang dikirim, default bulan berjalan}
        {--thn= : Tahun data yang dikirim, default tahun berjalan}
        {--kodeljk= : Kode LJK, default dari cfgsys.kodeljk}
        {--sandicabang= : Sandi cabang, default kosong}';

    protected $description = 'Mengirim data kredit lunas kode kondisi 02 dari SQL Server lokal ke API VPS.';

    public function handle(LunasKreditSyncService $service): int
    {
        $bulan = (int) ($this->option('bln') ?: now()->month);
        $tahun = (int) ($this->option('thn') ?: now()->year);
        $cfgsys = Cfgsys::current();
        $kodeljk = (string) ($this->option('kodeljk') ?: $cfgsys?->kodeljk ?: '');
        $sandicabang = (string) ($this->option('sandicabang') ?: '');
        $targetUrl = rtrim((string) ($cfgsys?->api_url ?: env('SYNC_API_URL', '')), '/');
        $apiKey = (string) ($cfgsys?->api_key ?: env('SYNC_API_KEY', ''));

        if ($kodeljk === '' || $targetUrl === '' || $apiKey === '')
        {
            $this->error('Konfigurasi kodeljk, URL tujuan, atau API key belum lengkap.');

            return self::FAILURE;
        }

        $items = $service->getLunasKreditFromSqlServer($bulan, $tahun, $kodeljk, $sandicabang);

        try
        {
            $response = Http::withHeaders([
                'X-ATHENA-KEY' => $apiKey,
                'Accept' => 'application/json',
            ])->timeout(60)->post($targetUrl . '/api/sync/lunas-kredit/receive', [
                        'source' => compact('bulan', 'tahun', 'kodeljk', 'sandicabang'),
                        'items' => $items,
                    ]);
        }
        catch (ConnectionException $exception)
        {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (!$response->successful())
        {
            $this->error('Server tujuan menolak data. Status: ' . $response->status());
            $this->line($response->body());

            return self::FAILURE;
        }

        $this->info('Berhasil mengirim ' . count($items) . ' data lunas kredit.');

        return self::SUCCESS;
    }
}
