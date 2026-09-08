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
        Schema::create('sync_transaksi_cio', function (Blueprint $table) {
            $table->id();
            $table->char('kodeljk', 6)->nullable();
            $table->char('sandicabang', 3)->nullable();
            $table->date('tgltrx')->nullable();
            $table->integer('userid')->nullable();
            $table->string('username', 20)->nullable();
            $table->integer('trxid')->unique();
            $table->string('nopkg', 20)->nullable();
            $table->string('kodetrn', 4)->nullable();
            $table->string('noacc', 14)->nullable();
            $table->string('dokumen', 20)->nullable();
            $table->decimal('nominal', 18, 2)->nullable();
            $table->string('keterangan', 100)->nullable();
            $table->tinyInteger('ststrx')->nullable();
            $table->tinyInteger('stsbar')->nullable();
            $table->string('cd_trx1', 3)->nullable();
            $table->string('cd_trx2', 3)->nullable();
            $table->string('caller', 20)->nullable();
            $table->char('flag', 3)->nullable();
            $table->string('param1', 20)->nullable();
            $table->string('param2', 20)->nullable();
            $table->string('delete_reason', 100)->nullable();
            $table->dateTime('oto_date')->nullable();
            $table->string('oto_user', 20)->nullable();
            $table->dateTime('create_date')->nullable();
            $table->string('create_user', 20)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 20)->nullable();
            $table->dateTime('delete_date')->nullable();
            $table->dateTime('delete_user')->nullable();
            $table->timestamps();
            $table->index('tgltrx');
            $table->index('noacc');
            $table->index('kodeljk');
            $table->index('sandicabang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_transaksi_cio');
    }
};
