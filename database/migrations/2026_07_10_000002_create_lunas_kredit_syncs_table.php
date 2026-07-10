<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lunas_kredit_syncs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->string('notelp', 50)->nullable();
            $table->string('nohp', 50)->nullable();
            $table->string('kodeljk', 6);
            $table->string('sandicabang', 3);
            $table->string('cif', 50);
            $table->string('norekcrd', 50);
            $table->decimal('plafon', 18, 2)->default(0);
            $table->string('namalengkap', 150)->nullable();
            $table->string('noakad', 100)->nullable();
            $table->decimal('os', 18, 2)->default(0);
            $table->decimal('denda', 18, 2)->default(0);
            $table->decimal('penalty', 18, 2)->default(0);
            $table->string('kodekondisi', 2);
            $table->string('kondisi', 100)->nullable();
            $table->date('tglkondisi');
            $table->date('tgleff')->nullable();
            $table->string('namaproduk', 150)->nullable();
            $table->string('ao', 150)->nullable();
            $table->string('kolektibilitas', 10)->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['kodeljk', 'sandicabang', 'norekcrd', 'noakad', 'tglkondisi'], 'lunas_kredit_sync_unique');
            $table->index(['kodekondisi', 'tglkondisi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lunas_kredit_syncs');
    }
};
