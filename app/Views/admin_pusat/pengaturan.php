<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<!-- System Settings -->
<div class="row">
    <div class="col-md-8">
        <!-- Tahun Penilaian Settings -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i>
                <h3>Pengaturan Tahun Penilaian</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tahun</th>
                                <th>Nama Periode</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allTahun as $tahun): ?>
                                <tr>
                                    <td><strong><?= $tahun['tahun'] ?></strong></td>
                                    <td><?= htmlspecialchars($tahun['nama_periode']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($tahun['tanggal_mulai'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($tahun['tanggal_selesai'])) ?></td>
                                    <td>
                                        <?php if ($tahun['status_aktif']): ?>
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle"></i> AKTIF
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-pause-circle"></i> TIDAK AKTIF
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <?php if (!$tahun['status_aktif']): ?>
                                                <button class="btn btn-success btn-sm"
                                                    onclick="setActiveTahun(<?= $tahun['id'] ?>)">
                                                    <i class="fas fa-play"></i> Aktifkan
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-outline-primary btn-sm"
                                                onclick="editTahun(<?= $tahun['id'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" onclick="addNewTahun()">
                        <i class="fas fa-plus"></i> Tambah Tahun Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Unit Management -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-building"></i>
                <h3>Manajemen Unit</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Unit</th>
                                <th>Nama Unit</th>
                                <th>Tipe</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUnits as $unit): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($unit['kode_unit']) ?></strong></td>
                                    <td><?= htmlspecialchars($unit['nama_unit']) ?></td>
                                    <td>
                                        <span class="type-badge type-<?= $unit['tipe_unit'] ?? 'unit' ?>">
                                            <?= ucfirst($unit['tipe_unit'] ?? 'Unit') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= $unit['deskripsi'] ? htmlspecialchars($unit['deskripsi']) : '-' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($unit['status_aktif']): ?>
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle"></i> AKTIF
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-times-circle"></i> TIDAK AKTIF
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-outline-primary btn-sm"
                                                onclick="editUnit(<?= $unit['id'] ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-outline-<?= $unit['status_aktif'] ? 'warning' : 'success' ?> btn-sm"
                                                onclick="toggleUnitStatus(<?= $unit['id'] ?>, <?= $unit['status_aktif'] ? 'false' : 'true' ?>)">
                                                <i class="fas fa-<?= $unit['status_aktif'] ? 'pause' : 'play' ?>"></i>
                                                <?= $unit['status_aktif'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary" onclick="addNewUnit()">
                        <i class="fas fa-plus"></i> Tambah Unit Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- System Information -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i>
                <h3>Informasi Sistem</h3>
            </div>
            <div class="card-body">
                <div class="system-info">
                    <div class="info-item">
                        <div class="info-label">Versi Sistem:</div>
                        <div class="info-value">UIGM Dashboard v2.0</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Database:</div>
                        <div class="info-value">MySQL 8.0</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Framework:</div>
                        <div class="info-value">CodeIgniter 4.4.6</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">PHP Version:</div>
                        <div class="info-value"><?= phpversion() ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Server Time:</div>
                        <div class="info-value"><?= date('d/m/Y H:i:s') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-users"></i>
                <h3>Manajemen Pengguna</h3>
            </div>
            <div class="card-body">
                <div class="user-stats">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-content">
                            <h4><?= count(array_filter($allUsers, fn($u) => $u['role'] === 'admin_pusat')) ?></h4>
                            <p>Admin Pusat</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-content">
                            <h4><?= count(array_filter($allUsers, fn($u) => $u['role'] === 'admin_unit')) ?></h4>
                            <p>Admin Unit</p>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="stat-content">
                            <h4><?= count(array_filter($allUsers, fn($u) => $u['role'] === 'super_admin')) ?></h4>
                            <p>Super Admin</p>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary btn-sm w-100" onclick="manageUsers()">
                        <i class="fas fa-users-cog"></i> Kelola Pengguna
                    </button>
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
                <div class="quick-actions">
                    <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="backupDatabase()">
                        <i class="fas fa-download"></i> Backup Database
                    </button>
                    <button class="btn btn-outline-info btn-sm w-100 mb-2" onclick="exportData()">
                        <i class="fas fa-file-export"></i> Export Data
                    </button>
                    <button class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="clearCache()">
                        <i class="fas fa-broom"></i> Clear Cache
                    </button>
                    <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="sendNotification()">
                        <i class="fas fa-bell"></i> Kirim Notifikasi
                    </button>
                </div>
            </div>
        </div>

        <!-- System Health -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-heartbeat"></i>
                <h3>Status Sistem</h3>
            </div>
            <div class="card-body">
                <div class="health-checks">
                    <div class="health-item">
                        <div class="health-status status-good">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Database Connection</div>
                            <div class="health-value">Normal</div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-status status-good">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">File Permissions</div>
                            <div class="health-value">OK</div>
                        </div>
                    </div>
                    <div class="health-item">
                        <div class="health-status status-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="health-info">
                            <div class="health-label">Disk Space</div>
                            <div class="health-value">75% Used</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<style>
    .status-active {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-inactive {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .type-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .type-fakultas {
        background: #e3f2fd;
        color: #1976d2;
    }

    .type-jurusan {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .type-unit_kerja {
        background: #e8f5e8;
        color: #388e3c;
    }

    .type-lembaga {
        background: #fff3e0;
        color: #f57c00;
    }

    .system-info {
        margin-top: 15px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: #666;
    }

    .info-value {
        font-weight: 600;
        color: #333;
    }

    .user-stats {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .stat-item {
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

    .quick-actions {
        margin-top: 15px;
    }

    .health-checks {
        margin-top: 15px;
    }

    .health-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .health-item:last-child {
        border-bottom: none;
    }

    .health-status {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .health-status.status-good {
        background: #d4edda;
        color: #155724;
    }

    .health-status.status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .health-status.status-error {
        background: #f8d7da;
        color: #721c24;
    }

    .health-info {
        flex: 1;
    }

    .health-label {
        font-weight: 500;
        color: #333;
        font-size: 14px;
    }

    .health-value {
        font-size: 12px;
        color: #666;
    }

    .btn-group .btn {
        margin-right: 5px;
    }
</style>

<script>
    function setActiveTahun(tahunId) {
        if (confirm('Apakah Anda yakin ingin mengaktifkan tahun ini? Tahun aktif sebelumnya akan dinonaktifkan.')) {
            fetch('<?= base_url('/admin-pusat/update-tahun-aktif') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'tahun_id=' + tahunId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tahun aktif berhasil diperbarui');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan: ' + error);
                });
        }
    }

    function editTahun(tahunId) {
        alert('Fitur edit tahun akan segera tersedia untuk ID: ' + tahunId);
    }

    function addNewTahun() {
        alert('Fitur tambah tahun baru akan segera tersedia');
    }

    function editUnit(unitId) {
        alert('Fitur edit unit akan segera tersedia untuk ID: ' + unitId);
    }

    function toggleUnitStatus(unitId, newStatus) {
        const action = newStatus === 'true' ? 'mengaktifkan' : 'menonaktifkan';
        if (confirm(`Apakah Anda yakin ingin ${action} unit ini?`)) {
            alert(`Fitur ${action} unit akan segera tersedia untuk ID: ${unitId}`);
        }
    }

    function addNewUnit() {
        alert('Fitur tambah unit baru akan segera tersedia');
    }

    function manageUsers() {
        alert('Fitur manajemen pengguna akan segera tersedia');
    }

    function backupDatabase() {
        if (confirm('Apakah Anda yakin ingin membuat backup database?')) {
            alert('Fitur backup database akan segera tersedia');
        }
    }

    function exportData() {
        alert('Fitur export data akan segera tersedia');
    }

    function clearCache() {
        if (confirm('Apakah Anda yakin ingin menghapus cache sistem?')) {
            alert('Fitur clear cache akan segera tersedia');
        }
    }

    function sendNotification() {
        alert('Fitur kirim notifikasi akan segera tersedia');
    }

    // Auto-refresh system time
    setInterval(function() {
        const now = new Date();
        const timeString = now.toLocaleString('id-ID');
        const timeElement = document.querySelector('.info-item:last-child .info-value');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }, 1000);
</script>