<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\AuditLog;
use App\Helpers\AuditHelper;  // <- Tambahkan ini jika ingin pakai helper
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    /* =========================================================
     | FRONTEND / PUBLIK
     * ========================================================= */

    public function frontendIndex()
    {
        $beritas = Berita::latest()->paginate(9);
        return view('berita.index', compact('beritas'));
    }

    public function latest()
    {
        $beritas = Berita::latest()
            ->take(6)
            ->get()
            ->map(function ($item) {
                $gambarUrl = null;

                if (!empty($item->gambar)) {
                    if (Str::startsWith($item->gambar, ['http://', 'https://'])) {
                        $gambarUrl = $item->gambar;
                    } else {
                        $path = ltrim(str_replace('storage/', '', $item->gambar), '/');
                        $gambarUrl = asset('storage/' . $path);
                    }
                }

                return [
                    'id' => $item->id,
                    'judul' => $item->judul ?? 'Tanpa Judul',
                    'isi' => $item->isi ?? '',
                    'kategori' => $item->kategori ?? 'Umum',
                    'deskripsi' => Str::limit(strip_tags($item->isi ?? ''), 120),
                    'penulis' => $item->penulis ?? 'Admin',
                    'gambar' => $item->gambar,
                    'gambar_url' => $gambarUrl,
                    'created_at' => $item->created_at,
                    'tanggal' => optional($item->created_at)->toDateTimeString(),
                    'url' => route('berita.detail', $item->id),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $beritas
        ]);
    }

    public function show($id)
    {
        $berita = Berita::findOrFail($id);
        $relatedBeritas = Berita::where('id', '!=', $berita->id)
            ->latest()
            ->take(6)
            ->get();

        // Untuk kompatibilitas blade
        $beritas = $relatedBeritas;

        return view('berita.detail', compact('berita', 'relatedBeritas', 'beritas'));
    }

    /* =========================================================
     | ADMIN - INDEX
     * ========================================================= */

    public function index(Request $request)
    {
        $query = Berita::query();

        // Pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $beritas = $query->latest()->paginate(10)->withQueryString();

        // Statistik
        $totalBeritaCount = Berita::count();
        $beritaBulanIni = Berita::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        $totalKategori = Berita::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->count('kategori');
        $kategoriList = Berita::whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('admin.berita.index', compact(
            'beritas',
            'totalBeritaCount',
            'beritaBulanIni',
            'totalKategori',
            'kategoriList'
        ));
    }

    public function create()
    {
        return view('admin.berita.create');
    }

    /* =========================================================
     | ADMIN - STORE (CREATE)
     * ========================================================= */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'isi'      => 'required|string',
            'gambar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $berita = Berita::create($validated);

        // ✅ AUDIT LOG CREATE
        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'Admin',
            'aksi'       => 'CREATE',
            'modul'      => 'BERITA',
            'deskripsi'  => 'Menambahkan berita: ' . $berita->judul,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil ditambahkan.',
                'data' => $berita
            ]);
        }

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil ditambahkan.');
    }

    /* =========================================================
     | ADMIN - EDIT
     * ========================================================= */

    public function edit(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'berita' => $berita
            ]);
        }

        return view('admin.berita.edit', compact('berita'));
    }

    /* =========================================================
     | ADMIN - UPDATE
     * ========================================================= */

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        
        // Simpan data LAMA untuk audit log
        $oldJudul = $berita->judul;

        $validated = $request->validate([
            'judul'    => 'required|string|max:255',
            'kategori' => 'nullable|string|max:100',
            'isi'      => 'required|string',
            'gambar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Hapus gambar lama jika upload gambar baru
        if ($request->hasFile('gambar')) {
            if (!empty($berita->gambar) && !Str::startsWith($berita->gambar, ['http://', 'https://'])) {
                $oldPath = ltrim(str_replace('storage/', '', $berita->gambar), '/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $validated['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $berita->update($validated);

        // ✅ AUDIT LOG UPDATE
        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'Admin',
            'aksi'       => 'UPDATE',
            'modul'      => 'BERITA',
            'deskripsi'  => 'Mengubah berita: ' . $oldJudul . ' → ' . $berita->judul,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil diperbarui.',
                'data' => $berita
            ]);
        }

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil diperbarui.');
    }

    /* =========================================================
     | ADMIN - DESTROY (DELETE)
     * ========================================================= */

    public function destroy(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);
        
        // Simpan judul untuk audit log SEBELUM dihapus
        $judulBerita = $berita->judul;

        // Hapus file gambar jika ada
        if (!empty($berita->gambar) && !Str::startsWith($berita->gambar, ['http://', 'https://'])) {
            $oldPath = ltrim(str_replace('storage/', '', $berita->gambar), '/');
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // ✅ AUDIT LOG DELETE (SEBELUM delete)
        AuditLog::create([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'Admin',
            'aksi'       => 'DELETE',
            'modul'      => 'BERITA',
            'deskripsi'  => 'Menghapus berita: ' . $judulBerita,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $berita->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berita berhasil dihapus.'
            ]);
        }

        return redirect()
            ->route('admin.berita.index')
            ->with('success', 'Berita berhasil dihapus.');
    }
}