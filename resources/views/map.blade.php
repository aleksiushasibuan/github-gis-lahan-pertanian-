<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no, maximum-scale=1.0">
<meta name="theme-color" content="#5e361b">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Web GIS Pertanian Kabupaten Bengkalis</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ==================== SEMUA STYLE SAMA SEPERTI SEBELUMNYA ==================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --header-height: 70px;
}

body {
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
    width: 100%;
    min-height: 100vh;
    background: #f0f2f5;
    overflow-x: hidden;
}

.header {
    height: var(--header-height);
    background: #2E7D32;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 30px;
    position: relative;
    z-index: 10;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.header h3 {
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.nav-menu {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.25s ease;
}

.nav-menu a:hover {
    background: rgba(255,255,255,0.12);
}

.nav-menu a.active {
    background: rgba(255,255,255,0.18);
    font-weight: 700;
}

.nav-menu .login-btn {
    background: white;
    color: #2E7D32 !important;
    font-weight: 700;
}

.menu-toggle {
    display: none;
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 10px;
    background: rgba(255,255,255,0.14);
    color: white;
    cursor: pointer;
    font-size: 1rem;
    align-items: center;
    justify-content: center;
    margin-left: 10px;
}

.mobile-menu {
    position: fixed;
    top: calc(var(--header-height) + 8px);
    left: 12px;
    right: 12px;
    background: rgba(27, 94, 32, 0.98);
    border-radius: 14px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.18);
    z-index: 1200;
    padding: 10px;
    display: grid;
    gap: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: 0.25s ease;
}

.mobile-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.mobile-menu a {
    color: white;
    text-decoration: none;
    padding: 12px 14px;
    border-radius: 10px;
    font-size: 0.92rem;
    font-weight: 600;
}

.mobile-menu a.login-btn {
    background: white;
    color: #2E7D32 !important;
}

#map {
    width: 100%;
    height: calc(100vh - var(--header-height));
    position: relative;
    background: #e8f5e9;
}

.leaflet-control-zoom a {
    width: 50px !important;
    height: 50px !important;
    line-height: 50px !important;
    font-size: 22px !important;
    background: white !important;
    color: #2E7D32 !important;
}

.leaflet-control-home {
    background: white !important;
    border-radius: 0 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2) !important;
    margin-top: 10px !important;
}

.leaflet-control-home a {
    width: 50px !important;
    height: 50px !important;
    line-height: 50px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 22px !important;
    color: #2E7D32 !important;
    background: white !important;
    text-decoration: none !important;
}

.map-logo {
    position: absolute;
    top: 80px;
    right: 20px;
    background: rgba(255,255,255,0.95);
    padding: 10px 14px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    z-index: 1000;
    border: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.map-logo img {
    height: 65px;
    display: block;
    object-fit: contain;
}

.legend-toggle {
    position: fixed;
    bottom: 30px;
    right: 25px;
    width: 60px;
    height: 60px;
    border: none;
    background: #2E7D32;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    z-index: 1100;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border-radius: 50%;
}

.legend-panel {
    position: fixed;
    bottom: 105px;
    right: 25px;
    background: white;
    padding: 20px;
    width: 340px;
    max-width: calc(100vw - 30px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    z-index: 1100;
    border: 1px solid #e0e0e0;
    transition: all 0.2s;
    border-radius: 12px;
    max-height: 75vh;
    overflow-y: auto;
}

.legend-panel.hidden {
    display: none;
}

.legend-title {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #2E7D32;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #2E7D32;
}

.legend-section {
    margin-top: 14px;
    margin-bottom: 12px;
}

.legend-section-title {
    font-size: 0.78rem;
    font-weight: 700;
    color: #1B5E20;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 8px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
    cursor: pointer;
    padding: 6px 0;
}

.legend-item.compact {
    justify-content: space-between;
}

.legend-color {
    width: 32px;
    height: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.legend-text {
    flex: 1;
    font-size: 0.86rem;
    font-weight: 500;
    color: #333;
}

.legend-checkbox, .legend-radio {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #2E7D32;
}

.legend-select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 9px 10px;
    font-size: 0.82rem;
    outline: none;
}

.reset-filter {
    width: 100%;
    margin-top: 12px;
    padding: 10px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    font-size: 0.85rem;
    font-weight: 600;
    color: #2E7D32;
    cursor: pointer;
    border-radius: 8px;
}

.legend-stats {
    font-size: 0.78rem;
    color: #666;
    text-align: center;
    margin-top: 12px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

#loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 15px 30px;
    font-size: 16px;
    border-radius: 8px;
    z-index: 2000;
    display: none;
}

.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
}

.custom-popup .leaflet-popup-content {
    margin: 0;
    min-width: 380px;
    max-width: 480px;
}

.popup-simple {
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.popup-simple-header {
    background: #2E7D32;
    padding: 15px 20px;
    color: white;
    font-weight: 700;
    font-size: 1rem;
}

.popup-simple-header.overlay-desa {
    background: #1e40af;
}

.popup-simple-header.overlay-kecamatan {
    background: #6b7280;
}

.popup-simple-header.overlay-hutan-lindung {
    background: #059669;
}

.popup-simple-header.overlay-hutan-produksi-tetap {
    background: #7c3aed;
}

.popup-simple-header.overlay-hutan-produksi-terbatas {
    background: #ef4444;
}

.popup-simple-header.overlay-badan-air {
    background: #0284c7;
}

.popup-simple-header.overlay-perkebunan {
    background: #d97706;
}

.popup-simple-header.overlay-permukiman {
    background: #dc2626;
}

.popup-simple-header.overlay-industri {
    background: #4f46e5;
}

.popup-simple-header.overlay-pariwisata {
    background: #ec4899;
}

.popup-simple-header.overlay-lainnya {
    background: #8b5cf6;
}

.popup-simple-body {
    padding: 18px 20px;
    max-height: 500px;
    overflow-y: auto;
}

.popup-row {
    display: flex;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f0f0;
}

.popup-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.popup-label {
    width: 140px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #666;
}

.popup-label i {
    width: 22px;
    color: #2E7D32;
}

.popup-value {
    flex: 1;
    font-size: 0.85rem;
    font-weight: 500;
    color: #222;
    word-break: break-word;
}

.popup-value.highlight {
    color: #2E7D32;
    font-weight: 700;
}

.popup-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
}

.popup-chip.lindung {
    background: #fee2e2;
    color: #b91c1c;
}

.popup-chip.non-lindung {
    background: #dcfce7;
    color: #166534;
}

.leaflet-tooltip.custom-label {
    background: rgba(27, 94, 32, 0.92);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    pointer-events: none;
}

/* ==================== STYLE IKON PADI DENGAN GAMBAR (TANPA ANIMASI) ==================== */
.paddy-marker-wrapper {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    cursor: pointer;
    z-index: 1000 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.paddy-marker-wrapper:hover {
    z-index: 2000 !important;
}

.paddy-icon-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Lingkaran latar belakang ikon */
.paddy-icon-bg {
    width: 38px;
    height: 38px;
    border-radius: 100%;
    background: linear-gradient(145deg, #2E7D32, #43A047);
    box-shadow: 0 2px 10px rgba(46, 125, 50, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 2px solid rgba(255,255,255,0.3);
}

/* Gambar ikon padi */
.paddy-icon-bg img {
    width: 24px;
    height: 24px;
    object-fit: contain;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.2));
    display: block;
}

/* Versi untuk hutan lindung (MERAH) */
.paddy-icon-bg.protected {
    background: linear-gradient(145deg, #b91c1c, #dc2626) !important;
    box-shadow: 0 2px 10px rgba(185, 28, 28, 0.5) !important;
    border-color: rgba(255,255,255,0.4);
}

.paddy-icon-bg.protected img {
    filter: drop-shadow(0 1px 3px rgba(0,0,0,0.3));
}

/* ==================== STYLE LAINNYA ==================== */
.overlay-control-group {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    margin-top: 8px;
}

.overlay-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 4px;
    border-bottom: 1px solid #f3f4f6;
}

.overlay-item:last-child {
    border-bottom: none;
}

.overlay-name {
    font-size: 0.8rem;
    font-weight: 500;
    color: #374151;
}

.overlay-badge {
    font-size: 0.65rem;
    padding: 2px 6px;
    border-radius: 10px;
    background: #e5e7eb;
    color: #374151;
}

.welcome-overlay {
    position: fixed;
    inset: 0;
    background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 50%, #388E3C 100%);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 0.8s ease-out;
    cursor: pointer;
}

.welcome-overlay.fade-out {
    opacity: 0;
    pointer-events: none;
}

.welcome-content {
    text-align: center;
    color: white;
    padding: 40px;
    animation: welcomeScaleIn 0.8s ease-out;
}

@keyframes welcomeScaleIn {
    0% { opacity: 0; transform: scale(0.5); }
    100% { opacity: 1; transform: scale(1); }
}

.welcome-icon { font-size: 80px; margin-bottom: 30px; }
.welcome-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 15px; }
.welcome-subtitle { font-size: 1.2rem; margin-bottom: 30px; }
.welcome-button {
    background: white;
    color: #2E7D32;
    border: none;
    padding: 12px 32px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 50px;
    cursor: pointer;
    margin-top: 20px;
}

@media (max-width: 992px) {
    .nav-menu { display: none; }
    .menu-toggle { display: inline-flex; }
    .map-logo { top: calc(var(--header-height) + 10px); right: 12px; padding: 8px 12px; }
    .map-logo img { height: 42px; }
    .legend-panel { width: 310px; }
}

@media (max-width: 768px) {
    :root { --header-height: 58px; }
    .header { padding: 0 12px; }
    .header h3 { font-size: 0.88rem; gap: 8px; }
    .custom-popup .leaflet-popup-content { min-width: 280px; max-width: 300px; }
    .popup-label { width: 100px; font-size: 0.7rem; }
    .popup-value { font-size: 0.74rem; }
    .legend-toggle { width: 46px; height: 46px; right: 12px; bottom: 14px; font-size: 1.1rem; }
    .legend-panel { right: 10px; bottom: 68px; width: min(300px, calc(100vw - 20px)); max-height: 62vh; padding: 12px; }
    .paddy-icon-bg { width: 30px; height: 30px; }
    .paddy-icon-bg img { width: 18px; height: 18px; }
}
</style>
</head>

<body>
    <div class="header">
    <h3>
        <i class="fas fa-map-marked-alt"></i>
        WEB GIS LAHAN PERTANIAN KABUPATEN BENGKALIS
    </h3>

    <nav class="nav-menu">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('map') }}" class="active">Web GIS</a>
        <a href="{{ route('berita.index') }}">Berita</a>

        <a href="{{ route('login') }}" class="login-btn">
            Login Akun
        </a>
    </nav>

    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
