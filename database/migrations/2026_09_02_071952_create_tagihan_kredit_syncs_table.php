<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihan_kredit_syncs', function (Blueprint $table) {
            $table->id();

            // Identitas sumber
            $table->string('kodeljk', 6);
            $table->string('sandicabang', 3);
            $table->string('norekcrd', 50);

            // Debitur
            $table->string('namalengkap', 150)->nullable();
            $table->text('alamatktp')->nullable();
            $table->text('alamatdomisili')->nullable();
            $table->string('notelp', 50)->nullable();
            $table->string('nohp', 50)->nullable();

            // Kredit
            $table->string('noakad', 100)->nullable();
            $table->decimal('bakidebet', 20, 2)->nullable();

            // Jadwal
            $table->unsignedTinyInteger('tgltempo')->nullable();
            $table->date('tglangsuran')->nullable();
            $table->date('tglefektif')->nullable();
            $table->integer('graceperiod')->nullable();

            // Status kredit
            $table->string('statusrek', 100)->nullable();

            // Tagihan
            $table->decimal('tagpokok', 20, 2)->nullable();
            $table->decimal('tagbunga', 20, 2)->nullable();
            $table->decimal('tagdenda', 20, 2)->nullable();
            $table->decimal('totalangsuran', 20, 2)->nullable();
            $table->integer('haritunggakkan')->nullable();

            // Rekening tabungan
            $table->string('norekpembayaran', 50)->nullable();
            $table->decimal('saldotab', 20, 2)->nullable();
            $table->decimal('saldotabactual', 20, 2)->nullable();

            // AO
            $table->string('kodeao', 50)->nullable();
            $table->string('ao', 150)->nullable();

            // Instansi
            $table->string('ketinstansi', 150)->nullable();

            // Waktu sinkronisasi
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            // Satu rekening bisa memiliki banyak jadwal angsuran
            $table->unique(
                ['kodeljk', 'sandicabang', 'norekcrd', 'tglangsuran'],
                'tagihan_kredit_sync_unique'
            );

            $table->index(['kodeljk', 'sandicabang']);
            $table->index('norekcrd');
            $table->index('tglangsuran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_kredit_syncs');
    }
};
