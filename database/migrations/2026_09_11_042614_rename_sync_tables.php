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
        Schema::rename('tagihan_kredit_syncs', 'sync_tagihan_kredit');

        Schema::table('sync_tagihan_kredit', function (Blueprint $table) {
            $table->date('tgljthtempo')->after('tglefektif')->nullable();
        });

        Schema::rename('lunas_kredit_syncs', 'sync_lunas_kredit');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('sync_lunas_kredit', 'lunas_kredit_syncs');

        Schema::table('sync_tagihan_kredit', function (Blueprint $table) {
            $table->dropColumn('tgljthtempo');
        });

        Schema::rename('sync_tagihan_kredit', 'tagihan_kredit_syncs');
    }
};
