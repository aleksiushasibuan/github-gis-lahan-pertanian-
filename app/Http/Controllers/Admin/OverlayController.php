<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Overlay;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OverlayController extends Controller
{
    // Halaman kelola overlay
    public function index()
    {
        $overlays = Overlay::orderBy('created_at', 'desc')->get();
        return view('admin.overlay', compact('overlays'));
    }

    // List overlay
    public function list()
    {
        $overlays = Overlay::orderBy('created_at', 'desc')->get();

        $overlays = $overlays->map(function ($overlay) {
            $overlay->file_url = $overlay->file ? asset('storage/' . $overlay->file) : null;
            return $overlay;
        });

        return response()->json([
            'success' => true,
            'overlays' => $overlays
        ]);
    }

    // Statistik overlay
    public function getData()
    {
        $overlays = Overlay::orderBy('created_at', 'desc')->get();

        $totalGeometri = 0;
        $totalFileSize = 0;

        foreach ($overlays as $overlay) {
            $totalGeometri += $overlay->jumlah_fitur ?? 0;

            if ($overlay->file && Storage::disk('public')->exists($overlay->file)) {
                $totalFileSize += Storage::disk('public')->size($overlay->file);
            }
        }

        $statistics = [
            'total_overlays' => $overlays->count(),
            'total_geometri' => $totalGeometri,
            'total_file_size' => $this->formatBytes($totalFileSize),
            'total_jenis' => $overlays->groupBy('jenis')->count()
        ];

        return response()->json([
            'success' => true,
            'overlays' => $overlays,
            'statistics' => $statistics
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE OVERLAY (MANUAL)
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'geojson' => 'required|file|mimes:json,geojson|max:20480'
        ]);

        try {
            $file = $request->file('geojson');
            $content = file_get_contents($file->getRealPath());
            
            if (empty($content)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong'
                ], 422);
            }
            
            $geojson = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan JSON valid: ' . json_last_error_msg()
                ], 422);
            }
            
            if (!isset($geojson['features']) || !is_array($geojson['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'GeoJSON harus memiliki properti "features"'
                ], 400);
            }
            
            $jumlahFitur = count($geojson['features']);
            $uuid = (string) Str::uuid();
            $filename = 'overlay_' . time() . '_' . $uuid . '.geojson';
            $path = $file->storeAs('overlays', $filename, 'public');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan file'
                ], 500);
            }
            
            $overlay = Overlay::create([
                'nama' => $request->nama,
                'jenis' => $request->jenis,
                'file' => $path,
                'geojson_data' => $content, // SIMPAN JUGA KE DATABASE
                'uuid' => $uuid,
                'jumlah_fitur' => $jumlahFitur,
                'status' => 'active'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil ditambahkan',
                'data' => $overlay
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO UPLOAD
    |--------------------------------------------------------------------------
    */

    public function autoUpload(Request $request)
    {
        $request->validate([
            'geojson' => 'required|file|mimes:json,geojson|max:20480'
        ]);

        try {
            $file = $request->file('geojson');
            $content = file_get_contents($file->getRealPath());
            
            if (empty($content)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong'
                ], 422);
            }
            
            $geojson = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan JSON valid: ' . json_last_error_msg()
                ], 422);
            }
            
            if (!isset($geojson['features']) || !is_array($geojson['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan GeoJSON valid (tidak memiliki features)'
                ], 400);
            }
            
            $jumlahFitur = count($geojson['features']);
            $uuid = (string) Str::uuid();
            $filename = 'overlay_' . time() . '_' . $uuid . '.geojson';
            $path = $file->storeAs('overlays', $filename, 'public');
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan file'
                ], 500);
            }
            
            $namaLayer = $this->detectLayerName($geojson, $file);
            $jenisLayer = $this->detectLayerType($geojson);
            
            $overlay = Overlay::create([
                'nama' => $namaLayer,
                'jenis' => $jenisLayer,
                'file' => $path,
                'geojson_data' => $content, // SIMPAN JUGA KE DATABASE
                'uuid' => $uuid,
                'jumlah_fitur' => $jumlahFitur,
                'status' => 'active'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil diupload',
                'data' => $overlay
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload overlay: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT NAMA LAYER
    |--------------------------------------------------------------------------
    */

    private function detectLayerName($geojson, $file)
    {
        if (isset($geojson['features'][0]['properties'])) {
            $props = $geojson['features'][0]['properties'];
            
            $nameFields = ['NAMOBJ', 'nama', 'name', 'NAM', 'NAMOBJECT', 'label', 'title'];
            
            foreach ($nameFields as $field) {
                if (isset($props[$field]) && !empty($props[$field])) {
                    return $props[$field];
                }
            }
        }
        
        $originalName = $file->getClientOriginalName();
        return pathinfo($originalName, PATHINFO_FILENAME);
    }

    /*
    |--------------------------------------------------------------------------
    | DETECT JENIS LAYER
    |--------------------------------------------------------------------------
    */

    private function detectLayerType($geojson)
    {
        if (!isset($geojson['features'][0]['properties'])) {
            return 'Layer Lainnya';
        }
        
        $props = $geojson['features'][0]['properties'];
        
        $namobj = strtolower($props['NAMOBJ'] ?? $props['namobj'] ?? $props['NAMOBJECT'] ?? $props['nama'] ?? '');
        $jenisRencana = strtolower($props['JNSRPR'] ?? $props['jnsrpr'] ?? $props['JENIS'] ?? $props['jenis'] ?? '');
        
        if (str_contains($namobj, 'kecamatan')) {
            return 'Batas Kecamatan';
        }
        
        if (str_contains($namobj, 'desa') || str_contains($namobj, 'kelurahan')) {
            return 'Batas Desa';
        }
        
        if (str_contains($namobj, 'hutan lindung') || $jenisRencana === '31000000') {
            return 'Hutan Lindung';
        }
        
        if (str_contains($namobj, 'hutan produksi') || $jenisRencana === '32000000') {
            return 'Hutan Produksi';
        }
        
        if (str_contains($namobj, 'badan air') || str_contains($namobj, 'sungai') || str_contains($namobj, 'danau')) {
            return 'Badan Air';
        }
        
        return 'Layer Lainnya';
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        try {
            $overlay = Overlay::findOrFail($id);
            return response()->json([
                'success' => true,
                'overlay' => $overlay
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255',
                'jenis' => 'required|string|max:255'
            ]);

            $overlay = Overlay::findOrFail($id);

            $overlay->update([
                'nama' => $request->nama,
                'jenis' => $request->jenis
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        try {
            $overlay = Overlay::findOrFail($id);

            if ($overlay->file && Storage::disk('public')->exists($overlay->file)) {
                Storage::disk('public')->delete($overlay->file);
            }

            $overlay->delete();

            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus overlay: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | TOGGLE STATUS
    |--------------------------------------------------------------------------
    */

    public function toggleStatus($id, Request $request)
    {
        try {
            $overlay = Overlay::findOrFail($id);
            $overlay->status = $request->status;
            $overlay->save();

            return response()->json([
                'success' => true,
                'message' => 'Status overlay berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET ALL GEOJSON (UNTUK FRONTEND MAP)
    | ENDPOINT: /api/overlay/geojson
    |--------------------------------------------------------------------------
    */

    public function getAllGeoJSON()
    {
        $overlays = Overlay::where('status', 'active')->get();
        $allFeatures = [];

        foreach ($overlays as $overlay) {
            // PRIORITAS: ambil dari geojson_data dulu
            if ($overlay->geojson_data) {
                $geojson = is_string($overlay->geojson_data) ? json_decode($overlay->geojson_data, true) : $overlay->geojson_data;
                
                if ($geojson && isset($geojson['features'])) {
                    foreach ($geojson['features'] as $feature) {
                        if (!isset($feature['properties'])) {
                            $feature['properties'] = [];
                        }
                        
                        $feature['properties']['overlay_id'] = $overlay->id;
                        $feature['properties']['overlay_name'] = $overlay->nama;
                        $feature['properties']['overlay_type'] = $overlay->jenis;
                        $feature['properties']['overlay_uuid'] = $overlay->uuid;
                        
                        $allFeatures[] = $feature;
                    }
                    continue;
                }
            }
            
            // Jika tidak ada geojson_data, coba dari file
            if ($overlay->file) {
                $content = null;
                
                if (Storage::disk('public')->exists($overlay->file)) {
                    $content = Storage::disk('public')->get($overlay->file);
                } elseif (Storage::disk('public')->exists('overlays/' . $overlay->file)) {
                    $content = Storage::disk('public')->get('overlays/' . $overlay->file);
                }
                
                if ($content) {
                    $geojson = json_decode($content, true);
                    
                    if ($geojson && isset($geojson['features'])) {
                        foreach ($geojson['features'] as $feature) {
                            if (!isset($feature['properties'])) {
                                $feature['properties'] = [];
                            }
                            
                            $feature['properties']['overlay_id'] = $overlay->id;
                            $feature['properties']['overlay_name'] = $overlay->nama;
                            $feature['properties']['overlay_type'] = $overlay->jenis;
                            $feature['properties']['overlay_uuid'] = $overlay->uuid;
                            
                            $allFeatures[] = $feature;
                        }
                    }
                }
            }
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $allFeatures,
            'total_overlays' => $overlays->count(),
            'total_features' => count($allFeatures)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GET SINGLE GEOJSON
    |--------------------------------------------------------------------------
    */

    public function getGeoJSON($id)
    {
        try {
            $overlay = Overlay::findOrFail($id);

            // Prioritaskan dari geojson_data
            if ($overlay->geojson_data) {
                $geojson = is_string($overlay->geojson_data) ? json_decode($overlay->geojson_data, true) : $overlay->geojson_data;
                return response()->json($geojson);
            }

            if (!$overlay->file) {
                return response()->json([
                    'type' => 'FeatureCollection',
                    'features' => []
                ]);
            }

            $content = null;
            if (Storage::disk('public')->exists($overlay->file)) {
                $content = Storage::disk('public')->get($overlay->file);
            } elseif (Storage::disk('public')->exists('overlays/' . $overlay->file)) {
                $content = Storage::disk('public')->get('overlays/' . $overlay->file);
            }
            
            if (!$content) {
                return response()->json([
                    'type' => 'FeatureCollection',
                    'features' => []
                ]);
            }

            $geojson = json_decode($content, true);
            return response()->json($geojson);
            
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FORMAT BYTES
    |--------------------------------------------------------------------------
    */

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}