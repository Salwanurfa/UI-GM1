<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<!-- Filter Section -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-filter"></i>
        <h3>Filter Data Penilaian</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('/admin-pusat/data-penilaian') ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tahun Penilaian:</label>
                <select name="tahun" class="form-control">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($allTahun as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($filters['tahun'] == $t['id']) ? 'selected' : '' ?>>
                            <?= $t['tahun'] ?> - <?= $t['nama_periode'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit:</label>
                <select name="unit" class="form-control">
                    <option value="">Semua Unit</option>
                    <?php foreach ($allUnits as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($filters['unit'] == $u['id']) ? 'selected' : '' ?>>
                            <?= $u['nama_unit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h4><?= count($submissions) ?></h4>
            <p>Total Pengiriman<br><small>Tahun <?= $tahun['tahun'] ?></small></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h4><?= count(array_filter($submissions, fn($s) => $s['status_pengiriman'] === 'disetujui')) ?></h4>
            <p>Disetujui<br><small>Data lengkap</small></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= count(array_filter($submissions, fn($s) => in_array($s['status_pengiriman'], ['dikirim', 'review']))) ?></h4>
            <p>Dalam Review<br><small>Sedang diproses</small></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-content">
            <h4><?= count(array_filter($submissions, fn($s) => $s['status_pengiriman'] === 'perlu_revisi')) ?></h4>
            <p>Perlu Revisi<br><small>Butuh perbaikan</small></p>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table"></i>
        <h3>Data Penilaian Unit</h3>
    </div>
    <div class="card-body">
        <?php if (empty($submissions)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data penilaian</h5>
                <p class="text-muted">Belum ada unit yang mengirimkan data untuk tahun ini.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Tanggal Kirim</th>
                            <th>Review Stats</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($submission['nama_unit']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($submission['kode_unit']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress-item">
                                        <div class="progress-header">
                                            <span class="progress-value"><?= number_format($submission['progress_persen'], 1) ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $submission['progress_persen'] ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($submission['status_pengiriman']) {
                                        case 'disetujui':
                                            $statusClass = 'status-lengkap';
                                            $statusText = 'DISETUJUI';
                                            break;
                                        case 'dikirim':
                                        case 'review':
                                            $statusClass = 'status-proses';
                                            $statusText = 'REVIEW';
                                            break;
                                        case 'perlu_revisi':
                                            $statusClass = 'status-perlu-revisi';
                                            $statusText = 'PERLU REVISI';
                                            break;
                                        default:
                                            $statusClass = 'status-proses';
                                            $statusText = strtoupper($submission['status_pengiriman']);
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <?php if ($submission['tanggal_kirim']): ?>
                                        <?= date('d/m/Y H:i', strtotime($submission['tanggal_kirim'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Belum dikirim</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="review-stats">
                                        <small>
                                            <i class="fas fa-check-circle text-success"></i> <?= $submission['review_stats']['disetujui'] ?> |
                                            <i class="fas fa-clock text-warning"></i> <?= $submission['review_stats']['pending'] ?> |
                                            <i class="fas fa-edit text-danger"></i> <?= $submission['review_stats']['perlu_revisi'] ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('/admin-pusat/review/' . $submission['id']) ?>"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Category Breakdown -->
<?php if (!empty($submissions)): ?>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-pie"></i>
            <h3>Breakdown per Kategori</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $categories = ['SI', 'EC', 'WS', 'WR', 'TR', 'ED'];
                $categoryNames = [
                    'SI' => 'Setting & Infrastructure',
                    'EC' => 'Energy & Climate Change',
                    'WS' => 'Waste',
                    'WR' => 'Water',
                    'TR' => 'Transportation',
                    'ED' => 'Education & Research'
                ];
                ?>
                <?php foreach ($categories as $cat): ?>
                    <div class="col-md-4 mb-3">
                        <div class="category-card">
                            <h6><?= $cat ?> - <?= $categoryNames[$cat] ?></h6>
                            <?php
                            $catStats = ['disetujui' => 0, 'pending' => 0, 'perlu_revisi' => 0];
                            foreach ($submissions as $submission) {
                                foreach ($submission['reviews'] as $review) {
                                    if ($review['kode_kategori'] === $cat) {
                                        $catStats[$review['status_review']]++;
                                    }
                                }
                            }
                            $total = array_sum($catStats);
                            ?>
                            <div class="category-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Disetujui:</span>
                                    <span class="stat-value text-success"><?= $catStats['disetujui'] ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Pending:</span>
                                    <span class="stat-value text-warning"><?= $catStats['pending'] ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Revisi:</span>
                                    <span class="stat-value text-danger"><?= $catStats['perlu_revisi'] ?></span>
                                </div>
                            </div>
                            <?php if ($total > 0): ?>
                                <div class="progress-bar mt-2">
                                    <div class="progress-fill" style="width: <?= ($catStats['disetujui'] / $total) * 100 ?>%"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<style>
    .category-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid #4a90e2;
    }

    .category-stats {
        margin-top: 10px;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 13px;
        color: #666;
    }

    .stat-value {
        font-weight: 600;
    }

    .review-stats {
        font-size: 12px;
    }

    .btn-group .btn {
        margin-right: 5px;
    }
</style>