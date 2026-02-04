<?php
// Helper function untuk icon
function getWasteIcon($jenis) {
    $icons = [
        'Kertas' => 'file-alt',
        'Plastik' => 'wine-bottle',
        'Organik' => 'seedling',
        'Anorganik' => 'cube',
        'Limbah Cair' => 'flask',
        'B3' => 'exclamation-triangle'
    ];
    return $icons[$jenis] ?? 'trash';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= $title ?? 'Waste Management Admin' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-trash-alt"></i> Waste Management</h1>
                <p>Kelola dan monitor data sampah dari semua unit</p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/waste/export-csv?' . http_build_query($filters)) ?>" 
                   class="btn btn-success">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i>
                <h3>Filter Data</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= base_url('/admin-pusat/waste') ?>" class="filter-form">
                    <div class="row">
                        <div class="col-md-2">
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
                        
                        <div class="col-md-2">
                            <label for="jenis" class="form-label">Jenis Sampah</label>
                            <select name="jenis" id="jenis" class="form-select">
                                <option value="">Semua Jenis</option>
                                <?php foreach ($allJenis as $jenis): ?>
                                <option value="<?= $jenis ?>" <?= ($filters['jenis'] == $jenis) ? 'selected' : '' ?>>
                                    <?= $jenis ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Semua Status</option>
                                <?php foreach ($allStatus as $status): ?>
                                <option value="<?= $status ?>" <?= ($filters['status'] == $status) ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $status)) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" 
                                   value="<?= $filters['tanggal_mulai'] ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                                   value="<?= $filters['tanggal_selesai'] ?>">
                        </div>
                        
                        <div class="col-md-2">
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

        <!-- Waste Data Table -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-table"></i>
                <h3>Data Waste Management (<?= count($wasteData) ?>)</h3>
                <div class="ms-auto">
                    <button type="button" class="btn btn-success btn-sm" onclick="bulkApprove()">
                        <i class="fas fa-check"></i> Bulk Approve
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="bulkReject()">
                        <i class="fas fa-times"></i> Bulk Reject
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($wasteData)): ?>
                <div class="empty-state">
                    <i class="fas fa-trash"></i>
                    <p>Tidak ada data waste management</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllWaste" class="form-check-input">
                                </th>
                                <th>Unit</th>
                                <th>PIC</th>
                                <th>Tanggal</th>
                                <th>Jenis Sampah</th>
                                <th>Jumlah</th>
                                <th>Gedung</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wasteData as $item): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input waste-checkbox" 
                                           value="<?= $item['id'] ?>">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['nama_unit']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($item['nama_lengkap']) ?></td>
                                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $item['jenis_sampah'] ?></span>
                                </td>
                                <td>
                                    <strong><?= number_format($item['jumlah'], 2) ?></strong>
                                    <small class="text-muted"><?= $item['satuan'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($item['gedung']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $item['status'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($item['catatan_admin'])): ?>
                                        <div class="catatan-admin">
                                            <i class="fas fa-comment-alt"></i>
                                            <?= htmlspecialchars($item['catatan_admin']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($item['status'] === 'dikirim'): ?>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="approveWaste(<?= $item['id'] ?>)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="rejectWaste(<?= $item['id'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
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

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Data Waste</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectNote" class="form-label">Catatan Admin (Wajib)</label>
                            <textarea class="form-control" id="rejectNote" rows="4" 
                                      placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="confirmReject()">
                            <i class="fas fa-times"></i> Tolak Data
                        </button>
                    </div>
                </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedWasteIds = [];
        let currentRejectId = null;

        // Select all functionality
        document.getElementById('selectAllWaste').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.waste-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedWasteIds();
        });

        // Update selected IDs
        function updateSelectedWasteIds() {
            selectedWasteIds = [];
            document.querySelectorAll('.waste-checkbox:checked').forEach(checkbox => {
                selectedWasteIds.push(parseInt(checkbox.value));
            });
        }

        // Listen to individual checkbox changes
        document.querySelectorAll('.waste-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedWasteIds);
        });

        // Approve waste
        function approveWaste(id) {
            if (confirm('Apakah Anda yakin ingin menyetujui data waste ini?')) {
                window.location.href = '<?= base_url('/admin-pusat/waste/approve/') ?>' + id;
            }
        }

        // Reject waste
        function rejectWaste(id) {
            currentRejectId = id;
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        // Confirm reject
        function confirmReject() {
            const note = document.getElementById('rejectNote').value.trim();
            if (!note) {
                alert('Catatan admin wajib diisi');
                return;
            }

            // Disable button to prevent double click
            const confirmBtn = document.querySelector('#rejectModal .btn-danger');
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            const formData = new FormData();
            formData.append('id', currentRejectId);
            formData.append('catatan_admin', note);
            
            // Add CSRF token from meta tag
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfHash = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfHash);

            fetch('<?= base_url('/admin-pusat/waste/reject') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Success - close modal and reload
                    bootstrap.Modal.getInstance(document.getElementById('rejectModal')).hide();
                    location.reload();
                } else {
                    // Error - show message
                    alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                    
                    // Re-enable button
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i class="fas fa-times"></i> Tolak Data';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Terjadi kesalahan sistem: ' + error.message);
                
                // Re-enable button
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-times"></i> Tolak Data';
            });
        }

        // Bulk approve
        function bulkApprove() {
            updateSelectedWasteIds();
            if (selectedWasteIds.length === 0) {
                alert('Pilih data yang akan disetujui');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin menyetujui ${selectedWasteIds.length} data?`)) {
                bulkAction('approve', 'Data disetujui oleh Admin Pusat');
            }
        }

        // Bulk reject
        function bulkReject() {
            updateSelectedWasteIds();
            if (selectedWasteIds.length === 0) {
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

            bulkAction('reject', note);
            bootstrap.Modal.getInstance(document.getElementById('bulkRejectModal')).hide();
        }

        // Bulk action
        function bulkAction(action, note) {
            const formData = new FormData();
            formData.append('ids', JSON.stringify(selectedWasteIds));
            formData.append('action', action);
            formData.append('catatan_admin', note);
            
            // Add CSRF token
            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';
            formData.append(csrfName, csrfHash);

            fetch('<?= base_url('/admin-pusat/waste/bulk-action') ?>', {
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

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-draft {
    background: #e9ecef;
    color: #6c757d;
}

.status-dikirim {
    background: #cce5ff;
    color: #0066cc;
}

.status-review {
    background: #fff3cd;
    color: #856404;
}

.status-disetujui {
    background: #d4edda;
    color: #155724;
}

.status-perlu_revisi {
    background: #f8d7da;
    color: #721c24;
}

.catatan-admin {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 12px;
    color: #856404;
    max-width: 200px;
}

.catatan-admin i {
    margin-right: 5px;
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
    
    .action-buttons {
        flex-direction: column;
    }
}
</style>