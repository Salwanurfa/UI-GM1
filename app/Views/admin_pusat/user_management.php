<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= $title ?? 'User Management' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-users"></i> Manajemen Akun</h1>
                <p>Kelola akun pengguna dan hak akses sistem</p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importUserModal">
                    <i class="fas fa-file-import"></i> Import Data
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['total'] ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['active'] ?></h3>
                    <p>Active Users</p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['user_role'] ?></h3>
                    <p>Unit Users</p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['tps_role'] ?? 0 ?></h3>
                    <p>Pengelola TPS</p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['admin_role'] ?></h3>
                    <p>Admin Pusat</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Filter Users</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/user-management') ?>" class="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="filter_role" class="form-label">Role</label>
                            <select name="role" id="filter_role" class="form-select">
                                <option value="">Semua Role</option>
                                <?php foreach ($allRoles as $roleKey => $roleLabel): ?>
                                <option value="<?= $roleKey ?>" <?= ($filters['role'] == $roleKey) ? 'selected' : '' ?>>
                                    <?= $roleLabel ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filter_unit" class="form-label">Unit</label>
                            <select name="unit" id="filter_unit" class="form-select">
                                <option value="">Semua Unit</option>
                                <?php foreach ($allUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= ($filters['unit'] == $unit['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unit['nama_unit']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="filter_status" class="form-label">Status</label>
                            <select name="status" id="filter_status" class="form-select">
                                <option value="">Semua Status</option>
                                <?php foreach ($allStatus as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($filters['status'] == $value) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i>
                <h3>Daftar Users (<?= count($users) ?>)</h3>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>Tidak ada user ditemukan</p>
                    <?php if (isset($error)): ?>
                    <p class="text-danger"><?= $error ?></p>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Role</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $userData): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($userData['username']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($userData['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($userData['email']) ?></td>
                                <td>
                                    <code class="password-display"><?= htmlspecialchars($userData['password']) ?></code>
                                </td>
                                <td>
                                    <span class="role-badge role-<?= $userData['role'] ?>">
                                        <?php 
                                        $roleLabels = [
                                            'user' => 'User Unit',
                                            'pengelola_tps' => 'Pengelola TPS',
                                            'admin_pusat' => 'Admin Pusat'
                                        ];
                                        echo $roleLabels[$userData['role']] ?? ucfirst(str_replace('_', ' ', $userData['role']));
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $userData['nama_unit'] ? htmlspecialchars($userData['nama_unit']) : '-' ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $userData['status_aktif'] ? 'active' : 'inactive' ?>">
                                        <?= $userData['status_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($userData['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="editUser(<?= $userData['id'] ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="toggleStatus(<?= $userData['id'] ?>, <?= $userData['status_aktif'] ?>)">
                                            <i class="fas fa-<?= $userData['status_aktif'] ? 'user-slash' : 'user-check' ?>"></i>
                                        </button>
                                        <?php 
                                        $currentUser = session()->get('user');
                                        if (!$currentUser || $userData['id'] != $currentUser['id']): 
                                        ?>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="deleteUser(<?= $userData['id'] ?>, '<?= htmlspecialchars($userData['username']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
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

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah User Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="addUserForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_username" class="form-label">Username *</label>
                                        <input type="text" class="form-control" name="username" id="add_username" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" id="add_email" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_nama_lengkap" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" name="nama_lengkap" id="add_nama_lengkap" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_password" class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" id="add_password" required>
                                <small class="form-text text-muted">Minimal 8 karakter</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_role" class="form-label">Role *</label>
                                        <select class="form-select" name="role" id="add_role" required>
                                            <option value="">Pilih Role</option>
                                            <option value="user">User Unit</option>
                                            <option value="pengelola_tps">Pengelola TPS</option>
                                            <option value="admin_pusat">Admin Pusat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="add_unit_id" class="form-label">Gedung</label>
                                        <div class="input-group">
                                            <select class="form-select" name="unit_id" id="add_unit_id">
                                                <option value="">Pilih Gedung</option>
                                                <?php foreach ($allUnits as $unit): ?>
                                                <option value="<?= $unit['id'] ?>">
                                                    <?= htmlspecialchars($unit['nama_unit']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#quickAddUnitModal" title="Tambah Gedung Baru">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="add_status_aktif" class="form-label">Status *</label>
                                <select class="form-select" name="status_aktif" id="add_status_aktif" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editUserForm">
                        <input type="hidden" name="user_id" id="edit_user_id">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_username" class="form-label">Username *</label>
                                        <input type="text" class="form-control" name="username" id="edit_username" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" id="edit_email" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_nama_lengkap" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" name="nama_lengkap" id="edit_nama_lengkap" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Password</label>
                                <input type="text" class="form-control" name="password" id="edit_password">
                                <small class="form-text text-muted">Password saat ini akan ditampilkan. Kosongkan jika tidak ingin mengubah. Minimal 8 karakter jika diubah.</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_role" class="form-label">Role *</label>
                                        <select class="form-select" name="role" id="edit_role" required>
                                            <option value="user">User Unit</option>
                                            <option value="pengelola_tps">Pengelola TPS</option>
                                            <option value="admin_pusat">Admin Pusat</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="edit_unit_id" class="form-label">Gedung</label>
                                        <div class="input-group">
                                            <select class="form-select" name="unit_id" id="edit_unit_id">
                                                <option value="">Pilih Gedung</option>
                                                <?php foreach ($allUnits as $unit): ?>
                                                <option value="<?= $unit['id'] ?>">
                                                    <?= htmlspecialchars($unit['nama_unit']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#quickAddUnitModal" title="Tambah Gedung Baru">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_status_aktif" class="form-label">Status *</label>
                                <select class="form-select" name="status_aktif" id="edit_status_aktif" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Import User Modal -->
        <div class="modal fade" id="importUserModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data User dari Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="importUserForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Panduan Import User dari Excel:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>Format File:</strong> Upload file Excel (.xlsx). Maks file 5MB. Baris max 2000.</li>
                                    <li><strong>Template:</strong> Gunakan template Excel resmi. Download: 
                                        <a href="<?= base_url('/admin-pusat/user-management/download-template') ?>" class="alert-link">
                                            <i class="fas fa-download"></i> Template Excel (.xlsx)
                                        </a>
                                    </li>
                                    <li><strong>Kolom WAJIB:</strong> <code>username</code>, <code>email</code>, <code>nama_lengkap</code>, <code>role</code></li>
                                    <li><strong>Kolom OPSIONAL:</strong> <code>nama_gedung</code>, <code>is_active</code>, <code>password</code></li>
                                    <li><strong>Username:</strong> 3-100 karakter, boleh menggunakan spasi, titik, underscore. Harus unik.</li>
                                    <li><strong>Email:</strong> Format email valid. Harus unik.</li>
                                    <li><strong>Nama Lengkap:</strong> Maksimal 150 karakter.</li>
                                    <li><strong>Role:</strong> Pilih salah satu: <code>user</code>, <code>pengelola_tps</code>, <code>admin_pusat</code>, <code>super_admin</code></li>
                                    <li><strong>Nama Gedung:</strong> Opsional. Boleh pakai spasi. Jika gedung tidak ditemukan, sistem akan membuat gedung baru secara otomatis.</li>
                                    <li><strong>is_active:</strong> Opsional. <code>1</code> (aktif) atau <code>0</code> (nonaktif). Default: 1 jika kosong.</li>
                                    <li><strong>Password:</strong> Opsional. Minimal 6 karakter. Kosongkan untuk auto-generate password acak yang aman.</li>
                                    <li><strong>Catatan:</strong> Baris kosong akan diabaikan. Username dan email harus unik (tidak boleh duplikat). <strong>Usahakan username jangan terlalu panjang agar memudahkan pengguna untuk login. Saran: gunakan nama panggilan sebagai username (contoh: ahmad = mad, budi = bud).</strong></li>
                                </ul>
                            </div>
                            
                            <div class="mb-3">
                                <label for="excel_file" class="form-label">File Excel *</label>
                                <input type="file" class="form-control" name="excel_file" id="excel_file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <small class="form-text text-muted">Format: .xlsx (Max 5MB, Max 2000 baris)</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="skip_duplicates" name="skip_duplicates" checked>
                                    <label class="form-check-label" for="skip_duplicates">
                                        Skip duplicate username/email (lewati jika sudah ada)
                                    </label>
                                </div>
                            </div>
                            
                            <div id="import_preview" class="d-none">
                                <h6>Preview Data:</h6>
                                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Nama Lengkap</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="preview_tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-import"></i> Upload Excel (.xlsx)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Add Unit Modal -->
        <div class="modal fade" id="quickAddUnitModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Gedung Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="quickAddUnitForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="quick_nama_unit" class="form-label">Nama Gedung *</label>
                                <input type="text" class="form-control" name="nama_unit" id="quick_nama_unit" required>
                                <small class="form-text text-muted">Contoh: Gedung A – Gedung Kuliah</small>
                            </div>
                            
                            <!-- Hidden field for auto-generated kode_unit -->
                            <input type="hidden" name="kode_unit" id="quick_kode_unit_hidden" value="">
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan & Gunakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Initialize Select2 for unit dropdowns
        $(document).ready(function() {
            // Initialize Select2 on page load
            initializeSelect2();
            
            // Re-initialize when modals are shown
            $('#addUserModal').on('shown.bs.modal', function() {
                initializeSelect2();
            });
            
            $('#editUserModal').on('shown.bs.modal', function() {
                initializeSelect2();
            });
        });

        function initializeSelect2() {
            // Destroy existing Select2 instances first (if any)
            if ($('#add_unit_id').hasClass('select2-hidden-accessible')) {
                $('#add_unit_id').select2('destroy');
            }
            if ($('#edit_unit_id').hasClass('select2-hidden-accessible')) {
                $('#edit_unit_id').select2('destroy');
            }
            
            // Initialize Select2 with search
            $('#add_unit_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Gedung',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addUserModal')
            });
            
            $('#edit_unit_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Gedung',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#editUserModal')
            });
        }

        // Add User
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            fetch('<?= base_url('/admin-pusat/user-management/create') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        });

        // Edit User
        function editUser(id) {
            fetch(`<?= base_url('/admin-pusat/user-management/get/') ?>${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.data;
                    document.getElementById('edit_user_id').value = user.id;
                    document.getElementById('edit_username').value = user.username;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_nama_lengkap').value = user.nama_lengkap;
                    document.getElementById('edit_password').value = user.password || ''; // Show current password
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_unit_id').value = user.unit_id || '';
                    document.getElementById('edit_status_aktif').value = user.status_aktif;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }

        // Update User
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userId = document.getElementById('edit_user_id').value;
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            fetch(`<?= base_url('/admin-pusat/user-management/update/') ?>${userId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        });

        // Toggle Status
        function toggleStatus(id, currentStatus) {
            const action = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
            if (confirm(`Apakah Anda yakin ingin ${action} user ini?`)) {
                fetch(`<?= base_url('/admin-pusat/user-management/toggle-status/') ?>${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem');
                });
            }
        }

        // Delete User
        function deleteUser(id, username) {
            if (confirm(`Apakah Anda yakin ingin menghapus user "${username}"?\n\nData yang sudah dihapus tidak dapat dikembalikan.`)) {
                fetch(`<?= base_url('/admin-pusat/user-management/delete/') ?>${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan sistem');
                });
            }
        }

        // Role change handler - Hide unit for admin_pusat
        document.getElementById('add_role').addEventListener('change', function() {
            const unitField = document.getElementById('add_unit_id');
            const unitContainer = unitField.parentElement.parentElement; // Get col-md-6 parent
            
            if (this.value === 'admin_pusat' || this.value === 'super_admin') {
                // Hide unit field for admin
                unitContainer.style.display = 'none';
                unitField.required = false;
                unitField.value = ''; // Clear selection
            } else if (this.value === 'user' || this.value === 'pengelola_tps') {
                // Show and require unit for user/tps
                unitContainer.style.display = 'block';
                unitField.required = true;
                unitField.parentElement.querySelector('label').innerHTML = 'Gedung *';
            } else {
                // Show but not required for other roles
                unitContainer.style.display = 'block';
                unitField.required = false;
                unitField.parentElement.querySelector('label').innerHTML = 'Gedung';
            }
        });

        document.getElementById('edit_role').addEventListener('change', function() {
            const unitField = document.getElementById('edit_unit_id');
            const unitContainer = unitField.parentElement.parentElement; // Get col-md-6 parent
            
            if (this.value === 'admin_pusat' || this.value === 'super_admin') {
                // Hide unit field for admin
                unitContainer.style.display = 'none';
                unitField.required = false;
                unitField.value = ''; // Clear selection
            } else if (this.value === 'user' || this.value === 'pengelola_tps') {
                // Show and require unit for user/tps
                unitContainer.style.display = 'block';
                unitField.required = true;
                unitField.parentElement.querySelector('label').innerHTML = 'Gedung *';
            } else {
                // Show but not required for other roles
                unitContainer.style.display = 'block';
                unitField.required = false;
                unitField.parentElement.querySelector('label').innerHTML = 'Gedung';
            }
        });

        // Import User - Validasi client-side untuk Excel (VERSION 2 - NO EXTENSION CHECK)
        console.log('Import validation script loaded - VERSION 2');
        
        document.getElementById('excel_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran (max 5MB)
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('Ukuran file maksimal 5MB (file Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB)');
                    this.value = '';
                    return;
                }
                
                // Log untuk debugging
                console.log('File selected:', file.name, 'Type:', file.type, 'Size:', file.size);
            }
        });

        // Import User
        document.getElementById('importUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submitted - VERSION 2');
            
            // Validasi file sebelum submit
            const fileInput = document.getElementById('excel_file');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Pilih file Excel terlebih dahulu');
                return;
            }
            
            // Validasi ukuran saja (hapus validasi ekstensi yang strict)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Ukuran file maksimal 5MB');
                return;
            }
            
            console.log('Validation passed, sending to server...');
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
            
            fetch('<?= base_url('/admin-pusat/user-management/import') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    alert(`Import berhasil!\n\nTotal: ${data.total}\nBerhasil: ${data.inserted}\nGagal: ${data.failed}\n\n${data.message}`);
                    bootstrap.Modal.getInstance(document.getElementById('importUserModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Terjadi kesalahan sistem: ' + error.message);
            });
        });

        // Quick Add Unit
        let previousModal = null; // Track which modal opened the Quick Add Unit modal
        
        // Track which modal button was clicked
        document.querySelectorAll('[data-bs-target="#quickAddUnitModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                // Determine which modal this button belongs to
                const parentModal = this.closest('.modal');
                if (parentModal) {
                    previousModal = parentModal.id;
                }
            });
        });
        
        // Auto-generate kode_unit from nama_unit
        document.getElementById('quick_nama_unit').addEventListener('input', function() {
            const namaUnit = this.value;
            // Generate kode from nama_unit: take first letters, remove special chars, uppercase
            let kode = namaUnit
                .replace(/[^\w\s]/gi, '') // Remove special characters
                .split(' ')
                .map(word => word.charAt(0))
                .join('')
                .toUpperCase()
                .substring(0, 10); // Max 10 characters
            
            // Add timestamp to ensure uniqueness
            const timestamp = Date.now().toString().slice(-4);
            kode = kode + timestamp;
            
            document.getElementById('quick_kode_unit_hidden').value = kode;
        });
        
        document.getElementById('quickAddUnitForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Ensure kode_unit is generated before submit
            const namaUnit = document.getElementById('quick_nama_unit').value;
            if (!document.getElementById('quick_kode_unit_hidden').value) {
                let kode = namaUnit
                    .replace(/[^\w\s]/gi, '')
                    .split(' ')
                    .map(word => word.charAt(0))
                    .join('')
                    .toUpperCase()
                    .substring(0, 10);
                const timestamp = Date.now().toString().slice(-4);
                kode = kode + timestamp;
                document.getElementById('quick_kode_unit_hidden').value = kode;
            }
            
            const formData = new FormData(this);
            
            // Add CSRF token
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            
            fetch('<?= base_url('/admin-pusat/unit-management/create') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    // Close Quick Add Unit modal
                    const quickAddModal = bootstrap.Modal.getInstance(document.getElementById('quickAddUnitModal'));
                    quickAddModal.hide();
                    
                    // Reset form
                    document.getElementById('quickAddUnitForm').reset();
                    
                    // Refresh unit list and auto-select the new unit
                    refreshUnitDropdowns().then(() => {
                        // Show success message
                        alert('Gedung berhasil ditambahkan dan sudah dipilih!');
                        
                        // Re-open the previous modal (Add or Edit User)
                        if (previousModal) {
                            setTimeout(() => {
                                const prevModal = new bootstrap.Modal(document.getElementById(previousModal));
                                prevModal.show();
                            }, 300);
                        }
                    });
                } else {
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        });

        // Function to refresh unit dropdowns after adding new unit
        function refreshUnitDropdowns() {
            return new Promise((resolve, reject) => {
                // Fetch updated unit list
                fetch('<?= base_url('/admin-pusat/user-management') ?>', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse the HTML to extract unit options
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Get the unit options from the parsed HTML
                    const addUnitSelect = doc.querySelector('#add_unit_id');
                    const editUnitSelect = doc.querySelector('#edit_unit_id');
                    
                    if (addUnitSelect && editUnitSelect) {
                        // Destroy Select2 before updating
                        $('#add_unit_id').select2('destroy');
                        $('#edit_unit_id').select2('destroy');
                        
                        // Update both dropdowns
                        document.getElementById('add_unit_id').innerHTML = addUnitSelect.innerHTML;
                        document.getElementById('edit_unit_id').innerHTML = editUnitSelect.innerHTML;
                        
                        // Re-initialize Select2
                        initializeSelect2();
                        
                        // Auto-select the last option (newly added unit)
                        const addOptions = document.getElementById('add_unit_id').options;
                        const editOptions = document.getElementById('edit_unit_id').options;
                        
                        if (addOptions.length > 1) {
                            const lastValue = addOptions[addOptions.length - 1].value;
                            $('#add_unit_id').val(lastValue).trigger('change');
                        }
                        
                        if (editOptions.length > 1) {
                            const lastValue = editOptions[editOptions.length - 1].value;
                            $('#edit_unit_id').val(lastValue).trigger('change');
                        }
                        
                        resolve();
                    } else {
                        reject('Could not find unit selects');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing unit list:', error);
                    reject(error);
                });
            });
        }
    </script>
    
    <style>
