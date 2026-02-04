<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Review Detail' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-clipboard-check"></i> Review Detail</h1>
                <p><?= htmlspecialchars($penilaian['indikator']) ?></p>
            </div>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/review') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
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

        <div class="row">
            <!-- Data Detail -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Detail Data Penilaian</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label>Unit</label>
                                    <div class="info-value">
                                        <strong><?= htmlspecialchars($penilaian['nama_unit']) ?></strong>
                                    </div>
                                </div>
                                
                                <div class="info-group">
                                    <label>PIC (Person in Charge)</label>
                                    <div class="info-value">
                                        <?= htmlspecialchars($penilaian['nama_lengkap']) ?>
                                        <br>
                                        <small class="text-muted"><?= htmlspecialchars($penilaian['email']) ?></small>
                                    </div>
                                </div>
                                
                                <div class="info-group">
                                    <label>Kategori UIGM</label>
                                    <div class="info-value">
                                        <span class="badge bg-primary fs-6"><?= $penilaian['kategori_uigm'] ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label>Indikator</label>
                                    <div class="info-value">
                                        <?= htmlspecialchars($penilaian['indikator']) ?>
                                    </div>
                                </div>
                                
                                <div class="info-group">
                                    <label>Nilai Input</label>
                                    <div class="info-value">
                                        <?php if ($penilaian['nilai_input']): ?>
                                            <strong class="text-primary fs-4">
                                                <?= number_format($penilaian['nilai_input'], 2) ?>
                                            </strong>
                                        <?php else: ?>
                                            <span class="text-muted">Belum diisi</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="info-group">
                                    <label>Status Saat Ini</label>
                                    <div class="info-value">
                                        <span class="status-badge status-<?= $penilaian['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $penilaian['status'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="info-group">
                                    <label>Tanggal Dibuat</label>
                                    <div class="info-value">
                                        <?= date('d/m/Y H:i:s', strtotime($penilaian['created_at'])) ?>
                                    </div>
                                </div>
                                
                                <div class="info-group">
                                    <label>Terakhir Diperbarui</label>
                                    <div class="info-value">
                                        <?= date('d/m/Y H:i:s', strtotime($penilaian['updated_at'])) ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($penilaian['catatan_admin'])): ?>
                                <div class="info-group">
                                    <label>Catatan Admin Sebelumnya</label>
                                    <div class="info-value">
                                        <div class="alert alert-info">
                                            <i class="fas fa-comment-alt"></i>
                                            <?= htmlspecialchars($penilaian['catatan_admin']) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Data -->
                <?php if (!empty($relatedData) && count($relatedData) > 1): ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list"></i>
                        <h3>Data Terkait (Kategori <?= $penilaian['kategori_uigm'] ?>)</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Indikator</th>
                                        <th>Nilai Input</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($relatedData as $item): ?>
                                    <tr class="<?= ($item['id'] == $penilaian['id']) ? 'table-primary' : '' ?>">
                                        <td>
                                            <?= htmlspecialchars($item['indikator']) ?>
                                            <?php if ($item['id'] == $penilaian['id']): ?>
                                                <span class="badge bg-warning ms-2">Current</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['nilai_input']): ?>
                                                <?= number_format($item['nilai_input'], 2) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?= $item['status'] ?>">
                                                <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($item['id'] != $penilaian['id'] && $item['status'] == 'dikirim'): ?>
                                                <a href="<?= base_url('/admin-pusat/review/detail/' . $item['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
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
            </div>

            <!-- Review Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-clipboard-check"></i>
                        <h3>Review Actions</h3>
                    </div>
                    <div class="card-body">
                        <form id="reviewForm">
                            <input type="hidden" name="id" value="<?= $penilaian['id'] ?>">
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Review</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="">Pilih Status</option>
                                    <option value="review">Sedang Review</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="perlu_revisi">Perlu Revisi</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="catatan_admin" class="form-label">Catatan Admin</label>
                                <textarea name="catatan_admin" id="catatan_admin" class="form-control" rows="4" 
                                          placeholder="Masukkan catatan untuk unit..."></textarea>
                                <div class="form-text">
                                    Catatan wajib diisi jika status "Perlu Revisi"
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Review
                                </button>
                                
                                <button type="button" class="btn btn-success" onclick="quickApprove()">
                                    <i class="fas fa-check"></i> Quick Approve
                                </button>
                                
                                <button type="button" class="btn btn-warning" onclick="quickReject()">
                                    <i class="fas fa-times"></i> Quick Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Review Guidelines -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i>
                        <h3>Panduan Review</h3>
                    </div>
                    <div class="card-body">
                        <div class="guideline-item">
                            <h6><i class="fas fa-check-circle text-success"></i> Disetujui</h6>
                            <p>Data sudah sesuai dan dapat diterima</p>
                        </div>
                        
                        <div class="guideline-item">
                            <h6><i class="fas fa-edit text-warning"></i> Perlu Revisi</h6>
                            <p>Data perlu diperbaiki, wajib beri catatan yang jelas</p>
                        </div>
                        
                        <div class="guideline-item">
                            <h6><i class="fas fa-clock text-info"></i> Sedang Review</h6>
                            <p>Tandai jika masih dalam proses review</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Reject Modal -->
        <div class="modal fade" id="quickRejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Quick Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quickRejectNote" class="form-label">Catatan Penolakan (Wajib)</label>
                            <textarea class="form-control" id="quickRejectNote" rows="4" 
                                      placeholder="Masukkan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" onclick="confirmQuickReject()">
                            <i class="fas fa-times"></i> Tolak Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const status = formData.get('status');
            const catatan = formData.get('catatan_admin');
            
            // Validate required note for rejection
            if (status === 'perlu_revisi' && !catatan.trim()) {
                alert('Catatan admin wajib diisi untuk status Perlu Revisi');
                return;
            }
            
            submitReview(formData);
        });

        // Quick approve
        function quickApprove() {
            if (confirm('Apakah Anda yakin ingin menyetujui data ini?')) {
                const formData = new FormData();
                formData.append('id', <?= $penilaian['id'] ?>);
                formData.append('status', 'disetujui');
                formData.append('catatan_admin', 'Data disetujui oleh Admin Pusat');
                
                submitReview(formData);
            }
        }

        // Quick reject
        function quickReject() {
            const modal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
            modal.show();
        }

        // Confirm quick reject
        function confirmQuickReject() {
            const note = document.getElementById('quickRejectNote').value.trim();
            if (!note) {
                alert('Catatan penolakan wajib diisi');
                return;
            }

            const formData = new FormData();
            formData.append('id', <?= $penilaian['id'] ?>);
            formData.append('status', 'perlu_revisi');
            formData.append('catatan_admin', note);
            
            submitReview(formData);
            bootstrap.Modal.getInstance(document.getElementById('quickRejectModal')).hide();
        }

        // Submit review
        function submitReview(formData) {
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
                    alert('Review berhasil disimpan');
                    window.location.href = '<?= base_url('/admin-pusat/review') ?>';
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }

        // Status change handler
        document.getElementById('status').addEventListener('change', function() {
            const catatanField = document.getElementById('catatan_admin');
            if (this.value === 'perlu_revisi') {
                catatanField.required = true;
                catatanField.parentElement.classList.add('required-field');
            } else {
                catatanField.required = false;
                catatanField.parentElement.classList.remove('required-field');
            }
        });
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
}

.card-body {
    padding: 25px;
}

.info-group {
    margin-bottom: 20px;
}

.info-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.info-value {
    color: #495057;
    font-size: 15px;
    line-height: 1.5;
}

.status-badge {
    padding: 8px 16px;
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

.guideline-item {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e9ecef;
}

.guideline-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.guideline-item h6 {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
}

.guideline-item p {
    margin: 0;
    font-size: 13px;
    color: #6c757d;
}

.required-field {
    position: relative;
}

.required-field::after {
    content: '*';
    color: #dc3545;
    position: absolute;
    top: 0;
    right: 10px;
    font-size: 18px;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
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
}
</style>