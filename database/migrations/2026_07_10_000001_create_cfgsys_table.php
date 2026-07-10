<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cfgsys', function (Blueprint $table) {
            $table->id();
            $table->string('api_key', 255);
            $table->string('api_url')->nullable();
            $table->string('kodeljk', 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cfgsys');
    }
};
