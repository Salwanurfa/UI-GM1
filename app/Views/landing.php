<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI GreenMetric POLBAN - Sistem Pengelolaan Sampah</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #5C8CBF;
            --primary-dark: #4a7ba7;
            --primary-light: #e8f0f8;
            --light-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            max-width: 100vw;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 0.5rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand i {
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(92, 140, 191, 0.4);
            color: white;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--light-bg) 0%, #e3f2fd 100%);
            padding: 140px 0 80px;
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: -10%;
            width: 40%;
            height: 100%;
            background: linear-gradient(135deg, rgba(92, 140, 191, 0.1) 0%, rgba(92, 140, 191, 0.05) 100%);
            border-radius: 50%;
            transform: translateY(-20%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 100%;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero h1 .highlight {
            color: var(--primary-color);
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: white;
            width: 100%;
            overflow: hidden;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--text-muted);
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 30px rgba(92, 140, 191, 0.2);
            border-color: var(--primary-light);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .feature-icon.blue {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        }

        .feature-icon.green {
            background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);
        }

        .feature-icon.orange {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .feature-card p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 0;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            width: 100%;
            overflow: hidden;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
            width: 100%;
        }

        .stat-box {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-5px);
        }

        .stat-box i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }

        .stat-box h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-box p {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: var(--light-bg);
            text-align: center;
            width: 100%;
            overflow: hidden;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .cta-section p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 3rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(92, 140, 191, 0.4);
            color: white;
        }

        /* Footer */
        .footer {
            background: var(--text-dark);
            color: white;
            padding: 3rem 0 1.5rem;
            width: 100%;
            overflow: hidden;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
            width: 100%;
        }

        .footer h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer p, .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            line-height: 1.8;
        }

        .footer a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.8s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }

            .hero {
                padding: 100px 0 60px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .hero-stats {
                gap: 1rem;
                justify-content: center;
            }

            .stat-number {
                font-size: 2rem;
            }

            .stat-label {
                font-size: 0.8rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .section-title p {
                font-size: 1rem;
            }

            .feature-card {
                margin-bottom: 1.5rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand i {
                font-size: 1.2rem;
            }

            .btn-login {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .cta-section h2 {
                font-size: 1.8rem;
            }

            .cta-section p {
                font-size: 1rem;
            }

            .btn-cta {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.5rem;
            }

            .hero p {
                font-size: 0.9rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .section-title h2 {
                font-size: 1.5rem;
            }

            .feature-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .feature-card h3 {
                font-size: 1.1rem;
            }

            .stat-box h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="fas fa-recycle"></i>
                UI GreenMetric POLBAN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fitur">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistik">Statistik</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a href="<?= base_url('/auth/login') ?>" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-content animate-fade-in">
                    <h1>
                        Sistem Informasi <span class="highlight">UI GreenMetric</span> POLBAN
                    </h1>
                    <p>
                        Platform digital terintegrasi untuk monitoring dan pengelolaan 6 kategori UI GreenMetric 
                        (Setting & Infrastructure, Energy & Climate, Waste Management, Water Management, 
                        Transportation, Education & Research) secara real-time.
                    </p>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number" data-target="6">6</span>
                            <span class="stat-label">Kategori UIGM</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" data-target="<?= $stats['total_data'] ?? 0 ?>"><?= $stats['total_data'] ?? 0 ?></span>
                            <span class="stat-label">Total Data</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number" data-target="<?= $stats['total_tps'] ?? 0 ?>"><?= $stats['total_tps'] ?? 0 ?></span>
                            <span class="stat-label">Unit Aktif</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="fitur">
        <div class="container">
            <div class="section-title">
                <h2>6 Kategori UI GreenMetric</h2>
                <p>Sistem terintegrasi untuk semua aspek keberlanjutan kampus</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>Setting & Infrastructure</h3>
                        <p>
                            Pengelolaan data infrastruktur kampus meliputi gedung, area hijau, 
                            jalan, dan area parkir untuk mendukung kampus berkelanjutan.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Energy & Climate</h3>
                        <p>
                            Monitoring konsumsi energi, penggunaan energi terbarukan, 
                            emisi CO2, dan efisiensi energi kampus secara real-time.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="fas fa-trash-alt"></i>
                        </div>
                        <h3>Waste Management</h3>
                        <p>
                            Sistem pengelolaan sampah kampus dengan tracking berat, jenis, 
                            nilai ekonomi, dan laporan lengkap untuk setiap unit.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="fas fa-tint"></i>
                        </div>
                        <h3>Water Management</h3>
                        <p>
                            Pengelolaan konsumsi air, air daur ulang, sumber air, 
                            dan efisiensi penggunaan air di seluruh kampus.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="fas fa-car"></i>
                        </div>
                        <h3>Transportation</h3>
                        <p>
                            Manajemen transportasi kampus termasuk kendaraan, shuttle bus, 
                            sepeda kampus, dan charging station untuk kendaraan listrik.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>Education & Research</h3>
                        <p>
                            Tracking mata kuliah lingkungan, penelitian, publikasi, 
                            dan organisasi mahasiswa terkait keberlanjutan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section" id="statistik">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white;">Pencapaian UI GreenMetric</h2>
                <p style="color: rgba(255, 255, 255, 0.9);">Data monitoring keberlanjutan kampus secara keseluruhan</p>
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <i class="fas fa-layer-group"></i>
                    <h3>6</h3>
                    <p>Kategori UIGM</p>
                </div>

                <div class="stat-box">
                    <i class="fas fa-database"></i>
                    <h3><?= $stats['total_data'] ?? 0 ?></h3>
                    <p>Total Data Terinput</p>
                </div>

                <div class="stat-box">
                    <i class="fas fa-check-circle"></i>
                    <h3><?= $stats['disetujui'] ?? 0 ?></h3>
                    <p>Data Tervalidasi</p>
                </div>

                <div class="stat-box">
                    <i class="fas fa-building"></i>
                    <h3><?= $stats['total_tps'] ?? 0 ?></h3>
                    <p>Unit Aktif</p>
                </div>

                <div class="stat-box">
                    <i class="fas fa-trash-alt"></i>
                    <h3><?= number_format($stats['total_berat'] ?? 0, 2, ',', '.') ?></h3>
                    <p>Kg Sampah Terkelola</p>
                </div>

                <div class="stat-box">
                    <i class="fas fa-leaf"></i>
                    <h3><?= $stats['bisa_dijual'] ?? 0 ?></h3>
                    <p>Sampah Bernilai Ekonomi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Siap Berkontribusi untuk Kampus Berkelanjutan?</h2>
            <p>Bergabunglah dalam sistem monitoring UI GreenMetric POLBAN untuk kampus yang lebih hijau dan berkelanjutan</p>
            <a href="<?= base_url('/auth/login') ?>" class="btn btn-cta">
                <i class="fas fa-sign-in-alt"></i>
                Login ke Dashboard
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <h4><i class="fas fa-leaf me-2"></i>UI GreenMetric POLBAN</h4>
                    <p>
                        Sistem Informasi UI GreenMetric terintegrasi untuk monitoring 
                        6 kategori keberlanjutan kampus dan mendukung pencapaian 
                        UI GreenMetric World University Rankings.
                    </p>
                </div>

                <div>
                    <h4>Kategori UIGM</h4>
                    <p>
                        <i class="fas fa-building me-2"></i>Setting & Infrastructure<br>
                        <i class="fas fa-bolt me-2"></i>Energy & Climate<br>
                        <i class="fas fa-trash-alt me-2"></i>Waste Management<br>
                        <i class="fas fa-tint me-2"></i>Water Management<br>
                        <i class="fas fa-car me-2"></i>Transportation<br>
                        <i class="fas fa-graduation-cap me-2"></i>Education & Research
                    </p>
                </div>

                <div>
                    <h4>Kontak</h4>
                    <p>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Politeknik Negeri Bandung<br>
                        Jl. Gegerkalong Hilir, Bandung
                    </p>
                    <h4 class="mt-3">Quick Links</h4>
                    <p>
                        <a href="<?= base_url('/auth/login') ?>"><i class="fas fa-sign-in-alt me-2"></i>Login</a><br>
                        <a href="#fitur"><i class="fas fa-layer-group me-2"></i>Kategori</a><br>
                        <a href="#statistik"><i class="fas fa-chart-bar me-2"></i>Statistik</a>
                    </p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> UI GreenMetric POLBAN. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Counter animation
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    element.textContent = target.toLocaleString('id-ID');
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(current).toLocaleString('id-ID');
                }
            }, 16);
        }

        // Trigger counter animation on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        if (!counter.classList.contains('animated')) {
                            animateCounter(counter);
                            counter.classList.add('animated');
                        }
                    });
                }
            });
        });

        const heroStats = document.querySelector('.hero-stats');
        if (heroStats) {
            observer.observe(heroStats);
        }
    </script>
</body>
</html>
