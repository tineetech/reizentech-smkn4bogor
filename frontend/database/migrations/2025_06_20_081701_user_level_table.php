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

        Schema::create('user_level', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                // $table->ulid('id')->primary()->default(DB::raw('gen_ulid()')); // PASTIKAN SUDAH INSTALL LIB https://github.com/pksunkara/pgx_ulid
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            } else {
                $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            }
            $table->foreignId('biodata_ref_id')->constrained(table: 'biodata_ref')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('app_id')->constrained(table: 'app')->onUpdate('cascade')->onDelete('restrict');
            $table->string('user_level_name', 255);
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->unique(['biodata_ref_id', 'app_id', 'user_level_name'], 'unique_user_level_name');
            $table->index('biodata_ref_id');
            $table->index('app_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON user_level
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
        Schema::dropIfExists('user_level');
    }
};
