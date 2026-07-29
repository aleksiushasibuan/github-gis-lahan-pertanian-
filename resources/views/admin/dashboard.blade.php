<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI Lahan Terpadu - Dashboard</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .stat-card-modern {
            background: white;
            border-radius: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.6);
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

        .card-icon-modern {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .stat-card-modern:hover .card-icon-modern { transform: scale(1.05); }

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
        .data-row:hover { background: #f0fdf4; }

        .btn-animate { transition: all 0.2s ease; }
        .btn-animate:active { transform: scale(0.97); }

        .data-table td, .data-table th { white-space: nowrap; padding: 12px 16px; }
        .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        .chart-card {
            background: white;
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 10px 25px -12px rgba(15, 23, 42, 0.12);
        }

        .chart-wrap {
            position: relative;
            height: 320px;
        }

        .overlay-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 18px;
        }

        @media (max-width: 480px) {
            .counter-number { font-size: 1.75rem !important; }
            .card-icon-modern { width: 42px !important; height: 42px !important; border-radius: 14px !important; }
            .card-icon-modern i { font-size: 1.1rem !important; }
            .stat-card-modern { padding: 14px !important; }
            .data-table td, .data-table th { padding: 8px 12px !important; font-size: 12px !important; }
            .header-title { font-size: 1.25rem !important; }
            .chart-wrap { height: 260px; }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .counter-number { font-size: 2rem !important; }
            .card-icon-modern { width: 46px !important; height: 46px !important; }
            .data-table td, .data-table th { padding: 10px 14px !important; font-size: 13px !important; }
        }

        .badge-padi { background: #dcfce7; color: #166534; }
        .badge-jagung { background: #fef3c7; color: #92400e; }
        .badge-sawit { background: #ecfccb; color: #4d7c0f; }
        .badge-karet { background: #ccfbf1; color: #0f766e; }
        .badge-default { background: #e0e7ff; color: #3730a3; }

        .dropzone-area { transition: all 0.2s ease; cursor: pointer; }
        .dropzone-area.drag-over { border-color: #3b82f6 !important; background-color: #eff6ff !important; }

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
    </style>
</head>
<body>
    @php
        use Illuminate\Support\Str;
    @endphp

    <!-- MODAL TAMBAH LAHAN -->
    <div id="modalTambahLahan" class="fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModalTambahLahan()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn">
            <div class="bg-[#129bff] p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold">🌾 Tambah Lahan Baru</h2>
                    <p class="text-white/80 text-sm">Upload file GeoJSON, data lahan akan terbaca otomatis</p>
                </div>
                <button type="button" onclick="closeModalTambahLahan()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8">
                <div class="bg-[#129bff]/10 border-l-4 border-[#129bff] text-[#129bff] p-4 mb-6 rounded flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div class="text-sm">
                        Pastikan file GeoJSON memiliki <b>properties</b> seperti:
                        <ul class="list-disc pl-5 mt-1">
                            <li>nama_desa</li>
                            <li>pemilik</li>
                            <li>jenis_tanaman</li>
                            <li>luas</li>
                        </ul>
                    </div>
                </div>
<form id="ajaxUploadForm" action="/admin/upload-gis" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-[#129bff] mb-2">File GeoJSON (.json / .geojson)</label>
                        <div id="dropZone" class="border-2 border-dashed border-slate-300 hover:border-[#129bff] rounded-xl p-8 text-center transition-all bg-slate-50 cursor-pointer">
                            <i class="fas fa-file-code text-5xl text-slate-400 mb-4 block" id="fileIcon"></i>
                            <span class="text-sm font-bold text-[#129bff] block" id="fileNameLabel">Klik untuk pilih file GeoJSON</span>
                            <p class="text-xs text-slate-400 mt-2">Maksimal 5MB • Format GeoJSON valid (WGS84)</p>
                        </div>
                        <input type="file" name="geojson" accept=".json,.geojson" required class="hidden" id="fileInput">
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <button type="button" onclick="closeModalTambahLahan()" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 text-sm font-medium transition">
                            <i class="fas fa-arrow-left"></i> Batal
                        </button>
                        <button type="submit" id="btnSubmitUpload" class="w-full md:w-auto bg-[#129bff] hover:bg-[#0f8ae6] text-white font-bold py-3 px-10 rounded-xl shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Tambah Lahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH BERITA -->
    <div id="modalTambahBerita" class="fixed inset-0 z-[1100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalTambahBerita()"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
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
                <form id="ajaxBeritaForm" action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="judul" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Masukkan judul berita">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                        <input type="text" name="kategori" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="Contoh: Kegiatan, Penyuluhan, Informasi">
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

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Isi Berita <span class="text-red-500">*</span></label>
                        <textarea name="isi" rows="8" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none" placeholder="Tuliskan isi berita di sini..."></textarea>
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

    <!-- MODAL UPLOAD OVERLAY -->
    <div id="modalOverlay" class="fixed inset-0 z-[1150] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalOverlay()"></div>
        <div class="relative bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden animate-scaleIn max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-violet-700 to-fuchsia-700 p-6 text-white flex justify-between items-center sticky top-0">
                <div>
                    <h2 class="text-xl font-bold">🗂 Upload Overlay Otomatis</h2>
                    <p class="text-slate-200 text-sm">Sistem membaca layer otomatis dari GeoJSON</p>
                </div>
                <button type="button" onclick="closeModalOverlay()" class="hover:rotate-90 transition-transform text-white/70 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-8 space-y-6">
                <div class="bg-violet-50 border-l-4 border-violet-500 text-violet-700 p-4 rounded flex items-start gap-3">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div class="text-sm">
                        File akan dipisahkan otomatis berdasarkan <code>NAMOBJ</code> dari GeoJSON.
                    </div>
                </div>

                <form id="autoOverlayForm" class="overlay-card space-y-5" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">File GeoJSON</label>
                        <input type="file" name="geojson" accept=".json,.geojson" required class="w-full px-4 py-3 border border-slate-300 rounded-xl bg-white" id="overlayFileInput">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" id="btnUploadOverlay" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Upload Overlay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT LAHAN -->
    <div id="modalEditLahan" class="fixed inset-0 z-[1200] hidden">
        <div class="absolute inset-0 z-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModalEditLahan()"></div>
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden animate-scaleIn">
                <div class="bg-gradient-to-r from-amber-600 to-orange-600 p-6 text-white flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">✏️ Edit Data Lahan</h2>
                        <p class="text-slate-100 text-sm">Ubah informasi lahan pertanian</p>
                    </div>
                    <button type="button" onclick="closeModalEditLahan()" class="text-white/80 hover:text-white text-2xl leading-none">&times;</button>
                </div>

                <div class="p-8">
                    <form id="ajaxEditLahanForm" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="edit_lahan_id">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Desa</label>
                                <input type="text" name="nama_desa" id="edit_lahan_nama_desa" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pemilik</label>
                                <input type="text" name="pemilik" id="edit_lahan_pemilik" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Luas</label>
                                <input type="number" step="any" name="luas" id="edit_lahan_luas" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Tanaman</label>
                                <input type="text" name="jenis_tanaman" id="edit_lahan_jenis_tanaman" required class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Kecamatan</label>
                                <input type="text" name="kecamatan" id="edit_lahan_kecamatan" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Poktan</label>
                                <input type="text" name="poktan" id="edit_lahan_poktan" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Kode Persil</label>
                                <input type="text" name="kode_persil" id="edit_lahan_kode_persil" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Kondisi</label>
                                <input type="text" name="kondisi" id="edit_lahan_kondisi" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Pola Ruang</label>
                                <input type="text" name="pola_ruang" id="edit_lahan_pola_ruang" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Sumber Air</label>
                                <input type="text" name="sumber_air" id="edit_lahan_sumber_air" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>

                        <div class="pt-6 border-t flex justify-between items-center">
                            <button type="button" onclick="closeModalEditLahan()" class="text-gray-500 hover:text-gray-700">Batal</button>
                            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-8 py-3 rounded-xl font-semibold">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DRAWER -->
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="p-5 border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-emerald-400 to-blue-500 flex items-center justify-center shadow-lg">
                    <i class="fas fa-map-marked-alt text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-sm">SI Lahan Terpadu</h1>
                    <p class="text-white/50 text-[10px]">Kabupaten Bengkalis </p>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600/30 text-blue-300 font-medium transition-all">
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

            <button type="button" onclick="openModalOverlay(); closeDrawer();" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400"></i>
                <span class="text-sm">Upload Overlay</span>
            </button>

            <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-layer-group w-5 text-center text-fuchsia-400"></i>
                <span class="text-sm">Kelola Overlay</span>
            </a>

            <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-newspaper w-5 text-center text-blue-400"></i>
                <span class="text-sm">Kelola Berita</span>
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
        <div class="md:hidden bg-gradient-to-r from-slate-900 to-slate-800 text-white px-4 py-3 flex items-center justify-between sticky top-0 z-50 shadow-lg">
            <button onclick="openDrawer()" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center active:bg-white/20 transition-all">
                <i class="fas fa-bars text-white text-xl"></i>
            </button>
            <div class="flex items-center gap-2">
                <i class="fas fa-map-marked-alt text-emerald-400"></i>
                <span class="font-semibold text-sm">SI Lahan Terpadu</span>
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 shadow-md shadow-blue-500/20 font-medium transition-all">
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

                <button type="button" onclick="openModalOverlay()" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400 group-hover:text-violet-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Upload Overlay</span>
                </button>

                <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-layer-group w-5 text-center text-fuchsia-400 group-hover:text-fuchsia-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Overlay</span>
                </a>

                <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-newspaper w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Berita</span>
                </a>
                    <a href="{{ route('admin.audit') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
    <i class="fas fa-history w-5 text-center text-cyan-400 group-hover:text-cyan-300"></i>
    <span class="text-slate-300 group-hover:text-white">
        Audit Log
    </span>
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

        <main class="md:ml-72 p-4 md:p-6 lg:p-8 pb-8">
            @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle text-lg"></i>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="ml-auto">&times;</button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-triangle text-lg"></i>
                <div>
                    <strong>Terjadi kesalahan:</strong>
                    <ul class="list-disc pl-5 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" class="ml-auto">&times;</button>
            </div>
            @endif

            <!-- HEADER -->
            <div class="mb-6 animate-fadeInUp">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-emerald-600 font-medium mb-1">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard / Manajemen Lahan</span>
                        </div>
                        <h2 class="header-title text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800">🗺️ Data Spasial Lahan</h2>
                        <p class="text-slate-500 text-xs md:text-sm mt-1">Kelola data lahan pertanian beserta informasi jenis tanaman</p>
                    </div>
                    <div class="flex gap-3 w-full md:w-auto flex-wrap">
                        @if(($totalLahanCount ?? 0) > 0)
                        <button onclick="confirmDeleteAll()" type="button" class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-red-500/20 transition-all btn-animate">
                            <i class="fas fa-trash-alt"></i> Hapus Semua
                        </button>
                        @endif

                        <button type="button" onclick="openModalTambahLahan()" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-emerald-500/20 transition-all btn-animate">
                            <i class="fas fa-plus-circle"></i> Tambah Lahan
                        </button>

                        <button type="button" onclick="openModalTambahBerita()" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-blue-500/20 transition-all btn-animate">
                            <i class="fas fa-newspaper"></i> Tambah Berita
                        </button>

                        <button type="button" onclick="openModalOverlay()" class="bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-700 hover:to-fuchsia-700 text-white px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 shadow-md shadow-violet-500/20 transition-all btn-animate">
                            <i class="fas fa-layer-group"></i> Upload Overlay
                        </button>
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-5 md:gap-6 lg:gap-8 mb-8">
                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                           <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Lahan</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalLahanCount ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="inline-flex items-center text-xs  text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="sm:text-[10px] mr-1"></i> keseluruhan bidang
                                </span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-200">
                            <i class="fas fa-layer-group text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Estimasi Luas</p>
                            <div class="counter-number text-2xl sm:text-3xl md:text-4x1 font-extrabold text-slate-800 tracking-tight">{{ number_format($totalLuasKeseluruhan ?? 0, 2) }}</div>
                            <div class="flex items-center gap-1 mt-3">
                              <span class="inline-flex items-center whitespace-nowrap text-xs text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full font-medium">
    <i class="fas fa-chart-area text-[10px] mr-1"></i>
    Total Hektar
</span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-200">
                            <i class="fas fa-chart-line text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Poktan</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalPoktanCount ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                               <span class="inline-flex items-center text-xs text-violet-700 bg-violet-50 px-3 py-1 rounded-xl font-medium">
                                    <i class="fas fa-users text-[9px] sm:text-[10px] mr-1"></i> kelompok tani aktif
                                </span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-violet-500 to-fuchsia-600 shadow-lg shadow-violet-200">
                            <i class="fas fa-users text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Total Berita</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalBeritaCount ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="inline-flex items-center text-xs text-blue-600 bg-blue-50 px-3 py-1 rounded-xl font-medium">
                                    <i class="fas fa-newspaper text-[9px] sm:text-[10px] mr-1"></i> publikasi aktif
                                </span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-blue-600 to-indigo-600 shadow-lg shadow-blue-200">
                            <i class="fas fa-bullhorn text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">File Spasial</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalFileSpasialCount ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="inline-flex items-center text-xs text-amber-600 bg-amber-50 px-3 py-1 rounded-xl font-medium">
                                    <i class="fas fa-map-marked-alt text-[9px] sm:text-[10px] mr-1"></i> GeoJSON tersedia
                                </span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-200">
                            <i class="fas fa-file-code text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card-modern p-5 md:p-6 shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Audit Log</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $totalAuditLog ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="inline-flex items-center text-xs text-cyan-600 bg-cyan-50 px-3 py-1 rounded-xl font-medium">
                                    <i class="fas fa-history text-[9px] sm:text-[10px] mr-1"></i> Aktivitas sistem
                                </span>
                            </div>
                        </div>
                        <div class="card-icon-modern bg-gradient-to-br from-cyan-500 to-blue-600 shadow-lg shadow-cyan-200">
                            <i class="fas fa-history text-white text-xl sm:text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHART SECTION -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
                <div class="chart-card p-5 md:p-6 animate-scaleIn">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-slate-800">📊 Luas Lahan per Desa</h3>
                            <p class="text-xs md:text-sm text-slate-500">Top desa berdasarkan total luas lahan</p>
                        </div>
                        <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Bar Chart</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="luasPerDesaChart"></canvas>
                    </div>
                </div>

                <div class="chart-card p-5 md:p-6 animate-scaleIn">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-slate-800">👥 Luas Lahan per Poktan</h3>
                            <p class="text-xs md:text-sm text-slate-500">Top poktan berdasarkan total luas lahan</p>
                        </div>
                        <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Horizontal Bar</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="luasPerPoktanChart"></canvas>
                    </div>
                </div>

                <div class="chart-card p-5 md:p-6 animate-scaleIn">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-slate-800">🌱 Komposisi Jenis Tanaman</h3>
                            <p class="text-xs md:text-sm text-slate-500">Distribusi jenis tanaman pada lahan</p>
                        </div>
                        <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Doughnut</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="jenisTanamanChart"></canvas>
                    </div>
                </div>

                <div class="chart-card p-5 md:p-6 animate-scaleIn">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-slate-800">📌 Kondisi Lahan</h3>
                            <p class="text-xs md:text-sm text-slate-500">Komposisi kondisi lahan</p>
                        </div>
                        <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Doughnut</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                </div>

                <div class="chart-card p-5 md:p-6 animate-scaleIn xl:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base md:text-lg font-bold text-slate-800">📈 Tren Input Data Lahan</h3>
                            <p class="text-xs md:text-sm text-slate-500">Perkembangan penambahan data dalam 6 bulan terakhir</p>
                        </div>
                        <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-3 py-1 rounded-full">Line Chart</span>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="trenLahanChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- TABEL LAHAN -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn">
                <div class="px-4 sm:px-5 md:px-6 py-3 sm:py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-table-list text-blue-500 text-base sm:text-lg"></i>
                        <h3 class="font-semibold text-slate-700 text-sm sm:text-base">📋 Daftar Bidang Lahan</h3>
                    </div>
                    <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-2 sm:px-3 py-1 rounded-full">
                        <i class="far fa-clock mr-1"></i> realtime
                    </span>
                </div>

                <div class="table-wrapper overflow-x-auto">
                    <table class="data-table w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Desa</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Pemilik</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Jenis Tanaman</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold text-center whitespace-nowrap">Luas (Ha)</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Kecamatan</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Poktan</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Kode Persil</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Kondisi</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Pola Ruang</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Sumber Air</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($lahans as $lahan)
                            <tr class="data-row">
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-map-pin text-xs"></i>
                                        </div>
                                        <span class="font-semibold text-slate-700">{{ $lahan->nama_desa ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">{{ $lahan->pemilik ?? '-' }}</td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    @php
                                        $jenisTanaman = $lahan->jenis_tanaman ?? $lahan->jenis ?? '-';
                                        $jenisLower = Str::lower($jenisTanaman);
                                        $badgeClass = 'badge-default';
                                        if (Str::contains($jenisLower, 'padi')) $badgeClass = 'badge-padi';
                                        elseif (Str::contains($jenisLower, 'jagung')) $badgeClass = 'badge-jagung';
                                        elseif (Str::contains($jenisLower, 'sawit')) $badgeClass = 'badge-sawit';
                                        elseif (Str::contains($jenisLower, 'karet')) $badgeClass = 'badge-karet';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        <i class="fas fa-leaf text-[10px]"></i> {{ $jenisTanaman }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 text-center whitespace-nowrap">
                                    <span class="font-bold text-slate-800">{{ number_format($lahan->luas ?? 0, 2) }}</span>
                                    <span class="text-slate-400 text-xs ml-0.5">Ha</span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">{{ $lahan->kecamatan ?? '-' }}</td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">{{ $lahan->poktan ?? '-' }}</td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <code class="text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs">{{ $lahan->kode_persil ?? '-' }}</code>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">{{ $lahan->kondisi ?? '-' }}</span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">{{ $lahan->pola_ruang ?? '-' }}</td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">{{ $lahan->sumber_air ?? '-' }}</td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="editLahan({{ $lahan->id }})" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-all duration-200">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button onclick="confirmDelete('{{ $lahan->id }}', '{{ $lahan->nama_desa }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                        <form id="delete-form-{{ $lahan->id }}" method="POST" action="{{ route('admin.lahan.destroy', $lahan->id) }}">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-map-marked-alt text-5xl text-slate-300"></i>
                                        <p class="text-slate-400 font-medium">Belum ada data lahan tersedia</p>
                                        <button type="button" onclick="openModalTambahLahan()" class="text-emerald-500 text-sm underline underline-offset-2 cursor-pointer">+ Tambah lahan pertama</button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($lahans, 'links') && $lahans->hasPages())
                <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50">
                    {{ $lahans->links() }}
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

    <form id="delete-all-form" method="POST" action="{{ route('admin.lahan.delete-all') }}" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        // ==================== DRAWER FUNCTIONS ====================
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

        // ==================== MODAL FUNCTIONS ====================
        function openModalTambahLahan() {
            const modal = document.getElementById('modalTambahLahan');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            
            const form = document.getElementById('ajaxUploadForm');
            if (form) form.reset();
            
            updateFileName(null);
        }

        function closeModalTambahLahan() {
            const modal = document.getElementById('modalTambahLahan');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

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

        function openModalOverlay() {
            const modal = document.getElementById('modalOverlay');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModalOverlay() {
            const modal = document.getElementById('modalOverlay');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function openModalEditLahan() {
            document.getElementById('modalEditLahan').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModalEditLahan() {
            document.getElementById('modalEditLahan').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ==================== DROPZONE LAHAN ====================
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const fileNameLabel = document.getElementById('fileNameLabel');
        const fileIcon = document.getElementById('fileIcon');

        if (dropZone && fileInput) {
            dropZone.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.click();
            });

            dropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                dropZone.classList.add('drag-over');
            });

            dropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
            });

            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('drag-over');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFileName(files[0].name);
                }
            });

            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    updateFileName(e.target.files[0].name);
                } else {
                    updateFileName(null);
                }
            });
        }

        function updateFileName(filename) {
            if (filename) {
                fileNameLabel.innerHTML = filename;
                fileNameLabel.classList.add('text-emerald-600');
                fileIcon.classList.remove('text-slate-400');
                fileIcon.classList.add('text-emerald-500');
            } else {
                fileNameLabel.innerHTML = 'Klik untuk pilih file GeoJSON';
                fileNameLabel.classList.remove('text-emerald-600');
                fileIcon.classList.remove('text-emerald-500');
                fileIcon.classList.add('text-slate-400');
            }
        }

        // ==================== UPLOAD LAHAN ====================
        const uploadForm = document.getElementById('ajaxUploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = document.getElementById('btnSubmitUpload');

                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Mengupload...';
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!response.ok) {
                        if (contentType.includes('application/json')) {
                            const err = await response.json();
                            throw new Error(err.message || 'Upload gagal');
                        }
                        throw new Error('Upload gagal');
                    }

                    if (contentType.includes('application/json')) {
                        return response.json();
                    }

                    return { success: true, message: 'Data lahan berhasil ditambahkan' };
                })
                .then(data => {
                    if (data.success) {
                        closeModalTambahLahan();
                        alert(data.message || 'Data lahan berhasil ditambahkan!');
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch((error) => {
                    alert(error.message || 'Terjadi kesalahan saat upload data lahan');
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Tambah Lahan';
                    }
                });
            });
        }

        // ==================== UPLOAD OVERLAY ====================
        const autoOverlayForm = document.getElementById('autoOverlayForm');
        if (autoOverlayForm) {
            autoOverlayForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = document.getElementById('btnUploadOverlay');
                const originalText = btn.innerHTML;

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-pulse mr-2"></i>Memproses...';

                try {
                    const response = await fetch('/admin/overlay/auto-upload', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message || 'Overlay berhasil diupload');
                        closeModalOverlay();
                        if (typeof loadOverlays === 'function') loadOverlays();
                    } else {
                        alert(data.message || 'Upload gagal');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Terjadi kesalahan saat upload overlay');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
        }

        // ==================== UPLOAD GAMBAR BERITA ====================
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

        // ==================== TAMBAH BERITA ====================
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
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!response.ok) {
                        if (contentType.includes('application/json')) {
                            const err = await response.json();
                            throw new Error(err.message || 'Simpan berita gagal');
                        }
                        throw new Error('Simpan berita gagal');
                    }

                    if (contentType.includes('application/json')) {
                        return response.json();
                    }

                    return { success: true, message: 'Berita berhasil ditambahkan' };
                })
                .then(data => {
                    if (data.success) {
                        closeModalTambahBerita();
                        alert(data.message || 'Berita berhasil ditambahkan!');
                        window.location.reload();
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                    }
                })
                .catch((error) => {
                    alert(error.message || 'Terjadi kesalahan saat menyimpan berita');
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Publikasikan';
                    }
                });
            });
        }

        // ==================== EDIT LAHAN ====================
        function editLahan(id) {
            fetch(`/admin/lahan/${id}/edit`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_lahan_id').value = data.lahan.id;
                    document.getElementById('edit_lahan_nama_desa').value = data.lahan.nama_desa ?? '';
                    document.getElementById('edit_lahan_pemilik').value = data.lahan.pemilik ?? '';
                    document.getElementById('edit_lahan_luas').value = data.lahan.luas ? parseFloat(data.lahan.luas).toFixed(6) : '';
                    document.getElementById('edit_lahan_jenis_tanaman').value = data.lahan.jenis_tanaman ?? data.lahan.jenis ?? '';
                    document.getElementById('edit_lahan_kecamatan').value = data.lahan.kecamatan ?? '';
                    document.getElementById('edit_lahan_poktan').value = data.lahan.poktan ?? '';
                    document.getElementById('edit_lahan_kode_persil').value = data.lahan.kode_persil ?? '';
                    document.getElementById('edit_lahan_kondisi').value = data.lahan.kondisi ?? '';
                    document.getElementById('edit_lahan_pola_ruang').value = data.lahan.pola_ruang ?? '';
                    document.getElementById('edit_lahan_sumber_air').value = data.lahan.sumber_air ?? '';

                    document.getElementById('ajaxEditLahanForm').action = `/admin/lahan/${id}`;
                    openModalEditLahan();
                } else {
                    alert('Gagal mengambil data lahan');
                }
            })
            .catch(() => alert('Gagal mengambil data lahan'));
        }

        const editLahanForm = document.getElementById('ajaxEditLahanForm');
        if (editLahanForm) {
            editLahanForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const btn = this.querySelector('button[type="submit"]');

                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = 'Menyimpan...';
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!response.ok) {
                        if (contentType.includes('application/json')) {
                            const err = await response.json();
                            throw new Error(err.message || 'Update gagal');
                        }
                        throw new Error('Update gagal');
                    }

                    if (contentType.includes('application/json')) {
                        return response.json();
                    }

                    return { success: true, message: 'Data lahan berhasil diupdate' };
                })
                .then(data => {
                    if (data.success) {
                        closeModalEditLahan();
                        alert(data.message || 'Data lahan berhasil diupdate');
                        window.location.reload();
                    } else {
                        alert('Gagal update data');
                    }
                })
                .catch((error) => alert(error.message || 'Terjadi kesalahan saat update'))
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Update';
                    }
                });
            });
        }

        // ==================== DELETE FUNCTIONS ====================
        function confirmDeleteAll() {
            const totalData = {{ $totalLahanCount ?? 0 }};
            if (totalData === 0) return;
            if (confirm(`Hapus semua ${totalData} data lahan?`)) {
                document.getElementById('delete-all-form').submit();
            }
        }

        function confirmDelete(id, nama) {
            if (confirm(`Hapus data lahan "${nama}"?`)) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function confirmLogout() {
            if (confirm('Yakin ingin keluar?')) {
                const form = document.getElementById('logout-form') || document.getElementById('logout-form-desktop');
                if (form) form.submit();
            }
        }

        // ==================== ESC KEY HANDLER ====================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDrawer();
                closeModalTambahLahan();
                closeModalTambahBerita();
                closeModalOverlay();
                closeModalEditLahan();
            }
        });

        // ==================== CHARTS ====================
        const luasPerDesaLabels = @json(isset($luasPerDesa) ? $luasPerDesa->pluck('nama_desa')->values() : []);
        const luasPerDesaData = @json(isset($luasPerDesa) ? $luasPerDesa->pluck('total')->map(fn($v) => (float)$v)->values() : []);

        const luasPerPoktanLabels = @json(isset($luasPerPoktan) ? $luasPerPoktan->pluck('poktan')->values() : []);
        const luasPerPoktanData = @json(isset($luasPerPoktan) ? $luasPerPoktan->pluck('total')->map(fn($v) => (float)$v)->values() : []);

        const jenisTanamanLabels = @json(isset($jenisTanamanChart) ? $jenisTanamanChart->pluck('jenis_tanaman')->values() : []);
        const jenisTanamanData = @json(isset($jenisTanamanChart) ? $jenisTanamanChart->pluck('total')->map(fn($v) => (int)$v)->values() : []);

        const kondisiLabels = @json(isset($kondisiChart) ? $kondisiChart->pluck('kondisi')->values() : []);
        const kondisiData = @json(isset($kondisiChart) ? $kondisiChart->pluck('total')->map(fn($v) => (int)$v)->values() : []);

        const trenLahanLabels = @json(isset($trenLahanBulanan) ? $trenLahanBulanan->pluck('bulan')->values() : []);
        const trenLahanData = @json(isset($trenLahanBulanan) ? $trenLahanBulanan->pluck('total')->map(fn($v) => (int)$v)->values() : []);

        const commonGridColor = 'rgba(148, 163, 184, 0.15)';
        const commonTickColor = '#64748b';

        // Chart Luas per Desa
        const luasCanvas = document.getElementById('luasPerDesaChart');
        if (luasCanvas && luasPerDesaLabels.length > 0) {
            new Chart(luasCanvas, {
                type: 'bar',
                data: {
                    labels: luasPerDesaLabels,
                    datasets: [{
                        label: 'Total Luas (Ha)',
                        data: luasPerDesaData,
                        backgroundColor: [
                            'rgba(59,130,246,0.85)',
                            'rgba(6,182,212,0.85)',
                            'rgba(16,185,129,0.85)',
                            'rgba(249,115,22,0.85)',
                            'rgba(139,92,246,0.85)',
                            'rgba(236,72,153,0.85)'
                        ],
                        borderRadius: 12,
                        borderSkipped: false,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: commonTickColor } },
                        y: { beginAtZero: true, grid: { color: commonGridColor }, ticks: { color: commonTickColor } }
                    }
                }
            });
        }

        // Chart Luas per Poktan
        const poktanCanvas = document.getElementById('luasPerPoktanChart');
        if (poktanCanvas && luasPerPoktanLabels.length > 0) {
            new Chart(poktanCanvas, {
                type: 'bar',
                data: {
                    labels: luasPerPoktanLabels,
                    datasets: [{
                        label: 'Total Luas per Poktan (Ha)',
                        data: luasPerPoktanData,
                        backgroundColor: [
                            'rgba(16,185,129,0.88)',
                            'rgba(59,130,246,0.88)',
                            'rgba(249,115,22,0.88)',
                            'rgba(168,85,247,0.88)',
                            'rgba(236,72,153,0.88)',
                            'rgba(20,184,166,0.88)'
                        ],
                        borderRadius: 12,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: commonGridColor }, ticks: { color: commonTickColor } },
                        y: { grid: { display: false }, ticks: { color: commonTickColor } }
                    }
                }
            });
        }

        // Chart Jenis Tanaman
        const jenisCanvas = document.getElementById('jenisTanamanChart');
        if (jenisCanvas && jenisTanamanLabels.length > 0) {
            new Chart(jenisCanvas, {
                type: 'doughnut',
                data: {
                    labels: jenisTanamanLabels,
                    datasets: [{
                        data: jenisTanamanData,
                        backgroundColor: ['#22c55e','#eab308','#3b82f6','#f97316','#8b5cf6','#14b8a6','#ef4444','#06b6d4'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: commonTickColor,
                                padding: 16
                            }
                        }
                    }
                }
            });
        }

        // Chart Kondisi
        const kondisiCanvas = document.getElementById('kondisiChart');
        if (kondisiCanvas && kondisiLabels.length > 0) {
            new Chart(kondisiCanvas, {
                type: 'doughnut',
                data: {
                    labels: kondisiLabels,
                    datasets: [{
                        data: kondisiData,
                        backgroundColor: ['#16a34a','#f59e0b','#dc2626','#3b82f6','#8b5cf6','#14b8a6'],
                        borderWidth: 0,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                color: commonTickColor,
                                padding: 16
                            }
                        }
                    }
                }
            });
        }

        // Chart Tren Lahan
        const trenCanvas = document.getElementById('trenLahanChart');
        if (trenCanvas && trenLahanLabels.length > 0) {
            new Chart(trenCanvas, {
                type: 'line',
                data: {
                    labels: trenLahanLabels,
                    datasets: [{
                        label: 'Jumlah Data',
                        data: trenLahanData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.14)',
                        fill: true,
                        tension: 0.38,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: commonTickColor } },
                        y: {
                            beginAtZero: true,
                            grid: { color: commonGridColor },
                            ticks: { color: commonTickColor, precision: 0 }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>