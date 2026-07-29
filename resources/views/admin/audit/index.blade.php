<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI Lahan Terpadu - Audit Log</title>

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

        @media (max-width: 480px) {
            .counter-number { font-size: 1.75rem !important; }
            .data-table td, .data-table th { padding: 8px 12px !important; font-size: 12px !important; }
            .header-title { font-size: 1.25rem !important; }
        }

        @media (min-width: 481px) and (max-width: 768px) {
            .counter-number { font-size: 2rem !important; }
            .data-table td, .data-table th { padding: 10px 14px !important; font-size: 13px !important; }
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

        .badge-aksi {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-create { background: #dcfce7; color: #166534; }
        .badge-update { background: #fef3c7; color: #92400e; }
        .badge-delete { background: #fee2e2; color: #991b1b; }
        .badge-upload { background: #e0e7ff; color: #3730a3; }
        .badge-login { background: #ccfbf1; color: #0f766e; }
        .badge-default { background: #f1f5f9; color: #475569; }

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
            background: linear-gradient(90deg, #06b6d4, #3b82f6, #8b5cf6);
            transition: left 0.4s ease;
        }

        .stat-card-modern:hover::before {
            left: 0;
        }

        .stat-card-modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 35px -12px rgba(0,0,0,0.15);
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
    </style>
</head>
<body>

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
            <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-plus-circle w-5 text-center text-emerald-400"></i>
                <span class="text-sm">Tambah Lahan</span>
            </button>
            <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400"></i>
                <span class="text-sm">Upload Overlay</span>
            </button>
            <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-layer-group w-5 text-center text-fuchsia-400"></i>
                <span class="text-sm">Kelola Overlay</span>
            </a>
            <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-plus-square w-5 text-center text-blue-400"></i>
                <span class="text-sm">Tambah Berita</span>
            </button>
            <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all text-slate-300">
                <i class="fas fa-newspaper w-5 text-center text-blue-400"></i>
                <span class="text-sm">Kelola Berita</span>
            </a>
            <a href="{{ route('admin.audit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-cyan-600/30 text-cyan-300 font-medium transition-all">
                <i class="fas fa-history w-5 text-center"></i>
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
                <i class="fas fa-history text-cyan-400"></i>
                <span class="font-semibold text-sm">Audit Log</span>
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
                <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-plus-circle w-5 text-center text-emerald-400 group-hover:text-emerald-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Tambah Lahan</span>
                </button>
                <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-cloud-upload-alt w-5 text-center text-violet-400 group-hover:text-violet-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Upload Overlay</span>
                </button>
                <a href="{{ route('admin.overlay.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-layer-group w-5 text-center text-fuchsia-400 group-hover:text-fuchsia-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Overlay</span>
                </a>
                <button type="button" onclick="window.location.href='{{ route('admin.dashboard') }}'" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-plus-square w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Tambah Berita</span>
                </button>
                <a href="{{ route('admin.berita.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all group">
                    <i class="fas fa-newspaper w-5 text-center text-blue-400 group-hover:text-blue-300"></i>
                    <span class="text-slate-300 group-hover:text-white">Kelola Berita</span>
                </a>
                <a href="{{ route('admin.audit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-700 shadow-md shadow-cyan-500/20 font-medium transition-all">
                    <i class="fas fa-history w-5 text-center"></i>
                    <span>Audit Log</span>
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
                        <div class="flex items-center gap-2 text-xs text-cyan-600 font-medium mb-1">
                            <i class="fas fa-history"></i>
                            <span>Admin / Audit Log</span>
                        </div>
                        <h2 class="header-title text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-800">📋 Audit Log Sistem</h2>
                        <p class="text-slate-500 text-xs md:text-sm mt-1">Riwayat lengkap aktivitas pengguna dalam sistem</p>
                    </div>
                    <div class="flex gap-3 w-full md:w-auto flex-wrap">
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 md:gap-6 lg:gap-8 mb-8">
                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Penambahan Data</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $statistics['CREATE'] ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-plus-circle text-[9px] sm:text-[10px] mr-1"></i> CREATE
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-200 flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Perubahan Data</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $statistics['UPDATE'] ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-edit text-[9px] sm:text-[10px] mr-1"></i> UPDATE
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-200 flex items-center justify-center">
                            <i class="fas fa-edit text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Penghapusan Data</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $statistics['DELETE'] ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-red-600 bg-red-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-trash-alt text-[9px] sm:text-[10px] mr-1"></i> DELETE
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-rose-600 shadow-lg shadow-red-200 flex items-center justify-center">
                            <i class="fas fa-trash-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Upload File</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $statistics['UPLOAD'] ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-cloud-upload-alt text-[9px] sm:text-[10px] mr-1"></i> UPLOAD
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-200 flex items-center justify-center">
                            <i class="fas fa-cloud-upload-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-slate-100 stat-card-modern">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-xs sm:text-sm font-semibold text-slate-400 uppercase tracking-wider mb-2">Login Sistem</p>
                            <div class="counter-number text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 tracking-tight">{{ $statistics['LOGIN'] ?? 0 }}</div>
                            <div class="flex items-center gap-1 mt-3">
                                <span class="text-[10px] sm:text-xs text-teal-600 bg-teal-50 px-2.5 py-1 rounded-full font-medium">
                                    <i class="fas fa-sign-in-alt text-[9px] sm:text-[10px] mr-1"></i> LOGIN
                                </span>
                            </div>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 shadow-lg shadow-teal-200 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTER -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn mb-6">
                <div class="px-4 sm:px-5 md:px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Cari Aktivitas</label>
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="searchInput" placeholder="Cari user, aksi, atau modul..."
                                    class="w-full pl-10 pr-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Aksi</label>
                            <select id="filterAksi" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-cyan-500 bg-white">
                                <option value="">Semua Aksi</option>
                                <option value="CREATE">CREATE</option>
                                <option value="UPDATE">UPDATE</option>
                                <option value="DELETE">DELETE</option>
                                <option value="UPLOAD">UPLOAD</option>
                                <option value="LOGIN">LOGIN</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Tanggal</label>
                            <input type="date" id="filterDate" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-cyan-500">
                        </div>
                    </div>
                    <div class="flex justify-end mt-3">
                        <button onclick="resetFilters()" class="text-slate-500 hover:text-slate-700 text-sm px-3 py-2">
                            <i class="fas fa-undo-alt mr-1"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABEL AUDIT LOG -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-100 animate-scaleIn">
                <div class="px-4 sm:px-5 md:px-6 py-3 sm:py-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-cyan-500 text-base sm:text-lg"></i>
                        <h3 class="font-semibold text-slate-700 text-sm sm:text-base">📋 Riwayat Aktivitas</h3>
                    </div>
                    <span class="text-[10px] sm:text-xs text-slate-400 bg-slate-100 px-2 sm:px-3 py-1 rounded-full">
                        <i class="far fa-clock mr-1"></i> realtime
                    </span>
                </div>

                <div class="table-wrapper overflow-x-auto">
                    <table class="data-table w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600 text-xs uppercase tracking-wider border-b border-slate-200">
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Tanggal & Waktu</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">User</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Aksi</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Modul</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">Deskripsi</th>
                                <th class="px-3 sm:px-4 md:px-5 py-3 font-semibold whitespace-nowrap">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="logTableBody">
                            @forelse($logs as $log)
                            <tr class="data-row" data-aksi="{{ $log->aksi }}" data-tanggal="{{ $log->created_at->format('Y-m-d') }}" data-user="{{ strtolower($log->user_name) }}" data-modul="{{ strtolower($log->modul) }}">
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap text-slate-600 text-sm">
                                    <div class="flex flex-col">
                                        <span>{{ $log->created_at->format('d/m/Y') }}</span>
                                        <span class="text-xs text-slate-400">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-full bg-gradient-to-r from-cyan-100 to-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user-circle text-cyan-600 text-sm"></i>
                                        </div>
                                        <span class="font-medium text-slate-700">{{ $log->user_name }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    @php
                                        $badgeClass = 'badge-default';
                                        $icon = 'fa-tag';
                                        if ($log->aksi == 'CREATE') {
                                            $badgeClass = 'badge-create';
                                            $icon = 'fa-plus-circle';
                                        } elseif ($log->aksi == 'UPDATE') {
                                            $badgeClass = 'badge-update';
                                            $icon = 'fa-edit';
                                        } elseif ($log->aksi == 'DELETE') {
                                            $badgeClass = 'badge-delete';
                                            $icon = 'fa-trash-alt';
                                        } elseif ($log->aksi == 'UPLOAD') {
                                            $badgeClass = 'badge-upload';
                                            $icon = 'fa-cloud-upload-alt';
                                        } elseif ($log->aksi == 'LOGIN') {
                                            $badgeClass = 'badge-login';
                                            $icon = 'fa-sign-in-alt';
                                        }
                                    @endphp
                                    <span class="badge-aksi {{ $badgeClass }}">
                                        <i class="fas {{ $icon }} text-xs"></i>
                                        {{ $log->aksi }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-mono">
                                        {{ $log->modul }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 min-w-[280px]">
                                    <span class="text-slate-600 text-sm">{{ $log->deskripsi }}</span>
                                </td>
                                <td class="px-3 sm:px-4 md:px-5 py-3 whitespace-nowrap">
                                    <code class="text-xs bg-slate-100 px-2 py-1 rounded">{{ $log->ip_address }}</code>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i class="fas fa-history text-5xl text-slate-300"></i>
                                        <p class="text-slate-400 font-medium">Belum ada aktivitas tercatat</p>
                                        <p class="text-slate-300 text-sm">Aktivitas pengguna akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($logs, 'links') && $logs->hasPages())
                <div class="px-4 sm:px-6 py-3 border-t border-slate-100 bg-slate-50">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>

            <div class="mt-6 md:mt-8 text-center text-[10px] sm:text-xs text-slate-400 flex justify-center items-center gap-1 sm:gap-2 flex-wrap">
                <i class="fas fa-shield-alt"></i>
                <span>Sistem Informasi Geografis Terpadu - Audit Trail</span>
                <span class="hidden sm:inline">•</span>
                <span>Kabupaten Bengkalis</span>
            </div>
        </main>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div id="toast" class="toast-notification hidden">
        <div class="bg-white rounded-xl shadow-2xl p-4 flex items-center gap-3 border-l-4 min-w-[280px]">
            <i id="toastIcon" class="fas fa-check-circle text-lg"></i>
            <span id="toastMessage" class="text-sm font-medium flex-1"></span>
            <button onclick="hideToast()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
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

        // ==================== FILTER FUNCTIONS ====================
        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filterAksi = document.getElementById('filterAksi').value;
            const filterDate = document.getElementById('filterDate').value;
            
            const rows = document.querySelectorAll('#logTableBody tr');
            
            rows.forEach(row => {
                if (row.cells && row.cells.length > 0) {
                    const user = row.getAttribute('data-user') || '';
                    const aksi = row.getAttribute('data-aksi') || '';
                    const modul = row.getAttribute('data-modul') || '';
                    const tanggal = row.getAttribute('data-tanggal') || '';
                    
                    let show = true;
                    
                    if (searchTerm && !user.includes(searchTerm) && !aksi.toLowerCase().includes(searchTerm) && !modul.includes(searchTerm)) {
                        show = false;
                    }
                    
                    if (filterAksi && aksi !== filterAksi) {
                        show = false;
                    }
                    
                    if (filterDate && tanggal !== filterDate) {
                        show = false;
                    }
                    
                    row.style.display = show ? '' : 'none';
                }
            });
        }
        
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterAksi').value = '';
            document.getElementById('filterDate').value = '';
            filterTable();
        }
        
        function refreshLogs() {
            window.location.reload();
        }
        
        function exportToCSV() {
            const rows = document.querySelectorAll('#logTableBody tr');
            const csvData = [];
            
            csvData.push(['Tanggal', 'User', 'Aksi', 'Modul', 'Deskripsi', 'IP Address']);
            
            rows.forEach(row => {
                if (row.style.display !== 'none' && row.cells) {
                    const tanggal = row.cells[0]?.innerText.replace(/\n/g, ' ') || '';
                    const user = row.cells[1]?.innerText.replace(/\n/g, ' ') || '';
                    const aksi = row.cells[2]?.innerText.replace(/\n/g, ' ') || '';
                    const modul = row.cells[3]?.innerText || '';
                    const deskripsi = row.cells[4]?.innerText || '';
                    const ip = row.cells[5]?.innerText || '';
                    
                    csvData.push([tanggal, user, aksi, modul, deskripsi, ip]);
                }
            });
            
            const csvContent = csvData.map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(',')).join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `audit_log_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            showToast('Data berhasil diexport ke CSV');
        }

        // ==================== LOGOUT ====================
        function confirmLogout() {
            if (confirm('Apakah Anda yakin ingin keluar dari dashboard?')) {
                const logoutForm = document.getElementById('logout-form') || document.getElementById('logout-form-desktop');
                if (logoutForm) logoutForm.submit();
            }
        }

        // ==================== EVENT LISTENERS ====================
        document.getElementById('searchInput')?.addEventListener('keyup', filterTable);
        document.getElementById('filterAksi')?.addEventListener('change', filterTable);
        document.getElementById('filterDate')?.addEventListener('change', filterTable);
        
        // ESC Key Handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDrawer();
            }
        });
    </script>
</body>
</html>