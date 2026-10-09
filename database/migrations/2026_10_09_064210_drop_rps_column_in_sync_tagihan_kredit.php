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
        Schema::table('sync_tagihan_kredit', function (Blueprint $table) {
            $table->dropColumn('tgltempo');
            $table->dropColumn('tagpokok');
            $table->dropColumn('tagbunga');
            $table->dropColumn('tagdenda');
            $table->dropColumn('totalangsuran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_tagihan_kredit', function (Blueprint $table) {
            $table->integer('tgltempo')->nullable();
            $table->decimal('tagpokok', 15, 2)->nullable();
            $table->decimal('tagbunga', 15, 2)->nullable();
            $table->decimal('tagdenda', 15, 2)->nullable();
            $table->decimal('totalangsuran', 15, 2)->nullable();
        });
    }
};
