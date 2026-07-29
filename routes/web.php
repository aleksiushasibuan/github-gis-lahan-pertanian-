<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\Overlay;

use App\Http\Controllers\LahanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

// HOME
Route::get('/', function () {
    return view('home');
})->name('home');

// MAP
Route::get('/map', [LahanController::class, 'map'])->name('map');
Route::get('/webgis', [LahanController::class, 'map'])->name('webgis');

// OPTIONAL PAGE
Route::get('/peta', function () {
    return view('peta');
})->name('peta');


/*
|--------------------------------------------------------------------------
| API UNTUK WEB GIS
|--------------------------------------------------------------------------
*/

// API LAHAN
Route::get('/api/lahan/geojson', [LahanController::class, 'geojson'])
    ->name('lahan.geojson');

// API BATAS DESA
Route::get('/api/batas-desa/geojson', function () {
    return response()->json([
        'type' => 'FeatureCollection',
        'features' => []
    ]);
})->name('api.batas-desa.geojson');

// API BATAS KECAMATAN
Route::get('/api/batas-kecamatan/geojson', function () {
    return response()->json([
        'type' => 'FeatureCollection',
        'features' => []
    ]);
})->name('api.batas-kecamatan.geojson');

// ============================================================
// API HUTAN LINDUNG - HANYA MENAMPILKAN YANG STATUS ACTIVE
// ============================================================
Route::get('/api/hutan-lindung/geojson', function () {
    try {
        // Cari overlay dengan nama mengandung "Hutan Lindung" dan status ACTIVE
        $overlay = Overlay::where('status', 'active')
            ->where(function($query) {
                $query->where('nama', 'LIKE', '%Hutan Lindung%')
                    ->orWhere('nama', 'LIKE', '%hutan lindung%')
                    ->orWhere('jenis', 'Kehutanan');
            })
            ->first();

        if (!$overlay) {
            // Coba cari berdasarkan file name
            $overlay = Overlay::where('status', 'active')
                ->where(function($query) {
                    $query->where('file', 'LIKE', '%hutan%lindung%')
                        ->orWhere('file', 'LIKE', '%Hutan%Lindung%');
                })
                ->first();
        }

        if (!$overlay) {
            // Jika tetap tidak ditemukan, coba ambil semua overlay aktif dan filter
            $allOverlays = Overlay::where('status', 'active')->get();
            foreach ($allOverlays as $o) {
                $lowerNama = strtolower($o->nama);
                if (str_contains($lowerNama, 'hutan lindung') || str_contains($lowerNama, 'hutan_lindung')) {
                    $overlay = $o;
                    break;
                }
            }
        }

        // Jika tidak ditemukan atau status tidak active, kembalikan kosong
        if (!$overlay || $overlay->status !== 'active') {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => [],
                'message' => 'Hutan Lindung tidak aktif atau belum diupload.'
            ]);
        }

        // PRIORITAS 1: Baca dari file fisik
        $filePath = public_path('data/' . $overlay->file);
        $geojson = null;
        
        if (file_exists($filePath)) {
            $geojson = json_decode(file_get_contents($filePath), true);
        }
        
        // PRIORITAS 2: Jika file tidak ada, baca dari database
        if (!$geojson && $overlay->geojson_data) {
            $geojson = json_decode($overlay->geojson_data, true);
        }

        if ($geojson && isset($geojson['features'])) {
            // Tambahkan properti overlay
            foreach ($geojson['features'] as &$feature) {
                if (!isset($feature['properties'])) {
                    $feature['properties'] = [];
                }
                $feature['properties']['overlay_id'] = $overlay->id;
                $feature['properties']['overlay_name'] = $overlay->nama;
                $feature['properties']['overlay_type'] = $overlay->jenis;
                $feature['properties']['overlay_status'] = $overlay->status;
            }
            
            return response()->json($geojson);
        }

        // Jika tidak ditemukan sama sekali, kembalikan kosong
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [],
            'message' => 'Hutan Lindung belum diupload. Silakan upload melalui Admin Panel.'
        ]);

    } catch (\Exception $e) {
        \Log::error('API Hutan Lindung error: ' . $e->getMessage());
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [],
            'error' => $e->getMessage()
        ]);
    }
})->name('api.hutan-lindung.geojson');

