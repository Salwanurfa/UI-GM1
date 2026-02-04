<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Antrian Review Data' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-clipboard-list"></i> Antrian Review Data</h1>
                <p>Data UIGM yang menunggu review dari unit</p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-success" onclick="bulkApprove()">
                    <i class="fas fa-check"></i> Bulk Approve
                </button>
                <button type="button" class="btn btn-warning" onclick="bulkReject()">
                    <i class="fas fa-times"></i> Bulk Reject
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Filter Data</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/review') ?>" class="filter-form">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="type" class="form-label">Tipe Data</label>
                            <select name="type" id="type" class="form-select">
                                <option value="all" <?= ($filters['type'] == 'all') ? 'selected' : '' ?>>Semua Data</option>
                                <option value="uigm" <?= ($filters['type'] == 'uigm') ? 'selected' : '' ?>>Data UIGM</option>
                                <option value="waste" <?= ($filters['type'] == 'waste') ? 'selected' : '' ?>>Data Waste</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="unit" class="form-label">Unit</label>
                            <select name="unit" id="unit" class="form-select">
                                <option value="">Semua Unit</option>
                                <?php foreach ($allUnits as $unit): ?>
                                <option value="<?= $unit['id'] ?>" <?= ($filters['unit'] == $unit['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($unit['nama_unit']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="kategori" class="form-label">Kategori UIGM</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($allKategori as $kategori): ?>
                                <option value="<?= $kategori ?>" <?= ($filters['kategori'] == $kategori) ? 'selected' : '' ?>>
                                    <?= $kategori ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select name="tahun" id="tahun" class="form-select">
                                <option value="">Semua Tahun</option>
                                <?php foreach ($allTahun as $tahun): ?>
                                <option value="<?= $tahun['tahun'] ?>" <?= ($filters['tahun'] == $tahun['tahun']) ? 'selected' : '' ?>>
                                    <?= $tahun['tahun'] ?>
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

        <!-- Review Queue Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i>
                <h3>Data Menunggu Review (<?= count($reviewQueue) ?>)</h3>
                <div class="ms-auto">
                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                    <label for="selectAll" class="form-check-label">Pilih Semua</label>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($reviewQueue)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Tidak ada data yang menunggu review</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllTable" class="form-check-input">
                                </th>
                                <th>Unit</th>
                                <th>PIC</th>
                                <th>Kategori</th>
                                <th>Indikator</th>
                                <th>Nilai Input</th>
                                <th>Tanggal Kirim</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewQueue as $item): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input item-checkbox" 
                                           value="<?= $item['id'] ?>">
                                </td>
                                <td>
                                    <div class="unit-info">
                                        <strong><?= htmlspecialchars($item['nama_unit']) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= $item['kategori_uigm'] ?></span>
                                </td>
                                <td>
                                    <div class="indikator-info">
                                        <?= htmlspecialchars($item['indikator']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($item['nilai_input']): ?>
                                        <strong><?= number_format($item['nilai_input'], 2) ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">Belum diisi</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($item['updated_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= base_url('/admin-pusat/review/detail/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="quickApprove(<?= $item['id'] ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="quickReject(<?= $item['id'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
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

        <!-- Bulk Reject Modal -->
        <div class="modal fade" id="bulkRejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Reject Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bulkRejectNote" class="form-label">Catatan Admin (Wajib)</label>
                            <textarea class="form-control" id="bulkRejectNote" rows="4" 
                                      placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Data yang ditolak akan dikembalikan ke unit untuk diperbaiki.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="confirmBulkReject()">
                            <i class="fas fa-times"></i> Tolak Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Reject Modal -->
        <div class="modal fade" id="quickRejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quickRejectNote" class="form-label">Catatan Admin (Wajib)</label>
                            <textarea class="form-control" id="quickRejectNote" rows="4" 
                                      placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="confirmQuickReject()">
                            <i class="fas fa-times"></i> Tolak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedIds = [];
        let currentRejectId = null;

        // Select all functionality
        document.getElementById('selectAllTable').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedIds();
        });

        // Update selected IDs
        function updateSelectedIds() {
            selectedIds = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                selectedIds.push(parseInt(checkbox.value));
            });
        }

        // Listen to individual checkbox changes
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedIds);
        });

        // Bulk approve
        function bulkApprove() {
            updateSelectedIds();
            if (selectedIds.length === 0) {
                alert('Pilih data yang akan disetujui');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menyetujui ${selectedIds.length} data?`)) {
                bulkUpdateStatus('disetujui', 'Data disetujui oleh Admin Pusat');
            }
        }

        // Bulk reject
        function bulkReject() {
            updateSelectedIds();
            if (selectedIds.length === 0) {
                alert('Pilih data yang akan ditolak');
                return;
            }

            const modal = new bootstrap.Modal(document.getElementById('bulkRejectModal'));
            modal.show();
        }

        // Confirm bulk reject
        function confirmBulkReject() {
            const note = document.getElementById('bulkRejectNote').value.trim();
            if (!note) {
                alert('Catatan admin wajib diisi');
                return;
            }

            bulkUpdateStatus('perlu_revisi', note);
            bootstrap.Modal.getInstance(document.getElementById('bulkRejectModal')).hide();
        }

        // Quick approve
        function quickApprove(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui data ini?')) {
                updateSingleStatus(id, 'disetujui', 'Data disetujui oleh Admin Pusat');
            }
        }

        // Quick reject
        function quickReject(id) {
            currentRejectId = id;
            const modal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
            modal.show();
        }

        // Confirm quick reject
        function confirmQuickReject() {
            const note = document.getElementById('quickRejectNote').value.trim();
            if (!note) {
                alert('Catatan admin wajib diisi');
                return;
            }

            updateSingleStatus(currentRejectId, 'perlu_revisi', note);
            bootstrap.Modal.getInstance(document.getElementById('quickRejectModal')).hide();
        }

        // Bulk update status
        function bulkUpdateStatus(status, note) {
            const formData = new FormData();
            formData.append('ids', JSON.stringify(selectedIds));
            formData.append('status', status);
            formData.append('catatan_admin', note);

            fetch('<?= base_url('/admin-pusat/review/bulk-update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }

        // Update single status
        function updateSingleStatus(id, status, note) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', status);
            formData.append('catatan_admin', note);

            fetch('<?= base_url('/admin-pusat/review/update-status') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }
    </script>
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

.header-actions {
    display: flex;
    gap: 10px;
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
    flex: 1;
}

.card-body {
    padding: 25px;
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
}

.table th {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
    font-size: 14px;
}

.unit-info strong {
    color: #2c3e50;
}

.indikator-info {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
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
    
    .header-actions {
        width: 100%;
        justify-content: stretch;
    }
    
    .header-actions .btn {
        flex: 1;
    }
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>