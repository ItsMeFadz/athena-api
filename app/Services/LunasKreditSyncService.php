<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LunasKreditSyncService
{
    /**
     * Ambil kredit lunas dari database SQL Server lokal.
     *
     * Kode kondisi 02 dipaksa di query agar server lokal hanya mengirim data lunas.
     */
    public function getLunasKreditFromSqlServer(int $bulan, int $tahun, string $kodeljk, string $sandicabang = ''): array
    {
        $rows = DB::connection('sqlsrv')->select(<<<'SQL'
            SELECT
                e.nama_bank,
                b.alamat,
                b.notelp,
                b.nohp,
                a.kodeljk,
                a.sandicabang,
                a.cif,
                a.norekcrd,
                CASE d.prk WHEN 1 THEN a.plafoninduk ELSE a.plafon END AS plafon,
                RTRIM(b.namalengkap) AS namalengkap,
                RTRIM(a.noakad) AS noakad,
                ISNULL((
                    SELECT SUM(ISNULL(h.nominal, 0))
                    FROM history_crd h
                    WHERE h.noacc = a.norekcrd
                        AND CONVERT(date, h.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND h.cd_trx1 = 420
                ), 0) + ISNULL((
                    SELECT SUM(ISNULL(t.nominal, 0))
                    FROM transaksi t
                    WHERE (t.dracc = a.norekcrd OR t.cracc = a.norekcrd)
                        AND CONVERT(date, t.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND t.cd_trx1 = 420
                        AND t.ststrx = 1
                ), 0) AS os,
                ISNULL((
                    SELECT SUM(ISNULL(h.nominal, 0))
                    FROM history_crd h
                    WHERE h.noacc = a.norekcrd
                        AND CONVERT(date, h.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND h.cd_trx1 = 422
                ), 0) + ISNULL((
                    SELECT SUM(ISNULL(t.nominal, 0))
                    FROM transaksi t
                    WHERE (t.dracc = a.norekcrd OR t.cracc = a.norekcrd)
                        AND CONVERT(date, t.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND t.cd_trx1 = 422
                        AND t.ststrx = 1
                ), 0) AS denda,
                ISNULL((
                    SELECT SUM(ISNULL(h.nominal, 0))
                    FROM history_crd h
                    WHERE h.noacc = a.norekcrd
                        AND CONVERT(date, h.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND h.cd_trx1 = 424
                ), 0) + ISNULL((
                    SELECT SUM(ISNULL(t.nominal, 0))
                    FROM transaksi t
                    WHERE (t.dracc = a.norekcrd OR t.cracc = a.norekcrd)
                        AND CONVERT(date, t.tgltrx) = CONVERT(date, a.tglkondisi)
                        AND t.cd_trx1 = 424
                        AND t.ststrx = 1
                ), 0) AS penalty,
                a.kodekondisi,
                RTRIM(c.kondisi) AS kondisi,
                CONVERT(varchar(10), a.tglkondisi, 23) AS tglkondisi,
                CONVERT(varchar(10), a.tglefektif, 23) AS tgleff,
                d.nama AS namaproduk,
                f.kode + ' - ' + f.ket AS ao,
                a.kolektibilitas
            FROM crdmaster a
            LEFT JOIN cif b ON a.cif = b.cif
            LEFT JOIN refojk_kondisi c ON a.kodekondisi = c.kodekondisi
            LEFT JOIN crd_setup d ON a.kodeprodukcrd = d.kodeprodukcrd
            LEFT JOIN company_setup e ON a.kodeljk = e.kodeljk AND a.sandicabang = e.sandicabang
            LEFT JOIN refintern_ao f ON a.kodeljk = f.kodeljk AND a.sandicabang = f.sandicabang AND a.kodeao = f.kode
            WHERE a.kodeljk = ?
                AND a.sandicabang LIKE ?
                AND a.kodekondisi = '02'
                AND a.bakidebet = 0
                AND DATEPART(month, a.tglkondisi) = ?
                AND DATEPART(year, a.tglkondisi) = ?
            ORDER BY a.tglkondisi
        SQL, [
            $kodeljk,
            '%'.$sandicabang.'%',
            $bulan,
            $tahun,
        ]);

        return array_map(static fn (object $row): array => (array) $row, $rows);
    }
}