// ============================================================
// API OVERLAY LIST - HANYA MENAMPILKAN YANG STATUS ACTIVE
// ============================================================
Route::get('/api/overlays/list', function () {

    $overlays = Overlay::where('status', 'active')
        ->latest()
        ->get();

    return response()->json([
        'success' => true,
        'overlays' => $overlays->map(function ($overlay) {
            return [
                'id' => $overlay->id,
                'nama_layer' => $overlay->nama,
                'jenis_layer' => $overlay->jenis,
                'jumlah_fitur' => $overlay->jumlah_fitur,
                'status' => $overlay->status,
                'file' => $overlay->file,
                'created_at' => $overlay->created_at,
            ];
        })
    ]);
});

// ============================================================
// API OVERLAY GABUNG - BACA DARI FILE (PRIORITAS)
// ============================================================
Route::get('/api/overlay/geojson', function () {

    $overlays = Overlay::where('status', 'active')->get();
    $allFeatures = [];

    foreach ($overlays as $overlay) {

        // PRIORITAS 1: Baca dari file fisik
        $filePath = public_path('data/' . $overlay->file);
        $geojson = null;
        
        if (file_exists($filePath)) {
            $geojson = json_decode(file_get_contents($filePath), true);
        }
        
        // PRIORITAS 2: Jika file tidak ada, baca dari database
        if (!$geojson && $overlay->geojson_data) {
            $geojson = json_decode($overlay->geojson_data, true);
            \Log::info("Membaca overlay dari database (file tidak ditemukan): {$overlay->file}");
        }

        if (!$geojson || !isset($geojson['features'])) {
            \Log::warning("GeoJSON tidak ditemukan untuk overlay: {$overlay->nama}");
            continue;
        }

        foreach ($geojson['features'] as $feature) {

            if (!isset($feature['properties'])) {
                $feature['properties'] = [];
            }

            $feature['properties']['overlay_id'] = $overlay->id;
            $feature['properties']['overlay_name'] = $overlay->nama;
            $feature['properties']['overlay_type'] = $overlay->jenis;
            $feature['properties']['overlay_uuid'] = $overlay->uuid;
            $feature['properties']['overlay_status'] = $overlay->status;

            $allFeatures[] = $feature;
        }
    }

    return response()->json([
        'type' => 'FeatureCollection',
        'features' => $allFeatures,
        'total_overlays' => $overlays->count(),
        'total_features' => count($allFeatures)
    ]);

})->name('api.overlay.geojson');

// ============================================================
// API SINGLE OVERLAY - BACA DARI FILE (PRIORITAS)
// ============================================================
Route::get('/api/overlay/{id}', function ($id) {

    $overlay = Overlay::find($id);

    if (!$overlay) {
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [],
            'error' => 'Overlay tidak ditemukan'
        ]);
    }

    // PRIORITAS 1: Baca dari file fisik
    $filePath = public_path('data/' . $overlay->file);
    $geojson = null;
    
    if (file_exists($filePath)) {
        $geojson = json_decode(file_get_contents($filePath), true);
    }
    
    // PRIORITAS 2: Jika file tidak ada, baca dari database
    if (!$geojson && $overlay->geojson_data) {
        $geojson = json_decode($overlay->geojson_data, true);
    }

    if (!$geojson) {
        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [],
            'error' => 'Data GeoJSON tidak ditemukan'
        ]);
    }

    return response()->json($geojson);

})->name('overlay.by.id');

