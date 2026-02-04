<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<style>
    .nav-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .nav-link {
        background: white;
        color: #4a90e2;
        padding: 20px 25px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(74, 144, 226, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .nav-link:hover::before {
        left: 100%;
    }

    .nav-link:hover,
    .nav-link.active {
        background: #4a90e2;
        color: white;
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
    }

    .nav-link i {
        font-size: 20px;
        width: 24px;
    }

    .progress-bar {
        width: 100%;
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .progress-text {
        font-size: 12px;
        font-weight: 600;
        color: white;
        text-align: center;
        line-height: 20px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-draft {
        background: #f5f5f5;
        color: #666;
    }

    .status-dikirim {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-review {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-disetujui {
        background: #e8f5e8;
        color: #388e3c;
    }

    .status-perlu_revisi {
        background: #ffebee;
        color: #d32f2f;
    }

    .filter-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 25px;
        margin-bottom: 30px;
    }

    .filter-section h5 {
        color: #4a90e2;
        margin-bottom: 20px;
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .form-control:focus {
        outline: none;
        border-color: #4a90e2;
        background: white;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .table-container {
        overflow-x: auto;
    }

    .table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #e0e0e0;
    }

    .table tr:hover {
        background: #f8f9fa;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-primary {
        background: #d1ecf1;
        color: #0c5460;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
</style>

<!-- Navigation Links -->
<div class="nav-links">
    <a href="<?= base_url('/admin-pusat/dashboard') ?>" class="nav-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="<?= base_url('/admin-pusat/monitoring') ?>" class="nav-link active">
        <i class="fas fa-chart-line"></i> Monitoring Unit
    </a>
    <a href="<?= base_url('/admin-pusat/notifikasi') ?>" class="nav-link">
        <i class="fas fa-bell"></i> Notifikasi
    </a>
    <a href="<?= base_url('/report') ?>" class="nav-link">
        <i class="fas fa-file-export"></i> Laporan
    </a>
</div>

<!-- Statistik -->
<div class="stats-grid">
    <?php
    $totalUnit = count($units ?? []);
    $dikirim = 0;
    $review = 0;
    $disetujui = 0;
    $perluRevisi = 0;
    $draft = 0;

    if (!empty($units)) {
        foreach ($units as $unit) {
            $status = $unit['status_pengiriman'] ?? 'draft';
            switch ($status) {
                case 'draft':
                    $draft++;
                    break;
                case 'dikirim':
                    $dikirim++;
                    break;
                case 'review':
                    $review++;
                    break;
                case 'disetujui':
                    $disetujui++;
                    break;
                case 'perlu_revisi':
                    $perluRevisi++;
                    break;
            }
        }
    }
    ?>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-content">
            <h4><?= $totalUnit ?></h4>
            <p>Total Unit</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-content">
            <h4><?= $draft ?></h4>
            <p>Draft</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h4><?= $dikirim + $review ?></h4>
            <p>Perlu Review</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check"></i>
        </div>
        <div class="stat-content">
            <h4><?= $disetujui ?></h4>
            <p>Disetujui</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <h5><i class="fas fa-filter me-2"></i>Filter Data</h5>
    <div class="filter-grid">
        <div>
            <label>Filter Status:</label>
            <select id="statusFilter" class="form-control" onchange="filterTable()">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="dikirim">Dikirim</option>
                <option value="review">Sedang Review</option>
                <option value="disetujui">Disetujui</option>
                <option value="perlu_revisi">Perlu Revisi</option>
            </select>
        </div>
        <div>
            <label>Cari Unit:</label>
            <input type="text" id="searchUnit" class="form-control" placeholder="Nama atau kode unit..." onkeyup="filterTable()">
        </div>
        <div>
            <label>&nbsp;</label>
            <button type="button" class="btn btn-outline-primary form-control" onclick="resetFilter()">
                <i class="fas fa-undo"></i> Reset Filter
            </button>
        </div>
    </div>
</div>

<!-- Units Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-building"></i>
        <h3>Daftar Unit dan Progress</h3>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table" id="unitsTable">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Tanggal Kirim</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): ?>
                        <?php foreach ($units as $unit): ?>
                            <tr data-status="<?= htmlspecialchars($unit['status_pengiriman'] ?? 'draft', ENT_QUOTES, 'UTF-8') ?>"
                                data-unit="<?= htmlspecialchars(strtolower(($unit['nama_unit'] ?? '') . ' ' . ($unit['kode_unit'] ?? '')), ENT_QUOTES, 'UTF-8') ?>">
                                <td>
                                    <strong><?= htmlspecialchars($unit['nama_unit'] ?? 'Nama Unit Tidak Tersedia', ENT_QUOTES, 'UTF-8') ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars(ucfirst($unit['tipe_unit'] ?? 'Tidak Diketahui'), ENT_QUOTES, 'UTF-8') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?= htmlspecialchars($unit['kode_unit'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <?php
                                    $status = $unit['status_pengiriman'] ?? 'draft';
                                    $statusText = '';
                                    switch ($status) {
                                        case 'draft':
                                            $statusText = 'Draft';
                                            break;
                                        case 'dikirim':
                                            $statusText = 'Dikirim';
                                            break;
                                        case 'review':
                                            $statusText = 'Sedang Review';
                                            break;
                                        case 'disetujui':
                                            $statusText = 'Disetujui';
                                            break;
                                        case 'perlu_revisi':
                                            $statusText = 'Perlu Revisi';
                                            break;
                                        default:
                                            $statusText = 'Belum Ada Data';
                                            $status = 'draft';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge status-<?= $status ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $progress = 0;
                                    if (isset($unit['progress_persen']) && $unit['progress_persen'] !== null) {
                                        $progress = (float)$unit['progress_persen'];
                                    }
                                    ?>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $progress ?>%">
                                            <?php if ($progress > 0): ?>
                                                <div class="progress-text"><?= number_format($progress, 1) ?>%</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($progress == 0): ?>
                                        <small class="text-muted">0%</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($unit['tanggal_kirim'])) {
                                        try {
                                            echo date('d/m/Y H:i', strtotime($unit['tanggal_kirim']));
                                        } catch (Exception $e) {
                                            echo '-';
                                        }
                                    } else {
                                        echo '<span class="text-muted">Belum Dikirim</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (!empty($unit['pengiriman_id'])): ?>
                                        <a href="<?= base_url('/admin-pusat/review/' . $unit['pengiriman_id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Belum ada data
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div style="padding: 40px;">
                                    <i class="fas fa-inbox" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                                    <p style="color: #666;">Tidak ada data unit yang tersedia</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function filterTable() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const searchUnit = document.getElementById('searchUnit').value.toLowerCase();
        const table = document.getElementById('unitsTable');
        const rows = table.getElementsByTagName('tr');
        let visibleRows = 0;

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const status = row.getAttribute('data-status') || '';
            const unitText = row.getAttribute('data-unit') || '';

            let showRow = true;

            // Filter berdasarkan status
            if (statusFilter && status !== statusFilter) {
                showRow = false;
            }

            // Filter berdasarkan pencarian unit
            if (searchUnit && !unitText.includes(searchUnit)) {
                showRow = false;
            }

            if (showRow) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        }

        // Tampilkan pesan jika tidak ada hasil
        const tbody = table.querySelector('tbody');
        let noResultsRow = tbody.querySelector('.no-results-row');

        if (visibleRows === 0 && (statusFilter || searchUnit)) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="6" class="text-center">
                        <div style="padding: 40px;">
                            <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                            <p style="color: #666;">Tidak ada unit yang sesuai dengan filter yang dipilih</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else if (noResultsRow) {
            noResultsRow.style.display = 'none';
        }
    }

    // Reset filter
    function resetFilter() {
        document.getElementById('statusFilter').value = '';
        document.getElementById('searchUnit').value = '';
        filterTable();
    }

    // Auto refresh setiap 2 menit
    setInterval(function() {
        console.log('Memperbarui data monitoring...');
        location.reload();
    }, 120000);

    // Tampilkan loading saat tombol review diklik
    document.addEventListener('DOMContentLoaded', function() {
        const reviewButtons = document.querySelectorAll('.btn-primary');
        reviewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (this.href) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat...';
                    this.style.pointerEvents = 'none';
                }
            });
        });

        // Tampilkan pesan jika ada error dari session
        <?php if (session()->getFlashdata('error')): ?>
            showAlert('<?= addslashes(session()->getFlashdata('error')) ?>', 'danger');
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            showAlert('<?= addslashes(session()->getFlashdata('success')) ?>', 'success');
        <?php endif; ?>
    });

    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.style.position = 'fixed';
        alertDiv.style.top = '20px';
        alertDiv.style.right = '20px';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'check-circle'}"></i>
            ${message}
            <button type="button" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;" onclick="this.parentElement.remove()">&times;</button>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Keyboard shortcut untuk pencarian
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('searchUnit').focus();
        }
    });
</script>
<?= $this->endSection() ?>