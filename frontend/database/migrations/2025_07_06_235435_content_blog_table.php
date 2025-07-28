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

        Schema::create('content_blog', function (Blueprint $table) use ($driver) {
            if ($driver === 'pgsql') {
                // $table->ulid('id')->primary()->default(DB::raw('gen_ulid()'));
                // $table->foreignUlid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
                // $table->foreignUlid('last_update_by_user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
                $table->uuid('id')->primary()->default(DB::raw('gen_random_uuid()'));
                $table->foreignUuid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignUuid('last_update_by_user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
            } else {
                $table->uuid('id')->primary()->default(DB::raw('UUID()'));
                $table->foreignUuid('user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
                $table->foreignUuid('last_update_by_user_role_id')->constrained(table: 'user_role')->onUpdate('cascade')->onDelete('restrict');
            }
            $table->foreignId('menu_role_id')->nullable()->constrained(table: 'menu_role')->onUpdate('cascade')->onDelete('restrict');
            $table->string('slug', 255)->unique();
            $table->string('title', 255);
            $table->string('description', 255)->nullable();
            $table->text('content');
            $table->enum('tipe', ['redirect_content', 'content']);
            $table->boolean('status');
            $table->timestamp('created_at')->useCurrent();
            if ($driver === 'pgsql') {
                $table->timestamp('updated_at')->useCurrent();
            } else { // mysql or mariadb
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            }

            $table->index('user_role_id');
            $table->index('last_update_by_user_role_id');
            $table->index('menu_role_id');
        });

        if ($driver === 'pgsql') {
            // Buat trigger sebelum update
            DB::statement("
                CREATE TRIGGER trigger_set_updated_at
                BEFORE UPDATE ON content_blog
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
        Schema::dropIfExists('content_blog');
    }
};
