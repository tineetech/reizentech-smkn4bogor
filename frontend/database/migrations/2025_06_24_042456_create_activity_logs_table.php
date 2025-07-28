<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        Schema::create('activity_logs', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                // $table->ulid('id')->primary()->default(DB::raw('gen_ulid()'));
                // $table->foreignUlid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
            } else {
                $table->uuid('id')->primary()->default(DB::raw('UUID()'));
                $table->foreignUuid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
            }
            $table->string('activity');
            $table->text('description')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('activity_time')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
