<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overlays', function (Blueprint $table) {

            $table->string('jenis')
                ->nullable()
                ->after('nama');

            $table->json('geojson_data')
                ->nullable()
                ->after('file');

            $table->integer('jumlah_fitur')
                ->default(0)
                ->after('geojson_data');

            $table->decimal('total_luas', 15, 2)
                ->nullable()
                ->after('jumlah_fitur');

            $table->string('uuid')
                ->unique()
                ->nullable()
                ->after('total_luas');

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active')
              ->after('uuid');
        });
    }

    public function down(): void
    {
        Schema::table('overlays', function (Blueprint $table) {

            $table->dropColumn([

                'jenis',
                'geojson_data',
                'jumlah_fitur',
                'total_luas',
                'uuid',
                'status'
            ]);
        });
    }
};