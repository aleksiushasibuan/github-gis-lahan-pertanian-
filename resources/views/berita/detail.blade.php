<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1B5E20">
    <title>{{ $berita->judul }} - Web GIS Pertanian Kabupaten Bengkalis</title>

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
            --primary-soft: #E8F5E9;
            --text-main: #1F2937;
            --text-soft: #6B7280;
            --line: #E5E7EB;
            --white: #FFFFFF;
            --bg: #F5F7F6;
            --shadow-soft: 0 12px 28px rgba(0,0,0,0.06);
            --shadow-hover: 0 18px 40px rgba(0,0,0,0.12);
            --header-height: 70px;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-main);
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
            animation: fadeUp 0.8s ease forwards;
        }

        .delay-1 { animation-delay: 0.12s; }
        .delay-2 { animation-delay: 0.24s; }
        .delay-3 { animation-delay: 0.36s; }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes zoomBg {
            from { transform: scale(1.06); }
            to { transform: scale(1); }
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
            background: var(--primary-soft);
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
            min-height: 420px;
            display: flex;
            align-items: end;
            padding: 80px 8% 60px;
            color: white;
            overflow: hidden;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(12, 44, 16, 0.88) 0%, rgba(27, 94, 32, 0.76) 45%, rgba(56, 142, 60, 0.58) 100%),
                url('{{ !empty($berita->gambar) ? (\Illuminate\Support\Str::startsWith($berita->gambar, ['http://', 'https://']) ? $berita->gambar : asset('storage/' . ltrim(str_replace('storage/', '', $berita->gambar), '/'))) : asset('images/hero-berita.jpg') }}') center center / cover no-repeat;
            animation: zoomBg 7s ease forwards;
            z-index: 1;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.16), transparent 24%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.10), transparent 30%);
            z-index: 1;
        }

        .hero-wrap {
            position: relative;
            z-index: 2;
            max-width: 1180px;
            width: 100%;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.16);
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 0.86rem;
            font-weight: 600;
            margin-bottom: 18px;
            backdrop-filter: blur(6px);
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.16;
            max-width: 850px;
            margin-bottom: 14px;
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            color: rgba(255,255,255,0.9);
            font-size: 0.95rem;
        }

        .hero-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* CONTENT */
        .content-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 70px 24px;
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 0.9fr);
            gap: 32px;
            align-items: start;
        }

        .article-card,
        .sidebar-card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 24px;
            box-shadow: var(--shadow-soft);
        }

        .article-card {
            overflow: hidden;
        }

        .article-cover {
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #e5e7eb;
        }

        .article-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
        }

        .article-body {
            padding: 32px;
        }

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--text-soft);
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: var(--primary);
            font-weight: 600;
        }

        .article-title {
            font-size: 2rem;
            line-height: 1.3;
            color: #111827;
            margin-bottom: 14px;
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            font-size: 0.92rem;
            color: var(--text-soft);
            margin-bottom: 26px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf2ef;
        }

        .article-meta span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .article-content {
            color: #374151;
            line-height: 1.95;
            font-size: 1rem;
            word-wrap: break-word;
        }

        .article-content p,
        .article-content ul,
        .article-content ol,
        .article-content blockquote {
            margin-bottom: 18px;
        }

        .article-content h2,
        .article-content h3,
        .article-content h4 {
            margin: 28px 0 14px;
            color: #14532d;
            line-height: 1.35;
        }

        .article-content img {
            border-radius: 18px;
            margin: 24px auto;
            box-shadow: var(--shadow-soft);
            max-width: 100%;
            height: auto;
        }

        .article-content iframe,
        .article-content table,
        .article-content video {
            max-width: 100%;
        }

        .article-content table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .article-actions {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #edf2ef;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-back,
        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 0.94rem;
            font-weight: 700;
            transition: 0.25s ease;
            text-align: center;
        }

        .btn-back {
            background: var(--primary);
            color: white;
        }

        .btn-back:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 1px solid #d9e7da;
            color: var(--primary);
            background: #f8fcf8;
        }

        .btn-outline:hover {
            background: var(--primary-soft);
            transform: translateY(-2px);
        }

        .sidebar-card {
            padding: 24px;
            position: sticky;
            top: 94px;
        }

        .sidebar-title {
            font-size: 1.15rem;
            color: #14532d;
            margin-bottom: 18px;
        }

        .mini-news-list {
            display: grid;
            gap: 16px;
        }

        .mini-news-item {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 14px;
            align-items: start;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .mini-news-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .mini-news-thumb {
            width: 92px;
            height: 78px;
            border-radius: 14px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .mini-news-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mini-news-text h4 {
            font-size: 0.95rem;
            line-height: 1.45;
            margin-bottom: 6px;
        }

        .mini-news-text h4 a {
            color: #111827;
        }

        .mini-news-text h4 a:hover {
            color: var(--primary);
        }

        .mini-news-text span {
            font-size: 0.82rem;
            color: var(--text-soft);
        }

        /* RELATED */
        .related-section {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-title h2 {
            font-size: 2rem;
            color: #14532d;
            margin-bottom: 10px;
        }

        .section-title p {
            color: var(--text-soft);
            max-width: 720px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 28px;
        }

        .news-card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .news-image {
            width: 100%;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #e5e7eb;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .news-card:hover .news-image img {
            transform: scale(1.07);
        }

        .news-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #dceddc 0%, #c8e6c9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 0.95rem;
            font-weight: 700;
        }

        .news-body {
            padding: 22px 22px 24px;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
        }

        .news-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.84rem;
            color: #9ca3af;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .news-title {
            font-size: 1.12rem;
            font-weight: 700;
            line-height: 1.45;
            color: #111827;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-excerpt {
            color: #4b5563;
            font-size: 0.94rem;
            line-height: 1.8;
            margin-bottom: 18px;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-top: 16px;
            border-top: 1px solid #f0f0f0;
            flex-wrap: wrap;
        }

        .news-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            font-size: 0.92rem;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .news-link:hover {
            color: var(--primary-dark);
        }

        .news-tag {
            font-size: 0.75rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
        }

        .footer {
            background: #14532d;
            color: rgba(255,255,255,0.9);
            padding: 24px 8%;
            text-align: center;
            font-size: 0.92rem;
            margin-top: 20px;
        }

        @media (max-width: 1100px) {
            .content-wrap {
                grid-template-columns: 1fr;
            }

            .sidebar-card {
                position: static;
            }

            .news-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hero h1 {
                font-size: 2.5rem;
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
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 70px;
            }

            .header {
                padding: 0 16px;
            }

            .header h3 {
                font-size: 1rem;
                gap: 8px;
            }

            .hero {
                min-height: auto;
                padding: 56px 16px 44px;
                align-items: end;
            }

            .hero::before {
                background-position: center center;
            }

            .hero-badge {
                font-size: 0.78rem;
                padding: 8px 12px;
                margin-bottom: 14px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero-meta {
                font-size: 0.88rem;
                gap: 12px;
            }

            .content-wrap,
            .related-section {
                padding-left: 16px;
                padding-right: 16px;
            }

            .content-wrap {
                padding-top: 50px;
                padding-bottom: 50px;
                gap: 24px;
            }

            .article-body,
            .sidebar-card {
                padding: 22px 18px;
            }

            .article-title {
                font-size: 1.55rem;
            }

            .article-meta {
                gap: 12px;
                font-size: 0.88rem;
            }

            .article-content {
                font-size: 0.95rem;
                line-height: 1.85;
            }

            .article-cover {
                aspect-ratio: 4 / 3;
            }

            .mini-news-item {
                grid-template-columns: 82px 1fr;
                gap: 12px;
            }

            .mini-news-thumb {
                width: 82px;
                height: 70px;
            }

            .section-title h2 {
                font-size: 1.55rem;
            }

            .news-grid {
                grid-template-columns: 1fr;
            }

            .news-image {
                aspect-ratio: 4 / 3;
            }

            .news-body {
                padding: 18px;
            }

            .btn-back,
            .btn-outline {
                width: 100%;
            }

            .footer {
                padding: 20px 16px;
                font-size: 0.84rem;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 44px 16px 36px;
            }

            .hero h1 {
                font-size: 1.75rem;
            }

            .hero-meta {
                font-size: 0.82rem;
            }

            .article-cover,
            .news-image {
                aspect-ratio: 1 / 1;
            }

            .breadcrumb {
                font-size: 0.82rem;
            }

            .article-title {
                font-size: 1.35rem;
            }

            .article-content {
                font-size: 0.92rem;
                line-height: 1.8;
            }
        }
    </style>
</head>
<body>
    @php
        $relatedItems = $relatedBeritas ?? collect();
    @endphp

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
        <div class="hero-wrap">
            <div class="hero-badge fade-up">
                <i class="fas fa-newspaper"></i> Detail Berita Pertanian
            </div>

            <h1 class="fade-up delay-1">{{ $berita->judul }}</h1>

            <div class="hero-meta fade-up delay-2">
                <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($berita->created_at)->translatedFormat('d F Y') }}</span>
                <span><i class="fas fa-user"></i> {{ $berita->penulis ?? 'Admin' }}</span>
            </div>
        </div>
    </section>

    <main class="content-wrap">
        <article class="article-card fade-up">
            @if(!empty($berita->gambar))
                <div class="article-cover">
                    <img
                        src="{{ \Illuminate\Support\Str::startsWith($berita->gambar, ['http://', 'https://']) ? $berita->gambar : asset('storage/' . ltrim(str_replace('storage/', '', $berita->gambar), '/')) }}"
                        alt="{{ $berita->judul }}"
                    >
                </div>
            @endif

            <div class="article-body">
                <div class="breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>/</span>
                    <a href="{{ route('berita.index') }}">Berita</a>
                    <span>/</span>
                    <span>{{ \Illuminate\Support\Str::limit($berita->judul, 45) }}</span>
                </div>

                <h2 class="article-title">{{ $berita->judul }}</h2>

                <div class="article-meta">
                    <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($berita->created_at)->translatedFormat('d F Y') }}</span>
                    <span><i class="fas fa-user"></i> {{ $berita->penulis ?? 'Admin' }}</span>
                    <span><i class="fas fa-folder-open"></i> Informasi Pertanian</span>
                </div>

                <div class="article-content">
                    {!! $berita->isi !!}
                </div>

                <div class="article-actions">
                    <a href="{{ route('berita.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Berita
                    </a>

                    <a href="{{ route('map') }}" class="btn-outline">
                        <i class="fas fa-map"></i> Lihat Web GIS
                    </a>
                </div>
            </div>
        </article>

        <aside class="sidebar-card fade-up delay-1">
            <h3 class="sidebar-title">Berita Lainnya</h3>

            <div class="mini-news-list">
                @forelse($relatedItems as $item)
                    <div class="mini-news-item">
                        <div class="mini-news-thumb">
                            @if(!empty($item->gambar))
                                <img
                                    src="{{ \Illuminate\Support\Str::startsWith($item->gambar, ['http://', 'https://']) ? $item->gambar : asset('storage/' . ltrim(str_replace('storage/', '', $item->gambar), '/')) }}"
                                    alt="{{ $item->judul }}"
                                >
                            @else
                                <div class="news-placeholder" style="font-size: 0.78rem;">Berita</div>
                            @endif
                        </div>

                        <div class="mini-news-text">
                            <h4>
                                <a href="{{ route('berita.detail', $item->id) }}">{{ \Illuminate\Support\Str::limit($item->judul, 60) }}</a>
                            </h4>
                            <span>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p style="color:#6b7280; line-height:1.8;">Belum ada berita lain yang tersedia.</p>
                @endforelse
            </div>
        </aside>
    </main>

    @if($relatedItems->count())
        <section class="related-section">
            <div class="section-title fade-up">
                <h2>Berita Terkait</h2>
                <p>
                    Baca juga informasi terbaru lainnya seputar kegiatan dan perkembangan pertanian.
                </p>
            </div>

            <div class="news-grid">
                @foreach($relatedItems as $item)
                    <article class="news-card">
                        <div class="news-image">
                            @if(!empty($item->gambar))
                                <img
                                    src="{{ \Illuminate\Support\Str::startsWith($item->gambar, ['http://', 'https://']) ? $item->gambar : asset('storage/' . ltrim(str_replace('storage/', '', $item->gambar), '/')) }}"
                                    alt="{{ $item->judul }}"
                                >
                            @else
                                <div class="news-placeholder">Berita Pertanian</div>
                            @endif
                        </div>

                        <div class="news-body">
                            <div class="news-meta">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}</span>
                            </div>

                            <h3 class="news-title">{{ $item->judul }}</h3>

                            <div class="news-excerpt">
                                {{ $item->deskripsi ?? \Illuminate\Support\Str::limit(strip_tags($item->isi), 160) }}
                            </div>

                            <div class="news-footer">
                                <a href="{{ route('berita.detail', $item->id) }}" class="news-link">
                                    Baca Selengkapnya
                                    <i class="fas fa-arrow-right"></i>
                                </a>

                                <span class="news-tag">Berita</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <footer class="footer">
        &copy; {{ date('Y') }} Web GIS Pertanian Kabupaten Bengkalis. All rights reserved.
    </footer>

    <script>
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
    </script>
</body>
</html>