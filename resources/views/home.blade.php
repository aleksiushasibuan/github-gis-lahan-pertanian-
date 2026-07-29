<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Web GIS Pertanian Kabupaten Bengkalis</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #2E7D32;
            --primary-dark: #1B5E20;
            --soft-green: #e8f5e9;
            --text-main: #1f2937;
            --text-soft: #6b7280;
            --white: #ffffff;
            --shadow-soft: 0 12px 28px rgba(0,0,0,0.08);
            --shadow-hover: 0 18px 40px rgba(0,0,0,0.12);
            --header-height: 70px;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f7f6;
            color: #1f2937;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        img {
            display: block;
            max-width: 100%;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(28px);
            animation: fadeUp 0.9s ease forwards;
        }

        .fade-up.delay-1 { animation-delay: 0.15s; }
        .fade-up.delay-2 { animation-delay: 0.3s; }
        .fade-up.delay-3 { animation-delay: 0.45s; }
        .fade-up.delay-4 { animation-delay: 0.6s; }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomHero {
            from { transform: scale(1.05); }
            to { transform: scale(1); }
        }

        @keyframes floatY {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 rgba(255,255,255,0); }
            50% { box-shadow: 0 0 24px rgba(255,255,255,0.12); }
        }

        /* HEADER */
        .header {
            height: var(--header-height);
            background: rgba(46, 125, 50, 0.96);
            backdrop-filter: blur(10px);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h3 {
            font-size: 1.25rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-left: auto;
        }

        .nav-menu a {
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            transition: 0.25s ease;
            white-space: nowrap;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-1px);
        }

        .nav-menu a.active {
            background: rgba(255,255,255,0.18);
            font-weight: 700;
        }

        .nav-menu .login-btn {
            background: white;
            color: var(--primary) !important;
            font-weight: 700;
        }

        .nav-menu .login-btn:hover {
            background: #e8f5e9;
        }

        .menu-toggle {
            display: none;
            width: 54px;
            height: 54px;
            border: none;
            border-radius: 14px;
            background: rgba(255,255,255,0.14);
            color: white;
            cursor: pointer;
            font-size: 1.3rem;
            align-items: center;
            justify-content: center;
            transition: 0.25s ease;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.06);
        }

        .menu-toggle:hover {
            background: rgba(255,255,255,0.22);
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
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            transition: 0.2s ease;
        }

        .mobile-menu a:hover,
        .mobile-menu a.active {
            background: rgba(255,255,255,0.12);
        }

        .mobile-menu .login-btn {
            background: white;
            color: var(--primary) !important;
        }

        /* HERO */
        .hero {
            position: relative;
            min-height: calc(100vh - var(--header-height));
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            align-items: center;
            gap: 40px;
            padding: 60px 8%;
            overflow: hidden;
            color: white;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(18, 58, 21, 0.88) 0%, rgba(27, 94, 32, 0.78) 45%, rgba(56, 142, 60, 0.60) 100%),
                url('{{ asset('images/hero-pertanian.jpg') }}') center center / cover no-repeat;
            animation: zoomHero 7s ease forwards;
            z-index: 1;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 28%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.12), transparent 30%);
            z-index: 1;
        }

        .hero-text,
        .hero-card {
            position: relative;
            z-index: 2;
        }

        .hero-text .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.14);
            color: white;
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 22px;
            border: 1px solid rgba(255,255,255,0.18);
            animation: pulseGlow 3s ease-in-out infinite;
        }

        .hero-text h1 {
            font-size: 3.15rem;
            line-height: 1.15;
            margin-bottom: 18px;
            color: white;
            max-width: 760px;
        }

        .hero-text p {
            font-size: 1rem;
            line-height: 1.85;
            color: rgba(255,255,255,0.92);
            max-width: 650px;
            margin-bottom: 28px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .btn {
            padding: 13px 22px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: 0.25s ease;
            text-align: center;
        }

        .btn-primary {
            background: white;
            color: var(--primary-dark);
            box-shadow: 0 10px 24px rgba(0,0,0,0.15);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            background: #f6fff6;
        }

        .btn-secondary {
            background: rgba(255,255,255,0.12);
            color: white;
            border: 1px solid rgba(255,255,255,0.18);
            backdrop-filter: blur(6px);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.18);
            transform: translateY(-3px);
        }

        .hero-card {
            background: rgba(255,255,255,0.12);
            border-radius: 26px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.18);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.18);
            backdrop-filter: blur(10px);
            animation: floatY 4.5s ease-in-out infinite;
        }

        .hero-card-top {
            background: rgba(255,255,255,0.14);
            color: white;
            padding: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .hero-card-top h2 {
            font-size: 1.3rem;
            margin-bottom: 8px;
        }

        .hero-card-top p {
            font-size: 0.92rem;
            opacity: 0.92;
            line-height: 1.7;
        }

        .hero-card-body {
            padding: 24px;
        }

        .info-list {
            display: grid;
            gap: 16px;
        }

        .info-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding: 16px;
            border-radius: 16px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.25s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.16);
        }

        .info-icon {
            width: 46px;
            height: 46px;
            min-width: 46px;
            border-radius: 14px;
            background: rgba(255,255,255,0.18);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .info-text h4 {
            font-size: 1rem;
            margin-bottom: 6px;
            color: white;
        }

        .info-text p {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.86);
            line-height: 1.6;
        }

        /* SECTIONS */
        .section {
            padding: 80px 8%;
        }

        .section-title {
            text-align: center;
            margin-bottom: 44px;
        }

        .section-title h2 {
            font-size: 2.1rem;
            color: #14532d;
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--text-soft);
            max-width: 760px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .feature-card {
            background: white;
            border-radius: 22px;
            padding: 28px 24px;
            box-shadow: var(--shadow-soft);
            border: 1px solid #edf2ef;
            transition: 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: var(--soft-green);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #111827;
        }

        .feature-card p {
            font-size: 0.94rem;
            line-height: 1.8;
            color: var(--text-soft);
        }

        .news-section {
            padding: 85px 8%;
            background: linear-gradient(180deg, #f3f7f4 0%, #eef6ef 100%);
        }

        .news-wrap {
            position: relative;
            z-index: 2;
        }

        .news-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .news-title h2 {
            font-size: 2.2rem;
            font-weight: 800;
            color: #14532d;
            margin-bottom: 10px;
        }

        .news-title p {
            color: var(--text-soft);
            max-width: 720px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }

        .news-card {
            background: white;
            border: 1px solid #dce6dd;
            border-radius: 18px;
            overflow: hidden;
            transition: 0.3s ease;
            display: flex;
            flex-direction: column;
            min-height: 100%;
            box-shadow: 0 10px 26px rgba(0,0,0,0.05);
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .news-image {
            width: 100%;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #dfe5df;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
            transition: transform 0.45s ease;
        }

        .news-card:hover .news-image img {
            transform: scale(1.07);
        }

        .news-body {
            padding: 18px 20px 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .news-meta {
            font-size: 0.88rem;
            color: #98a2b3;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .news-card h3 {
            font-size: 1.06rem;
            line-height: 1.4;
            color: #1f2937;
            margin-bottom: 14px;
            font-weight: 700;
        }

        .news-excerpt {
            font-size: 0.94rem;
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 12px;
            flex-grow: 1;
        }

        .news-link {
            color: var(--primary);
            font-size: 0.94rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s ease;
        }

        .news-link:hover {
            color: var(--primary-dark);
        }

        .news-empty,
        .news-loading {
            text-align: center;
            color: #6b7280;
            padding: 30px 0;
            font-size: 1rem;
            grid-column: 1 / -1;
        }

        .cta-box {
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 100%);
            border-radius: 28px;
            padding: 44px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            box-shadow: 0 20px 40px rgba(27,94,32,0.18);
        }

        .cta-box h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .cta-box p {
            line-height: 1.8;
            opacity: 0.95;
            max-width: 700px;
        }

        .cta-box .btn-secondary {
            background: white;
            border: none;
            color: var(--primary-dark);
            font-weight: 800;
            white-space: nowrap;
        }

        .footer {
            background: #14532d;
            color: rgba(255,255,255,0.9);
            padding: 24px 8%;
            text-align: center;
            font-size: 0.92rem;
        }

        @media (max-width: 1200px) {
            .news-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .hero {
                grid-template-columns: 1fr;
                padding: 44px 24px;
                min-height: auto;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .feature-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .cta-box {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 70px;
            }

            .header {
                padding: 0 16px;
            }

            .header h3 {
                font-size: 0.95rem;
                gap: 8px;
            }

            .menu-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 54px;
                height: 54px;
                font-size: 1.3rem;
                border-radius: 14px;
            }

            .hero {
                min-height: auto;
                padding: 28px 16px 30px;
                gap: 20px;
                align-items: start;
            }

            .hero::before {
                background:
                    linear-gradient(120deg, rgba(18, 58, 21, 0.9) 0%, rgba(27, 94, 32, 0.82) 45%, rgba(56, 142, 60, 0.66) 100%),
                    url('{{ asset('images/hero-pertanian.jpg') }}') center center / cover no-repeat;
            }

            .hero-text .badge {
                font-size: 0.75rem;
                padding: 8px 12px;
                margin-bottom: 16px;
            }

            .hero-text h1 {
                font-size: 2rem;
                line-height: 1.18;
                margin-bottom: 14px;
            }

            .hero-text p {
                font-size: 0.95rem;
                line-height: 1.75;
                margin-bottom: 20px;
            }

            .hero-actions {
                flex-direction: column;
                width: 100%;
            }

            .hero-actions .btn {
                width: 100%;
                padding: 14px 16px;
                font-size: 0.95rem;
            }

            .hero-card {
                display: none;
            }

            .section,
            .news-section {
                padding: 50px 16px;
            }

            .section-title h2,
            .news-title h2 {
                font-size: 1.6rem;
            }

            .feature-grid,
            .news-grid {
                grid-template-columns: 1fr;
            }

            .news-image {
                aspect-ratio: 4 / 3;
                min-height: 210px;
            }

            .cta-box {
                padding: 24px 18px;
            }

            .cta-box h3 {
                font-size: 1.35rem;
            }

            .cta-box p {
                font-size: 0.92rem;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 24px 16px 26px;
            }

            .hero-text h1 {
                font-size: 1.8rem;
            }

            .hero-text p {
                font-size: 0.92rem;
                line-height: 1.7;
            }

            .news-image {
                aspect-ratio: 1 / 1;
                min-height: 180px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <h3><i class="fas fa-map-marked-alt"></i> WEB GIS PERTANIAN</h3>

        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('map') }}" class="{{ request()->routeIs('map') || request()->routeIs('webgis') ? 'active' : '' }}">Web GIS</a>
            <a href="{{ route('berita.index') }}" class="{{ request()->routeIs('berita.index') || request()->routeIs('berita.detail') ? 'active' : '' }}">Berita</a>
            <a href="{{ route('login') }}" class="login-btn {{ request()->routeIs('login') ? 'active' : '' }}">Login Akun</a>
        </nav>

        <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
        <a href="{{ route('map') }}" class="{{ request()->routeIs('map') || request()->routeIs('webgis') ? 'active' : '' }}">Web GIS</a>
        <a href="{{ route('berita.index') }}" class="{{ request()->routeIs('berita.index') || request()->routeIs('berita.detail') ? 'active' : '' }}">Berita</a>
        <a href="{{ route('login') }}" class="login-btn {{ request()->routeIs('login') ? 'active' : '' }}">Login Akun</a>
    </div>

    <section class="hero">
        <div class="hero-text">
            <span class="badge fade-up">
                <i class="fas fa-leaf"></i> Kabupaten Bengkalis
            </span>

            <h1 class="fade-up delay-1">Sistem Informasi Geografis Pertanian Kabupaten Bengkalis</h1>

            <p class="fade-up delay-2">
                Platform digital untuk menampilkan informasi lahan pertanian secara visual, modern,
                dan mudah diakses. Jelajahi peta, baca berita terbaru, dan temukan data pendukung
                pertanian dalam satu sistem yang terintegrasi.
            </p>

            <div class="hero-actions fade-up delay-3">
                <a href="{{ route('map') }}" class="btn btn-primary">
                    <i class="fas fa-map"></i> Lihat Web GIS
                </a>
                <a href="{{ route('berita.index') }}" class="btn btn-secondary">
                    <i class="fas fa-newspaper" style="color: #f5f7f6;"></i> Baca Berita
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-user-lock" style="color: #f5f7f6;"></i> Login Admin
                </a>
            </div>
        </div>

        <div class="hero-card fade-up delay-4">
            <div class="hero-card-top">
                <h2>Informasi Utama</h2>
                <p>Kelola dan jelajahi data pertanian Kabupaten Bengkalis dengan tampilan yang lebih modern, interaktif, dan informatif.</p>
            </div>

            <div class="hero-card-body">
                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-draw-polygon"></i></div>
                        <div class="info-text">
                            <h4>Visualisasi Peta Lahan</h4>
                            <p>Menampilkan persebaran lahan pertanian dalam bentuk peta interaktif dan informatif.</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-filter"></i></div>
                        <div class="info-text">
                            <h4>Filter Jenis Sawah</h4>
                            <p>Memudahkan pengguna memilih data berdasarkan kategori dan jenis lahan pertanian.</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-circle-info"></i></div>
                        <div class="info-text">
                            <h4>Detail Informasi Lahan</h4>
                            <p>Setiap objek peta dapat menampilkan informasi pemilik, desa, luas, dan kondisi lahan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-title">
            <h2>Fitur Utama Sistem</h2>
            <p>
                Web GIS Pertanian dirancang untuk membantu penyajian data spasial pertanian secara lebih informatif,
                baik untuk masyarakat umum maupun pengelola data.
            </p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-map-location-dot"></i></div>
                <h3>Peta Interaktif</h3>
                <p>
                    Jelajahi peta pertanian secara langsung dengan fitur zoom, navigasi, dan tampilan data spasial yang responsif.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-seedling"></i></div>
                <h3>Data Lahan Pertanian</h3>
                <p>
                    Tampilkan informasi lahan berdasarkan jenis sawah, lokasi desa, kecamatan, hingga sumber air.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-newspaper"></i></div>
                <h3>Berita Pertanian</h3>
                <p>
                    Sajikan informasi terbaru terkait program, kegiatan, dan perkembangan sektor pertanian di daerah.
                </p>
            </div>
        </div>
    </section>

    <section class="news-section">
        <div class="news-wrap">
            <div class="news-title">
                <h2>Berita Terbaru</h2>
                <p>
                    Informasi kegiatan, program, dan perkembangan terbaru sektor pertanian Kabupaten Bengkalis.
                </p>
            </div>

            <div class="news-grid" id="newsGrid">
                <div class="news-loading">Memuat berita terbaru...</div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="cta-box">
            <div>
                <h3>Mulai Jelajahi Data Pertanian</h3>
                <p>
                    Akses peta Web GIS untuk melihat distribusi lahan pertanian Kabupaten Bengkalis secara lebih detail,
                    cepat, dan mudah dipahami.
                </p>
            </div>

            <a href="{{ route('map') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> Buka Web GIS
            </a>
        </div>
    </section>

    <footer class="footer">
        &copy; {{ date('Y') }} Web GIS Pertanian Kabupaten Bengkalis. All rights reserved.
    </footer>

    <script>
        function formatTanggalIndonesia(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date)) return dateString;

            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        function stripHtml(html) {
            const temp = document.createElement('div');
            temp.innerHTML = html || '';
            return temp.textContent || temp.innerText || '';
        }

        function truncateText(text, maxLength = 145) {
            if (!text) return '';
            return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
        }

        function getImageUrl(item) {
            if (!item) return 'https://via.placeholder.com/600x400?text=Berita';
            return item.gambar_url || item.image || 'https://via.placeholder.com/600x400?text=Berita';
        }

        function getDetailUrl(item) {
            if (!item) return '#';
            return item.url || `/berita/${item.id}`;
        }

        function getJudul(item) {
            return item.judul || item.title || 'Judul berita tidak tersedia';
        }

        function getIsi(item) {
            return item.deskripsi || item.excerpt || item.isi || '';
        }

        function getTanggal(item) {
            return item.tanggal || item.published_at || item.created_at || '';
        }

        function getPenulis(item) {
            return item.penulis || 'Admin';
        }

        async function loadLatestNews() {
            const newsGrid = document.getElementById('newsGrid');

            try {
                const response = await fetch('/api/berita/latest');

                if (!response.ok) {
                    throw new Error('Gagal memuat berita');
                }

                const result = await response.json();

                let berita = [];

                if (Array.isArray(result)) {
                    berita = result;
                } else if (Array.isArray(result.data)) {
                    berita = result.data;
                } else if (Array.isArray(result.berita)) {
                    berita = result.berita;
                }

                if (!berita.length) {
                    newsGrid.innerHTML = `<div class="news-empty">Belum ada berita terbaru.</div>`;
                    return;
                }

                newsGrid.innerHTML = berita.slice(0, 4).map((item) => {
                    const judul = getJudul(item);
                    const isi = truncateText(stripHtml(getIsi(item)), 145);
                    const gambar = getImageUrl(item);
                    const link = getDetailUrl(item);
                    const tanggal = formatTanggalIndonesia(getTanggal(item));
                    const penulis = getPenulis(item);

                    return `
                        <div class="news-card">
                            <div class="news-image">
                                <img src="${gambar}" alt="${judul}" onerror="this.src='https://via.placeholder.com/600x400?text=Berita'">
                            </div>
                            <div class="news-body">
                                <div class="news-meta">
                                    ${tanggal}${penulis ? ' · Oleh ' + penulis : ''}
                                </div>
                                <h3>${judul}</h3>
                                <div class="news-excerpt">${isi}</div>
                                <a href="${link}" class="news-link">
                                    Baca selengkapnya <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (error) {
                console.error(error);
                newsGrid.innerHTML = `<div class="news-empty">Gagal memuat berita terbaru.</div>`;
            }
        }

        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                mobileMenu.classList.toggle('show');
                menuToggle.innerHTML = mobileMenu.classList.contains('show')
                    ? '<i class="fas fa-times"></i>'
                    : '<i class="fas fa-bars"></i>';
            });

            document.addEventListener('click', function(e) {
                if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                    mobileMenu.classList.remove('show');
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    mobileMenu.classList.remove('show');
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', loadLatestNews);
    </script>
</body>
</html>