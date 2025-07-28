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

        Schema::create('profile_content_blog', function (Blueprint $table) use ($driver) {
            $table->id('id');
            $table->foreignId('menu_role_id')->constrained(table: 'menu_role')->onUpdate('cascade')->onDelete('restrict');
            $table->string('slug', 255)->unique();
            $table->string('name', 255);
            $table->string('logo', 255)->nullable();
            $table->text('content');
            $table->enum('tipe', ['major', 'menu_code']);
            $table->tinyInteger('order');
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->index('menu_role_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON profile_content_blog
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
        Schema::dropIfExists('profile_content_blog');
    }
};
