<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_rps', function (Blueprint $table) {
            $table->id();
            $table->char('kodeljk', 6);
            $table->char('sandicabang', 3);
            $table->string('cif', 7);
            $table->string('norekcrd', 14);
            $table->integer('periode');
            $table->date('tglangsuran');
            $table->decimal('saldoawal', 20, 2)->nullable();
            $table->decimal('saldoakhir', 20, 2)->nullable();
            $table->decimal('tagpokok', 20, 2)->nullable();
            $table->decimal('tagbunga', 20, 2)->nullable();
            $table->decimal('totalangsuran', 20, 2)->nullable();
            $table->decimal('tagdenda', 20, 2)->nullable();
            $table->decimal('byrpokok', 20, 2)->nullable();
            $table->decimal('byrbunga', 20, 2)->nullable();
            $table->decimal('byrdenda', 20, 2)->nullable();
            $table->dateTime('tglbyr')->nullable();
            $table->decimal('sukubunga', 20, 2)->nullable();
            $table->string('noakad', 50)->nullable();
            $table->integer('jmlharidenda')->nullable();
            $table->date('tglbyrdenda')->nullable();
            $table->date('tglbyrbunga')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['norekcrd', 'tglangsuran'], 'sync_rps_account_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_rps');
    }
};
