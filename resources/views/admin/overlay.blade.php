{{-- resources/views/admin/overlay.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI Lahan Terpadu - Kelola Overlay</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #e2e8f0; }
        ::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; }

        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .animate-fadeInUp { animation: fadeInUp 0.4s ease-out forwards; }
        .animate-fadeInLeft { animation: fadeInLeft 0.4s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.3s ease-out forwards; }

        .drawer {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100%;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            z-index: 1000;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0,0,0,0.3);
        }

        .drawer.open { left: 0; }

        .drawer-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
            animation: fadeIn 0.3s ease-out;
        }

        .drawer-overlay.open { display: block; }

        .data-row { transition: all 0.2s ease; }
        .data-row:hover { background: #f3e8ff; }

        .btn-animate { transition: all 0.2s ease; }
        .btn-animate:active { transform: scale(0.97); }

        .data-table td, .data-table th { white-space: nowrap; padding: 12px 16px; }
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .overlay-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px;
        }

        @media (max-width: 480px) {
            .data-table td, .data-table th { padding: 8px 12px !important; font-size: 12px !important; }
            .header-title { font-size: 1.25rem !important; }
        }

        .alert {
            padding: 12px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success { background: #dcfce7; border-left: 4px solid #22c55e; color: #166534; }
        .alert-danger { background: #fee2e2; border-left: 4px solid #ef4444; color: #991b1b; }

        .modal {
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .modal.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .modal:not(.hidden) {
            opacity: 1;
            visibility: visible;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-inactive {
            background: #f1f5f9;
            color: #475569;
        }

        .dropzone-area {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .dropzone-area.drag-over {
            border-color: #8b5cf6 !important;
            background-color: #f3e8ff !important;
        }

        .toast-notification {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .stat-card-modern {
    position: relative;
    overflow: hidden;
    background: white;
    border-radius: 20px;
}

.stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #06b6d4, #10b981);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .stat-card-modern:hover::before { transform: translateX(0); }
        .stat-card-modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 35px -12px rgba(0, 0, 0, 0.15);
            border-color: transparent;
        }
    </style>
</head>
<body>

    <!-- ==================== MODAL TAMBAH LAHAN ==================== -->
    <div id="modalTambahLahan" class="modal fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalTambahLahan()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">🌾 Tambah Data Lahan</h2>
                    <p class="text-slate-200 text-sm">Upload file GeoJSON, data lahan akan terbaca otomatis</p>
                </div>
                <button type="button" onclick="closeModalTambahLahan()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-8">
               <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div class="text-sm">
                        Pastikan file GeoJSON memiliki properti seperti:
                        <ul class="list-disc pl-5 mt-1">
                            <li>nama_desa</li>
                            <li>pemilik</li>
                            <li>jenis_tanaman</li>
                            <li>luas</li>
                        </ul>
                    </div>
                </div>
                <form id="formTambahLahan" action="{{ route('admin.upload.gis') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">File GeoJSON</label>
                        <div id="lahanDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-blue-400 transition-all bg-slate-50 dropzone-area">
                            <i class="fas fa-file-code text-5xl text-slate-400 mb-4 block transition-colors" id="lahanFileIcon"></i>
                            <span class="text-sm font-bold text-blue-600 block" id="lahanFileNameLabel">Klik untuk pilih file GeoJSON</span>
                            <p class="text-xs text-slate-400 mt-2">Maksimal 10MB • Format .json / .geojson</p>
                            <input type="file" name="geojson" id="lahanFileInput" accept=".json,.geojson" required class="hidden">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="submit" id="btnSubmitLahan" class="bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Tambah Lahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL TAMBAH BERITA ==================== -->
    <div id="modalTambahBerita" class="modal fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalTambahBerita()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <!-- HEADER BIRU seperti di dashboard -->
            <div class="bg-gradient-to-r from-blue-800 to-indigo-800 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">📰 Tambah Berita Baru</h2>
                    <p class="text-slate-300 text-sm">Informasi kegiatan pertanian terbaru</p>
                </div>
                <button type="button" onclick="closeModalTambahBerita()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8">
                <form id="formTambahBerita" action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required 
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Masukkan judul berita">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                        <input type="text" name="kategori" 
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            placeholder="Contoh: Kegiatan, Penyuluhan, Informasi">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar</label>
                        <div id="beritaDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-blue-400 transition-all bg-slate-50 cursor-pointer dropzone-area">
                            <i class="fas fa-image text-4xl text-slate-400 mb-3 block" id="beritaFileIcon"></i>
                            <span class="text-sm font-medium text-blue-600 block" id="beritaFileNameLabel">Klik untuk pilih gambar</span>
                            <p class="text-xs text-slate-400 mt-2">Maksimal 2MB • JPG, JPEG, PNG</p>
                            <input type="file" name="gambar" id="beritaFileInput" accept="image/jpeg,image/png,image/jpg" class="hidden">
                        </div>
                        <div id="imagePreviewContainer" class="mt-4 hidden">
                            <div class="relative inline-block">
                                <img id="imagePreview" src="" alt="Preview" class="w-32 h-32 object-cover rounded-xl shadow-md">
                                <button type="button" onclick="removeImagePreview()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Isi Berita <span class="text-red-500">*</span></label>
                        <textarea name="isi" rows="8" required 
                            class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                            placeholder="Tuliskan isi berita di sini..."></textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <button type="button" onclick="closeModalTambahBerita()" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium transition">
                            <i class="fas fa-arrow-left"></i> Batal
                        </button>
                        <button type="submit" id="btnSubmitBerita" class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i> Publikasikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL UPLOAD OVERLAY ==================== -->
    <div id="modalUploadOverlay" class="modal fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalUploadOverlay()"></div>
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-violet-700 to-fuchsia-700 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">🗂 Upload Overlay Otomatis</h2>
                    <p class="text-slate-200 text-sm">Sistem membaca layer otomatis dari GeoJSON</p>
                </div>
                <button type="button" onclick="closeModalUploadOverlay()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-violet-50 border-l-4 border-violet-500 text-violet-700 p-4 rounded flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div class="text-sm">
                        File akan dipisahkan otomatis berdasarkan <code>NAMOBJ</code> dari GeoJSON.
                        <ul class="list-disc pl-5 mt-2">
                            <li>Batas Desa</li>
                            <li>Batas Kecamatan</li>
                            <li>Hutan Lindung</li>
                            <li>Hutan Produksi</li>
                            <li>Badan Air</li>
                            <li>Dan layer lainnya</li>
                        </ul>
                    </div>
                </div>
                <form id="uploadOverlayForm" class="overlay-card space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">File GeoJSON</label>
                        <div id="overlayDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-violet-400 transition-all bg-slate-50 dropzone-area">
                            <i class="fas fa-file-code text-5xl text-slate-400 mb-4 block transition-colors" id="overlayFileIcon"></i>
                            <span class="text-sm font-bold text-violet-600 block" id="overlayFileNameLabel">Klik untuk pilih file GeoJSON</span>
                            <p class="text-xs text-slate-400 mt-2">Maksimal 10MB • Format .json / .geojson</p>
                            <input type="file" name="geojson" id="overlayFileInput" accept=".json,.geojson" required class="hidden">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModalUploadOverlay()" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">Batal</button>
                        <button type="submit" id="btnSubmitUploadOverlay" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Overlay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL EDIT OVERLAY ==================== -->
    <div id="modalEditOverlay" class="modal fixed inset-0 z-[1200] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalEditOverlay()"></div>
        <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden animate-scaleIn">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">✏️ Edit Overlay</h2>
                    <p class="text-slate-100 text-sm">Ubah informasi layer</p>
                </div>
                <button type="button" onclick="closeModalEditOverlay()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-8">
                <form id="editOverlayForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_overlay_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Layer</label>
                            <input type="text" name="nama_layer" id="edit_nama_layer" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Layer</label>
                            <input type="text" name="jenis_layer" id="edit_jenis_layer" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                            <select name="status" id="edit_status" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8">
                        <button type="button" onclick="closeModalEditOverlay()" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">Batal</button>
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-3 rounded-xl font-semibold">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== TOAST NOTIFICATION ==================== -->
    <div id="toast" class="toast-notification hidden">
        <div class="bg-white rounded-xl shadow-2xl p-4 flex items-center gap-3 border-l-4 min-w-[280px]">
            <i id="toastIcon" class="fas fa-check-circle text-lg"></i>
            <span id="toastMessage" class="text-sm font-medium flex-1"></span>
            <button onclick="hideToast()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
    </div>

    <!-- ==================== DRAWER MOBILE ==================== -->
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-400 to-blue-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-map-marked-alt text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-sm">SI Lahan Terpadu</h1>
                    <p class="text-white/50 text-[10px]">Kabupaten Bengkalis</p>
                </div>
            </div>
        </div>
        <nav class="p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-database w-5 text-center"></i>
                <span class="text-sm">Dashboard</span>
            </a>
            <button type="button" onclick="openModalTambahLahan(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-plus-circle w-5 text-center text-emerald-400"></i>
                <span class="text-sm">Tambah Lahan</span>
            </button>
            <button type="button" onclick="openModalTambahBerita(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-plus-square w-5 text-center text-blue-400"></i>
                <span class="text-sm">Tambah Berita</span>
            </button>
            <button type="button" onclick="openModalUploadOverlay(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400"></i>
                <span class="text-sm">Upload Overlay</span>
            </button>
            <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-violet-600/30 text-violet-300 font-medium transition-all">
                <i class="fas fa-layer-group w-5 text-center"></i>
                <span class="text-sm">Kelola Overlay</span>
            </a>
            <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-newspaper w-5 text-center"></i>
                <span class="text-sm">Kelola Berita</span>
            </a>
            <a href="{{ route('admin.audit') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
    <i class="fas fa-history w-5 text-center text-cyan-400"></i>
    <span class="text-sm">Audit Log</span>
</a>
            <a href="{{ route('map') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-map w-5 text-center"></i>
                <span class="text-sm">Lihat Peta</span>
            </a>
            <div class="pt-6 mt-4 border-t border-white/10">
                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 transition-all text-red-300">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i>
                        <span class="text-sm">Keluar</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <div class="min-h-screen">
        <!-- MOBILE HEADER -->
        <div class="md:hidden bg-gradient-to-r from-slate-900 to-slate-800 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-50 shadow-lg">
            <button onclick="openDrawer()" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center active:bg-white/20 transition-all">
                <i class="fas fa-bars text-white text-xl"></i>
            </button>
            <div class="flex items-center gap-2">
                <i class="fas fa-layer-group text-violet-400"></i>
                <span class="font-semibold text-sm">Kelola Overlay</span>
            </div>
            <div class="w-10"></div>
        </div>

        <!-- SIDEBAR DESKTOP -->
        <aside class="hidden md:block fixed left-0 top-0 w-72 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white h-full shadow-2xl z-10 animate-fadeInLeft">
            <div class="p-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-tr from-emerald-400 to-blue-500 flex items-center justify-center shadow-lg">
                        <i class="fas fa-map-marked-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight">SI Lahan Terpadu</h1>
                        <p class="text-xs text-white/50">Kabupaten Bengkalis</p>
                    </div>
                </div>
            </div>
            <nav class="mt-6 px-5 space-y-1.5">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                    <i class="fas fa-database w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>
                <button type="button" onclick="openModalTambahLahan()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-plus-circle w-5 text-center text-emerald-400 group-hover:text-emerald-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Tambah Lahan</span>
                </button>
                <button type="button" onclick="openModalTambahBerita()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-plus-square w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Tambah Berita</span>
                </button>
                <button type="button" onclick="openModalUploadOverlay()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400 group-hover:text-violet-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Upload Overlay</span>
                </button>
                <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 shadow-md shadow-violet-500/20 font-medium transition-all">
                    <i class="fas fa-layer-group w-5 text-center"></i>
                    <span>Kelola Overlay</span>
                </a>
                <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-newspaper w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Berita</span>
                </a>
                </a>
                <a href="{{ route('admin.audit') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
    <i class="fas fa-history w-5 text-center text-cyan-400"></i>
    <span class="text-sm">Audit Log</span>
</a>
            
                <a href="{{ route('map') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-map w-5 text-center text-slate-300 group-hover:text-white"></i>
                    <span>Lihat Peta</span>
                </a>
                <div class="pt-6 mt-4 border-t border-white/10">
                    <form id="logout-form-desktop" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/20 transition-all text-red-300">
                            <i class="fas fa-sign-out-alt w-5 text-center"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="md:ml-72 p-4 md:p-6 lg:p-8 pb-8">
            <!-- HEADER dengan tombol aksi -->
            <div class="mb-6 animate-fadeInUp">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-violet-600 font-medium mb-1">
                            <i class="fas fa-layer-group"></i>
                            <span>Admin / Kelola Overlay</span>
                        </div>
                        <h2 class="header-title text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800">🗺️ Kelola Overlay Peta</h2>
                        <p class="text-slate-500 text-xs md:text-sm mt-1">Kelola layer spasial yang ditampilkan pada peta</p>
                    </div>
                    <div class="flex gap-3 w-full md:w-auto flex-wrap">
                        <button type="button" onclick="openModalTambahLahan()" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-emerald-500/20 transition-all btn-animate">
                            <i class="fas fa-plus-circle"></i> Tambah Lahan
                        </button>
                        <button type="button" onclick="openModalTambahBerita()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-500/20 transition-all btn-animate">
                            <i class="fas fa-newspaper"></i> Tambah Berita
                        </button>
                        <button type="button" onclick="openModalUploadOverlay()" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-violet-500/20 transition-all btn-animate">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Overlay
                        </button>
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="stat-card-modern bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Overlay</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1" id="totalOverlayCount">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center">
                            <i class="fas fa-layer-group text-violet-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card-modern bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Layer Aktif</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1" id="activeOverlayCount">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card-modern bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Layer Tidak Aktif</p>
                            <p class="text-3xl font-extrabold text-slate-800 mt-1" id="inactiveOverlayCount">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-card-modern bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jenis Layer</p>
                            <p class="text-xl font-extrabold text-slate-800 mt-1" id="uniqueJenisCount">0</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i class="fas fa-tags text-amber-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABEL OVERLAY -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-list-ul text-violet-500 text-base"></i>
                        <h3 class="font-semibold text-slate-700">📋 Daftar Overlay</h3>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="refreshOverlays()" class="text-slate-500 hover:text-violet-600 transition text-sm">
                            <i class="fas fa-sync-alt mr-1"></i> Refresh
                        </button>
                        <span class="text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
                            <i class="far fa-clock mr-1"></i> <span id="lastUpdateTime">-</span>
                        </span>
                    </div>
                </div>

                <div class="table-wrapper overflow-x-auto">
                    <table class="data-table w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                                <th class="px-4 py-3 font-semibold whitespace-nowrap">No</th>
                                <th class="px-4 py-3 font-semibold whitespace-nowrap">Nama Layer</th>
                                <th class="px-4 py-3 font-semibold whitespace-nowrap">Jenis Layer</th>
                                <th class="px-4 py-3 font-semibold whitespace-nowrap">File</th>
                                <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 font-semibold whitespace-nowrap">Dibuat</th>
                                <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="overlayTableBody">
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-spinner fa-pulse text-3xl text-violet-400"></i>
                                        <p class="text-slate-400 font-medium">Memuat data overlay...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-400 flex justify-center items-center gap-2 flex-wrap">
                <i class="fas fa-globe-asia"></i>
                <span>Sistem Informasi Geografis Terpadu</span>
                <span>•</span>
                <span>Kabupaten Bengkalis</span>
            </div>
        </main>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // ==================== TOAST ====================
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastIcon = document.getElementById('toastIcon');
            const toastMessage = document.getElementById('toastMessage');
            const toastDiv = toast.querySelector('div');
            
            if (type === 'success') {
                toastIcon.className = 'fas fa-check-circle text-lg text-emerald-500';
                toastDiv.style.borderLeftColor = '#22c55e';
            } else {
                toastIcon.className = 'fas fa-exclamation-triangle text-lg text-red-500';
                toastDiv.style.borderLeftColor = '#ef4444';
            }
            
            toastMessage.innerText = message;
            toast.classList.remove('hidden');
            setTimeout(() => hideToast(), 4000);
        }
        
        function hideToast() {
            document.getElementById('toast').classList.add('hidden');
        }

        // ==================== DRAWER ====================
        function openDrawer() {
            document.getElementById('drawer').classList.add('open');
            document.getElementById('drawerOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            document.getElementById('drawer').classList.remove('open');
            document.getElementById('drawerOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }

        // ==================== MODAL TAMBAH LAHAN ====================
        function openModalTambahLahan() {
            document.getElementById('modalTambahLahan').classList.remove('hidden');
            document.getElementById('modalTambahLahan').classList.add('flex');
            document.body.style.overflow = 'hidden';
            resetLahanForm();
        }

        function closeModalTambahLahan() {
            document.getElementById('modalTambahLahan').classList.add('hidden');
            document.getElementById('modalTambahLahan').classList.remove('flex');
            document.body.style.overflow = '';
        }

        function resetLahanForm() {
            const fileInput = document.getElementById('lahanFileInput');
            if (fileInput) fileInput.value = '';
            const label = document.getElementById('lahanFileNameLabel');
            const icon = document.getElementById('lahanFileIcon');
            if (label) label.innerHTML = 'Klik untuk pilih file GeoJSON';
            if (icon) {
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
        }

        // LAHAN DROPZONE
        const lahanDropZone = document.getElementById('lahanDropZone');
        const lahanFileInput = document.getElementById('lahanFileInput');
        if (lahanDropZone && lahanFileInput) {
            lahanDropZone.addEventListener('click', () => lahanFileInput.click());
            lahanDropZone.addEventListener('dragover', (e) => { e.preventDefault(); lahanDropZone.classList.add('drag-over'); });
            lahanDropZone.addEventListener('dragleave', () => lahanDropZone.classList.remove('drag-over'));
            lahanDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                lahanDropZone.classList.remove('drag-over');
                if (e.dataTransfer.files.length > 0) {
                    lahanFileInput.files = e.dataTransfer.files;
                    updateLahanFileName(e.dataTransfer.files[0].name);
                }
            });
            lahanFileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) updateLahanFileName(e.target.files[0].name);
                else updateLahanFileName(null);
            });
        }

        function updateLahanFileName(filename) {
            const label = document.getElementById('lahanFileNameLabel');
            const icon = document.getElementById('lahanFileIcon');
            if (filename) {
                label.innerHTML = filename;
                label.classList.add('text-emerald-600');
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-emerald-500');
            } else {
                label.innerHTML = 'Klik untuk pilih file GeoJSON';
                label.classList.remove('text-emerald-600');
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
        }

        // FORM TAMBAH LAHAN
        const lahanForm = document.getElementById('formTambahLahan');
        if (lahanForm) {
            lahanForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = document.getElementById('btnSubmitLahan');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>Mengupload...';
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message || 'Data lahan berhasil ditambahkan!');
                        closeModalTambahLahan();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Upload gagal', 'error');
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan saat upload', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // ==================== MODAL TAMBAH BERITA ====================
        function openModalTambahBerita() {
            document.getElementById('modalTambahBerita').classList.remove('hidden');
            document.getElementById('modalTambahBerita').classList.add('flex');
            document.body.style.overflow = 'hidden';
            resetBeritaForm();
        }

        function closeModalTambahBerita() {
            document.getElementById('modalTambahBerita').classList.add('hidden');
            document.getElementById('modalTambahBerita').classList.remove('flex');
            document.body.style.overflow = '';
        }

        function resetBeritaForm() {
            const fileInput = document.getElementById('beritaFileInput');
            if (fileInput) fileInput.value = '';
            const label = document.getElementById('beritaFileNameLabel');
            const icon = document.getElementById('beritaFileIcon');
            if (label) label.innerHTML = 'Klik untuk pilih gambar';
            if (icon) {
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
            document.getElementById('imagePreviewContainer')?.classList.add('hidden');
            // Reset form fields
            const form = document.getElementById('formTambahBerita');
            if (form) form.reset();
        }

        function removeImagePreview() {
            const fileInput = document.getElementById('beritaFileInput');
            if (fileInput) fileInput.value = '';
            const label = document.getElementById('beritaFileNameLabel');
            const icon = document.getElementById('beritaFileIcon');
            if (label) label.innerHTML = 'Klik untuk pilih gambar';
            if (icon) {
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
            document.getElementById('imagePreviewContainer')?.classList.add('hidden');
        }

        // BERITA DROPZONE
        const beritaDropZone = document.getElementById('beritaDropZone');
        const beritaFileInput = document.getElementById('beritaFileInput');
        if (beritaDropZone && beritaFileInput) {
            beritaDropZone.addEventListener('click', () => beritaFileInput.click());
            beritaDropZone.addEventListener('dragover', (e) => { e.preventDefault(); beritaDropZone.classList.add('drag-over'); });
            beritaDropZone.addEventListener('dragleave', () => beritaDropZone.classList.remove('drag-over'));
            beritaDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                beritaDropZone.classList.remove('drag-over');
                if (e.dataTransfer.files.length > 0) {
                    beritaFileInput.files = e.dataTransfer.files;
                    updateBeritaFileName(e.dataTransfer.files[0]);
                }
            });
            beritaFileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) updateBeritaFileName(e.target.files[0]);
                else updateBeritaFileName(null);
            });
        }

        function updateBeritaFileName(file) {
            const label = document.getElementById('beritaFileNameLabel');
            const icon = document.getElementById('beritaFileIcon');
            if (file) {
                label.innerHTML = file.name;
                label.classList.add('text-emerald-600');
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-emerald-500');
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                label.innerHTML = 'Klik untuk pilih gambar';
                label.classList.remove('text-emerald-600');
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
                document.getElementById('imagePreviewContainer')?.classList.add('hidden');
            }
        }

        // FORM TAMBAH BERITA
        const beritaForm = document.getElementById('formTambahBerita');
        if (beritaForm) {
            beritaForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = document.getElementById('btnSubmitBerita');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>Menyimpan...';
                
                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message || 'Berita berhasil ditambahkan!');
                        closeModalTambahBerita();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan berita', 'error');
                    }
                } catch (err) {
                    console.error(err);
                    showToast('Terjadi kesalahan saat menyimpan berita', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // ==================== MODAL UPLOAD OVERLAY ====================
        function openModalUploadOverlay() {
            document.getElementById('modalUploadOverlay').classList.remove('hidden');
            document.getElementById('modalUploadOverlay').classList.add('flex');
            document.body.style.overflow = 'hidden';
            resetOverlayForm();
        }

        function closeModalUploadOverlay() {
            document.getElementById('modalUploadOverlay').classList.add('hidden');
            document.getElementById('modalUploadOverlay').classList.remove('flex');
            document.body.style.overflow = '';
        }

        function resetOverlayForm() {
            const fileInput = document.getElementById('overlayFileInput');
            if (fileInput) fileInput.value = '';
            const label = document.getElementById('overlayFileNameLabel');
            const icon = document.getElementById('overlayFileIcon');
            if (label) label.innerHTML = 'Klik untuk pilih file GeoJSON';
            if (icon) {
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
        }

        // OVERLAY DROPZONE
        const overlayDropZone = document.getElementById('overlayDropZone');
        const overlayFileInput = document.getElementById('overlayFileInput');
        if (overlayDropZone && overlayFileInput) {
            overlayDropZone.addEventListener('click', () => overlayFileInput.click());
            overlayDropZone.addEventListener('dragover', (e) => { e.preventDefault(); overlayDropZone.classList.add('drag-over'); });
            overlayDropZone.addEventListener('dragleave', () => overlayDropZone.classList.remove('drag-over'));
            overlayDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                overlayDropZone.classList.remove('drag-over');
                if (e.dataTransfer.files.length > 0) {
                    overlayFileInput.files = e.dataTransfer.files;
                    updateOverlayFileName(e.dataTransfer.files[0].name);
                }
            });
            overlayFileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) updateOverlayFileName(e.target.files[0].name);
                else updateOverlayFileName(null);
            });
        }

        function updateOverlayFileName(filename) {
            const label = document.getElementById('overlayFileNameLabel');
            const icon = document.getElementById('overlayFileIcon');
            if (filename) {
                label.innerHTML = filename;
                label.classList.add('text-emerald-600');
                icon.classList.remove('text-slate-400');
                icon.classList.add('text-emerald-500');
            } else {
                label.innerHTML = 'Klik untuk pilih file GeoJSON';
                label.classList.remove('text-emerald-600');
                icon.classList.remove('text-emerald-500');
                icon.classList.add('text-slate-400');
            }
        }

        // FORM UPLOAD OVERLAY
        const uploadOverlayForm = document.getElementById('uploadOverlayForm');
        if (uploadOverlayForm) {
            uploadOverlayForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = document.getElementById('btnSubmitUploadOverlay');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>Memproses...';
                
                try {
                    const response = await fetch('/admin/overlay/upload', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message || 'Overlay berhasil diupload');
                        closeModalUploadOverlay();
                        loadOverlays();
                    } else {
                        showToast(data.message || 'Upload gagal', 'error');
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan saat upload overlay', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // ==================== MODAL EDIT OVERLAY ====================
        function openModalEditOverlay(overlay) {
            document.getElementById('edit_overlay_id').value = overlay.id;
            document.getElementById('edit_nama_layer').value = overlay.nama_layer || overlay.nama || '';
            document.getElementById('edit_jenis_layer').value = overlay.jenis_layer || overlay.tipe || overlay.jenis || '';
            document.getElementById('edit_status').value = overlay.status || 'active';
            document.getElementById('modalEditOverlay').classList.remove('hidden');
            document.getElementById('modalEditOverlay').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModalEditOverlay() {
            document.getElementById('modalEditOverlay').classList.add('hidden');
            document.getElementById('modalEditOverlay').classList.remove('flex');
            document.body.style.overflow = '';
        }

        const editOverlayForm = document.getElementById('editOverlayForm');
        if (editOverlayForm) {
            editOverlayForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_overlay_id').value;
                const btn = this.querySelector('button[type="submit"]');
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>Menyimpan...';
                
                try {
                    const response = await fetch(`/admin/overlay/${id}`, {
                        method: 'PUT',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            nama_layer: document.getElementById('edit_nama_layer').value,
                            jenis_layer: document.getElementById('edit_jenis_layer').value,
                            status: document.getElementById('edit_status').value
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        showToast(data.message || 'Overlay berhasil diupdate');
                        closeModalEditOverlay();
                        loadOverlays();
                    } else {
                        showToast(data.message || 'Gagal mengupdate overlay', 'error');
                    }
                } catch (error) {
                    showToast('Terjadi kesalahan saat mengupdate overlay', 'error');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // ==================== TOGGLE STATUS ====================
        async function toggleOverlayStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            try {
                const response = await fetch(`/admin/overlay/${id}/toggle`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await response.json();
                if (data.success) {
                    loadOverlays();
                } else {
                    showToast(data.message || 'Gagal mengubah status', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        }

        // ==================== DELETE OVERLAY ====================
        async function deleteOverlay(id, name) {
            if (!confirm(`Hapus overlay "${name}"? Data tidak dapat dikembalikan.`)) return;
            try {
                const response = await fetch(`/admin/overlay/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' }
                });
                const data = await response.json();
                if (data.success) {
                    showToast(data.message || 'Overlay berhasil dihapus');
                    loadOverlays();
                } else {
                    showToast(data.message || 'Gagal menghapus overlay', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan saat menghapus', 'error');
            }
        }

        // ==================== LOAD OVERLAYS ====================
        async function loadOverlays() {
            const tbody = document.getElementById('overlayTableBody');
            if (!tbody) return;
            try {
                const response = await fetch('/admin/overlay/list', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat data');
                const data = await response.json();
                const now = new Date();
                document.getElementById('lastUpdateTime').innerHTML = now.toLocaleTimeString('id-ID');
                
                if (!data.success || !data.overlays || data.overlays.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-16 text-center"><div class="flex flex-col items-center gap-3"><i class="fas fa-layer-group text-6xl text-slate-300"></i><p class="text-slate-400 font-medium">Belum ada overlay yang diupload</p><button onclick="openModalUploadOverlay()" class="mt-2 bg-violet-500 hover:bg-violet-600 text-white px-4 py-2 rounded-xl text-sm font-semibold"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload Overlay Pertama</button></div></td></tr>`;
                    updateStats(0, 0, 0);
                    return;
                }
                
                const overlays = data.overlays;
                const total = overlays.length;
                const active = overlays.filter(o => o.status === 'active').length;
                const inactive = total - active;
                const uniqueJenis = new Set(overlays.map(o => o.jenis_layer || o.tipe || o.jenis).filter(Boolean)).size;
                updateStats(total, active, inactive, uniqueJenis);
                
                tbody.innerHTML = overlays.map((overlay, index) => `
                    <tr class="data-row hover:bg-violet-50 transition">
                        <td class="px-4 py-3 text-center text-slate-500">${index + 1}                        <td class="px-4 py-3"><div class="flex items-center gap-2"><div class="h-8 w-8 rounded-lg bg-violet-100 flex items-center justify-center"><i class="fas fa-map-marked-alt text-sm text-violet-600"></i></div><span class="font-semibold text-slate-700">${escapeHtml(overlay.nama_layer || overlay.nama || 'Unnamed')}</span></div></td>
                        <td class="px-4 py-3"><span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-violet-100 text-violet-700"><i class="fas fa-tag text-[10px] mr-1"></i> ${escapeHtml(overlay.jenis_layer || overlay.tipe || overlay.jenis || '-')}</span></td>
                        <td class="px-4 py-3"><code class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded">${escapeHtml((overlay.file_path || overlay.file || '-').split('/').pop())}</code></td>
                        <td class="px-4 py-3 text-center"><button onclick="toggleOverlayStatus('${overlay.id}', '${overlay.status}')" class="status-badge ${overlay.status === 'active' ? 'status-active' : 'status-inactive'} hover:opacity-80"><i class="fas ${overlay.status === 'active' ? 'fa-eye' : 'fa-eye-slash'} text-[10px]"></i> ${overlay.status === 'active' ? 'Aktif' : 'Nonaktif'}</button></td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><i class="far fa-calendar-alt mr-1"></i> ${overlay.created_at ? new Date(overlay.created_at).toLocaleDateString('id-ID') : '-'}</td>
                        <td class="px-4 py-3 text-center"><div class="flex items-center justify-center gap-2"><button onclick='openModalEditOverlay(${JSON.stringify(overlay)})' class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition"><i class="fas fa-edit text-sm"></i></button><button onclick="deleteOverlay('${overlay.id}', '${escapeHtml(overlay.nama_layer || overlay.nama || 'Layer')}')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition"><i class="fas fa-trash-alt text-sm"></i></button></div></td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error(error);
                tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-12 text-center"><div class="flex flex-col items-center gap-3"><i class="fas fa-exclamation-triangle text-4xl text-red-400"></i><p class="text-slate-500 font-medium">Gagal memuat data overlay</p><button onclick="loadOverlays()" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded-xl text-sm">Coba Lagi</button></div></td></tr>`;
                updateStats(0, 0, 0);
            }
        }

        function updateStats(total, active, inactive, uniqueJenis = 0) {
            const totalEl = document.getElementById('totalOverlayCount');
            const activeEl = document.getElementById('activeOverlayCount');
            const inactiveEl = document.getElementById('inactiveOverlayCount');
            const uniqueEl = document.getElementById('uniqueJenisCount');
            
            if (totalEl) totalEl.innerText = total;
            if (activeEl) activeEl.innerText = active;
            if (inactiveEl) inactiveEl.innerText = inactive;
            if (uniqueEl) uniqueEl.innerHTML = uniqueJenis + ' <span class="text-xs font-normal">jenis</span>';
        }

        function refreshOverlays() { loadOverlays(); }
        
        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, (m) => m === '&' ? '&amp;' : (m === '<' ? '&lt;' : '&gt;'));
        }
        
        function confirmLogout() {
            if (confirm('Yakin ingin keluar?')) {
                const form = document.getElementById('logout-form') || document.getElementById('logout-form-desktop');
                if (form) form.submit();
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDrawer();
                closeModalTambahLahan();
                closeModalTambahBerita();
                closeModalUploadOverlay();
                closeModalEditOverlay();
            }
        });
        
        document.addEventListener('DOMContentLoaded', () => loadOverlays());
    </script>
</body>
</html>