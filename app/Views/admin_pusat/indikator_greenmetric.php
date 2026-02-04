<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<?php
// Helper function untuk warna kategori default
if (!function_exists('getCategoryColor')) {
    function getCategoryColor($kodeKategori) {
        $colors = [
            'SI' => '#28a745',
            'EC' => '#dc3545', 
            'WS' => '#ffc107',
            'WR' => '#17a2b8',
            'TR' => '#6f42c1',
            'ED' => '#fd7e14'
        ];
        return $colors[$kodeKategori] ?? '#007bff';
    }
}
?>

<!-- Header Info -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle"></i>
        <h3>Tentang UI GreenMetric</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <p>UI GreenMetric World University Ranking adalah sistem pemeringkatan universitas berdasarkan komitmen terhadap keberlanjutan lingkungan. Penilaian dilakukan berdasarkan 6 kategori utama dengan total skor maksimal 10.000 poin.</p>
                <p><strong>Tahun Penilaian Aktif:</strong> <?= $tahun ? $tahun['tahun'] . ' - ' . $tahun['nama_periode'] : 'Tidak ada tahun aktif' ?></p>
            </div>
            <div class="col-md-4 text-center">
                <div class="greenmetric-logo">
                    <i class="fas fa-leaf fa-4x text-success mb-3"></i>
                    <h5>UI GreenMetric</h5>
                    <p class="text-muted">World University Ranking</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Overview -->
<div class="row">
    <?php foreach ($categories as $category): ?>
        <?php
        $stats = $categoryStats[$category['id']] ?? ['pending' => 0, 'disetujui' => 0, 'perlu_revisi' => 0, 'total' => 0];
        $completionRate = $stats['total'] > 0 ? ($stats['disetujui'] / $stats['total']) * 100 : 0;
        ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="category-card">
                <div class="category-header">
                    <div class="category-icon" style="background: <?= $category['warna'] ?? getCategoryColor($category['kode_kategori']) ?>">
                        <i class="fas fa-<?= getCategoryIcon($category['kode_kategori']) ?>"></i>
                    </div>
                    <div class="category-info">
                        <h5><?= $category['kode_kategori'] ?></h5>
                        <p><?= $category['nama_kategori'] ?></p>
                    </div>
                    <div class="category-weight">
                        <span class="weight-badge"><?= number_format($category['bobot_persen'], 0) ?>%</span>
                    </div>
                </div>

                <div class="category-description">
                    <p><?= $category['deskripsi'] ?></p>
                </div>

                <div class="category-stats">
                    <div class="stats-row">
                        <div class="stat-item">
                            <span class="stat-number text-success"><?= $stats['disetujui'] ?></span>
                            <span class="stat-label">Disetujui</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number text-warning"><?= $stats['pending'] ?></span>
                            <span class="stat-label">Pending</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number text-danger"><?= $stats['perlu_revisi'] ?></span>
                            <span class="stat-label">Revisi</span>
                        </div>
                    </div>

                    <div class="completion-progress">
                        <div class="progress-header">
                            <span>Tingkat Penyelesaian</span>
                            <span class="progress-percent"><?= number_format($completionRate, 1) ?>%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $completionRate ?>%; background: <?= $category['warna'] ?? getCategoryColor($category['kode_kategori']) ?>"></div>
                        </div>
                    </div>
                </div>

                <div class="category-actions">
                    <a href="<?= base_url('/admin-pusat/data-penilaian?kategori=' . $category['id']) ?>"
                        class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye"></i> Lihat Data
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Detailed Information -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i>
        <h3>Rincian Penilaian GreenMetric</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Bobot</th>
                        <th>Deskripsi</th>
                        <th>Total Unit</th>
                        <th>Disetujui</th>
                        <th>Pending</th>
                        <th>Perlu Revisi</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <?php $stats = $categoryStats[$category['id']] ?? ['pending' => 0, 'disetujui' => 0, 'perlu_revisi' => 0, 'total' => 0]; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon-small me-3" style="background: <?= $category['warna'] ?? getCategoryColor($category['kode_kategori']) ?>">
                                        <i class="fas fa-<?= getCategoryIcon($category['kode_kategori']) ?>"></i>
                                    </div>
                                    <div>
                                        <strong><?= $category['kode_kategori'] ?></strong>
                                        <br>
                                        <small class="text-muted"><?= $category['nama_kategori'] ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="weight-badge"><?= number_format($category['bobot_persen'], 0) ?>%</span>
                            </td>
                            <td>
                                <small><?= $category['deskripsi'] ?></small>
                            </td>
                            <td><?= $stats['total'] ?></td>
                            <td>
                                <span class="badge bg-success"><?= $stats['disetujui'] ?></span>
                            </td>
                            <td>
                                <span class="badge bg-warning"><?= $stats['pending'] ?></span>
                            </td>
                            <td>
                                <span class="badge bg-danger"><?= $stats['perlu_revisi'] ?></span>
                            </td>
                            <td>
                                <?php $progress = $stats['total'] > 0 ? ($stats['disetujui'] / $stats['total']) * 100 : 0; ?>
                                <div class="progress-item">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $progress ?>%; background: <?= $category['warna'] ?? getCategoryColor($category['kode_kategori']) ?>"></div>
                                    </div>
                                    <small><?= number_format($progress, 1) ?>%</small>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- GreenMetric Guidelines -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-book"></i>
        <h3>Panduan Penilaian</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Kriteria Penilaian:</h6>
                <ul class="guideline-list">
                    <li><strong>Setting & Infrastructure (15%):</strong> Luas area kampus, ruang terbuka hijau, anggaran untuk keberlanjutan</li>
                    <li><strong>Energy & Climate Change (21%):</strong> Penggunaan energi, program efisiensi energi, energi terbarukan</li>
                    <li><strong>Waste (18%):</strong> Program daur ulang, pengolahan sampah organik, pengurangan sampah</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6>Kriteria Lanjutan:</h6>
                <ul class="guideline-list">
                    <li><strong>Water (10%):</strong> Program konservasi air, daur ulang air, penggunaan air</li>
                    <li><strong>Transportation (18%):</strong> Kebijakan transportasi, kendaraan ramah lingkungan</li>
                    <li><strong>Education & Research (18%):</strong> Mata kuliah keberlanjutan, penelitian lingkungan</li>
                </ul>
            </div>
        </div>

        <div class="mt-4">
            <h6>Skor Total:</h6>
            <div class="score-breakdown">
                <div class="score-item">
                    <span class="score-range">8000-10000</span>
                    <span class="score-grade excellent">Excellent</span>
                </div>
                <div class="score-item">
                    <span class="score-range">6000-7999</span>
                    <span class="score-grade good">Good</span>
                </div>
                <div class="score-item">
                    <span class="score-range">4000-5999</span>
                    <span class="score-grade fair">Fair</span>
                </div>
                <div class="score-item">
                    <span class="score-range">0-3999</span>
                    <span class="score-grade poor">Needs Improvement</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php
