<?php
/**
 * Dashboard User - UI GreenMetric POLBAN
 * Dashboard untuk user waste management
 */

// Safety checks for variables
$user = $user ?? ['nama_lengkap' => 'User'];
$unit = $unit ?? ['nama_unit' => 'Unit'];
$stats = $stats ?? [];
$wasteOverallStats = $wasteOverallStats ?? [];
$wasteStats = $wasteStats ?? [];
$recentActivities = $recentActivities ?? [];
$featureData = $featureData ?? [];
$limbah_b3_list = $limbah_b3_list ?? [];

// Helper function untuk display stats
if (!function_exists('displayStat')) {
    function displayStat($stats, $key) {
        return $stats[$key] ?? 0;
    }
}

// Load feature helper
helper('feature');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard User' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard User Waste Management</h1>
            <p>Kelola data waste management unit Anda</p>
            
            <!-- Development Info -->
            <?php if (ENVIRONMENT === 'development'): ?>
            <div class="feature-indicator mt-2">
                <small class="text-muted">
                    <i class="fas fa-cog text-info"></i> 
                    Dashboard dengan feature toggles aktif
                </small>
            </div>
            <?php endif; ?>
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
        <?= renderDisabledFeaturesAlert('user') ?>

        <!-- Statistics Cards - Feature Toggle: dashboard_statistics_cards -->
        <?php if (isFeatureEnabled('dashboard_statistics_cards', 'user')): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'disetujui') ?></h3>
                    <p>Data Disetujui</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'ditolak') ?></h3>
                    <p>Data Ditolak</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'menunggu_review') ?></h3>
                    <p>Menunggu Review</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-save"></i>
                </div>
                <div class="stat-content">
                    <h3><?= displayStat($wasteOverallStats, 'draft') ?></h3>
                    <p>Draft</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-weight-hanging"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format(displayStat($wasteOverallStats, 'total_berat'), 3, ',', '.') ?> kg</h3>
                    <p>Total Berat</p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Dashboard statistics are currently disabled by administrator.
        </div>
        <?php endif; ?>

        <!-- Recent Activity Notifications -->
        <?php if (!empty($recentActivities)): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell"></i>
                <h3>Aktivitas Terbaru</h3>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentActivities as $activity): ?>
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

        <!-- Waste Management Summary - Feature Toggle: dashboard_waste_summary -->
        <?php if (isFeatureEnabled('dashboard_waste_summary', 'user')): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-trash-alt"></i>
                <h3>List Data Sampah</h3>
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="<?= base_url('/user/waste') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Input Data
                    </a>
                    <?php if (isFeatureEnabled('export_data', 'user')): ?>
                    <a href="<?= base_url('/user/waste/export-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
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
                    <a href="<?= base_url('/user/waste') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lihat Semua Data
                    </a>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Belum ada data waste management. Mulai dengan menginput data baru.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            Waste management summary is currently disabled by administrator.
        </div>
        <?php endif; ?>

        <!-- Limbah B3 Summary - Tabel Daftar Limbah B3 -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-skull-crossbones"></i>
                <h3>Daftar Limbah B3</h3>
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="<?= base_url('/user/limbah-b3') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Input Data
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php 
                $limbah_b3_list = $limbah_b3_list ?? [];
                if (!empty($limbah_b3_list)): 
                ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama Limbah</th>
                                <th>Lokasi</th>
                                <th>Timbulan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($limbah_b3_list as $limbah): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($limbah['tanggal_input'])) ?></td>
                                <td><?= esc($limbah['nama_limbah']) ?></td>
                                <td><?= esc($limbah['lokasi'] ?? '-') ?></td>
                                <td><?= number_format($limbah['timbulan'], 2) ?> <?= esc($limbah['satuan']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($limbah['status']) {
                                        case 'draft':
                                            $statusClass = 'badge bg-secondary';
                                            $statusText = 'Draft';
                                            break;
                                        case 'dikirim_ke_tps':
                                            $statusClass = 'badge bg-warning';
                                            $statusText = 'Menunggu Review TPS';
                                            break;
                                        case 'disetujui_tps':
                                            $statusClass = 'badge bg-info';
                                            $statusText = 'Disetujui TPS';
                                            break;
                                        case 'ditolak_tps':
                                            $statusClass = 'badge bg-danger';
                                            $statusText = 'Ditolak TPS';
                                            break;
                                        case 'disetujui_admin':
                                            $statusClass = 'badge bg-success';
                                            $statusText = 'Disetujui Admin';
                                            break;
                                        default:
                                            $statusClass = 'badge bg-secondary';
                                            $statusText = ucfirst($limbah['status']);
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
                    <a href="<?= base_url('/user/limbah-b3') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lihat Semua Data Limbah B3
                    </a>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Belum ada data Limbah B3. Mulai dengan menginput data baru.
                </div>
                <?php endif; ?>
            </div>
        </div>

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

        <!-- Help Section -->
        <div class="help-section mt-4">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="card-title text-info">
                        <i class="fas fa-question-circle"></i> Bantuan
                    </h6>
                    <p class="card-text">
                        Gunakan menu <strong>Waste Management</strong> untuk input data sampah. 
                        Data akan otomatis dihitung nilainya berdasarkan harga yang berlaku.
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
    
    <!-- Real-time Updates -->
    <?php if (isFeatureEnabled('real_time_updates', 'user')): ?>
    <script>
        // Auto refresh data based on feature configuration
        const realTimeConfig = <?= json_encode($featureData['real_time_config'] ?? ['enabled' => false]) ?>;
        
        if (realTimeConfig.enabled) {
            const refreshInterval = (realTimeConfig.refresh_interval || 30) * 1000;
            
            console.log('Real-time updates enabled. Refresh interval:', refreshInterval / 1000, 'seconds');
            
            setInterval(function() {
                fetch('<?= base_url('/user/dashboard/api-stats') ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateDashboardStats(data.stats);
                            console.log('Dashboard stats updated at', data.stats.timestamp);
                        }
                    })
                    .catch(error => console.log('Auto-refresh failed:', error));
            }, refreshInterval);
        }
        
        function updateDashboardStats(stats) {
            // Update stat cards with new data
            const statCards = document.querySelectorAll('.stat-content h3');
            if (stats.wasteOverallStats) {
                const overall = stats.wasteOverallStats;
                if (statCards[0]) statCards[0].textContent = overall.disetujui || 0;
                if (statCards[1]) statCards[1].textContent = overall.perlu_revisi || 0;
                if (statCards[2]) statCards[2].textContent = overall.dikirim || 0;
                if (statCards[3]) statCards[3].textContent = overall.draft || 0;
            }
        }
    </script>
    <?php endif; ?>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

<style>
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
}

.dashboard-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.dashboard-header h1 {
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 5px;
}

.dashboard-header p {
    color: #7f8c8d;
    font-size: 16px;
    margin: 0;
}

.feature-indicator {
    margin-top: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: none;
    display: flex;
    align-items: center;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 24px;
    color: white;
}

.stat-icon.green { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-icon.red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-icon.purple { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
.stat-icon.orange { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

.stat-content h3 {
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.stat-content p {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0;
    font-weight: 500;
}

.card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.quick-actions {
    display: flex;
    gap: 10px;
}

.waste-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.waste-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.waste-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.waste-header h5 {
    margin: 0;
    color: #2c3e50;
}

.waste-total {
    background: #007bff;
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.waste-stats {
    margin-bottom: 15px;
}

.waste-stat {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.activity-list {
    max-height: 300px;
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

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 10px;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}

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
    
    .waste-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        flex-direction: column;
        gap: 10px;
    }

    .quick-actions .btn {
        width: 100%;
    }

    .card {
        max-width: 100%;
        overflow-x: hidden;
    }

    .card-header {
        padding: 12px 15px;
    }

    .card-body {
        padding: 15px 10px;
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

    .page-header h1 {
        font-size: 22px;
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
}
</style>