/* Select2 Custom Styles */
.select2-container--bootstrap-5 .select2-selection {
    min-height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--bootstrap-5 .select2-selection--single {
    padding: 0.375rem 0.75rem;
}

.select2-container--bootstrap-5 .select2-dropdown {
    border-color: #ced4da;
}

.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    padding: 0.375rem 0.75rem;
}

/* Fix Select2 in input-group */
.input-group .select2-container {
    flex: 1 1 auto;
    width: 1% !important;
}

.input-group .select2-container .select2-selection {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

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

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.header-content h1 {
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 28px;
    font-weight: 700;
}

.header-content p {
    color: #6c757d;
    margin: 0;
    font-size: 16px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    transition: transform 0.3s ease;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
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
}

.stat-card.primary .stat-icon { background: #007bff; }
.stat-card.success .stat-icon { background: #28a745; }
.stat-card.warning .stat-icon { background: #ffc107; }
.stat-card.info .stat-icon { background: #17a2b8; }

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

.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
    max-height: none;
}

.table-responsive {
    max-height: 600px;
    overflow-y: auto;
}

.filter-form .row {
    align-items: end;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.table {
    margin-bottom: 0;
    width: 100%;
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
    white-space: nowrap;
    padding: 12px 8px;
}

.table td {
    vertical-align: middle;
    font-size: 14px;
    padding: 12px 8px;
}

.role-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.role-user {
    background: #e3f2fd;
    color: #1976d2;
}

.role-pengelola_tps {
    background: #e8f5e8;
    color: #2e7d32;
}

.role-admin_pusat {
    background: #fff3e0;
    color: #f57c00;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.password-display {
    background: #f8f9fa;
    color: #495057;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    border: 1px solid #dee2e6;
    display: inline-block;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.action-buttons {
    display: flex;
    gap: 5px;
    align-items: center;
    flex-wrap: nowrap;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    min-width: auto;
    height: auto;
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>