// ============================================================
// TEST API OVERLAY
// ============================================================
Route::get('/api/overlay/test', function() {
    $overlay = Overlay::find(8);
    
    if (!$overlay) {
        return response()->json([
            'success' => false,
            'message' => 'Overlay dengan ID 8 tidak ditemukan'
        ]);
    }
    
    // Cek file fisik
    $filePath = public_path('data/' . $overlay->file);
    $fileExists = file_exists($filePath);
    
    // Cek database
    $hasGeojsonData = !empty($overlay->geojson_data);
    
    return response()->json([
        'success' => true,
        'id' => $overlay->id,
        'nama' => $overlay->nama,
        'jenis' => $overlay->jenis,
        'jumlah_fitur' => $overlay->jumlah_fitur,
        'status' => $overlay->status,
        'file' => $overlay->file,
        'file_exists' => $fileExists,
        'file_path' => $filePath,
        'geojson_data_ada' => $hasGeojsonData ? 'YA' : 'TIDAK',
        'geojson_data_size' => $hasGeojsonData ? strlen($overlay->geojson_data) . ' bytes' : '0',
        'sample_features' => $hasGeojsonData ? 
            (isset(json_decode($overlay->geojson_data, true)['features']) ? 
                count(json_decode($overlay->geojson_data, true)['features']) . ' fitur' : 
                'Tidak ada features') : 
            'Tidak ada data di database'
    ]);
});


/*
|--------------------------------------------------------------------------
| BERITA
|--------------------------------------------------------------------------
*/

Route::get('/berita', [BeritaController::class, 'frontendIndex'])
    ->name('berita.index');

Route::get('/api/berita/latest', [BeritaController::class, 'latest'])
    ->name('berita.latest');

