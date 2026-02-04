<?php
/**
 * Admin Pusat - Waste Management
 * Halaman untuk review dan approve data sampah dari User dan TPS
 */

// Helper functions
if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format($number, 0, ',', '.');
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}

// Safety checks
$waste_list = $waste_list ?? [];
$summary = $summary ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Manajemen Sampah Admin' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Mobile Responsive CSS -->
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-clipboard-check"></i> Manajemen Data Sampah</h1>
            <p>Review dan kelola data sampah yang dikirim dari semua unit</p>
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

        <!-- Waste Data Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="mb-0"><i class="fas fa-list"></i> Data Sampah</h3>
                    
                    <!-- Search Bar -->
                    <div class="search-box" style="width: 300px;">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   class="form-control border-start-0 ps-0" 
                                   id="searchInput" 
                                   placeholder="Cari unit, jenis, atau nama sampah..."
                                   style="box-shadow: none;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($waste_list)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Unit</th>
                                    <th>Jenis Sampah</th>
                                    <th>Nama Sampah</th>
                                    <th>Berat (kg)</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($waste_list as $index => $waste): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= date('d/m/Y', strtotime($waste['tanggal'])) ?></td>
                                    <td><?= $waste['nama_unit'] ?? 'N/A' ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?= $waste['jenis_sampah'] ?></span>
                                    </td>
                                    <td><?= $waste['nama_jenis'] ?? '-' ?></td>
                                    <td><?= number_format($waste['berat_kg'], 2) ?></td>
                                    <td><?= formatCurrency($waste['nilai_rupiah'] ?? 0) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = match($waste['status']) {
                                            'disetujui' => 'success',
                                            'dikirim' => 'warning',
                                            'review' => 'info',
                                            'perlu_revisi' => 'danger',
                                            default => 'secondary'
                                        };
                                        $statusLabel = match($waste['status']) {
                                            'disetujui' => 'Disetujui',
                                            'dikirim' => 'Dikirim',
                                            'review' => 'Review',
                                            'perlu_revisi' => 'Perlu Revisi',
                                            default => 'Draft'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info" onclick="showDetail(<?= htmlspecialchars(json_encode($waste), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            <?php if (in_array($waste['status'], ['dikirim', 'review'])): ?>
                                            <button type="button" class="btn btn-success" onclick="approveWaste(<?= $waste['id'] ?>)">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="rejectWaste(<?= $waste['id'] ?>)">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-danger" onclick="deleteWaste(<?= $waste['id'] ?>, '<?= esc($waste['jenis_sampah']) ?>')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada data sampah yang perlu direview.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Data Sampah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm">
                    <?= csrf_field() ?>
                    <input type="hidden" id="reject_waste_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reject_catatan" class="form-label">Catatan Penolakan *</label>
                            <textarea class="form-control" id="reject_catatan" name="catatan" rows="4" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailModalLabel">
                        <i class="fas fa-info-circle"></i> Detail Data Sampah
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Informasi Sampah</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>ID:</strong></td>
                                    <td id="detail-id">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td id="detail-tanggal">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Unit:</strong></td>
                                    <td id="detail-unit">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Sampah:</strong></td>
                                    <td id="detail-jenis">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Sampah:</strong></td>
                                    <td id="detail-nama">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Berat:</strong></td>
                                    <td id="detail-berat">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Satuan:</strong></td>
                                    <td id="detail-satuan">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Nilai (Rp):</strong></td>
                                    <td id="detail-nilai">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td id="detail-status">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3"><i class="fas fa-user"></i> Informasi Pelapor</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="40%"><strong>Nama Pelapor:</strong></td>
                                    <td id="detail-created-by">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat:</strong></td>
                                    <td id="detail-created-at">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate:</strong></td>
                                    <td id="detail-updated-at">-</td>
                                </tr>
                            </table>
                            
                            <div id="detail-catatan-section" style="display: none;">
                                <h6 class="text-warning mb-3"><i class="fas fa-comment"></i> Catatan</h6>
                                <div class="alert alert-info" id="detail-catatan">-</div>
                            </div>
                            
                            <div id="detail-foto-section" style="display: none;">
                                <h6 class="text-primary mb-3"><i class="fas fa-image"></i> Foto Bukti</h6>
                                <img id="detail-foto" src="" alt="Foto Bukti" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetail(data) {
            // Populate modal with data
            document.getElementById('detail-id').textContent = data.id || '-';
            document.getElementById('detail-tanggal').textContent = formatDate(data.tanggal || data.created_at);
            document.getElementById('detail-unit').textContent = data.nama_unit || '-';
            document.getElementById('detail-jenis').innerHTML = '<span class="badge bg-primary">' + (data.jenis_sampah || '-') + '</span>';
            document.getElementById('detail-nama').textContent = data.nama_jenis || '-';
            document.getElementById('detail-berat').textContent = formatNumber(data.berat_kg) + ' kg';
            document.getElementById('detail-satuan').textContent = data.satuan || 'kg';
            document.getElementById('detail-nilai').textContent = formatCurrency(data.nilai_rupiah);
            
            // Status badge
            let statusBadge = '';
            let statusClass = '';
            switch(data.status) {
                case 'disetujui':
                    statusClass = 'success';
                    statusBadge = 'Disetujui';
                    break;
                case 'dikirim':
                    statusClass = 'warning';
                    statusBadge = 'Dikirim';
                    break;
                case 'review':
                    statusClass = 'info';
                    statusBadge = 'Review';
                    break;
                case 'perlu_revisi':
                    statusClass = 'danger';
                    statusBadge = 'Perlu Revisi';
                    break;
                default:
                    statusClass = 'secondary';
                    statusBadge = 'Draft';
            }
            document.getElementById('detail-status').innerHTML = '<span class="badge bg-' + statusClass + '">' + statusBadge + '</span>';
            
            // Creator info
            document.getElementById('detail-created-by').textContent = data.created_by_name || data.user_name || '-';
            document.getElementById('detail-created-at').textContent = formatDateTime(data.created_at);
            document.getElementById('detail-updated-at').textContent = data.updated_at ? formatDateTime(data.updated_at) : '-';
            
            // Catatan (if exists)
            const catatanSection = document.getElementById('detail-catatan-section');
            if (data.catatan) {
                document.getElementById('detail-catatan').textContent = data.catatan;
                catatanSection.style.display = 'block';
            } else {
                catatanSection.style.display = 'none';
            }
            
            // Photo (if exists)
            const fotoSection = document.getElementById('detail-foto-section');
            if (data.foto_bukti) {
                document.getElementById('detail-foto').src = '<?= base_url('/uploads/') ?>' + data.foto_bukti;
                fotoSection.style.display = 'block';
            } else {
                fotoSection.style.display = 'none';
            }
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        }
        
        function formatNumber(num) {
            if (!num) return '0,00';
            return parseFloat(num).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
        
        function formatCurrency(amount) {
            if (!amount) return 'Rp 0';
            return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        }
        
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
        
        function formatDateTime(dateStr) {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    
        function approveWaste(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui data sampah ini?')) {
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                fetch(`<?= base_url('/admin-pusat/waste/approve/') ?>${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyetujui data');
                });
            }
        }

        function rejectWaste(id) {
            document.getElementById('reject_waste_id').value = id;
            const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
            rejectModal.show();
        }

        function deleteWaste(id, jenisSampah) {
            if (confirm(`Apakah Anda yakin ingin menghapus data sampah "${jenisSampah}"?\n\nData yang dihapus tidak dapat dikembalikan.`)) {
                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                
                fetch(`<?= base_url('/admin-pusat/waste/delete/') ?>${id}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghapus data');
                });
            }
        }

        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const wasteId = document.getElementById('reject_waste_id').value;
            const formData = new FormData(this);
            
            fetch(`<?= base_url('/admin-pusat/waste/reject/') ?>${wasteId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menolak data');
            });
        });

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('.table tbody tr');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    
                    tableRows.forEach(row => {
                        // Get text from relevant columns: Unit, Jenis Sampah, Nama Sampah
                        const unit = row.cells[2]?.textContent.toLowerCase() || '';
                        const jenisSampah = row.cells[3]?.textContent.toLowerCase() || '';
                        const namaSampah = row.cells[4]?.textContent.toLowerCase() || '';
                        
                        // Check if any column contains the search term
                        const matches = unit.includes(searchTerm) || 
                                      jenisSampah.includes(searchTerm) || 
                                      namaSampah.includes(searchTerm);
                        
                        // Show/hide row based on match
                        row.style.display = matches ? '' : 'none';
                    });
                });
            }
        });
    </script>
    <!-- Mobile Menu JS -->
    <script src="<?= base_url('/js/mobile-menu.js') ?>"></script>
</body>
</html>

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

/* ===== PAGE HEADER ===== */
.page-header {
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.page-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 28px;
    font-weight: 700;
}

.page-header p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
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

.search-box .input-group {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.search-box .input-group-text {
    border: none;
    padding: 8px 12px;
}

.search-box .form-control {
    border: none;
    padding: 8px 12px;
    color: #2c3e50;
}

.search-box .form-control:focus {
    outline: none;
    box-shadow: none;
}

.search-box .form-control::placeholder {
    color: #adb5bd;
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
    font-size: 14px;
}

.table td {
    border: none;
    padding: 15px;
    vertical-align: middle;
    font-size: 14px;
}

.table tbody tr {
    border-bottom: 1px solid #e9ecef;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

/* ===== EMPTY STATE ===== */
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

.empty-state p {
    margin: 0 0 25px 0;
    font-size: 18px;
}

/* ===== ALERTS ===== */
.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
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

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* ===== MODALS ===== */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.modal-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border-radius: 15px 15px 0 0;
    border: none;
}

.modal-title {
    font-weight: 600;
}

.btn-close {
    filter: invert(1);
}

/* ===== FORM ELEMENTS ===== */
.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .card-header {
        padding: 15px 20px;
    }

    .card-header .d-flex {
        flex-direction: column !important;
        gap: 15px;
        align-items: flex-start !important;
    }

    .search-box {
        width: 100% !important;
        max-width: 100% !important;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .table-responsive {
        font-size: 12px;
        max-width: 100%;
        overflow-x: auto;
    }
    
    .btn-group {
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 3px;
    }

    .btn-group .btn {
        padding: 4px 8px;
        font-size: 11px;
    }

    /* Modal on mobile */
    .modal-dialog {
        margin: 10px;
        max-width: calc(100% - 20px);
    }

    .modal-body {
        padding: 15px;
    }

    .modal-body h6 {
        font-size: 13px;
        margin-bottom: 10px;
    }

    .modal-body table {
        font-size: 11px;
    }

    .modal-body table td {
        padding: 5px 0;
        word-break: break-word;
    }

    .modal-body .row > div {
        margin-bottom: 15px;
    }

    .modal-footer {
        padding: 10px;
    }

    .modal-footer .btn {
        font-size: 12px;
        padding: 8px 15px;
    }

    .modal-body img {
        max-width: 100%;
        height: auto;
    }
}
</style>
