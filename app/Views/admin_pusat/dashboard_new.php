<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h4><?= $totalUnits ?></h4>
            <p>Total Unit</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h4><?= $completedSubmissions ?></h4>
            <p>Data Lengkap</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $pendingReviews ?></h4>
            <p>Menunggu Review</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <h4><?= number_format($overallProgress, 1) ?>%</h4>
            <p>Progress Keseluruhan</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-bolt"></i>
        <h3>Aksi Cepat</h3>
    </div>
    <div class="card-body">
        <div class="shortcuts-grid">
            <a href="<?= base_url('/admin-pusat/monitoring') ?>" class="shortcut-item">
                <div class="shortcut-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="shortcut-title">Monitoring Unit</div>
                <div class="shortcut-desc">Pantau progress semua unit</div>
            </a>
            <a href="<?= base_url('/admin-pusat/data-penilaian') ?>" class="shortcut-item">
                <div class="shortcut-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="shortcut-title">Data Penilaian</div>
                <div class="shortcut-desc">Kelola data penilaian</div>
            </a>
            <a href="<?= base_url('/report') ?>" class="shortcut-item">
                <div class="shortcut-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="shortcut-title">Laporan</div>
                <div class="shortcut-desc">Generate laporan</div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i>
        <h3>Aktivitas Terbaru</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($recentActivities)): ?>
            <?php foreach ($recentActivities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-<?= $activity['icon'] ?? 'info-circle' ?>"></i>
                    </div>
                    <div class="activity-content">
                        <h5><?= htmlspecialchars($activity['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <p><?= htmlspecialchars($activity['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="activity-time"><?= $activity['time'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="activity-content">
                    <h5>Belum ada aktivitas</h5>
                    <p>Aktivitas terbaru akan muncul di sini</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Progress Overview -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i>
        <h3>Progress Overview</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($progressData)): ?>
            <?php foreach ($progressData as $category => $progress): ?>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label"><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="progress-value"><?= number_format($progress, 1) ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Data progress belum tersedia</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Dashboard specific scripts
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress bars
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    });
</script>
<?= $this->endSection() ?>