Route::get('/berita/{id}', [BeritaController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('berita.detail');


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'loginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/verify-otp', [AuthController::class, 'verifyOtpForm']);

Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])
    ->name('resend.otp');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/', [LahanController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/dashboard', [LahanController::class, 'dashboard'])
        ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOG
    |--------------------------------------------------------------------------
    */
    Route::get('/audit', function (Request $request) {
        $query = AuditLog::latest();
        
        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }
        
        if ($request->filled('modul')) {
            $query->where('modul', 'like', '%' . $request->modul . '%');
        }
        
        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $logs = $query->paginate(10);
        
        $statistics = [
            'CREATE' => AuditLog::where('aksi', 'CREATE')->count(),
            'UPDATE' => AuditLog::where('aksi', 'UPDATE')->count(),
            'DELETE' => AuditLog::where('aksi', 'DELETE')->count(),
            'UPLOAD' => AuditLog::where('aksi', 'UPLOAD')->count(),
            'LOGIN' => AuditLog::where('aksi', 'LOGIN')->count(),
        ];
        
        return view('admin.audit.index', compact('logs', 'statistics'));
    })->name('admin.audit');

    /*
    |--------------------------------------------------------------------------
    | LAHAN
    |--------------------------------------------------------------------------
    */

    Route::get('/lahan', [LahanController::class, 'index'])
        ->name('admin.lahan.index');

    Route::delete('/lahan/delete-all', [LahanController::class, 'deleteAll'])
        ->name('admin.lahan.delete-all');

    Route::post('/lahan/mass-destroy', [LahanController::class, 'massDestroy'])
        ->name('admin.lahan.massDestroy');

    Route::post('/lahan', [LahanController::class, 'store'])
        ->name('admin.lahan.store');

    Route::get('/lahan/{id}/edit', [LahanController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('admin.lahan.edit');

    Route::put('/lahan/{id}', [LahanController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('admin.lahan.update');

    Route::delete('/lahan/{id}', [LahanController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('admin.lahan.destroy');

    Route::post('/upload-gis', [LahanController::class, 'uploadGis'])
        ->name('admin.upload.gis');

    // ============================================================
    // ROUTE UPLOAD OVERLAY - SIMPAN KE DATABASE DAN FILE
    // ============================================================
    Route::post('/overlay/upload', function (Request $request) {
        try {
            ini_set('memory_limit', '2048M');
            ini_set('max_execution_time', 600);

            $request->validate([
                'geojson' => 'required|file|max:102400'
            ]);

            $file = $request->file('geojson');

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 422);
            }

            $extension = strtolower($file->getClientOriginalExtension());
            if (!in_array($extension, ['json', 'geojson'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'File harus format .json atau .geojson'
                ], 422);
            }

            $content = file_get_contents($file->getRealPath());
            $json = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'JSON tidak valid : ' . json_last_error_msg()
                ], 422);
            }

            if (!isset($json['type']) || $json['type'] !== 'FeatureCollection' || !isset($json['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format GeoJSON tidak valid'
                ], 422);
            }

            $jumlahFitur = count($json['features']);
            $layerName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $jenisLayer = 'Umum';

            $lower = strtolower($layerName);
            if (str_contains($lower, 'hutan')) {
                $jenisLayer = 'Kehutanan';
            } elseif (str_contains($lower, 'desa') || str_contains($lower, 'kecamatan') || str_contains($lower, 'batas')) {
                $jenisLayer = 'Administrasi';
            } elseif (str_contains($lower, 'jalan')) {
                $jenisLayer = 'Transportasi';
            } elseif (str_contains($lower, 'sawah') || str_contains($lower, 'lahan')) {
                $jenisLayer = 'Pertanian';
            } elseif (str_contains($lower, 'sungai') || str_contains($lower, 'air')) {
                $jenisLayer = 'Hidrologi';
            }

            $fileName = $file->getClientOriginalName();

            // ============================================================
            // SIMPAN KE DATABASE (GEOJSON_DATA)
            // ============================================================
            $overlay = Overlay::create([
                'nama' => $layerName,
                'jenis' => $jenisLayer,
                'file' => $fileName,
                'geojson_data' => json_encode($json),
                'jumlah_fitur' => $jumlahFitur,
                'status' => 'active'
            ]);

            // ============================================================
            // SIMPAN KE FOLDER public/data/ (FILE FISIK)
            // ============================================================
            $dataPath = public_path('data');
            if (!file_exists($dataPath)) {
                mkdir($dataPath, 0755, true);
            }
            $file->move($dataPath, $fileName);

            // Catat ke audit log
            AuditLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Admin',
                'aksi' => 'UPLOAD',
                'modul' => 'Overlay',
                'deskripsi' => 'Upload overlay: ' . $layerName . ' (' . $jumlahFitur . ' fitur) - File: ' . $fileName,
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Overlay berhasil diupload (disimpan di database dan folder)',
                'overlay' => [
                    'id' => $overlay->id,
                    'nama_layer' => $overlay->nama,
                    'jenis_layer' => $overlay->jenis,
                    'jumlah_fitur' => $overlay->jumlah_fitur,
                    'status' => $overlay->status,
                    'file' => $overlay->file,
                    'geojson_data_size' => strlen($overlay->geojson_data) . ' bytes',
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload overlay error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload gagal : ' . $e->getMessage()
            ], 500);
        }
    })->name('admin.overlay.upload');


    /*
    |--------------------------------------------------------------------------
    | BERITA ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/berita', [BeritaController::class, 'index'])
        ->name('admin.berita.index');

    Route::get('/berita/create', [BeritaController::class, 'create'])
        ->name('admin.berita.create');

    Route::post('/berita', [BeritaController::class, 'store'])
        ->name('admin.berita.store');

    Route::get('/berita/{id}/edit', [BeritaController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('admin.berita.edit');

    Route::put('/berita/{id}', [BeritaController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('admin.berita.update');

    Route::delete('/berita/{id}', [BeritaController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('admin.berita.destroy');


    /*
    |--------------------------------------------------------------------------
    | OVERLAY SYSTEM
    |--------------------------------------------------------------------------
    */

    Route::get('/overlay', function () {
        if (view()->exists('admin.overlay')) {
            return view('admin.overlay');
        } elseif (view()->exists('admin.lahan.overlay')) {
            return view('admin.lahan.overlay');
        } else {
            return '<h1>Kelola Overlay</h1><p>View belum tersedia. Silakan buat file view di resources/views/admin/overlay.blade.php</p>';
        }
    })->name('admin.overlay.index');

    Route::get('/overlay/list', function () {
        $overlays = Overlay::latest()->get();
        $data = $overlays->map(function ($overlay) {
            return [
                'id' => $overlay->id,
                'nama_layer' => $overlay->nama,
                'jenis_layer' => $overlay->jenis,
                'file' => $overlay->file,
                'jumlah_fitur' => $overlay->jumlah_fitur,
                'status' => $overlay->status,
                'geojson_data_size' => $overlay->geojson_data ? strlen($overlay->geojson_data) . ' bytes' : '0',
                'created_at' => $overlay->created_at,
            ];
        });
        return response()->json([
            'success' => true,
            'overlays' => $data
        ]);
    });

    Route::get('/overlay/{id}/detail', function ($id) {
        $overlay = Overlay::find($id);
        if (!$overlay) {
            return response()->json([
                'success' => false,
                'message' => 'Overlay tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $overlay
        ]);
    });

    Route::patch('/overlay/{id}/toggle', function (Request $request, $id) {
        $overlay = Overlay::find($id);
        if (!$overlay) {
            return response()->json([
                'success' => false,
                'message' => 'Overlay tidak ditemukan'
            ], 404);
        }

        $status = $request->input('status');
        if ($status) {
            $overlay->status = $status;
        } else {
            $isActive = filter_var($request->input('is_active', false), FILTER_VALIDATE_BOOLEAN);
            $overlay->status = $isActive ? 'active' : 'inactive';
        }

        $overlay->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'aksi' => 'UPDATE',
            'modul' => 'Overlay',
            'deskripsi' => 'Ubah status overlay: ' . $overlay->nama . ' menjadi ' . $overlay->status,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah',
            'status' => $overlay->status
        ]);
    })->name('admin.overlay.toggle');

    // ROUTE PUT UNTUK UPDATE OVERLAY
    Route::put('/overlay/{id}', function (Request $request, $id) {
        $overlay = Overlay::find($id);
        
        if (!$overlay) {
            return response()->json([
                'success' => false,
                'message' => 'Overlay tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama_layer' => 'required|string|max:255',
            'jenis_layer' => 'required|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $oldName = $overlay->nama;

        $overlay->nama = $request->nama_layer;
        $overlay->jenis = $request->jenis_layer;
        $overlay->status = $request->status;
        $overlay->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'aksi' => 'UPDATE',
            'modul' => 'Overlay',
            'deskripsi' => 'Update overlay: ' . $oldName . ' -> ' . $request->nama_layer,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Overlay berhasil diperbarui',
            'data' => [
                'id' => $overlay->id,
                'nama_layer' => $overlay->nama,
                'jenis_layer' => $overlay->jenis,
                'status' => $overlay->status
            ]
        ]);
    })->name('admin.overlay.update');

    // ============================================================
    // DELETE OVERLAY - HAPUS DARI DATABASE DAN FILE
    // ============================================================
    Route::delete('/overlay/{id}', function (Request $request, $id) {
        $overlay = Overlay::find($id);
        if (!$overlay) {
            return response()->json([
                'success' => false,
                'message' => 'Overlay tidak ditemukan'
            ], 404);
        }
        
        $overlayName = $overlay->nama;
        $fileName = $overlay->file;
        
        // Hapus dari database
        $overlay->delete();
        
        // Hapus file fisik jika ada
        $filePath = public_path('data/' . $fileName);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Admin',
            'aksi' => 'DELETE',
            'modul' => 'Overlay',
            'deskripsi' => 'Hapus overlay: ' . $overlayName . ' (File: ' . $fileName . ')',
            'ip_address' => $request->ip(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Overlay berhasil dihapus dari database dan folder'
        ]);
    })->name('admin.overlay.delete');
    
    
});

// ============================================================
// ROUTE CHECK OVERLAY
// ============================================================
Route::get('/api/check-overlay', function() {
    $dataPath = public_path('data');
    $files = [];
    
    if (file_exists($dataPath)) {
        $files = array_diff(scandir($dataPath), ['.', '..']);
    }
    
    $overlays = Overlay::all();
    
    return response()->json([
        'success' => true,
        'data_path' => $dataPath,
        'folder_exists' => file_exists($dataPath),
        'files_in_folder' => array_values($files),
        'overlays_in_db' => $overlays->map(function($o) {
            $filePath = public_path('data/' . $o->file);
            return [
                'id' => $o->id,
                'nama' => $o->nama,
                'file' => $o->file,
                'file_exists' => file_exists($filePath),
                'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
                'geojson_data_size' => $o->geojson_data ? strlen($o->geojson_data) : 0,
                'jumlah_fitur' => $o->jumlah_fitur,
                'status' => $o->status
            ];
        })
    ]);

    
});