</div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('map') }}" class="active">Web GIS</a>
        <a href="{{ route('berita.index') }}">Berita</a>
        @auth
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="background:none; border:none; color:white; text-align:left; padding:12px 14px; width:100%; cursor:pointer">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="login-btn">Login Akun</a>
        @endauth
    </div>

    <div id="map"></div>

    <div class="map-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.src='https://via.placeholder.com/80x65?text=LOGO'">
    </div>

    <div id="loader"><i class="fas fa-spinner fa-pulse"></i> Memuat data...</div>

    <button class="legend-toggle" id="toggleLegend"><i class="fas fa-layer-group"></i></button>

    <div class="legend-panel hidden" id="legendPanel">
        <div class="legend-title"><i class="fas fa-layer-group"></i> PENGATURAN OVERLAY</div>

        <div class="legend-section">
            <div class="legend-section-title">Basemap</div>
            <label class="legend-item compact"><span class="legend-text">OpenStreetMap</span><input type="radio" name="baseMap" value="osm" checked></label>
            <label class="legend-item compact"><span class="legend-text">Google Roadmap</span><input type="radio" name="baseMap" value="roadmap"></label>
            <label class="legend-item compact"><span class="legend-text">Google Satellite</span><input type="radio" name="baseMap" value="satellite"></label>
            <label class="legend-item compact"><span class="legend-text">Google Hybrid</span><input type="radio" name="baseMap" value="hybrid"></label>
            <label class="legend-item compact"><span class="legend-text">Google Terrain</span><input type="radio" name="baseMap" value="terrain"></label>
            <label class="legend-item compact"><span class="legend-text">Topografi</span><input type="radio" name="baseMap" value="topografi"></label>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Overlay Peta Utama</div>
            <label class="legend-item compact"><span class="legend-text">🌾 Lahan Pertanian</span><input type="checkbox" id="chkLahan" checked></label>
            <label class="legend-item compact"><span class="legend-text">🏷️ Label Lahan</span><input type="checkbox" id="chkLabel"></label>
            <label class="legend-item compact"><span class="legend-text"><img src="{{ asset('images/padi-icon.png') }}" style="width:20px;height:20px;vertical-align:middle;border-radius:4px;" onerror="this.style.display='none'"> Ikon Padi</span><input type="checkbox" id="chkIkonPadi" checked></label>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Overlay Database</div>
            <div id="additionalOverlaysList" class="overlay-control-group">
                <div class="overlay-item"><span class="overlay-name">Memuat data overlay...</span></div>
            </div>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Toggle Overlay Database</div>
            <label class="legend-item compact"><span class="legend-text">🌳 Hutan Lindung</span><input type="checkbox" id="chkOverlayHutanLindung"></label>
            <label class="legend-item compact"><span class="legend-text">🌲 Hutan Produksi Tetap</span><input type="checkbox" id="chkOverlayHutanProduksiTetap"></label>
            <label class="legend-item compact"><span class="legend-text">🌲 Hutan Produksi Terbatas</span><input type="checkbox" id="chkOverlayHutanProduksiTerbatas"></label>
            <label class="legend-item compact"><span class="legend-text">💧 Badan Air</span><input type="checkbox" id="chkOverlayBadanAir"></label>
            <label class="legend-item compact"><span class="legend-text">🌴 Perkebunan</span><input type="checkbox" id="chkOverlayPerkebunan"></label>
            <label class="legend-item compact"><span class="legend-text">🏠 Permukiman</span><input type="checkbox" id="chkOverlayPermukiman"></label>
            <label class="legend-item compact"><span class="legend-text">🏭 Industri</span><input type="checkbox" id="chkOverlayIndustri"></label>
            <label class="legend-item compact"><span class="legend-text">🏖️ Pariwisata</span><input type="checkbox" id="chkOverlayPariwisata"></label>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Tampilan warna lahan</div>
            <label class="legend-item"><span class="legend-text">Warna berdasarkan Jenis Sawah</span><input type="radio" class="legend-radio" name="colorMode" value="jenis" checked></label>
            <label class="legend-item"><span class="legend-text">Warna berdasarkan Poktan</span><input type="radio" class="legend-radio" name="colorMode" value="poktan"></label>
            <label class="legend-item"><span class="legend-text">Warna berdasarkan Kondisi</span><input type="radio" class="legend-radio" name="colorMode" value="kondisi"></label>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Filter data</div>
            <select id="filterDesa" class="legend-select"><option value="">Semua Desa</option></select>
            <div style="height:8px"></div>
            <select id="filterPoktan" class="legend-select"><option value="">Semua Poktan</option></select>
        </div>

        <div class="legend-section">
            <div class="legend-section-title">Legenda aktif</div>
            <div id="dynamicLegend"></div>
        </div>

        <button class="reset-filter" id="resetFilter">Reset Filter & Warna</button>
        <div class="legend-stats" id="legendStats">Memuat data...</div>
    </div>

    <div class="welcome-overlay" id="welcomeOverlay">
        <div class="welcome-content">
            <div class="welcome-icon"><i class="fas fa-seedling"></i></div>
            <div class="welcome-title">WEB GIS PERTANIAN</div>
            <div class="welcome-subtitle">Kabupaten Bengkalis</div>
            <div class="welcome-desc">Sistem informasi geografis lahan pertanian dengan overlay hutan lindung, batas wilayah, dan filter data.</div>
            <button class="welcome-button" id="closeWelcome"><i class="fas fa-map-marked-alt"></i> Mulai Jelajahi</button>
        </div>
    </div>

    <script>
        // ======================== INISIALISASI MAP ========================
        const center = [1.45, 102.12];
        let zoomLevel = window.innerWidth < 768 ? 10 : 13;

        const map = L.map('map', {
            center: center,
            zoom: zoomLevel,
            zoomControl: false,
            dragging: true,
            touchZoom: true,
            scrollWheelZoom: true
        });

        L.control.zoom({ position: 'topleft' }).addTo(map);

        // Basemap Layers
        const baseOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 19
        }).addTo(map);

        const googleRoadmap = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google'
        });

        const googleSatellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google Satellite'
        });

        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google Hybrid'
        });

        const googleTerrain = L.tileLayer('https://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
            maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google Terrain'
        });

        const topografiLayer = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{r}.png', {
            attribution: 'Map tiles by Stamen Design, under CC BY 3.0. Data by OpenStreetMap, under ODbL.',
            subdomains: 'abcd',
            minZoom: 0,
            maxZoom: 18,
        });

        // ======================== BUAT PANE UNTUK OVERLAY ========================
        map.createPane('overlayPane');
        map.getPane('overlayPane').style.zIndex = 400;
        
        map.createPane('lahanPane');
        map.getPane('lahanPane').style.zIndex = 450;
        
        map.createPane('labelPane');
        map.getPane('labelPane').style.zIndex = 500;
        
        map.createPane('batasPane');
        map.getPane('batasPane').style.zIndex = 430;
        
        map.createPane('markerPane');
        map.getPane('markerPane').style.zIndex = 550;

        // ======================== LAYER GROUPS ========================
        let lahanLayer = L.featureGroup();
        let desaLayer = L.featureGroup();
        let kecamatanLayer = L.featureGroup();
        let hutanLayer = L.featureGroup();
        let labelLayer = L.layerGroup();
        let padiMarkerLayer = L.layerGroup();

        // Layer untuk overlay database
        let overlayDesaLayer = L.featureGroup();
        let overlayKecamatanLayer = L.featureGroup();
        let overlayHutanLindungLayer = L.featureGroup();
        let overlayHutanProduksiTetapLayer = L.featureGroup();
        let overlayHutanProduksiTerbatasLayer = L.featureGroup();
        let overlayBadanAirLayer = L.featureGroup();
        let overlayPerkebunanLayer = L.featureGroup();
        let overlayPermukimanLayer = L.featureGroup();
        let overlayIndustriLayer = L.featureGroup();
        let overlayPariwisataLayer = L.featureGroup();
        let overlayLainnyaLayer = L.featureGroup();

        // Set pane
        lahanLayer.options.pane = 'lahanPane';
        desaLayer.options.pane = 'batasPane';
        kecamatanLayer.options.pane = 'batasPane';
        hutanLayer.options.pane = 'overlayPane';
        labelLayer.options.pane = 'labelPane';
        padiMarkerLayer.options.pane = 'markerPane';
        
        overlayDesaLayer.options.pane = 'overlayPane';
        overlayKecamatanLayer.options.pane = 'overlayPane';
        overlayHutanLindungLayer.options.pane = 'overlayPane';
        overlayHutanProduksiTetapLayer.options.pane = 'overlayPane';
        overlayHutanProduksiTerbatasLayer.options.pane = 'overlayPane';
        overlayBadanAirLayer.options.pane = 'overlayPane';
        overlayPerkebunanLayer.options.pane = 'overlayPane';
        overlayPermukimanLayer.options.pane = 'overlayPane';
        overlayIndustriLayer.options.pane = 'overlayPane';
        overlayPariwisataLayer.options.pane = 'overlayPane';
        overlayLainnyaLayer.options.pane = 'overlayPane';

        // State overlay
        let overlayStates = {
            lahan: true,
            label: false,
            ikonPadi: true,
            overlayDesa: false,
            overlayKecamatan: false,
            overlayHutanLindung: false,
            overlayHutanProduksiTetap: false,
            overlayHutanProduksiTerbatas: false,
            overlayBadanAir: false,
            overlayPerkebunan: false,
            overlayPermukiman: false,
            overlayIndustri: false,
            overlayPariwisata: false,
            overlayLainnya: false
        };

        // Global variables
        let currentGeoJsonData = null;
        let desaGeoJsonData = { type: 'FeatureCollection', features: [] };
        let kecGeoJsonData = { type: 'FeatureCollection', features: [] };
        let hutanGeoJsonData = { type: 'FeatureCollection', features: [] };
        
        let colorMode = 'jenis';
        let prioritizeProtected = true;
        let selectedDesa = '';
        let selectedPoktan = '';

        // ======================== FUNGSI HELPER ========================
        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        function normalizeValue(v, fallback = '-') {
            if (v === null || v === undefined || String(v).trim() === '') return fallback;
            return String(v);
        }

        function safeToString(value) {
            if (value === null || value === undefined) return '';
            if (typeof value === 'number') return String(value);
            return String(value);
        }

        function safeToLower(value) {
            return safeToString(value).toLowerCase();
        }

        function switchBaseLayer(layer) {
            const baseLayers = [baseOSM, googleRoadmap, googleSatellite, googleHybrid, googleTerrain, topografiLayer];
            baseLayers.forEach(base => { if (map.hasLayer(base)) map.removeLayer(base); });
            layer.addTo(map);
        }

        // ======================== FUNGSI GET OVERLAY JENIS ========================
        function getOverlayJenis(props) {
            const namobj = safeToLower(props.NAMOBJ || props.namobj || props.NAMOBJECT || props.nama || '');
            const jenisRencana = safeToLower(props.JNSRPR || props.jnsrpr || props.JENIS || props.jenis || '');
            const keterangan = safeToLower(props.REMARK || props.remark || props.KETERANGAN || props.keterangan || '');
            const kelas = safeToLower(props.KELAS || props.kelas || '');
            const type = safeToLower(props.type || '');
            
            if (namobj.includes('hutan produksi tetap') || namobj.includes('hutan_produksi_tetap') ||
                namobj.includes('produksi tetap') || 
                jenisRencana.includes('produksi tetap') || 
                keterangan.includes('produksi tetap')) {
                return 'hutan_produksi_tetap';
            }
            
            if (namobj.includes('hutan produksi terbatas') || namobj.includes('hutan_produksi_terbatas') ||
                namobj.includes('produksi terbatas') || 
                jenisRencana.includes('produksi terbatas') || 
                keterangan.includes('produksi terbatas')) {
                return 'hutan_produksi_terbatas';
            }
            
            if (namobj.includes('perkebunan') || namobj.includes('kebun') || 
                jenisRencana.includes('perkebunan') || kelas.includes('perkebunan')) {
                return 'perkebunan';
            }
            
            if (namobj.includes('permukiman') || namobj.includes('pemukiman') || 
                namobj.includes('perumahan') || namobj.includes('perdesaan') ||
                jenisRencana.includes('permukiman') || kelas.includes('permukiman')) {
                return 'permukiman';
            }
            
            if (namobj.includes('industri') || namobj.includes('pabrik') ||
                jenisRencana.includes('industri') || kelas.includes('industri')) {
                return 'industri';
            }
            
            if (namobj.includes('pariwisata') || namobj.includes('wisata') ||
                namobj.includes('objek wisata') || jenisRencana.includes('pariwisata')) {
                return 'pariwisata';
            }
            
            if (namobj.includes('sungai') || namobj.includes('danau') || namobj.includes('waduk') || 
                namobj.includes('bendungan') || namobj.includes('air') || namobj.includes('rawa') ||
                namobj.includes('empang') || namobj.includes('kolam') || namobj.includes('saluran') ||
                jenisRencana === '31010000' || jenisRencana === '31020000' ||
                kelas === 'sungai' || kelas === 'danau' || kelas === 'badan air') {
                return 'badan_air';
            }
            
            if (namobj.includes('hutan lindung') || namobj.includes('hutan_lindung') || 
                jenisRencana === '31000000' || keterangan.includes('lindung') ||
                kelas === 'hutan lindung') {
                return 'hutan_lindung';
            }
            
            if (namobj.includes('hutan produksi') || namobj.includes('hutan_produksi') || 
                jenisRencana === '32000000' || keterangan.includes('produksi') ||
                kelas === 'hutan produksi') {
                return 'hutan_produksi_tetap';
            }
            
            return 'lainnya';
        }

        // ======================== WARNA OVERLAY ========================
        function getOverlayColor(jenis) {
            switch(jenis) {
                case 'desa':
                    return { color: '#1e40af', fillColor: '#3b82f6', weight: 2.5, dashArray: null, fillOpacity: 0.25 };
                case 'kecamatan':
                    return { color: '#6b7280', fillColor: '#9ca3af', weight: 2.5, dashArray: '6,4', fillOpacity: 0.2 };
                case 'hutan_lindung':
                    return { color: '#059669', fillColor: '#34d399', weight: 3, dashArray: null, fillOpacity: 0.35 };
                case 'hutan_produksi_tetap':
                    return { color: '#7c3aed', fillColor: '#a78bfa', weight: 2.5, dashArray: null, fillOpacity: 0.3 };
                case 'hutan_produksi_terbatas':
                    return { color: '#ef4444', fillColor: '#fca5a5', weight: 2.5, dashArray: '8,5', fillOpacity: 0.3 };
                case 'badan_air':
                    return { color: '#0284c7', fillColor: '#38bdf8', weight: 2, dashArray: null, fillOpacity: 0.35 };
                case 'perkebunan':
                    return { color: '#d97706', fillColor: '#fbbf24', weight: 2.5, dashArray: null, fillOpacity: 0.3 };
                case 'permukiman':
                    return { color: '#dc2626', fillColor: '#f87171', weight: 2.5, dashArray: null, fillOpacity: 0.3 };
                case 'industri':
                    return { color: '#4f46e5', fillColor: '#818cf8', weight: 2.5, dashArray: '4,4', fillOpacity: 0.3 };
                case 'pariwisata':
                    return { color: '#ec4899', fillColor: '#f472b6', weight: 2.5, dashArray: null, fillOpacity: 0.3 };
                default:
                    return { color: '#8b5cf6', fillColor: '#c4b5fd', weight: 2, dashArray: null, fillOpacity: 0.25 };
            }
        }

        // ======================== POPUP OVERLAY ========================
        function createOverlayPopupContent(props) {
            const nama = normalizeValue(props.NAMOBJ || props.namobj || props.overlay_nama || props.nama || '-', '-');
            
            let luas = '-';
            if (props.LUASHA) luas = Number(props.LUASHA).toFixed(2) + ' Ha';
            else if (props.luas) luas = Number(props.luas).toFixed(2) + ' Ha';
            else if (props.LUAS) luas = Number(props.LUAS).toFixed(2) + ' Ha';
            
            const kecamatan = normalizeValue(props.WADMKC || props.kecamatan || '-', '-');
            const kabupaten = normalizeValue(props.WADMKK || props.kabupaten || 'Bengkalis', '-');
            const provinsi = normalizeValue(props.WADMPR || props.provinsi || 'Riau', '-');
            const kerawanan = normalizeValue(props.KRB_03 || props.kerawanan || 'Tidak Ada', 'Tidak Ada');
            const cagarBudaya = normalizeValue(props.CAGBUD || props.cagar_budaya || 'Tidak Ada', 'Tidak Ada');
            const sumberAir = normalizeValue(props.RESAIR || props.sumber_air || 'Tidak Ada', 'Tidak Ada');
            const kawasanStrategis = normalizeValue(props.KSMPDN || props.kawasan_strategis || 'Tidak Ada', 'Tidak Ada');
            const hankam = normalizeValue(props.HANKAM || props.hankam || 'Tidak Ada', 'Tidak Ada');
            const keterangan = normalizeValue(props.REMARK || props.remark || props.keterangan || 'Tidak Ada', 'Tidak Ada');
            const kawasanKarst = normalizeValue(props.KKARST || props.karst || 'Tidak Ada', 'Tidak Ada');
            const pertambangan = normalizeValue(props.PTBGMB || props.pertambangan || 'Tidak Ada', 'Tidak Ada');
            const kkop = normalizeValue(props.KKOP_1 || props.kkop || 'Tidak Ada', 'Tidak Ada');
            const kp2b = normalizeValue(props.KP2B_2 || props.kp2b || 'Tidak Ada', 'Tidak Ada');
            const objectId = props.OBJECTID || props.objectid || '-';
            
            let jenis = getOverlayJenis(props);
            let headerClass = 'overlay-lainnya';
            let icon = '📦';

            if (jenis === 'hutan_lindung') { headerClass = 'overlay-hutan-lindung'; icon = '🌳'; }
            else if (jenis === 'hutan_produksi_tetap') { headerClass = 'overlay-hutan-produksi-tetap'; icon = '🌲'; }
            else if (jenis === 'hutan_produksi_terbatas') { headerClass = 'overlay-hutan-produksi-terbatas'; icon = '🌲'; }
            else if (jenis === 'badan_air') { headerClass = 'overlay-badan-air'; icon = '💧'; }
            else if (jenis === 'desa') { headerClass = 'overlay-desa'; icon = '🏘️'; }
            else if (jenis === 'kecamatan') { headerClass = 'overlay-kecamatan'; icon = '🏛️'; }
            else if (jenis === 'perkebunan') { headerClass = 'overlay-perkebunan'; icon = '🌴'; }
            else if (jenis === 'permukiman') { headerClass = 'overlay-permukiman'; icon = '🏠'; }
            else if (jenis === 'industri') { headerClass = 'overlay-industri'; icon = '🏭'; }
            else if (jenis === 'pariwisata') { headerClass = 'overlay-pariwisata'; icon = '🏖️'; }

            return `
                <div class="popup-simple">
                    <div class="popup-simple-header ${headerClass}">
                        <i class="fas fa-draw-polygon"></i> ${icon} DETAIL OVERLAY
                    </div>
                    <div class="popup-simple-body">
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-tag"></i> Nama</div>
                            <div class="popup-value highlight">${escapeHtml(nama)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-chart-area"></i> Luas</div>
                            <div class="popup-value">${luas}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-city"></i> Kecamatan</div>
                            <div class="popup-value">${escapeHtml(kecamatan)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-map-marker-alt"></i> Kabupaten</div>
                            <div class="popup-value">${escapeHtml(kabupaten)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-globe"></i> Provinsi</div>
                            <div class="popup-value">${escapeHtml(provinsi)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-burn"></i> Kerawanan Bencana</div>
                            <div class="popup-value">${escapeHtml(kerawanan)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-landmark"></i> Cagar Budaya</div>
                            <div class="popup-value">${escapeHtml(cagarBudaya)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-water"></i> Sumber Daya Air</div>
                            <div class="popup-value">${escapeHtml(sumberAir)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-university"></i> Kawasan Strategis</div>
                            <div class="popup-value">${escapeHtml(kawasanStrategis)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-shield-alt"></i> Pertahanan Keamanan</div>
                            <div class="popup-value">${escapeHtml(hankam)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-info-circle"></i> Keterangan</div>
                            <div class="popup-value">${escapeHtml(keterangan)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-mountain"></i> Kawasan Karst</div>
                            <div class="popup-value">${escapeHtml(kawasanKarst)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-drafting-compass"></i> Pertambangan</div>
                            <div class="popup-value">${escapeHtml(pertambangan)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-tree"></i> KKOP</div>
                            <div class="popup-value">${escapeHtml(kkop)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-chart-line"></i> KP2B</div>
                            <div class="popup-value">${escapeHtml(kp2b)}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-hashtag"></i> OBJECTID</div>
                            <div class="popup-value">${objectId}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        // ======================== WARNA LAHAN (Hijau) ========================
        function getJenisColor(jenis) {
            const j = normalizeValue(jenis, '').toLowerCase();
            if (j.includes('tadah')) return '#43A047';
            if (j.includes('irigasi')) return '#1B5E20';
            if (j.includes('lebak')) return '#66BB6A';
            if (j.includes('pasang')) return '#A5D6A7';
            if (j.includes('rawa')) return '#4CAF50';
            if (j.includes('gogo')) return '#2E7D32';
            if (j.includes('sawah')) return '#388E3C';
            return '#66BB6A';
        }

        function getKondisiColor(kondisi) {
            const k = normalizeValue(kondisi, '').toLowerCase();
            if (k.includes('eksisting')) return '#1B5E20';
            if (k.includes('potensi')) return '#66BB6A';
            if (k.includes('terlantar')) return '#9E9E9E';
            if (k.includes('produktif')) return '#2E7D32';
            if (k.includes('kurang produktif')) return '#81C784';
            return '#66BB6A';
        }

        function hashColor(str) {
            const palette = ['#1B5E20', '#2E7D32', '#388E3C', '#43A047', '#4CAF50', '#66BB6A', '#81C784', '#A5D6A7', '#C8E6C9'];
            let hash = 0;
            const value = String(str || 'lainnya');
            for (let i = 0; i < value.length; i++) {
                hash = value.charCodeAt(i) + ((hash << 5) - hash);
            }
            return palette[Math.abs(hash) % palette.length];
        }

        function getFeatureColor(properties) {
            if (prioritizeProtected && properties.is_hutan_lindung) return '#b91c1c';
            if (colorMode === 'poktan') return hashColor(properties.poktan);
            if (colorMode === 'kondisi') return getKondisiColor(properties.kondisi);
            return getJenisColor(properties.jenis);
        }

        // ======================== FUNGSI IKON PADI DENGAN GAMBAR (TANPA ANIMASI) ========================
        function createPadiIcon(isProtected = false) {
            const protectedClass = isProtected ? 'protected' : '';
            
            // Gunakan gambar padi dari folder public/images/
            const iconHtml = `
                <div class="paddy-marker-wrapper">
                    <div class="paddy-icon-container">
                        <div class="paddy-icon-bg ${protectedClass}">
                            <img src="{{ asset('images/padi-icon.png') }}" alt="Padi" onerror="this.style.display='none'">
                        </div>
                    </div>
                </div>
            `;
            
            return L.divIcon({
                html: iconHtml,
                className: 'paddy-marker-wrapper',
                iconSize: [38, 38],
                iconAnchor: [19, 19],
                popupAnchor: [0, -19]
            });
        }

        // ======================== TAMBAHKAN IKON PADI ========================
        function addPadiIcons(features) {
            padiMarkerLayer.clearLayers();
            
            if (!overlayStates.ikonPadi) return;
            
            features.forEach(feature => {
                const props = feature.properties || {};
                const isProtected = props.is_hutan_lindung || false;
                
                try {
                    let centerPoint;
                    if (feature.geometry.type === 'Polygon') {
                        const coords = feature.geometry.coordinates[0];
                        let latSum = 0, lngSum = 0;
                        coords.forEach(coord => {
                            latSum += coord[1];
                            lngSum += coord[0];
                        });
                        const centerLat = latSum / coords.length;
                        const centerLng = lngSum / coords.length;
                        centerPoint = [centerLat, centerLng];
                    } else if (feature.geometry.type === 'MultiPolygon') {
                        const coords = feature.geometry.coordinates[0][0];
                        let latSum = 0, lngSum = 0;
                        coords.forEach(coord => {
                            latSum += coord[1];
                            lngSum += coord[0];
                        });
                        const centerLat = latSum / coords.length;
                        const centerLng = lngSum / coords.length;
                        centerPoint = [centerLat, centerLng];
                    } else {
                        return;
                    }
                    
                    const marker = L.marker(centerPoint, {
                        icon: createPadiIcon(isProtected),
                        interactive: true,
                        pane: 'markerPane'
                    });
                    
                    marker.bindPopup(createPopupContent(props), {
                        className: 'custom-popup',
                        maxWidth: 420,
                        minWidth: 300
                    });
                    
                    const tooltipText = isProtected ? 
                        `🌳 ${normalizeValue(props.kode_persil, normalizeValue(props.pemilik, 'Lahan'))}` :
                        `🌾 ${normalizeValue(props.kode_persil, normalizeValue(props.pemilik, 'Lahan'))}`;
                    
                    marker.bindTooltip(tooltipText, {
                        permanent: false,
                        direction: 'top',
                        className: 'custom-label'
                    });
                    
                    padiMarkerLayer.addLayer(marker);
                    
                } catch (e) {
                    // Skip
                }
            });
        }

        // ======================== POPUP LAHAN ========================
        function createPopupContent(p) {
            const statusChip = p.is_hutan_lindung ?
                '<span class="popup-chip lindung"><i class="fas fa-tree"></i> ✅ HUTAN LINDUNG</span>' :
                '<span class="popup-chip non-lindung"><i class="fas fa-check-circle"></i> ❌ BUKAN HUTAN LINDUNG</span>';

            const luasValue = typeof p.luas === 'number' ? p.luas.toFixed(2) : Number(p.luas || 0).toFixed(2);

            return `
                <div class="popup-simple">
                    <div class="popup-simple-header">
                        <i class="fas fa-wheat"></i> DETAIL LAHAN PERTANIAN
                    </div>
                    <div class="popup-simple-body">
                        <div style="margin-bottom:12px">${statusChip}</div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-user"></i> Pemilik</div>
                            <div class="popup-value highlight">${escapeHtml(normalizeValue(p.pemilik))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-map-marker-alt"></i> Desa</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.nama_desa))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-city"></i> Kecamatan</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.kecamatan))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-users"></i> Poktan</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.poktan))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-barcode"></i> Kode Persil</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.kode_persil))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-chart-area"></i> Luas</div>
                            <div class="popup-value highlight">${luasValue} Ha</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-tint"></i> Jenis Sawah</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.jenis))}</div>
                        </div>
                        <div class="popup-row">
                            <div class="popup-label"><i class="fas fa-chart-line"></i> Kondisi</div>
                            <div class="popup-value">${escapeHtml(normalizeValue(p.kondisi))}</div>
                        </div>
                    </div>
                </div>
            `;
        }

        // ======================== CEK HUTAN LINDUNG ========================
        function enrichHutanStatusFromData(features) {
            if (!features || !features.length) return features;
            if (!hutanGeoJsonData?.features?.length) {
                return features.map(f => {
                    f.properties = f.properties || {};
                    f.properties.is_hutan_lindung = false;
                    return f;
                });
            }

            const hutanFeatures = hutanGeoJsonData.features;
            
            return features.map(feature => {
                let isProtected = false;
                try {
                    if (typeof turf !== 'undefined' && turf.booleanIntersects) {
                        for (const hutan of hutanFeatures) {
                            if (turf.booleanIntersects(feature, hutan)) {
                                isProtected = true;
                                break;
                            }
                        }
                    }
                } catch (e) {}
                feature.properties = feature.properties || {};
                feature.properties.is_hutan_lindung = isProtected;
                return feature;
            });
        }

        // ======================== TOGGLE OVERLAY ========================
        function applyOverlayVisibility() {
            if (overlayStates.lahan && lahanLayer.getLayers().length > 0) {
                if (!map.hasLayer(lahanLayer)) map.addLayer(lahanLayer);
            } else if (map.hasLayer(lahanLayer)) map.removeLayer(lahanLayer);
            
            if (overlayStates.label && labelLayer.getLayers().length > 0) {
                if (!map.hasLayer(labelLayer)) map.addLayer(labelLayer);
            } else if (map.hasLayer(labelLayer)) map.removeLayer(labelLayer);
            
            if (overlayStates.ikonPadi && padiMarkerLayer.getLayers().length > 0) {
                if (!map.hasLayer(padiMarkerLayer)) map.addLayer(padiMarkerLayer);
            } else if (map.hasLayer(padiMarkerLayer)) map.removeLayer(padiMarkerLayer);
            
            const overlayLayers = [
                { state: overlayStates.overlayDesa, layer: overlayDesaLayer },
                { state: overlayStates.overlayKecamatan, layer: overlayKecamatanLayer },
                { state: overlayStates.overlayHutanLindung, layer: overlayHutanLindungLayer },
                { state: overlayStates.overlayHutanProduksiTetap, layer: overlayHutanProduksiTetapLayer },
                { state: overlayStates.overlayHutanProduksiTerbatas, layer: overlayHutanProduksiTerbatasLayer },
                { state: overlayStates.overlayBadanAir, layer: overlayBadanAirLayer },
                { state: overlayStates.overlayPerkebunan, layer: overlayPerkebunanLayer },
                { state: overlayStates.overlayPermukiman, layer: overlayPermukimanLayer },
                { state: overlayStates.overlayIndustri, layer: overlayIndustriLayer },
                { state: overlayStates.overlayPariwisata, layer: overlayPariwisataLayer },
                { state: overlayStates.overlayLainnya, layer: overlayLainnyaLayer }
            ];
            
            overlayLayers.forEach(({ state, layer }) => {
                if (state && layer.getLayers().length > 0) {
                    if (!map.hasLayer(layer)) map.addLayer(layer);
                } else if (map.hasLayer(layer)) map.removeLayer(layer);
            });
        }

        // ======================== RENDER MAP ========================
        function renderMap() {
            if (!currentGeoJsonData?.features?.length) {
                console.log('Menunggu data lahan...');
                return;
            }

            let features = JSON.parse(JSON.stringify(currentGeoJsonData.features));
            features = enrichHutanStatusFromData(features);

            if (selectedDesa) {
                features = features.filter(f => normalizeValue(f.properties?.nama_desa, '') === selectedDesa);
            }
            if (selectedPoktan) {
                features = features.filter(f => normalizeValue(f.properties?.poktan, '') === selectedPoktan);
            }

            labelLayer.clearLayers();
            lahanLayer.clearLayers();
            padiMarkerLayer.clearLayers();

            if (!features.length) {
                document.getElementById('legendStats').innerHTML = 'Tidak ada data untuk filter saat ini';
                document.getElementById('dynamicLegend').innerHTML = '<div class="legend-text">Tidak ada data</div>';
                applyOverlayVisibility();
                return;
            }

            const geoLayer = L.geoJSON({
                type: 'FeatureCollection',
                features: features
            }, {
                pane: 'lahanPane',
                style: function(feature) {
                    const p = feature.properties || {};
                    return {
                        color: p.is_hutan_lindung ? '#b91c1c' : '#1B5E20',
                        weight: p.is_hutan_lindung ? 2.5 : 1.5,
                        fillColor: getFeatureColor(p),
                        fillOpacity: p.is_hutan_lindung ? 0.8 : 0.6
                    };
                },
                onEachFeature: function(feature, layer) {
                    const p = feature.properties || {};
                    
                    layer.bindPopup(createPopupContent(p), {
                        className: 'custom-popup',
                        maxWidth: 420,
                        minWidth: 300
                    });
                    
                    const tooltipText = p.is_hutan_lindung ? 
                        `🌳 ${normalizeValue(p.kode_persil, normalizeValue(p.pemilik, 'Lahan'))}` :
                        `🌾 ${normalizeValue(p.kode_persil, normalizeValue(p.pemilik, 'Lahan'))}`;
                    
                    layer.bindTooltip(tooltipText, {
                        permanent: false,
                        direction: 'top',
                        className: 'custom-label'
                    });

                    layer.on('mouseover', function() {
                        this.setStyle({ 
                            fillOpacity: 0.9, 
                            weight: 3, 
                            color: '#2E7D32'
                        });
                    });

                    layer.on('mouseout', function() {
                        const props = feature.properties || {};
                        this.setStyle({
                            color: props.is_hutan_lindung ? '#b91c1c' : '#1B5E20',
                            weight: props.is_hutan_lindung ? 2.5 : 1.5,
                            fillColor: getFeatureColor(props),
                            fillOpacity: props.is_hutan_lindung ? 0.8 : 0.6
                        });
                    });

                    if (overlayStates.label) {
                        try {
                            const centerPoint = layer.getBounds().getCenter();
                            const labelText = p.is_hutan_lindung ? 
                                `🌳 ${normalizeValue(p.kode_persil, normalizeValue(p.pemilik, 'Lahan'))}` :
                                `🌾 ${normalizeValue(p.kode_persil, normalizeValue(p.pemilik, 'Lahan'))}`;
                            
                            const label = L.tooltip({
                                permanent: true,
                                direction: 'center',
                                className: 'custom-label',
                                offset: [0, 0],
                                pane: 'labelPane'
                            }).setContent(labelText).setLatLng(centerPoint);
                            
                            labelLayer.addLayer(label);
                        } catch (e) {}
                    }
                }
            });

            lahanLayer.addLayer(geoLayer);
            addPadiIcons(features);

            const uniqueColors = new Map();
            features.forEach(f => {
                const p = f.properties || {};
                let label = '';
                if (prioritizeProtected && p.is_hutan_lindung) {
                    label = '🌳 Hutan Lindung';
                } else if (colorMode === 'poktan') {
                    label = normalizeValue(p.poktan, 'Poktan Lain');
                } else if (colorMode === 'kondisi') {
                    label = normalizeValue(p.kondisi, 'Kondisi Lain');
                } else {
                    label = normalizeValue(p.jenis, 'Jenis Lain');
                }
                uniqueColors.set(label, getFeatureColor(p));
            });

            const dynamicLegendEl = document.getElementById('dynamicLegend');
            if (dynamicLegendEl) {
                dynamicLegendEl.innerHTML = Array.from(uniqueColors.entries()).map(([label, color]) => `
                    <div class="legend-item" style="cursor:default">
                        <div class="legend-color" style="background:${color}"></div>
                        <span class="legend-text">${escapeHtml(label)}</span>
                    </div>
                `).join('') || '<div class="legend-text">Tidak ada data</div>';
            }

            const protectedCount = features.filter(f => f.properties?.is_hutan_lindung).length;
            document.getElementById('legendStats').innerHTML = `📊 Total: ${features.length} lahan | 🌳 Hutan Lindung: ${protectedCount} | ✅ Non Lindung: ${features.length - protectedCount}`;
            
            applyOverlayVisibility();
        }

        // ======================== LOAD OVERLAY DARI DATABASE ========================
        async function loadOverlayFromDatabase() {
            try {
                const response = await fetch('/api/overlay/geojson');

                if (!response.ok) {
                    console.error('❌ API error:', response.status);
                    return;
                }

                const data = await response.json();

                console.log('📊 Data overlay dari database:', data.features?.length || 0, 'fitur');
                
                overlayDesaLayer.clearLayers();
                overlayKecamatanLayer.clearLayers();
                overlayHutanLindungLayer.clearLayers();
                overlayHutanProduksiTetapLayer.clearLayers();
                overlayHutanProduksiTerbatasLayer.clearLayers();
                overlayBadanAirLayer.clearLayers();
                overlayPerkebunanLayer.clearLayers();
                overlayPermukimanLayer.clearLayers();
                overlayIndustriLayer.clearLayers();
                overlayPariwisataLayer.clearLayers();
                overlayLainnyaLayer.clearLayers();
                
                let stats = { 
                    desa: 0, kecamatan: 0, hutan_lindung: 0, 
                    hutan_produksi_tetap: 0, hutan_produksi_terbatas: 0,
                    badan_air: 0, perkebunan: 0, permukiman: 0, industri: 0,
                    pariwisata: 0, lainnya: 0 
                };
                
                if (data.features && data.features.length > 0) {
                    data.features.forEach(feature => {
                        const props = feature.properties || {};
                        const jenis = getOverlayJenis(props);
                        const overlayStyle = getOverlayColor(jenis);
                        
                        stats[jenis] = (stats[jenis] || 0) + 1;
                        
                        // ===== DEBUG: CETAK JENIS =====
                        console.log('🔍 Jenis overlay:', jenis, '| Nama:', props.NAMOBJ || props.nama || 'Unknown');
                        
                        const layer = L.geoJSON(feature, {
                            pane: 'overlayPane',
                            style: {
                                color: overlayStyle.color,
                                weight: overlayStyle.weight || 2,
                                dashArray: overlayStyle.dashArray || null,
                                fillColor: overlayStyle.fillColor,
                                fillOpacity: overlayStyle.fillOpacity || 0.3
                            },
                            onEachFeature: function(feat, layer) {
                                layer.bindPopup(createOverlayPopupContent(feat.properties), {
                                    className: 'custom-popup',
                                    maxWidth: 480
                                });
                                const nama = feat.properties?.NAMOBJ || feat.properties?.namobj || feat.properties?.nama || 'Overlay';
                                layer.bindTooltip(escapeHtml(nama), {
                                    className: 'custom-label',
                                    direction: 'top'
                                });
                            }
                        });
                        
                        // ===== TAMBAHKAN CASE UNTUK HUTAN LINDUNG =====
                        switch(jenis) {
                            case 'desa': 
                                overlayDesaLayer.addLayer(layer); 
                                break;
                            case 'kecamatan': 
                                overlayKecamatanLayer.addLayer(layer); 
                                break;
                            case 'hutan_lindung': 
                                overlayHutanLindungLayer.addLayer(layer); 
                                console.log('🌳 Menambahkan Hutan Lindung ke layer'); 
                                break;
                            case 'hutan_produksi_tetap': 
                                overlayHutanProduksiTetapLayer.addLayer(layer); 
                                break;
                            case 'hutan_produksi_terbatas': 
                                overlayHutanProduksiTerbatasLayer.addLayer(layer); 
                                break;
                            case 'badan_air': 
                                overlayBadanAirLayer.addLayer(layer); 
                                break;
                            case 'perkebunan': 
                                overlayPerkebunanLayer.addLayer(layer); 
                                break;
                            case 'permukiman': 
                                overlayPermukimanLayer.addLayer(layer); 
                                break;
                            case 'industri': 
                                overlayIndustriLayer.addLayer(layer); 
                                break;
                            case 'pariwisata': 
                                overlayPariwisataLayer.addLayer(layer); 
                                break;
                            default: 
                                overlayLainnyaLayer.addLayer(layer); 
                                break;
                        }
                    });
                    
                    console.log('✅ Statistik overlay:', stats);
                }

                              // ===== DEBUG: CEK JUMLAH LAYER =====
                console.log('📊 Jumlah layer Hutan Lindung:', overlayHutanLindungLayer.getLayers().length);
                console.log('📊 Jumlah layer Hutan Produksi Terbatas:', overlayHutanProduksiTerbatasLayer.getLayers().length);

                const container = document.getElementById('additionalOverlaysList');
                if (container) {
                    container.innerHTML = `
                        <div class="overlay-item"><span class="overlay-name">🌳 Hutan Lindung</span><span class="overlay-badge">${stats.hutan_lindung} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🌲 Hutan Produksi Tetap</span><span class="overlay-badge">${stats.hutan_produksi_tetap} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🌲 Hutan Produksi Terbatas</span><span class="overlay-badge">${stats.hutan_produksi_terbatas} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">💧 Badan Air</span><span class="overlay-badge">${stats.badan_air} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🌴 Perkebunan</span><span class="overlay-badge">${stats.perkebunan} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🏠 Permukiman</span><span class="overlay-badge">${stats.permukiman} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🏭 Industri</span><span class="overlay-badge">${stats.industri} fitur</span></div>
                        <div class="overlay-item"><span class="overlay-name">🏖️ Pariwisata</span><span class="overlay-badge">${stats.pariwisata} fitur</span></div>
                        <div class="overlay-item" style="justify-content:center; border-top:1px solid #e5e7eb; margin-top:4px;">
                            <span class="overlay-name">Total: ${data.features.length} fitur overlay</span>
                        </div>
                    `;
                }
                
                // ===== AKTIFKAN HUTAN LINDUNG OTOMATIS =====
                if (stats.hutan_lindung > 0) {
                    overlayStates.overlayHutanLindung = true;
                    document.getElementById('chkOverlayHutanLindung').checked = true;
                    console.log('✅ Hutan Lindung diaktifkan otomatis');
                }
                
                // ===== AKTIFKAN HUTAN PRODUKSI TERBATAS OTOMATIS =====
                if (stats.hutan_produksi_terbatas > 0) {
                    overlayStates.overlayHutanProduksiTerbatas = true;
                    document.getElementById('chkOverlayHutanProduksiTerbatas').checked = true;
                    console.log('✅ Hutan Produksi Terbatas diaktifkan otomatis');
                }
                
                // ===== ZOOM KE LOKASI HUTAN LINDUNG =====
                if (stats.hutan_lindung > 0 && overlayHutanLindungLayer.getLayers().length > 0) {
                    try {
                        const bounds = overlayHutanLindungLayer.getBounds();
                        if (bounds.isValid()) {
                            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
                            console.log('✅ Zoom ke Hutan Lindung');
                        }
                    } catch (e) {
                        console.warn('Gagal zoom ke Hutan Lindung:', e);
                    }
                }
                
                // ===== PASTIKAN LAYER DITAMBAHKAN KE MAP =====
                applyOverlayVisibility();
                
                // ===== DEBUG: CEK APAKAH LAYER ADA DI MAP =====
                console.log('📍 Hutan Lindung di peta:', map.hasLayer(overlayHutanLindungLayer));
                console.log('📍 Hutan Produksi Terbatas di peta:', map.hasLayer(overlayHutanProduksiTerbatasLayer));
                
            } catch (error) {
                console.error('❌ Error loading overlay:', error);
            }
        }

        // ======================== LOAD DATA ========================
        function loadData() {
            const loader = document.getElementById('loader');
            if (loader) loader.style.display = 'flex';

            Promise.all([
                fetch('/api/lahan/geojson?v=' + Date.now()).then(r => r.ok ? r.json() : { type: 'FeatureCollection', features: [] }),
                fetch('/api/batas-desa/geojson?v=' + Date.now()).then(r => r.ok ? r.json() : { type: 'FeatureCollection', features: [] }),
                fetch('/api/batas-kecamatan/geojson?v=' + Date.now()).then(r => r.ok ? r.json() : { type: 'FeatureCollection', features: [] })
            ])
            .then(([lahanData, desaData, kecData]) => {
                currentGeoJsonData = lahanData;
                desaGeoJsonData = { type: 'FeatureCollection', features: [...(desaData.features || [])] };
                kecGeoJsonData = kecData;

                console.log('📊 Data lahan:', lahanData.features?.length || 0);
                console.log('📊 Data desa API:', desaGeoJsonData.features?.length || 0);
                console.log('📊 Data kecamatan API:', kecGeoJsonData.features?.length || 0);

                const desaSet = new Set();
                const poktanSet = new Set();
                if (lahanData.features) {
                    lahanData.features.forEach(f => {
                        const p = f.properties || {};
                        if (normalizeValue(p.nama_desa, '')) desaSet.add(normalizeValue(p.nama_desa));
                        if (normalizeValue(p.poktan, '')) poktanSet.add(normalizeValue(p.poktan));
                    });
                }

                const filterDesaEl = document.getElementById('filterDesa');
                const filterPoktanEl = document.getElementById('filterPoktan');
                if (filterDesaEl) {
                    filterDesaEl.innerHTML = '<option value="">Semua Desa</option>' + 
                        [...desaSet].sort().map(v => `<option value="${escapeHtml(v)}">${escapeHtml(v)}</option>`).join('');
                }
                if (filterPoktanEl) {
                    filterPoktanEl.innerHTML = '<option value="">Semua Poktan</option>' + 
                        [...poktanSet].sort().map(v => `<option value="${escapeHtml(v)}">${escapeHtml(v)}</option>`).join('');
                }

                renderMap();
                loadOverlayFromDatabase();

                if (loader) loader.style.display = 'none';
                setTimeout(() => zoomToVisibleLahan(), 500);
            })
            .catch(error => {
                console.error('Error loading data:', error);
                if (loader) loader.style.display = 'none';
                document.getElementById('legendStats').innerHTML = '❌ Gagal memuat data. Silahkan refresh halaman.';
            });
        }

        function zoomToVisibleLahan() {
            try {
                if (lahanLayer && lahanLayer.getLayers().length > 0) {
                    const bounds = lahanLayer.getBounds();
                    if (bounds.isValid()) {
                        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
                        return;
                    }
                }
                map.setView(center, zoomLevel);
            } catch (e) {
                map.setView(center, zoomLevel);
            }
        }

        // Home Control
        const HomeControl = L.Control.extend({
            options: { position: 'topleft' },
            onAdd: function() {
                const container = L.DomUtil.create('div', 'leaflet-control-home');
                const link = L.DomUtil.create('a', '', container);
                link.innerHTML = '<i class="fas fa-home"></i>';
                link.href = '#';
                L.DomEvent.on(link, 'click', function(e) {
                    L.DomEvent.preventDefault(e);
                    zoomToVisibleLahan();
                });
                return container;
            }
        });
        map.addControl(new HomeControl());

        // ======================== EVENT LISTENERS ========================
        document.querySelectorAll('input[name="baseMap"]').forEach(el => {
            el.addEventListener('change', function() {
                if (this.value === 'osm') switchBaseLayer(baseOSM);
                if (this.value === 'roadmap') switchBaseLayer(googleRoadmap);
                if (this.value === 'satellite') switchBaseLayer(googleSatellite);
                if (this.value === 'hybrid') switchBaseLayer(googleHybrid);
                if (this.value === 'terrain') switchBaseLayer(googleTerrain);
                if (this.value === 'topografi') switchBaseLayer(topografiLayer);
            });
        });

        document.getElementById('chkLahan')?.addEventListener('change', e => {
            overlayStates.lahan = e.target.checked;
            applyOverlayVisibility();
        });

        document.getElementById('chkLabel')?.addEventListener('change', e => {
            overlayStates.label = e.target.checked;
            if (overlayStates.label) {
                renderMap();
            } else {
                applyOverlayVisibility();
            }
        });

        document.getElementById('chkIkonPadi')?.addEventListener('change', e => {
            overlayStates.ikonPadi = e.target.checked;
            if (overlayStates.ikonPadi) {
                renderMap();
            } else {
                padiMarkerLayer.clearLayers();
                applyOverlayVisibility();
            }
        });

        const overlayCheckboxes = [
            'chkOverlayHutanLindung',
            'chkOverlayHutanProduksiTetap', 
            'chkOverlayHutanProduksiTerbatas',
            'chkOverlayBadanAir', 
            'chkOverlayPerkebunan',
            'chkOverlayPermukiman', 
            'chkOverlayIndustri', 
            'chkOverlayPariwisata'
        ];

        const stateMap = {
            'chkOverlayHutanLindung': 'overlayHutanLindung',
            'chkOverlayHutanProduksiTetap': 'overlayHutanProduksiTetap',
            'chkOverlayHutanProduksiTerbatas': 'overlayHutanProduksiTerbatas',
            'chkOverlayBadanAir': 'overlayBadanAir',
            'chkOverlayPerkebunan': 'overlayPerkebunan',
            'chkOverlayPermukiman': 'overlayPermukiman',
            'chkOverlayIndustri': 'overlayIndustri',
            'chkOverlayPariwisata': 'overlayPariwisata'
        };

        overlayCheckboxes.forEach(id => {
            document.getElementById(id)?.addEventListener('change', function(e) {
                const stateKey = stateMap[id];
                if (stateKey) {
                    overlayStates[stateKey] = e.target.checked;
                    applyOverlayVisibility();
                }
            });
        });

        document.querySelectorAll('input[name="colorMode"]').forEach(el => {
            el.addEventListener('change', function () {
                colorMode = this.value;
                renderMap();
            });
        });

        document.getElementById('filterDesa')?.addEventListener('change', function (e) {
            selectedDesa = e.target.value;
            renderMap();
        });

        document.getElementById('filterPoktan')?.addEventListener('change', function (e) {
            selectedPoktan = e.target.value;
            renderMap();
        });

        document.getElementById('resetFilter')?.addEventListener('click', function () {
            colorMode = 'jenis';
            prioritizeProtected = true;
            selectedDesa = '';
            selectedPoktan = '';

            overlayStates = {
                lahan: true,
                label: false,
                ikonPadi: true,
                overlayDesa: false,
                overlayKecamatan: false,
                overlayHutanLindung: false,
                overlayHutanProduksiTetap: false,
                overlayHutanProduksiTerbatas: false,
                overlayBadanAir: false,
                overlayPerkebunan: false,
                overlayPermukiman: false,
                overlayIndustri: false,
                overlayPariwisata: false,
                overlayLainnya: false
            };

            document.querySelector('input[name="baseMap"][value="osm"]').checked = true;
            document.querySelector('input[name="colorMode"][value="jenis"]').checked = true;
            document.getElementById('filterDesa').value = '';
            document.getElementById('filterPoktan').value = '';
            document.getElementById('chkLahan').checked = true;
            document.getElementById('chkLabel').checked = false;
            document.getElementById('chkIkonPadi').checked = true;

            overlayCheckboxes.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.checked = false;
            });

            switchBaseLayer(baseOSM);
            renderMap();

            setTimeout(() => {
                map.invalidateSize();
            }, 300);
        });

        const legendPanel = document.getElementById('legendPanel');
        const toggleLegend = document.getElementById('toggleLegend');
        toggleLegend?.addEventListener('click', () => legendPanel?.classList.toggle('hidden'));

        const welcomeOverlay = document.getElementById('welcomeOverlay');
        const closeWelcome = document.getElementById('closeWelcome');
        function closeWelcomeOverlay() {
            welcomeOverlay?.classList.add('fade-out');
            setTimeout(() => {
                if (welcomeOverlay) welcomeOverlay.style.display = 'none';
                zoomToVisibleLahan();
            }, 800);
        }
        closeWelcome?.addEventListener('click', (e) => { e.stopPropagation(); closeWelcomeOverlay(); });
        welcomeOverlay?.addEventListener('click', (e) => { if (e.target === welcomeOverlay) closeWelcomeOverlay(); });

        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        menuToggle?.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu?.classList.toggle('show');
            menuToggle.innerHTML = mobileMenu?.classList.contains('show') ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        document.addEventListener('click', function(e) {
            if (!mobileMenu?.contains(e.target) && !menuToggle?.contains(e.target)) {
                mobileMenu?.classList.remove('show');
                if (menuToggle) menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });

        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => map.invalidateSize(), 200);
        });

        map.whenReady(() => {
            loadData();
            setTimeout(() => map.invalidateSize(), 300);
        });
    </script>
</body>
</html>