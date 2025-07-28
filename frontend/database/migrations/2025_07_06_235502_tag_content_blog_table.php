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
        Schema::create('tag_content_blog', function (Blueprint $table) use($driver) {
            if ($driver === 'pgsql') {
                // $table->foreignUlid('content_blog_id')->primary()->constrained(table: 'content_blog')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignUuid('content_blog_id')->primary()->constrained(table: 'content_blog')->onUpdate('cascade')->onDelete('cascade');
            } else {
                $table->foreignUuid('content_blog_id')->primary()->constrained(table: 'content_blog')->onUpdate('cascade')->onDelete('cascade');
            }
            $table->foreignId('tag_blog_id')->constrained(table: 'tag_blog')->onUpdate('cascade')->onDelete('restrict');

            $table->index('content_blog_id');
            $table->index('tag_blog_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_content_blog');
    }
};
