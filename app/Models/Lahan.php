<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lahan extends Model
{
    protected $table = 'lahans';

    protected $fillable = [
        'nama_desa',
        'pemilik',
        'luas',
        'jenis',
        'kecamatan',
        'poktan',
        'kode_persil',
        'kondisi',
        'pola_ruang',
        'sumber_air',
        'sumber',
        'geojson',
    ];
}