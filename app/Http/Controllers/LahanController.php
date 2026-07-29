<?php

namespace App\Http\Controllers;

use App\Models\Lahan;
use App\Models\Berita;
use App\Models\Overlay;
use App\Models\AuditLog;   
use App\Helpers\AuditHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LahanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $lahans = Lahan::latest()->paginate(10);

        $totalLahanCount = Lahan::count();
        $totalLuasKeseluruhan = Lahan::sum('luas');
        $totalFileSpasialCount = Lahan::whereNotNull('geojson')
            ->where('geojson', '!=', '')
            ->count();

        $totalBeritaCount = Berita::count();
        $totalAuditLog = AuditLog::count();

        $totalAktivitasHariIni = AuditLog::whereDate('created_at', today())->count();

        $auditLogs = AuditLog::latest()->take(10)->get();

        $totalPoktanCount = Lahan::whereNotNull('poktan')
            ->where('poktan', '!=', '')
            ->distinct()
            ->count('poktan');

        $luasPerDesa = Lahan::select('nama_desa', DB::raw('SUM(luas) as total'))
            ->whereNotNull('nama_desa')
            ->where('nama_desa', '!=', '')
            ->groupBy('nama_desa')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $luasPerPoktan = Lahan::select('poktan', DB::raw('SUM(luas) as total'))
            ->whereNotNull('poktan')
            ->where('poktan', '!=', '')
            ->groupBy('poktan')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $jenisTanamanChart = Lahan::select('jenis', DB::raw('COUNT(*) as total'))
            ->whereNotNull('jenis')
            ->where('jenis', '!=', '')
            ->groupBy('jenis')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $kondisiChart = Lahan::select('kondisi', DB::raw('COUNT(*) as total'))
            ->whereNotNull('kondisi')
            ->where('kondisi', '!=', '')
            ->groupBy('kondisi')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $trenLahanBulanan = Lahan::selectRaw("
                DATE_FORMAT(created_at, '%b %Y') as bulan,
                COUNT(*) as total
            ")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupByRaw("YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b %Y')")
            ->orderByRaw("YEAR(created_at), MONTH(created_at)")
            ->get();

        return view('admin.dashboard', compact(
            'lahans',
            'totalLahanCount',
            'totalLuasKeseluruhan',
            'totalFileSpasialCount',
            'totalBeritaCount',
            'totalPoktanCount',
            'luasPerDesa',
            'luasPerPoktan',
            'jenisTanamanChart',
            'kondisiChart',
            'trenLahanBulanan',
            'totalAuditLog',
            'totalAktivitasHariIni',
            'auditLogs'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | INDEX LAHAN (HALAMAN KELOLA LAHAN)
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $lahans = Lahan::latest()->paginate(10);
        return view('admin.lahan.index', compact('lahans'));
    }

    /*
    |--------------------------------------------------------------------------
    | MAP
    |--------------------------------------------------------------------------
    */

    public function map()
    {
        return view('map');
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD LAHAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_desa'   => 'nullable|string|max:255',
            'pemilik'     => 'required|string|max:255',
            'luas'        => 'required|numeric',
            'jenis'       => 'nullable|string|max:255',
            'kecamatan'   => 'nullable|string|max:255',
            'poktan'      => 'nullable|string|max:255',
            'kode_persil' => 'nullable|string|max:255',
            'kondisi'     => 'nullable|string|max:255',
            'pola_ruang'  => 'nullable|string|max:255',
            'sumber_air'  => 'nullable|string|max:255',
            'geojson'     => 'nullable'
        ]);

        $lahan = Lahan::create($validated);

        AuditHelper::log('CREATE', 'Lahan', 'Menambahkan data lahan - Pemilik: ' . ($validated['pemilik'] ?? 'Unknown'));

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan'
        ]);
    }

    public function edit($id)
    {
        $lahan = Lahan::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'lahan' => $lahan
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_desa'   => 'nullable|string|max:255',
            'pemilik'     => 'required|string|max:255',
            'luas'        => 'required|numeric',
            'jenis'       => 'nullable|string|max:255',
            'kecamatan'   => 'nullable|string|max:255',
            'poktan'      => 'nullable|string|max:255',
            'kode_persil' => 'nullable|string|max:255',
            'kondisi'     => 'nullable|string|max:255',
            'pola_ruang'  => 'nullable|string|max:255',
            'sumber_air'  => 'nullable|string|max:255',
            'geojson'     => 'nullable'
        ]);

        $lahan = Lahan::findOrFail($id);
        
        // Simpan nilai LAMA sebelum update
        $oldPemilik = $lahan->pemilik;
        $oldNamaDesa = $lahan->nama_desa;
        $oldLuas = $lahan->luas;
        $oldJenis = $lahan->jenis;
        $oldKecamatan = $lahan->kecamatan;
        $oldPoktan = $lahan->poktan;
        $oldKodePersil = $lahan->kode_persil;
        $oldKondisi = $lahan->kondisi;
        $oldPolaRuang = $lahan->pola_ruang;
        $oldSumberAir = $lahan->sumber_air;
        
        $lahan->update($validated);
        
        // Refresh untuk mendapatkan nilai BARU
        $lahan->refresh();
        
        // Kumpulkan perubahan yang terjadi
        $changes = [];
        
        if ($oldPemilik != $lahan->pemilik) {
            $changes[] = "Pemilik: " . ($oldPemilik ?: 'Kosong') . " → " . ($lahan->pemilik ?: 'Kosong');
        }
        if ($oldNamaDesa != $lahan->nama_desa) {
            $changes[] = "Nama Desa: " . ($oldNamaDesa ?: 'Kosong') . " → " . ($lahan->nama_desa ?: 'Kosong');
        }
        if ($oldLuas != $lahan->luas) {
            $changes[] = "Luas: " . ($oldLuas ?: '0') . " Ha → " . ($lahan->luas ?: '0') . " Ha";
        }
        if ($oldJenis != $lahan->jenis) {
            $changes[] = "Jenis Tanaman: " . ($oldJenis ?: 'Kosong') . " → " . ($lahan->jenis ?: 'Kosong');
        }
        if ($oldKecamatan != $lahan->kecamatan) {
            $changes[] = "Kecamatan: " . ($oldKecamatan ?: 'Kosong') . " → " . ($lahan->kecamatan ?: 'Kosong');
        }
        if ($oldPoktan != $lahan->poktan) {
            $changes[] = "Poktan: " . ($oldPoktan ?: 'Kosong') . " → " . ($lahan->poktan ?: 'Kosong');
        }
        if ($oldKodePersil != $lahan->kode_persil) {
            $changes[] = "Kode Persil: " . ($oldKodePersil ?: 'Kosong') . " → " . ($lahan->kode_persil ?: 'Kosong');
        }
        if ($oldKondisi != $lahan->kondisi) {
            $changes[] = "Kondisi: " . ($oldKondisi ?: 'Kosong') . " → " . ($lahan->kondisi ?: 'Kosong');
        }
        if ($oldPolaRuang != $lahan->pola_ruang) {
            $changes[] = "Pola Ruang: " . ($oldPolaRuang ?: 'Kosong') . " → " . ($lahan->pola_ruang ?: 'Kosong');
        }
        if ($oldSumberAir != $lahan->sumber_air) {
            $changes[] = "Sumber Air: " . ($oldSumberAir ?: 'Kosong') . " → " . ($lahan->sumber_air ?: 'Kosong');
        }
        
        // Buat pesan audit log
        if (!empty($changes)) {
            if (count($changes) == 1 && strpos($changes[0], 'Pemilik:') === 0) {
                preg_match('/Pemilik: (.*?) → (.*)/', $changes[0], $matches);
                if (count($matches) == 3) {
                    $deskripsi = 'Mengupdate data lahan - Dari: ' . trim($matches[1]) . ' → Menjadi: ' . trim($matches[2]);
                } else {
                    $deskripsi = 'Mengupdate data lahan - ' . $changes[0];
                }
            } else {
                $deskripsi = 'Mengupdate data lahan - ' . implode('; ', $changes);
            }
        } else {
            $deskripsi = 'Mengupdate data lahan - Tidak ada perubahan (Pemilik: ' . ($validated['pemilik'] ?? 'Unknown') . ')';
        }
        
        AuditHelper::log('UPDATE', 'Lahan', $deskripsi);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $lahan = Lahan::findOrFail($id);
        $pemilik = $lahan->pemilik ?? 'Unknown';
        $namaDesa = $lahan->nama_desa ?? 'Unknown';
        
        AuditHelper::log('DELETE', 'Lahan', 'Menghapus data lahan - Pemilik: ' . $pemilik . ', Desa: ' . $namaDesa);
        
        $lahan->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function deleteAll()
    {
        $jumlah = Lahan::count();
        
        AuditHelper::log('DELETE', 'Lahan', 'Menghapus semua data lahan - Total: ' . $jumlah . ' data');
        
        Lahan::truncate();

        return redirect()->back()->with('success', 'Semua data berhasil dihapus');
    }

    public function massDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        $jumlah = count($request->ids);
        
        $lahans = Lahan::whereIn('id', $request->ids)->get();
        $pemilikList = $lahans->pluck('pemilik')->implode(', ');
        
        AuditHelper::log('DELETE', 'Lahan', 'Menghapus massal data lahan - Total: ' . $jumlah . ' data (Pemilik: ' . $pemilikList . ')');
        
        Lahan::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | API GEOJSON LAHAN
    |--------------------------------------------------------------------------
    */

    public function geojson()
    {
        $data = Lahan::whereNotNull('geojson')
            ->where('geojson', '!=', '')
            ->get();

        $features = [];

        foreach ($data as $item) {
            $geometry = json_decode($item->geojson, true);

            if (!$geometry || !isset($geometry['type'])) {
                continue;
            }

            $features[] = [
                'type' => 'Feature',
                'geometry' => $geometry,
                'properties' => [
                    'id' => $item->id,
                    'nama_desa' => $item->nama_desa ?? '-',
                    'pemilik' => $item->pemilik ?? '-',
                    'luas' => (float) $item->luas,
                    'jenis' => $item->jenis ?? '-',
                    'kecamatan' => $item->kecamatan ?? '-',
                    'poktan' => $item->poktan ?? '-',
                    'kode_persil' => $item->kode_persil ?? '-',
                    'kondisi' => $item->kondisi ?? '-',
                    'pola_ruang' => $item->pola_ruang ?? '-',
                    'sumber_air' => $item->sumber_air ?? '-',
                ]
            ];
        }
 
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD GIS LAHAN
    |--------------------------------------------------------------------------
    */

    public function uploadGis(Request $request)
    {
        try {
            $request->validate([
                'geojson' => 'required|file|max:51200'
            ]);

            $file = $request->file('geojson');
            $content = file_get_contents($file->getRealPath());
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan JSON valid'
                ], 422);
            }

            if (!$json || !isset($json['features']) || !is_array($json['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format GeoJSON tidak valid'
                ], 422);
            }

            $jumlah = 0;
            $errors = [];

            foreach ($json['features'] as $index => $feature) {
                try {
                    $props = $feature['properties'] ?? [];
                    $geometry = $feature['geometry'] ?? null;

                    if (!$geometry) {
                        $errors[] = "Feature ke-" . ($index + 1) . " tidak memiliki geometry";
                        continue;
                    }

                    Lahan::create([
                        'nama_desa' => $props['Desa'] ?? $props['desa'] ?? $props['nama_desa'] ?? $props['NAMOBJ'] ?? '-',
                        'pemilik' => $props['Nama'] ?? $props['nama'] ?? $props['pemilik'] ?? '-',
                        'luas' => floatval($props['Luas_Ha'] ?? $props['LUASHA'] ?? $props['luas'] ?? 0),
                        'jenis' => $props['JenisSawah'] ?? $props['jenis'] ?? $props['jenis_tanaman'] ?? '-',
                        'kecamatan' => $props['Kecamatan'] ?? $props['WADMKC'] ?? $props['kecamatan'] ?? '-',
                        'poktan' => $props['Poktan'] ?? $props['poktan'] ?? '-',
                        'kode_persil' => $props['KodePersil'] ?? $props['KODKWS'] ?? $props['kode_persil'] ?? '-',
                        'kondisi' => $props['KondLahan'] ?? $props['kondisi'] ?? '-',
                        'pola_ruang' => $props['PolaRuang'] ?? $props['pola_ruang'] ?? '-',
                        'sumber_air' => $props['SmbrAir'] ?? $props['sumber_air'] ?? '-',
                        'geojson' => json_encode($geometry),
                    ]);

                    $jumlah++;
                } catch (\Exception $e) {
                    $errors[] = "Feature ke-" . ($index + 1) . ": " . $e->getMessage();
                }
            }

            AuditHelper::log('UPLOAD', 'Lahan', 'Import ' . $jumlah . ' data lahan dari file GeoJSON: ' . $file->getClientOriginalName());

            return response()->json([
                'success' => true,
                'message' => "Upload GIS berhasil: {$jumlah} data ditambahkan",
                'total_feature' => $jumlah,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Upload GIS error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OVERLAY MANAGEMENT
    |--------------------------------------------------------------------------
    */

    public function overlayIndex()
    {
        return view('admin.overlay');
    }

    public function overlayList()
    {
        try {
            $overlays = Overlay::orderBy('created_at', 'desc')->get();

            $transformed = $overlays->map(function ($overlay) {
                return [
                    'id' => $overlay->id,
                    'nama_layer' => $overlay->nama ?? $overlay->nama_layer ?? 'Unnamed',
                    'jenis_layer' => $overlay->jenis ?? $overlay->jenis_layer ?? 'Umum',
                    'file_path' => $overlay->file ?? $overlay->file_path ?? '-',
                    'is_active' => $overlay->status === 'active' || $overlay->is_active == 1,
                    'created_at' => $overlay->created_at
                ];
            });

            return response()->json([
                'success' => true,
                'overlays' => $transformed
            ]);

        } catch (\Exception $e) {
            Log::error('Overlay list error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'overlays' => []
            ]);
        }
    }

    public function overlayUpload(Request $request)
    {
        try {
            $request->validate([
                'geojson' => 'required|file|max:51200'
            ]);

            $file = $request->file('geojson');
            $originalName = $file->getClientOriginalName();
            $content = file_get_contents($file->getRealPath());
            $geojson = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukan JSON valid'
                ], 422);
            }

            if (!$geojson || !isset($geojson['features']) || !is_array($geojson['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format GeoJSON tidak valid'
                ], 422);
            }

            $jumlahFitur = count($geojson['features']);
            $layerName = pathinfo($originalName, PATHINFO_FILENAME);
            $jenisLayer = 'Umum';

            if (isset($geojson['features'][0]['properties'])) {
                $props = $geojson['features'][0]['properties'];

                if (isset($props['NAMOBJ'])) {
                    $layerName = $props['NAMOBJ'];
                } elseif (isset($props['nama'])) {
                    $layerName = $props['nama'];
                } elseif (isset($props['name'])) {
                    $layerName = $props['name'];
                }

                $layerLower = strtolower($layerName);

                if (str_contains($layerLower, 'sungai') || str_contains($layerLower, 'air')) {
                    $jenisLayer = 'Hidrologi';
                } elseif (str_contains($layerLower, 'hutan')) {
                    $jenisLayer = 'Kehutanan';
                } elseif (str_contains($layerLower, 'jalan')) {
                    $jenisLayer = 'Transportasi';
                } elseif (str_contains($layerLower, 'desa') || str_contains($layerLower, 'batas')) {
                    $jenisLayer = 'Administrasi';
                } elseif (str_contains($layerLower, 'sawah') || str_contains($layerLower, 'lahan')) {
                    $jenisLayer = 'Pertanian';
                }
            }

            $overlay = Overlay::create([
                'nama' => $layerName,
                'jenis' => $jenisLayer,
                'file' => $originalName,
                'geojson_data' => json_encode($geojson, JSON_UNESCAPED_UNICODE),
                'jumlah_fitur' => $jumlahFitur,
                'status' => 'active'
            ]);

            AuditHelper::log('UPLOAD', 'Overlay', 'Upload overlay baru: ' . $layerName . ' (' . $jumlahFitur . ' fitur)');

            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil diupload',
                'overlay' => [
                    'id' => $overlay->id,
                    'nama_layer' => $overlay->nama,
                    'jenis_layer' => $overlay->jenis,
                    'file_path' => $overlay->file,
                    'is_active' => $overlay->status === 'active',
                    'created_at' => $overlay->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Upload overlay error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function overlayToggle($id, Request $request)
    {
        try {
            $overlay = Overlay::findOrFail($id);
            $isActive = $request->input('is_active', false);
            $oldStatus = $overlay->status;
            $overlay->status = $isActive ? 'active' : 'inactive';
            $overlay->save();

            AuditHelper::log('UPDATE', 'Overlay', 'Mengubah status overlay: ' . ($overlay->nama ?? 'Unknown') . ' dari ' . $oldStatus . ' menjadi ' . $overlay->status);

            return response()->json([
                'success' => true,
                'message' => 'Status overlay berhasil diubah',
                'is_active' => $overlay->status === 'active'
            ]);

        } catch (\Exception $e) {
            Log::error('Toggle overlay error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function overlayDestroy($id)
    {
        try {
            $overlay = Overlay::findOrFail($id);
            $overlayName = $overlay->nama ?? 'Unknown';
            
            AuditHelper::log('DELETE', 'Overlay', 'Menghapus overlay: ' . $overlayName);
            
            $overlay->delete();

            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete overlay error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getOverlay($id)
    {
        $overlay = Overlay::find($id);

        if (!$overlay) {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }

        return response()->json(json_decode($overlay->geojson_data, true));
    }

    /*
    |--------------------------------------------------------------------------
    | API OVERLAY GEOJSON (SEMUA OVERLAY) - TAMBAHAN BARU
    |--------------------------------------------------------------------------
    */

    public function getOverlayGeoJSON()
    {
        try {
            // Ambil semua overlay yang aktif
            $overlays = Overlay::where('status', 'active')->get();
            $allFeatures = [];

            foreach ($overlays as $overlay) {
                $geojson = json_decode($overlay->geojson_data, true);
                
                if ($geojson && isset($geojson['features']) && is_array($geojson['features'])) {
                    // Tambahkan properti overlay_nama agar frontend tahu asalnya
                    foreach ($geojson['features'] as &$feature) {
                        $feature['properties']['overlay_nama'] = $overlay->nama;
                        $feature['properties']['overlay_jenis'] = $overlay->jenis;
                        $feature['properties']['overlay_id'] = $overlay->id;
                    }
                    $allFeatures = array_merge($allFeatures, $geojson['features']);
                }
            }

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => $allFeatures
            ]);

        } catch (\Exception $e) {
            Log::error('Get overlay geojson error: ' . $e->getMessage());
            
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | API HUTAN LINDUNG KHUSUS - TAMBAHAN BARU
    |--------------------------------------------------------------------------
    */

    public function getHutanLindungGeoJSON()
    {
        try {
            // Cari overlay dengan nama mengandung "Hutan Lindung"
            $overlay = Overlay::where('nama', 'LIKE', '%Hutan Lindung%')
                ->orWhere('nama', 'LIKE', '%hutan lindung%')
                ->orWhere('jenis', 'Kehutanan')
                ->first();

            if ($overlay) {
                $geojson = json_decode($overlay->geojson_data, true);
                
                // Tambahkan properti tambahan
                if ($geojson && isset($geojson['features'])) {
                    foreach ($geojson['features'] as &$feature) {
                        $feature['properties']['overlay_nama'] = $overlay->nama;
                        $feature['properties']['overlay_jenis'] = $overlay->jenis;
                    }
                }
                
                return response()->json($geojson ?? [
                    'type' => 'FeatureCollection',
                    'features' => []
                ]);
            }

            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);

        } catch (\Exception $e) {
            Log::error('Get Hutan Lindung error: ' . $e->getMessage());
            
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }
    }
}