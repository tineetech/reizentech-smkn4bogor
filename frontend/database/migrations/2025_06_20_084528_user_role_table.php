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

        Schema::create('user_role', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                // $table->ulid('id')->primary()->default(DB::raw('gen_ulid()'));
                // $table->foreignUlid('user_id')->constrained(table: 'users')->onUpdate('cascade')->onDelete('restrict');
                // $table->foreignUlid('user_level_id')->constrained(table: 'user_level')->onUpdate('cascade')->onDelete('restrict');
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('user_id')->constrained(table: 'users')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignUuid('user_level_id')->constrained(table: 'user_level')->onUpdate('cascade')->onDelete('restrict');
            } else {
                $table->uuid('id')->primary()->default(DB::raw('UUID()'));
                $table->foreignUuid('user_id')->constrained(table: 'users')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignUuid('user_level_id')->constrained(table: 'user_level')->onUpdate('cascade')->onDelete('restrict');
            }
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->unique(['user_id', 'user_level_id'], 'unique_user_role');
            $table->index('user_id');
            $table->index('user_level_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON user_role
                FOR EACH ROW
                EXECUTE FUNCTION set_updated_at();
            ");
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_role');
    }
};
