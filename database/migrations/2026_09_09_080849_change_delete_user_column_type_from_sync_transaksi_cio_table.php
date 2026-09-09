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
        Schema::table('sync_transaksi_cio', function (Blueprint $table) {
            $table->string('delete_user')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_transaksi_cio', function (Blueprint $table) {
            $table->dateTime('delete_user')->nullable()->change();
        });
    }
};