function getCategoryIcon($code)
{
    $icons = [
        'SI' => 'building',
        'EC' => 'bolt',
        'WS' => 'recycle',
        'WR' => 'tint',
        'TR' => 'car',
        'ED' => 'graduation-cap'
    ];
    return $icons[$code] ?? 'circle';
}
?>

<style>
    .category-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        height: 100%;
        transition: all 0.3s ease;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .category-header {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin-right: 15px;
    }

    .category-icon-small {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }

    .category-info h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }

    .category-info p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }

    .weight-badge {
        background: #f8f9fa;
        color: #495057;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .category-description {
        margin-bottom: 20px;
    }

    .category-description p {
        font-size: 13px;
        color: #666;
        line-height: 1.5;
    }

    .stats-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 20px;
        font-weight: 700;
    }

    .stat-label {
        font-size: 12px;
        color: #666;
    }

    .completion-progress {
        margin-bottom: 15px;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .progress-percent {
        font-weight: 600;
        color: #4a90e2;
    }

    .category-actions {
        text-align: center;
    }

    .greenmetric-logo {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 12px;
    }

    .guideline-list {
        padding-left: 20px;
    }

    .guideline-list li {
        margin-bottom: 10px;
        line-height: 1.5;
    }

    .score-breakdown {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .score-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .score-range {
        font-weight: 600;
        color: #333;
    }

    .score-grade {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .score-grade.excellent {
        background: #d4edda;
        color: #155724;
    }

    .score-grade.good {
        background: #d1ecf1;
        color: #0c5460;
    }

    .score-grade.fair {
        background: #fff3cd;
        color: #856404;
    }

    .score-grade.poor {
        background: #f8d7da;
        color: #721c24;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .bg-success {
        background-color: #28a745 !important;
        color: white;
    }

    .bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .bg-danger {
        background-color: #dc3545 !important;
        color: white;
    }
</style>