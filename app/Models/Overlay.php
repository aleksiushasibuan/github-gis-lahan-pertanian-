<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overlay extends Model
{
    use HasFactory;

    protected $table = 'overlays';

    protected $fillable = [

        'nama',
        'file',
        'jenis',
        'geojson_data',
        'jumlah_fitur',
        'total_luas',
        'uuid',
        'status'
    ];

    protected $casts = [

        'geojson_data' => 'array',
        'jumlah_fitur' => 'integer',
        'total_luas' => 'float',
    ];
}