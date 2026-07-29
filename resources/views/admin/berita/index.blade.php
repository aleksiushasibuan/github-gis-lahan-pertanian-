{{-- resources/views/admin/berita/index.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI Lahan Terpadu - Kelola Berita</title>

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
        .data-row:hover { background: #eff6ff; }

        .btn-animate { transition: all 0.2s ease; }
        .btn-animate:active { transform: scale(0.97); }

        .data-table td, .data-table th { white-space: nowrap; padding: 12px 16px; }
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .counter-number { font-size: 1.75rem !important; }
            .data-table td, .data-table th { padding: 8px 12px !important; font-size: 12px !important; }
            .header-title { font-size: 1.25rem !important; }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .counter-number { font-size: 2rem !important; }
            .data-table td, .data-table th { padding: 10px 14px !important; font-size: 13px !important; }
        }

        .dropzone-area { transition: all 0.2s ease; cursor: pointer; }
        .dropzone-area.drag-over { border-color: #2563eb !important; background-color: #eff6ff !important; }

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

        .badge-kategori {
            background: #dbeafe;
            color: #1d4ed8;
        }

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
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    background: white;
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 4px;
    background: linear-gradient(
        90deg,
        #3b82f6,
        #06b6d4,
        #10b981
    );
    transition: left 0.4s ease;
}

.stat-card-modern:hover::before {
    left: 0;
}

.stat-card-modern:hover {
    transform: translateY(-6px);
    box-shadow: 0 25px 35px -12px rgba(0,0,0,0.15);
}
    </style>
</head>
<body>

    <!-- ==================== MODAL TAMBAH LAHAN ==================== -->
    <div id="modalTambahLahan" class="modal fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalTambahLahan()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-emerald-700 to-teal-700 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">🌾 Tambah Data Lahan</h2>
                    <p class="text-slate-200 text-sm">Upload file GeoJSON, data lahan akan terbaca otomatis</p>
                </div>
                <button type="button" onclick="closeModalTambahLahan()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-8">
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 mb-6 rounded flex items-start gap-3">
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
                        <div id="lahanDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:border-emerald-400 transition-all bg-slate-50 dropzone-area">
                            <i class="fas fa-file-code text-5xl text-slate-400 mb-4 block transition-colors" id="lahanFileIcon"></i>
                            <span class="text-sm font-bold text-emerald-600 block" id="lahanFileNameLabel">Klik untuk pilih file GeoJSON</span>
                            <p class="text-xs text-slate-400 mt-2">Maksimal 10MB • Format .json / .geojson</p>
                            <input type="file" name="geojson" id="lahanFileInput" accept=".json,.geojson" required class="hidden">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModalTambahLahan()" class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">Batal</button>
                        <button type="submit" id="btnSubmitLahan" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
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
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-800 to-indigo-800 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">📰 Tambah Berita Baru</h2>
                    <p class="text-slate-300 text-sm">Tambah informasi kegiatan pertanian terbaru</p>
                </div>
                <button type="button" onclick="closeModalTambahBerita()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8">
                <form id="ajaxBeritaForm" action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
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
                            <div id="imageDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-blue-400 transition-all bg-slate-50 cursor-pointer dropzone-area">
                                <i class="fas fa-image text-4xl text-slate-400 mb-3 block" id="imageIcon"></i>
                                <span class="text-sm font-medium text-blue-600 block" id="imageFileNameLabel">Klik untuk pilih gambar</span>
                                <p class="text-xs text-slate-400 mt-2">Maksimal 2MB • JPG, JPEG, PNG</p>
                            </div>
                            <input type="file" name="gambar" accept="image/jpeg,image/png,image/jpg" class="hidden" id="gambarInput">
                            <div id="imagePreviewContainer" class="mt-4 hidden">
                                <div class="relative inline-block">
                                    <img id="imagePreview" src="" alt="Preview" class="w-32 h-32 object-cover rounded-xl shadow-md">
                                    <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Isi Berita <span class="text-red-500">*</span></label>
                            <textarea name="isi" rows="8" required
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                                placeholder="Tuliskan isi berita di sini..."></textarea>
                        </div>
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

    <!-- ==================== MODAL EDIT BERITA ==================== -->
    <div id="modalEditBerita" class="modal fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalEditBerita()"></div>
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">✏️ Edit Berita</h2>
                    <p class="text-slate-200 text-sm">Ubah informasi berita</p>
                </div>
                <button type="button" onclick="closeModalEditBerita()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8">
                <form id="ajaxEditBeritaForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Berita</label>
                            <input type="text" name="judul" id="edit_judul" required
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                            <input type="text" name="kategori" id="edit_kategori"
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Ganti Gambar</label>
                            <div id="editImageDropZone" class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-amber-400 transition-all bg-slate-50 cursor-pointer dropzone-area">
                                <i class="fas fa-image text-4xl text-slate-400 mb-3 block" id="editImageIcon"></i>
                                <span class="text-sm font-medium text-amber-600 block" id="editImageFileNameLabel">Klik untuk pilih gambar</span>
                            </div>
                            <input type="file" name="gambar" accept="image/jpeg,image/png,image/jpg" class="hidden" id="editGambarInput">

                            <div id="editCurrentImageContainer" class="mt-4 hidden">
                                <p class="text-xs text-slate-500 mb-2">Gambar saat ini:</p>
                                <img id="editCurrentImage" src="" alt="Gambar saat ini" class="w-32 h-32 object-cover rounded-xl shadow-md">
                            </div>

                            <div id="editImagePreviewContainer" class="mt-4 hidden">
                                <p class="text-xs text-slate-500 mb-2">Gambar baru:</p>
                                <div class="relative inline-block">
                                    <img id="editImagePreview" src="" alt="Preview gambar baru" class="w-32 h-32 object-cover rounded-xl shadow-md">
                                    <button type="button" onclick="removeEditImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs">✕</button>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Isi Berita</label>
                            <textarea name="isi" id="edit_isi" rows="8" required
                                class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t flex justify-between">
                        <button type="button" onclick="closeModalEditBerita()" class="text-gray-500 hover:text-gray-700">Batal</button>
                        <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-xl font-semibold">Update</button>
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
                        </ul>
                    </div>
                </div>
                <form id="uploadOverlayForm" class="space-y-5">
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
            <button type="button" onclick="openModalUploadOverlay(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400"></i>
                <span class="text-sm">Upload Overlay</span>
            </button>
            <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-layer-group w-5 text-center text-fuchsia-400"></i>
                <span class="text-sm">Kelola Overlay</span>
            </a>
            <button type="button" onclick="openModalTambahBerita(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-plus-square w-5 text-center text-blue-400"></i>
                <span class="text-sm">Tambah Berita</span>
            </button>
            <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600/30 text-blue-300 font-medium transition-all">
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
                <i class="fas fa-newspaper text-blue-400"></i>
                <span class="font-semibold text-sm">Kelola Berita</span>
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
                <button type="button" onclick="openModalUploadOverlay()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400 group-hover:text-violet-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Upload Overlay</span>
                </button>
                <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-layer-group w-5 text-center text-fuchsia-400 group-hover:text-fuchsia-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Overlay</span>
                </a>
                <button type="button" onclick="openModalTambahBerita()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-plus-square w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Tambah Berita</span>
                </button>
                <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 shadow-md shadow-blue-500/20 font-medium transition-all">
                    <i class="fas fa-newspaper w-5 text-center"></i>
                    <span>Kelola Berita</span>
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
            <!-- ALERT MESSAGES -->
            @if(session('success'))
            <div class="alert alert-success mb-4 animate-scaleIn">
                <i class="fas fa-check-circle text-lg"></i>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger mb-4 animate-scaleIn">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto">&times;</button>
            </div>
            @endif

            <!-- HEADER -->
            <div class="mb-6 animate-fadeInUp">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-blue-600 font-medium mb-1">
                            <i class="fas fa-newspaper"></i>
                            <span>Admin / Kelola Berita</span>
                        </div>
                        <h2 class="header-title text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800">📰 Kelola Berita</h2>
                        <p class="text-slate-500 text-xs md:text-sm mt-1">Kelola publikasi berita dan informasi pertanian</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6 lg:gap-8 mb-8">
                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
            <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Berita</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalBeritaCount ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-newspaper text-[9px] sm:text-[10px] mr-1"></i> semua publikasi
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-200 flex items-center justify-center">
                            <i class="fas fa-newspaper text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Bulan Ini</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $beritaBulanIni ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-calendar-check text-[9px] sm:text-[10px] mr-1"></i> publikasi terbaru
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-200 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Kategori Aktif</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalKategori ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-tags text-[9px] sm:text-[10px] mr-1"></i> kategori berita
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-200 flex items-center justify-center">
                            <i class="fas fa-tags text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTER -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn mb-6">
                <div class="px-4 sm:px-5 md:px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <form method="GET" action="{{ route('admin.berita.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Berita</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, isi, atau kategori..."
                                    class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kategori</label>
                            <select name="kategori" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                @foreach(($kategoriList ?? []) as $kategori)
                                    <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                        {{ $kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl">Filter</button>
                            <a href="{{ route('admin.berita.index') }}" class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold py-3 px-4 rounded-xl">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL BERITA -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn">
                <div class="px-4 sm:px-5 md:px-6 py-3 sm:py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-table-list text-blue-500 text-base sm:text-lg"></i>
                        <h3 class="font-semibold text-slate-700 text-sm sm:text-base">📋 Daftar Berita</h3>
                    </div>
                    <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-2 sm:px-3 py-1 rounded-full">
                        <i class="far fa-clock mr-1"></i> realtime
                    </span>
                </div>

                <div class="table-wrapper overflow-x-auto">
                    <table class="data-table w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">No</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Gambar</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Judul</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Kategori</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Ringkasan</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Tanggal</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($beritas as $index => $berita)
                            <tr class="data-row">
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap font-semibold text-slate-700">
                                    {{ $beritas->firstItem() + $index }}
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    @if($berita->gambar)
                                        <img src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}" class="w-16 h-12 rounded-lg object-cover border border-slate-200">
                                    @else
                                        <div class="w-16 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 min-w-[250px]">
                                    <div class="font-semibold text-slate-800 line-clamp-2">{{ $berita->judul }}</div>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold badge-kategori">
                                        <i class="fas fa-tag text-[10px]"></i>
                                        {{ $berita->kategori ?: 'Umum' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 min-w-[280px] text-slate-600">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($berita->isi), 100) }}
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap text-slate-600">
                                    {{ $berita->created_at ? $berita->created_at->format('d M Y') : '-' }}
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="editBerita({{ $berita->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <a href="{{ route('berita.detail', $berita->id) }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white transition-all duration-200">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <button type="button" onclick="confirmDeleteBerita({{ $berita->id }}, '{{ addslashes($berita->judul) }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-newspaper text-5xl text-slate-300"></i>
                                        <p class="text-slate-400 font-medium">Belum ada data berita tersedia</p>
                                        <button type="button" onclick="openModalTambahBerita()" class="text-blue-500 text-sm underline underline-offset-2 cursor-pointer">+ Tambah berita pertama</button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($beritas, 'links') && $beritas->hasPages())
                <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50">
                    {{ $beritas->links() }}
                </div>
                @endif
            </div>

            <div class="mt-6 md:mt-8 text-center text-[10px] sm:text-xs text-slate-400 flex justify-center items-center gap-1 sm:gap-2 flex-wrap">
                <i class="fas fa-globe-asia"></i>
                <span>Sistem Informasi Geografis Terpadu</span>
                <span class="hidden sm:inline">•</span>
                <span>Kabupaten Bengkalis</span>
            </div>
        </main>
    </div>

    <form id="delete-berita-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

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
            const modal = document.getElementById('modalTambahBerita');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            const form = document.getElementById('ajaxBeritaForm');
            if (form) form.reset();
            removeImage();
        }

        function closeModalTambahBerita() {
            const modal = document.getElementById('modalTambahBerita');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function closeModalEditBerita() {
            const modal = document.getElementById('modalEditBerita');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // BERITA IMAGE DROPZONE
        const imageDropZone = document.getElementById('imageDropZone');
        const gambarInput = document.getElementById('gambarInput');
        const imageFileNameLabel = document.getElementById('imageFileNameLabel');
        const imageIcon = document.getElementById('imageIcon');
        const imagePreviewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');

        if (imageDropZone && gambarInput) {
            imageDropZone.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                gambarInput.click();
            });

            imageDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                imageDropZone.classList.add('drag-over');
            });

            imageDropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                imageDropZone.classList.remove('drag-over');
            });

            imageDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                imageDropZone.classList.remove('drag-over');
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    gambarInput.files = e.dataTransfer.files;
                    handleImageChange();
                }
            });

            gambarInput.addEventListener('change', handleImageChange);
        }

        function handleImageChange() {
            if (gambarInput.files && gambarInput.files[0]) {
                const file = gambarInput.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau JPEG.');
                    gambarInput.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    gambarInput.value = '';
                    return;
                }

                imageFileNameLabel.innerHTML = file.name;
                imageIcon.classList.remove('text-slate-400');
                imageIcon.classList.add('text-emerald-500');

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                removeImage();
            }
        }

        function removeImage() {
            if (gambarInput) gambarInput.value = '';
            if (imageFileNameLabel) imageFileNameLabel.innerHTML = 'Klik untuk pilih gambar';
            if (imageIcon) {
                imageIcon.classList.remove('text-emerald-500');
                imageIcon.classList.add('text-slate-400');
            }
            if (imagePreviewContainer) imagePreviewContainer.classList.add('hidden');
            if (imagePreview) imagePreview.src = '';
        }

        // FORM TAMBAH BERITA
        const beritaForm = document.getElementById('ajaxBeritaForm');
        if (beritaForm) {
            beritaForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = document.getElementById('btnSubmitBerita');

                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Menyimpan...';
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Berita berhasil ditambahkan!');
                        closeModalTambahBerita();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal menyimpan berita', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan saat menyimpan berita', 'error');
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Publikasikan';
                    }
                });
            });
        }

        // ==================== EDIT BERITA ====================
        function editBerita(id) {
            fetch(`/admin/berita/${id}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_id').value = data.berita.id;
                    document.getElementById('edit_judul').value = data.berita.judul ?? '';
                    document.getElementById('edit_kategori').value = data.berita.kategori ?? '';
                    document.getElementById('edit_isi').value = data.berita.isi ?? '';

                    if (data.berita.gambar) {
                        document.getElementById('editCurrentImage').src = '/storage/' + data.berita.gambar;
                        document.getElementById('editCurrentImageContainer').classList.remove('hidden');
                    } else {
                        document.getElementById('editCurrentImageContainer').classList.add('hidden');
                    }

                    document.getElementById('ajaxEditBeritaForm').action = `/admin/berita/${id}`;
                    document.getElementById('modalEditBerita').classList.remove('hidden');
                    document.getElementById('modalEditBerita').classList.add('flex');
                    document.body.style.overflow = 'hidden';
                } else {
                    showToast('Gagal mengambil data berita', 'error');
                }
            })
            .catch(() => showToast('Terjadi kesalahan saat mengambil data berita', 'error'));
        }

        // EDIT BERITA IMAGE DROPZONE
        const editImageDropZone = document.getElementById('editImageDropZone');
        const editGambarInput = document.getElementById('editGambarInput');
        const editImageFileNameLabel = document.getElementById('editImageFileNameLabel');
        const editImagePreviewContainer = document.getElementById('editImagePreviewContainer');
        const editImagePreview = document.getElementById('editImagePreview');

        if (editImageDropZone && editGambarInput) {
            editImageDropZone.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                editGambarInput.click();
            });

            editImageDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                editImageDropZone.classList.add('drag-over');
            });

            editImageDropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                editImageDropZone.classList.remove('drag-over');
            });

            editImageDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                editImageDropZone.classList.remove('drag-over');
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    editGambarInput.files = e.dataTransfer.files;
                    handleEditImageChange();
                }
            });

            editGambarInput.addEventListener('change', handleEditImageChange);
        }

        function handleEditImageChange() {
            if (editGambarInput.files && editGambarInput.files[0]) {
                const file = editGambarInput.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau JPEG.');
                    editGambarInput.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    editGambarInput.value = '';
                    return;
                }

                editImageFileNameLabel.innerHTML = file.name;
                const reader = new FileReader();
                reader.onload = function(e) {
                    editImagePreview.src = e.target.result;
                    editImagePreviewContainer.classList.remove('hidden');
                    document.getElementById('editCurrentImageContainer').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                removeEditImage();
            }
        }

        function removeEditImage() {
            if (editGambarInput) editGambarInput.value = '';
            if (editImageFileNameLabel) editImageFileNameLabel.innerHTML = 'Klik untuk pilih gambar';
            if (editImagePreviewContainer) editImagePreviewContainer.classList.add('hidden');
            if (document.getElementById('editCurrentImageContainer')) {
                document.getElementById('editCurrentImageContainer').classList.remove('hidden');
            }
        }

        // FORM EDIT BERITA
        const editForm = document.getElementById('ajaxEditBeritaForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const btn = this.querySelector('button[type="submit"]');

                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Menyimpan...';
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Berita berhasil diupdate!');
                        closeModalEditBerita();
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showToast(data.message || 'Gagal mengupdate berita', 'error');
                    }
                })
                .catch(() => {
                    showToast('Terjadi kesalahan saat mengupdate berita', 'error');
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Update';
                    }
                });
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

        // ==================== DELETE BERITA ====================
        function confirmDeleteBerita(id, judul) {
            if (confirm(`Hapus berita "${judul}"?`)) {
                const form = document.getElementById('delete-berita-form');
                form.action = `/admin/berita/${id}`;
                form.submit();
            }
        }

        // ==================== LOGOUT ====================
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari dashboard?')) {
                const logoutForm = document.getElementById('logout-form') || document.getElementById('logout-form-desktop');
                if (logoutForm) logoutForm.submit();
            }
        }

        // ==================== ESC KEY ====================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDrawer();
                closeModalTambahLahan();
                closeModalTambahBerita();
                closeModalEditBerita();
                closeModalUploadOverlay();
            }
        });
    </script>
</body>
</html>