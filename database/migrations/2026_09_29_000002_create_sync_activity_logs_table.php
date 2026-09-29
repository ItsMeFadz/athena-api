<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 150);
            $table->string('method', 10);
            $table->unsignedSmallInteger('status_code');
            $table->string('status', 20);
            $table->unsignedInteger('received_count')->default(0);
            $table->unsignedInteger('saved_count')->nullable();
            $table->unsignedInteger('updated_count')->nullable();
            $table->longText('error_details')->nullable();
            $table->unsignedInteger('duration_ms');
            $table->timestamps();

            $table->index(['status', 'created_at'], 'sync_activity_status_created_idx');
            $table->index(['endpoint', 'created_at'], 'sync_activity_endpoint_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_activity_logs');
    }
};
