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

        Schema::create('app_config_blog', function (Blueprint $table) use ($driver) {
            $table->id('id');
            $table->string('name', 100);
            $table->string('video', 255)->nullable();
            $table->string('short_sentences', 150)->nullable();
            $table->string('logo_header', 150)->nullable();
            $table->string('logo_footer', 150)->nullable();
            $table->string('address', 255);
            $table->string('location_iframe', 255)->nullable();
            $table->boolean('show_profile_content_major')->default(true);
            $table->boolean('show_jumbotron')->default(true);
            $table->boolean('show_relation')->default(true);
            $table->enum('status', ['active', 'maintenance', 'non-active']);
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
                BEFORE UPDATE ON app_config_blog
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
        Schema::dropIfExists('app_config_blog');
    }
};
