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
        Schema::table('sync_tagihan_kredit', function (Blueprint $table)
        {
            $table->decimal('tungpokok', 20, 2)->nullable()->after('tagbunga');
            $table->decimal('tungbunga', 20, 2)->nullable()->after('tungpokok');
            $table->string('kolektibilitas', 10)->after('tungbunga');
            $table->string('kodekondisi', 2)->after('kolektibilitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_tagihan_kredit', function (Blueprint $table)
        {
            $table->dropColumn('tungpokok');
            $table->dropColumn('tungbunga');
            $table->dropColumn('kolektibilitas');
        });
    }
};
