<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->default('Lainnya');
            $table->text('isi');
            $table->text('gambar')->nullable();

            // lebih fleksibel dari enum
            $table->string('status')->default('publish');

            $table->integer('views')->default(0);

            // tambahan penting
            $table->timestamp('published_at')->nullable();

            // relasi user (opsional)
            $table->foreignId('user_id')->nullable()
                  ->constrained()
                  ->onDelete('set null');

            // index untuk performa
            $table->index('slug');
            $table->index('kategori');
            $table->index('status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};