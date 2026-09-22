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
        Schema::table('cfgsys', function (Blueprint $table) {
            $table->string('report_file_complaint')->after('kodeljk')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cfgsys', function (Blueprint $table) {
            $table->dropColumn('report_file_complaint');
        });
    }
};
