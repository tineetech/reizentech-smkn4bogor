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

        Schema::create('menu_role', function (Blueprint $table) use ($driver) {
            $table->id('id');
            $table->foreignId('menu_id')->constrained(table: 'menu')->onUpdate('cascade')->onDelete('restrict');
            // $table->foreignUlid('user_level_id')->constrained(table: 'user_level')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignUuid('user_level_id')->constrained(table: 'user_level')->onUpdate('cascade')->onDelete('restrict');
            $table->string('parent_menu_code', 10)->nullable();
            $table->string('menu_icon', 150)->nullable();
            $table->string('menu_url', 255)->nullable();
            $table->boolean('role_view')->default(true);
            $table->boolean('role_create')->default(false);
            $table->boolean('role_update')->default(false);
            $table->boolean('role_delete')->default(false);
            $table->tinyInteger('role_order');
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->unique(['menu_id', 'user_level_id', 'parent_menu_code'], 'unique_menu_role');
            $table->index('menu_id');
            $table->index('user_level_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON menu_role
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
        Schema::dropIfExists('menu_role');
    }
};
