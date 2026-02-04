<?php
/**
 * Dashboard TPS - UI GreenMetric POLBAN
 * Dashboard untuk pengelola TPS
 */

// Safety checks for variables
$stats = $stats ?? [
    'total_waste_today' => 0,
    'total_waste_month' => 0,
    'total_weight_today' => 0,
    'total_weight_month' => 0
];

$user = $user ?? ['nama_lengkap' => 'User'];
$tps_info = $tps_info ?? ['nama_unit' => 'TPS'];
$recent_waste = $recent_waste ?? [];
$monthly_summary = $monthly_summary ?? [];

// Helper function untuk display stats
if (!function_exists('displayStat')) {
    function displayStat($stats, $key) {
        return $stats[$key] ?? 0;
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard TPS' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-recycle"></i> Dashboard TPS</h1>
            <p>Selamat datang, <?= $user['nama_lengkap'] ?? 'Pengelola TPS' ?> - <?= $tps_info['nama_unit'] ?? 'TPS' ?></p>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Disabled Features Alert -->
        <?= renderDisabledFeaturesAlert('pengelola_tps') ?>

        <!-- Statistics Cards - Feature Toggle: dashboard_statistics_cards -->
        <?php if (isFeatureEnabled('dashboard_statistics_cards', 'pengelola_tps')): ?>
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'disetujui') ?></h3>
                    <p>Data Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'ditolak') ?></h3>
                    <p>Data Ditolak</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'menunggu_review') ?></h3>
                    <p>Menunggu Review</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-save"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'draft') ?></h3>
                    <p>Draft</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-weight-hanging"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format(displayStat($wasteOverallStats, 'total_berat'), 3, ',', '.') ?> kg</h3>
                    <p>Total Berat</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activity Notifications -->
        <?php if (!empty($recent_activities)): ?>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bell"></i> Aktivitas Terbaru</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Tanggal</th>
                                <th style="width: 150px;">Jenis Sampah</th>
                                <th style="width: 100px;">Berat (kg)</th>
                                <th style="width: 100px;">Satuan</th>
                                <th style="width: 120px;">Nilai (Rp)</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_activities as $activity): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($activity['tanggal_review'] ?? $activity['time'])) ?></td>
                                <td><?= esc($activity['jenis_sampah']) ?></td>
                                <td><?= number_format($activity['berat_kg'], 2) ?></td>
                                <td>kg</td>
                                <td><?= number_format($activity['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($activity['status']) {
                                        case 'draft':
                                            $statusClass = 'badge bg-secondary';
                                            $statusText = 'Draft';
                                            break;
                                        case 'dikirim':
                                            $statusClass = 'badge bg-info';
                                            $statusText = 'Dikirim';
                                            break;
                                        case 'review':
                                            $statusClass = 'badge bg-warning';
                                            $statusText = 'Review';
                                            break;
                                        case 'disetujui':
                                            $statusClass = 'badge bg-success';
                                            $statusText = 'Disetujui';
                                            break;
                                        case 'perlu_revisi':
                                        case 'ditolak':
                                            $statusClass = 'badge bg-danger';
                                            $statusText = 'Ditolak';
                                            break;
                                        default:
                                            $statusClass = 'badge bg-secondary';
                                            $statusText = ucfirst($activity['status']);
                                    }
                                    ?>
                                    <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <?php if ($activity['has_detail']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="showWasteDetail(<?= htmlspecialchars(json_encode($activity), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-info-circle"></i> Detail
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted" style="font-size: 11px;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Waste Data - Feature Toggle: dashboard_waste_summary -->
        <?php if (isFeatureEnabled('dashboard_waste_summary', 'pengelola_tps')): ?>
        <!-- Ringkasan Waste Management -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-trash-alt"></i> Ringkasan Waste Management</h3>
                <div class="card-actions">
                    <a href="<?= base_url('/pengelola-tps/waste') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Input Data
                    </a>
                    <?php if (isFeatureEnabled('export_data', 'pengelola_tps')): ?>
                    <a href="<?= base_url('/pengelola-tps/waste/export-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($wasteManagementSummary)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis Sampah</th>
                                    <th>Berat (kg)</th>
                                    <th>Satuan</th>
                                    <th>Nilai (Rp)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($wasteManagementSummary as $item): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
                                    <td><?= esc($item['jenis_sampah']) ?></td>
                                    <td><?= number_format($item['berat_kg'], 2) ?></td>
                                    <td><?= esc($item['satuan'] ?? 'kg') ?></td>
                                    <td><?= number_format($item['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch($item['status']) {
                                            case 'draft':
                                                $statusClass = 'badge bg-secondary';
                                                $statusText = 'Draft';
                                                break;
                                            case 'dikirim':
                                                $statusClass = 'badge bg-info';
                                                $statusText = 'Dikirim';
                                                break;
                                            case 'review':
                                                $statusClass = 'badge bg-warning';
                                                $statusText = 'Review';
                                                break;
                                            case 'disetujui':
                                                $statusClass = 'badge bg-success';
                                                $statusText = 'Disetujui';
                                                break;
                                            case 'perlu_revisi':
                                                $statusClass = 'badge bg-danger';
                                                $statusText = 'Perlu Revisi';
                                                break;
                                            default:
                                                $statusClass = 'badge bg-secondary';
                                                $statusText = ucfirst($item['status']);
                                        }
                                        ?>
                                        <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= base_url('/pengelola-tps/waste') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Lihat Semua Data
                        </a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data waste management. Mulai dengan menginput data baru.</p>
                        <a href="<?= base_url('/pengelola-tps/waste') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Input Data Pertama
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Waste Detail Modal -->
        <div class="modal fade" id="activityDetailModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Data Sampah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-item">
                            <strong>Jenis Sampah:</strong>
                            <span id="detail_jenis_sampah"></span>
                        </div>
                        <div class="detail-item">
                            <strong>Berat:</strong>
                            <span id="detail_berat"></span>
                        </div>
                        <div class="detail-item">
                            <strong>Nilai:</strong>
                            <span id="detail_nilai"></span>
                        </div>
                        <div class="detail-item">
                            <strong>Status:</strong>
                            <span id="detail_status"></span>
                        </div>
                        <div class="detail-item">
                            <strong>Direview oleh:</strong>
                            <span id="detail_reviewer"></span>
                        </div>
                        <div class="detail-item">
                            <strong>Tanggal Review:</strong>
                            <span id="detail_tanggal_review"></span>
                        </div>
                        <div class="detail-item">
                            <strong id="detail_catatan_label">Catatan:</strong>
                            <div id="detail_catatan" class="alert mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Summary Chart - Feature Toggle: dashboard_charts -->
        <?php if (isFeatureEnabled('dashboard_charts', 'pengelola_tps') && !empty($monthly_summary)): ?>
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> Ringkasan Bulanan <?= date('Y') ?></h3>
            </div>
            <div class="card-body">
                <div class="monthly-grid">
                    <?php 
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    $monthlyData = [];
                    foreach ($monthly_summary as $data) {
                        $monthlyData[$data['month']] = $data;
                    }
                    ?>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                    <div class="month-item">
                        <div class="month-header">
                            <h5><?= $months[$i-1] ?></h5>
                        </div>
                        <div class="month-stats">
                            <div class="month-stat">
                                <span class="stat-label">Data:</span>
                                <strong><?= $monthlyData[$i]['count'] ?? 0 ?></strong>
                            </div>
                            <div class="month-stat">
                                <span class="stat-label">Berat:</span>
                                <strong><?= formatNumber($monthlyData[$i]['total_weight'] ?? 0) ?> kg</strong>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <!-- Recent Activity - Feature Toggle: dashboard_recent_activity -->
        <?php if (isFeatureEnabled('dashboard_recent_activity', 'pengelola_tps') && !empty($recent_activities)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Aktivitas Terbaru</h3>
            </div>
            <div class="card-body">
                <div class="activity-list">
                    <?php foreach ($recent_activities as $activity): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-<?= $activity['icon'] ?>"></i>
                        </div>
                        <div class="activity-content">
                            <p><?= $activity['message'] ?></p>
                            <small class="text-muted"><?= $activity['time'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Data Disetujui TPS -->
        <div class="card mt-4">
            <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
                <h3><i class="fas fa-check-circle"></i> Data Disetujui oleh TPS</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($approved_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 140px;">Tanggal</th>
                                    <th style="width: 150px;">Unit</th>
                                    <th style="width: 150px;">User</th>
                                    <th style="width: 150px;">Jenis Sampah</th>
                                    <th style="width: 150px;">Nama Sampah</th>
                                    <th style="width: 80px;">Berat</th>
                                    <th style="width: 120px;">Total Nilai</th>
                                    <th style="width: 200px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($approved_data as $index => $data): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($data['tps_reviewed_at'])) ?></td>
                                    <td><?= esc($data['unit_nama'] ?? 'N/A') ?></td>
                                    <td><?= esc($data['user_nama'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= esc($data['jenis_sampah'] ?? 'N/A') ?></span>
                                    </td>
                                    <td><?= esc($data['nama_sampah'] ?? $data['jenis_sampah'] ?? 'N/A') ?></td>
                                    <td><?php 
                                        $berat = $data['berat_kg'] ?? 0;
                                        echo ($berat == floor($berat)) ? number_format($berat, 0, ',', '.') : number_format($berat, 2, ',', '.');
                                    ?> kg</td>
                                    <td>Rp <?= number_format($data['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                                    <td>
                                        <small class="text-success">
                                            <?= !empty($data['tps_catatan']) ? esc($data['tps_catatan']) : 'Disetujui oleh TPS' ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data yang disetujui.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Data Ditolak TPS -->
        <div class="card mt-4">
            <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                <h3><i class="fas fa-times-circle"></i> Data Ditolak oleh TPS</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($rejected_data)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th style="width: 140px;">Tanggal</th>
                                    <th style="width: 150px;">Unit</th>
                                    <th style="width: 150px;">User</th>
                                    <th style="width: 150px;">Jenis Sampah</th>
                                    <th style="width: 150px;">Nama Sampah</th>
                                    <th style="width: 80px;">Berat</th>
                                    <th style="width: 120px;">Total Nilai</th>
                                    <th style="width: 200px;">Alasan Penolakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rejected_data as $index => $data): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($data['tps_reviewed_at'])) ?></td>
                                    <td><?= esc($data['unit_nama'] ?? 'N/A') ?></td>
                                    <td><?= esc($data['user_nama'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= esc($data['jenis_sampah'] ?? 'N/A') ?></span>
                                    </td>
                                    <td><?= esc($data['nama_sampah'] ?? $data['jenis_sampah'] ?? 'N/A') ?></td>
                                    <td><?php 
                                        $berat = $data['berat_kg'] ?? 0;
                                        echo ($berat == floor($berat)) ? number_format($berat, 0, ',', '.') : number_format($berat, 2, ',', '.');
                                    ?> kg</td>
                                    <td>Rp <?= number_format($data['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                                    <td>
                                        <small class="text-danger">
                                            <?= !empty($data['tps_catatan']) ? esc($data['tps_catatan']) : (!empty($data['rejection_reason']) ? esc($data['rejection_reason']) : 'Tidak ada alasan') ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data yang ditolak.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Help Section -->
        <div class="help-section mt-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-question-circle"></i> Bantuan Dashboard TPS
                    </h6>
                    <p class="card-text">
                        Dashboard TPS ini dapat dikustomisasi oleh Administrator. 
                        Gunakan fitur yang tersedia untuk mengelola operasional TPS dengan efisien.
                    </p>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Dashboard terakhir diperbarui: <?= date('d/m/Y H:i') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Show activity detail modal
        function showActivityDetail(activity) {
            document.getElementById('detail_jenis_sampah').textContent = activity.jenis_sampah;
            document.getElementById('detail_berat').textContent = parseFloat(activity.berat_kg).toFixed(2) + ' kg';
            document.getElementById('detail_nilai').textContent = 'Rp ' + parseInt(activity.nilai_rupiah).toLocaleString('id-ID');
            
            // Status badge
            let statusBadge = '';
            let catatanClass = '';
            let catatanLabel = '';
            
            if (activity.status === 'disetujui') {
                statusBadge = '<span class="badge bg-success">Disetujui</span>';
                catatanClass = 'alert-success';
                catatanLabel = 'Alasan Disetujui:';
            } else if (activity.status === 'ditolak') {
                statusBadge = '<span class="badge bg-danger">Ditolak</span>';
                catatanClass = 'alert-danger';
                catatanLabel = 'Alasan Ditolak:';
            }
            
            document.getElementById('detail_status').innerHTML = statusBadge;
            document.getElementById('detail_reviewer').textContent = activity.reviewer_name;
            document.getElementById('detail_tanggal_review').textContent = new Date(activity.tanggal_review).toLocaleString('id-ID');
            
            // Catatan review
            document.getElementById('detail_catatan_label').textContent = catatanLabel;
            const catatanDiv = document.getElementById('detail_catatan');
            catatanDiv.className = 'alert mt-2 ' + catatanClass;
            catatanDiv.textContent = activity.catatan_review || 'Tidak ada catatan';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('activityDetailModal'));
            modal.show();
        }
    </script>
    
    <!-- Dashboard Auto-refresh -->
    <script>
        // Auto refresh dashboard data every 5 minutes
        setInterval(function() {
            // Refresh widget data via AJAX
            console.log('TPS Dashboard auto-refresh triggered');
            // Implementation would go here
        }, 300000); // 5 minutes
        
        // Widget interaction tracking
        document.querySelectorAll('.widget-container').forEach(function(widget) {
            widget.addEventListener('click', function() {
                const widgetType = this.dataset.widget;
                console.log('TPS Widget interaction:', widgetType);
                // Analytics tracking would go here
            });
        });
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

<?php
function getWasteIcon($jenis) {
    $icons = [
        'Kertas' => 'file-alt',
        'Plastik' => 'wine-bottle',
        'Organik' => 'seedling',
        'Anorganik' => 'cube',
        'Logam' => 'cog',
        'Residu' => 'trash',
        'Limbah Cair' => 'flask',
        'B3' => 'exclamation-triangle'
    ];
    return $icons[$jenis] ?? 'trash';
}
?>
<style>

/* ===== MAIN LAYOUT ===== */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f9fa;
}

.main-content {
    margin-left: 280px;
    padding: 30px;
    min-height: 100vh;
    max-width: calc(100vw - 280px);
    overflow-x: hidden;
}

/* ===== DASHBOARD HEADER ===== */
.dashboard-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.dashboard-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.dashboard-header p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* ===== STATISTICS CARDS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    min-height: 100px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.danger { border-left-color: #dc3545; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.info { border-left-color: #17a2b8; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}

.stat-card.primary .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card.danger .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-weight: 500;
    font-size: 14px;
}

/* ===== CARDS ===== */
.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: none;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-actions {
    display: flex;
    gap: 10px;
}

.card-actions .btn {
    border-radius: 8px;
    font-weight: 500;
}

.card-body {
    padding: 25px;
}

/* ===== TABLES ===== */
.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 15px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
}

.table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* ===== MONTHLY GRID ===== */
.monthly-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.month-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    border-left: 4px solid #007bff;
}

.month-header h5 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-weight: 600;
}

.month-stats {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.month-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
}

.stat-label {
    color: #6c757d;
}

/* ===== EMPTY STATE ===== */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0 0 20px 0;
    font-size: 16px;
}

/* ===== ACTIVITY LIST ===== */
.activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #e9ecef;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-content p {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.detail-item {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item strong {
    display: block;
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 14px;
}

.detail-item span {
    color: #6c757d;
    font-size: 14px;
}

/* ===== ALERTS ===== */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 10px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

/* ===== BUTTONS ===== */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px 10px;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .stat-card {
        padding: 20px 15px;
        flex-direction: column;
        text-align: center;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
        margin-right: 0;
        margin-bottom: 15px;
    }

    .stat-content h3 {
        font-size: 28px;
    }

    .stat-content p {
        font-size: 13px;
    }
    
    .dashboard-header {
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .dashboard-header h1 {
        font-size: 22px;
    }

    .dashboard-header p {
        font-size: 14px;
    }
    
    .card-header {
        padding: 12px 15px;
    }
    
    .card-body {
        padding: 15px 10px;
    }
    
    .monthly-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .card-actions {
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    .card-actions .btn {
        width: 100%;
    }

    .card {
        max-width: 100%;
        overflow-x: hidden;
    }

    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        font-size: 11px;
        min-width: 600px;
    }

    .table th,
    .table td {
        padding: 8px 6px;
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .main-content {
        padding: 10px 5px;
    }

    .stat-card {
        padding: 12px;
    }

    .card-header {
        padding: 10px 12px;
    }

    .card-body {
        padding: 12px 8px;
    }

    .monthly-grid {
        grid-template-columns: 1fr;
    }
}
</style>