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

        Schema::create('app_config_jumbotron', function (Blueprint $table) use ($driver) {
            $table->id('id');
            $table->foreignId('app_id')->constrained(table: 'app')->onUpdate('cascade')->onDelete('restrict');
            $table->string('banner', 255);
            $table->string('url', 255)->nullable();
            $table->enum('tipe', ['video', 'image']);
            $table->enum('show', ['forever', 'datetime']);
            $table->dateTime('show_start_datetime')->nullable();
            $table->dateTime('show_end_datetime')->nullable();
            $table->tinyInteger('order');
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->index('app_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON app_config_jumbotron
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
        Schema::dropIfExists('app_config_jumbotron');
    }
};
