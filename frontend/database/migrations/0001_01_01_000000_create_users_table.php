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

        if ($driver === 'pgsql') {
            // PASTIKAN SUDAH INSTALL LIB https://github.com/pksunkara/pgx_ulid
            // Aktifkan ekstensi ULID (jika belum)
            // DB::statement('CREATE EXTENSION IF NOT EXISTS ulid;');
            // DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

            DB::statement("
                CREATE OR REPLACE FUNCTION set_updated_at()
                RETURNS TRIGGER AS $$
                BEGIN
                    NEW.updated_at = NOW();
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            ");
        }

        Schema::create('users', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                // $table->ulid('id')->primary()->default(DB::raw('gen_ulid()'));
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
            } else {
                $table->uuid('id')->primary()->default(DB::raw('UUID()'));
            }
            $table->string('username')->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 15)->nullable()->unique();
            $table->timestamp('phone_number_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->enum('status', ['active', 'non-activate', 'banned']);
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }
        });

         if ($driver === 'pgsql') {
             // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON users
                FOR EACH ROW
                EXECUTE FUNCTION set_updated_at();
            ");
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) use ($driver) {
            $table->string('id')->primary();
            if ($driver === 'pgsql') {
                // $table->foreignUlid('user_id')->index();
                $table->foreignUuid('user_id')->index();
            } else {
                $table->foreignUuid('user_id')->index();
            }
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
