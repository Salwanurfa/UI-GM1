<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<!-- Filter Section -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-filter"></i>
        <h3>Filter Riwayat</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?= base_url('/admin-pusat/riwayat-penilaian') ?>" class="row g-3">
            <div class="col-md-5">
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
            <div class="col-md-5">
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
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary form-control">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Statistics -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-line"></i>
        <h3>Ringkasan Statistik per Tahun</h3>
    </div>
    <div class="card-body">
        <?php if (empty($summaryStats)): ?>
            <div class="text-center py-4">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada data riwayat</h5>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Draft</th>
                            <th>Dikirim</th>
                            <th>Review</th>
                            <th>Perlu Revisi</th>
                            <th>Disetujui</th>
                            <th>Total</th>
                            <th>Tingkat Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($summaryStats as $yearId => $stats): ?>
                            <?php
                            $total = array_sum(array_slice($stats, 1)); // Exclude 'tahun' key
                            $completed = $stats['disetujui'];
                            $completionRate = $total > 0 ? ($completed / $total) * 100 : 0;
                            ?>
                            <tr>
                                <td><strong><?= $stats['tahun'] ?></strong></td>
                                <td><span class="badge bg-secondary"><?= $stats['draft'] ?></span></td>
                                <td><span class="badge bg-info"><?= $stats['dikirim'] ?></span></td>
                                <td><span class="badge bg-warning"><?= $stats['review'] ?></span></td>
                                <td><span class="badge bg-danger"><?= $stats['perlu_revisi'] ?></span></td>
                                <td><span class="badge bg-success"><?= $stats['disetujui'] ?></span></td>
                                <td><strong><?= $total ?></strong></td>
                                <td>
                                    <div class="progress-item">
                                        <div class="progress-header">
                                            <span class="progress-value"><?= number_format($completionRate, 1) ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $completionRate ?>%"></div>
                                        </div>
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

<!-- Historical Data -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i>
        <h3>Riwayat Penilaian Detail</h3>
    </div>
    <div class="card-body">
        <?php if (empty($riwayat)): ?>
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada riwayat penilaian</h5>
                <p class="text-muted">Belum ada data penilaian yang selesai untuk filter yang dipilih.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun</th>
                            <th>Unit</th>
                            <th>Progress</th>
                            <th>Status Akhir</th>
                            <th>Tanggal Kirim</th>
                            <th>Tanggal Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $item): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?= $item['tahun'] ?></strong>
                                        <br>
                                        <small class="text-muted"><?= $item['nama_periode'] ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($item['nama_unit']) ?></strong>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($item['kode_unit']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="progress-item">
                                        <div class="progress-header">
                                            <span class="progress-value"><?= number_format($item['progress_persen'], 1) ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= $item['progress_persen'] ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    $statusIcon = '';
                                    switch ($item['status_pengiriman']) {
                                        case 'disetujui':
                                            $statusClass = 'status-approved';
                                            $statusText = 'DISETUJUI';
                                            $statusIcon = 'fas fa-check-circle';
                                            break;
                                        case 'perlu_revisi':
                                            $statusClass = 'status-revision';
                                            $statusText = 'PERLU REVISI';
                                            $statusIcon = 'fas fa-edit';
                                            break;
                                        default:
                                            $statusClass = 'status-process';
                                            $statusText = strtoupper($item['status_pengiriman']);
                                            $statusIcon = 'fas fa-clock';
                                    }
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <i class="<?= $statusIcon ?>"></i> <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($item['tanggal_kirim']): ?>
                                        <div>
                                            <strong><?= date('d/m/Y', strtotime($item['tanggal_kirim'])) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['tanggal_kirim'])) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($item['updated_at']): ?>
                                        <div>
                                            <strong><?= date('d/m/Y', strtotime($item['updated_at'])) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['updated_at'])) ?></small>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('/admin-pusat/review/' . $item['id']) ?>"
                                            class="btn btn-outline-primary btn-sm"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-info btn-sm"
                                            onclick="showHistoryDetail(<?= $item['id'] ?>)"
                                            title="Riwayat Perubahan">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($pager): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Trends Analysis -->
<?php if (!empty($summaryStats)): ?>
    <div class="card">
        <div class="card-header">
            <i class="fas fa-chart-area"></i>
            <h3>Analisis Tren</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Tren Penyelesaian per Tahun</h6>
                    <div class="trend-chart">
                        <?php foreach (array_reverse($summaryStats, true) as $yearId => $stats): ?>
                            <?php
                            $total = array_sum(array_slice($stats, 1));
                            $completionRate = $total > 0 ? ($stats['disetujui'] / $total) * 100 : 0;
                            ?>
                            <div class="trend-item">
                                <div class="trend-year"><?= $stats['tahun'] ?></div>
                                <div class="trend-bar">
                                    <div class="trend-fill" style="width: <?= $completionRate ?>%"></div>
                                </div>
                                <div class="trend-value"><?= number_format($completionRate, 1) ?>%</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Statistik Keseluruhan</h6>
                    <div class="overall-stats">
                        <?php
                        $totalSubmissions = 0;
                        $totalApproved = 0;
                        $totalRevision = 0;
                        foreach ($summaryStats as $stats) {
                            $totalSubmissions += array_sum(array_slice($stats, 1));
                            $totalApproved += $stats['disetujui'];
                            $totalRevision += $stats['perlu_revisi'];
                        }
                        $overallRate = $totalSubmissions > 0 ? ($totalApproved / $totalSubmissions) * 100 : 0;
                        ?>
                        <div class="stat-card-small">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?= $totalSubmissions ?></h4>
                                <p>Total Pengiriman</p>
                            </div>
                        </div>
                        <div class="stat-card-small">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?= $totalApproved ?></h4>
                                <p>Disetujui</p>
                            </div>
                        </div>
                        <div class="stat-card-small">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <h4><?= number_format($overallRate, 1) ?>%</h4>
                                <p>Tingkat Keberhasilan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<style>
    .status-approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-revision {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .status-process {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .trend-chart {
        margin-top: 15px;
    }

    .trend-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        gap: 15px;
    }

    .trend-year {
        width: 60px;
        font-weight: 600;
        font-size: 14px;
    }

    .trend-bar {
        flex: 1;
        height: 20px;
        background: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .trend-fill {
        height: 100%;
        background: linear-gradient(90deg, #4a90e2, #357abd);
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    .trend-value {
        width: 60px;
        text-align: right;
        font-weight: 600;
        font-size: 14px;
        color: #4a90e2;
    }

    .overall-stats {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
    }

    .stat-card-small {
        display: flex;
        align-items: center;
        gap: 15px;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }

    .stat-icon.bg-primary {
        background: #4a90e2;
    }

    .stat-icon.bg-success {
        background: #28a745;
    }

    .stat-icon.bg-warning {
        background: #ffc107;
    }

    .stat-content h4 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }

    .stat-content p {
        margin: 0;
        font-size: 13px;
        color: #666;
    }

    .btn-group .btn {
        margin-right: 5px;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .bg-secondary {
        background-color: #6c757d !important;
        color: white;
    }

    .bg-info {
        background-color: #17a2b8 !important;
        color: white;
    }
</style>

<script>
    function showHistoryDetail(pengirimanId) {
        // Placeholder for history detail modal
        alert('Fitur riwayat detail akan segera tersedia untuk pengiriman ID: ' + pengirimanId);
    }

    // Animate trend bars on load
    document.addEventListener('DOMContentLoaded', function() {
        const trendBars = document.querySelectorAll('.trend-fill');
        trendBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 500);
        });
    });
</script>