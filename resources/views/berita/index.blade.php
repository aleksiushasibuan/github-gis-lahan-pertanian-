<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1B5E20">
    <title>Berita Terkini - Web GIS Pertanian Kabupaten Bengkalis</title>

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

        /* NAVBAR */
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
            transition: 0.25s ease;
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

        /* ANIMATION */
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
            from { transform: scale(1.08); }
            to { transform: scale(1); }
        }

        /* HERO */
        .hero {
            position: relative;
            min-height: 430px;
            display: flex;
            align-items: center;
            padding: 90px 8% 80px;
            overflow: hidden;
            color: white;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(120deg, rgba(12, 44, 16, 0.88) 0%, rgba(27, 94, 32, 0.76) 45%, rgba(56, 142, 60, 0.58) 100%),
                url('{{ asset('images/hero-berita.jpg') }}') center center / cover no-repeat;
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
            line-height: 1.15;
            margin-bottom: 14px;
            max-width: 820px;
        }

        .hero p {
            max-width: 760px;
            line-height: 1.9;
            font-size: 1rem;
            color: rgba(255,255,255,0.92);
        }

        /* CONTENT */
        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 80px 24px;
        }

        .section-title {
            text-align: center;
            margin-bottom: 46px;
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

        /* GRID BERITA */
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
            opacity: 0;
            transform: translateY(24px);
            animation: fadeUp 0.85s ease forwards;
        }

        .news-card:nth-child(1) { animation-delay: 0.08s; }
        .news-card:nth-child(2) { animation-delay: 0.16s; }
        .news-card:nth-child(3) { animation-delay: 0.24s; }
        .news-card:nth-child(4) { animation-delay: 0.32s; }
        .news-card:nth-child(5) { animation-delay: 0.40s; }
        .news-card:nth-child(6) { animation-delay: 0.48s; }

        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .news-image {
            position: relative;
            width: 100%;
            height: 230px;
            overflow: hidden;
            background: #e5e7eb;
        }

        .news-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.08), transparent 45%);
            pointer-events: none;
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
            font-size: 1.16rem;
            font-weight: 700;
            line-height: 1.45;
            color: #111827;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.25s ease;
        }

        .news-card:hover .news-title {
            color: var(--primary);
        }

        .news-excerpt {
            color: #4b5563;
            font-size: 0.95rem;
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
            gap: 11px;
        }

        .news-tag {
            font-size: 0.75rem;
            color: #6b7280;
            background: #f3f4f6;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 600;
        }

        /* EMPTY */
        .empty-box {
            grid-column: 1 / -1;
            background: white;
            border: 1px dashed #d1d5db;
            border-radius: 22px;
            padding: 60px 24px;
            text-align: center;
            color: #6b7280;
            box-shadow: 0 10px 24px rgba(0,0,0,0.03);
        }

        .empty-box i {
            font-size: 2rem;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        .empty-box h3 {
            font-size: 1.1rem;
            color: #111827;
            margin-bottom: 8px;
        }

        /* PAGINATION */
        .pagination-wrap {
            margin-top: 44px;
            display: flex;
            justify-content: center;
            overflow-x: auto;
        }

        .pagination-wrap nav {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* FOOTER */
        .footer {
            background: #14532d;
            color: rgba(255,255,255,0.9);
            padding: 24px 8%;
            text-align: center;
            font-size: 0.92rem;
            margin-top: 30px;
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .hero h1 {
                font-size: 2.45rem;
            }

            .news-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
                --header-height: 58px;
            }

            .header {
                height: var(--header-height);
                padding: 0 12px;
            }

            .header h3 {
                font-size: 0.95rem;
                gap: 8px;
            }

            .hero {
                min-height: auto;
                padding: 56px 16px 46px;
            }

            .hero-badge {
                font-size: 0.76rem;
                padding: 8px 12px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 0.92rem;
                line-height: 1.7;
            }

            .container {
                padding: 56px 16px;
            }

            .section-title {
                margin-bottom: 32px;
            }

            .section-title h2 {
                font-size: 1.65rem;
            }

            .section-title p {
                font-size: 0.92rem;
                line-height: 1.7;
            }

            .news-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .news-image {
                height: 220px;
            }

            .news-body {
                padding: 18px;
            }

            .news-title {
                font-size: 1.04rem;
            }

            .news-excerpt {
                font-size: 0.9rem;
                line-height: 1.7;
            }

            .footer {
                padding: 20px 16px;
                font-size: 0.84rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.7rem;
            }

            .hero p {
                font-size: 0.88rem;
            }

            .news-image {
                height: 200px;
            }

            .news-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .empty-box {
                padding: 42px 18px;
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
        <div class="hero-wrap">
            <div class="hero-badge fade-up">
                <i class="fas fa-newspaper"></i> Informasi Pertanian
            </div>

            <h1 class="fade-up delay-1">Berita Terkini Pertanian Kabupaten Bengkalis</h1>

            <p class="fade-up delay-2">
                Ikuti informasi terbaru, kegiatan, program, dan perkembangan sektor pertanian
                yang disajikan secara ringkas, jelas, modern, dan mudah diakses oleh masyarakat.
            </p>
        </div>
    </section>

    <main class="container">
        <div class="section-title fade-up">
            <h2>Daftar Berita Pertanian</h2>
            <p>
                Temukan berbagai berita terbaru mengenai aktivitas, kebijakan, dan perkembangan
                pertanian di Kabupaten Bengkalis.
            </p>
        </div>

        <div class="news-grid">
            @forelse($beritas as $berita)
                <article class="news-card">
                    <div class="news-image">
                        @if(!empty($berita->gambar))
                            <img
                                src="{{ \Illuminate\Support\Str::startsWith($berita->gambar, ['http://', 'https://']) ? $berita->gambar : asset('storage/' . ltrim(str_replace('storage/', '', $berita->gambar), '/')) }}"
                                alt="{{ $berita->judul }}"
                            >
                        @else
                            <div class="news-placeholder">Berita Pertanian</div>
                        @endif
                    </div>

                    <div class="news-body">
                        <div class="news-meta">
                            <i class="fas fa-calendar-alt"></i>
                            <span>{{ \Carbon\Carbon::parse($berita->created_at)->translatedFormat('d F Y') }}</span>
                        </div>

                        <h3 class="news-title">{{ $berita->judul }}</h3>

                        <div class="news-excerpt">
                            {{ $berita->deskripsi ?? \Illuminate\Support\Str::limit(strip_tags($berita->isi), 160) }}
                        </div>

                        <div class="news-footer">
                            <a href="{{ route('berita.detail', $berita->id) }}" class="news-link">
                                Baca Selengkapnya
                                <i class="fas fa-arrow-right"></i>
                            </a>

                            <span class="news-tag">Berita</span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-box">
                    <i class="fas fa-newspaper"></i>
                    <h3>Belum ada berita tersedia</h3>
                    <p>Data berita belum tersedia saat ini. Silakan cek kembali nanti.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($beritas, 'links'))
            <div class="pagination-wrap">
                {{ $beritas->links() }}
            </div>
        @endif
    </main>

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