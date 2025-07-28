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

        Schema::create('menu', function (Blueprint $table) use ($driver) {
            $table->id('id');
            $table->string('menu_code', 10)->unique();
            $table->string('menu_name', 150);
            $table->enum('menu_type', ['main', 'sub']);
            $table->boolean('status');
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
                BEFORE UPDATE ON menu
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
        Schema::dropIfExists('menu');
    }
};
