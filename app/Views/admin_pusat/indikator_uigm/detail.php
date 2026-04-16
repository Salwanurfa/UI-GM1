<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Professional Page Header -->
        <div class="detail-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line text-primary"></i> <?= $title ?></h1>
                <p class="text-muted"><?= $indicator_config['description'] ?></p>
            </div>
            
            <div class="header-actions">
                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 120px;">
                        <?php foreach ($year_options as $yearValue => $yearLabel): ?>
                            <option value="<?= $yearValue ?>" <?= ($yearValue == $year) ? 'selected' : '' ?>>
                                <?= $yearLabel ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="<?= base_url('admin-pusat/indikator-uigm') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Professional Statistics Cards -->
        <div class="stats-grid-detail mb-4">
            <div class="stat-card-detail primary">
                <div class="stat-icon-detail">
                    <i class="fas fa-weight-hanging"></i>
                </div>
                <div class="stat-content-detail">
                    <h3><?= number_format($indicator_data['total_kg'], 2) ?> kg</h3>
                    <p>Total Berat</p>
                </div>
            </div>

            <div class="stat-card-detail info">
                <div class="stat-icon-detail">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="stat-content-detail">
                    <h3><?= number_format($indicator_data['total_l'], 2) ?> L</h3>
                    <p>Total Volume</p>
                </div>
            </div>

            <div class="stat-card-detail success">
                <div class="stat-icon-detail">
                    <i class="fas fa-list-ol"></i>
                </div>
                <div class="stat-content-detail">
                    <h3><?= $indicator_data['total_records'] ?></h3>
                    <p>Total Data</p>
                </div>
            </div>
        </div>
        <!-- Professional Data Table -->
        <div class="card shadow-lg">
            <div class="card-header bg-gradient-primary">
                <h3 class="mb-0 text-white">
                    <i class="fas fa-table me-2"></i>Riwayat Inputan User
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($detailed_records)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center" width="5%">#</th>
                                    <th width="20%">
                                        <i class="fas fa-user me-1"></i>Nama User/Unit
                                    </th>
                                    <th width="25%">
                                        <i class="fas fa-trash me-1"></i>Jenis Sampah
                                    </th>
                                    <th class="text-center" width="15%">
                                        <i class="fas fa-weight-hanging me-1"></i>Volume
                                    </th>
                                    <th class="text-center" width="15%">
                                        <i class="fas fa-calendar me-1"></i>Tanggal
                                    </th>
                                    <th class="text-center" width="10%">
                                        <i class="fas fa-building me-1"></i>Gedung
                                    </th>
                                    <th class="text-center" width="10%">
                                        <i class="fas fa-camera me-1"></i>Bukti
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detailed_records as $index => $record): ?>
                                    <tr class="table-row-hover">
                                        <td class="text-center fw-bold text-primary"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="fw-bold text-dark"><?= esc($record['nama_unit']) ?></div>
                                                <?php if (!empty($record['nama_user'])): ?>
                                                    <small class="text-muted">
                                                        <i class="fas fa-user-circle me-1"></i><?= esc($record['nama_user']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="waste-info">
                                                <div class="fw-bold text-dark"><?= esc($record['jenis_sampah']) ?></div>
                                                <?php if (!empty($record['nama_sampah']) && $record['nama_sampah'] !== $record['jenis_sampah']): ?>
                                                    <small class="text-muted"><?= esc($record['nama_sampah']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="volume-badge">
                                                <?php if ($record['volume_kg'] > 0): ?>
                                                    <span class="badge bg-primary rounded-pill">
                                                        <i class="fas fa-weight-hanging me-1"></i>
                                                        <?= number_format($record['volume_kg'], 2) ?> kg
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($record['volume_l'] > 0): ?>
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="fas fa-flask me-1"></i>
                                                        <?= number_format($record['volume_l'], 2) ?> L
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?= date('d/m/Y', strtotime($record['tanggal'])) ?>
                                                <br>
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('H:i', strtotime($record['tanggal'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill">
                                                <i class="fas fa-building me-1"></i>
                                                <?= esc($record['gedung']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!empty($record['bukti_foto'])): ?>
                                                <button class="btn btn-success btn-sm rounded-pill shadow-sm" 
                                                        onclick="viewEvidence('<?= esc($record['bukti_foto']) ?>')" 
                                                        title="Lihat Bukti">
                                                    <i class="fas fa-image me-1"></i>Lihat
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-primary btn-sm rounded-pill shadow-sm" 
                                                        onclick="openEvidenceUpload(<?= htmlspecialchars(json_encode([
                                                            'id' => $record['id'],
                                                            'source_table' => $record['source_table'] ?? 'waste_management',
                                                            'indikator_key' => $indicator_key,
                                                            'nama_unit' => $record['nama_unit'],
                                                            'jenis_sampah' => $record['jenis_sampah'],
                                                            'volume_kg' => $record['volume_kg'],
                                                            'volume_l' => $record['volume_l'],
                                                            'tanggal' => $record['tanggal'],
                                                            'indikator_name' => $title
                                                        ]), ENT_QUOTES, 'UTF-8') ?>)" 
                                                        title="Unggah Bukti">
                                                    <i class="fas fa-upload me-1"></i>Upload
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h4>Belum Ada Data</h4>
                        <p class="text-muted">Belum ada data untuk indikator ini pada tahun <?= $year ?></p>
                        <small class="text-muted">Data akan muncul setelah user menginput dan TPS menyetujui</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Professional Evidence Gallery -->
        <?php if (!empty($evidence_files)): ?>
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-gradient-success">
                    <h3 class="mb-0 text-white">
                        <i class="fas fa-images me-2"></i>Galeri Bukti Dukung
                        <span class="badge bg-light text-dark ms-2"><?= count($evidence_files) ?> Foto</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="evidence-grid">
                        <?php foreach ($evidence_files as $evidence): ?>
                            <div class="evidence-item">
                                <div class="evidence-image" onclick="viewEvidence('<?= esc($evidence['file_path']) ?>')">
                                    <img src="<?= base_url($evidence['file_path']) ?>" 
                                         alt="Bukti Dukung" 
                                         class="img-fluid">
                                    <div class="evidence-overlay">
                                        <i class="fas fa-expand-alt"></i>
                                    </div>
                                </div>
                                <div class="evidence-info">
                                    <h6 class="mb-1">
                                        <i class="fas fa-building text-primary me-1"></i>
                                        <?= esc($evidence['nama_unit']) ?>
                                    </h6>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-trash me-1"></i>
                                            <?= esc($evidence['jenis_sampah']) ?>
                                        </small>
                                    </p>
                                    <p class="mb-1">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y', strtotime($evidence['tanggal'])) ?>
                                        </small>
                                    </p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            <i class="fas fa-weight-hanging me-1"></i>
                                            <?php if ($evidence['volume_kg'] > 0): ?>
                                                <?= number_format($evidence['volume_kg'], 2) ?> kg
                                            <?php else: ?>
                                                <?= number_format($evidence['volume_l'], 2) ?> L
                                            <?php endif; ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Evidence Upload Modal -->
    <div class="modal fade" id="evidenceUploadModal" tabindex="-1" aria-labelledby="evidenceUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="evidenceUploadModalLabel">
                        <i class="fas fa-upload me-2"></i>Unggah Bukti Dukung
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="evidenceUploadForm" enctype="multipart/form-data">
                        <input type="hidden" id="upload_record_id" name="record_id">
                        <input type="hidden" id="upload_source_table" name="source_table">
                        <input type="hidden" id="upload_indicator_key" name="indicator_key">
                        
                        <!-- Record Information -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi Data</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Unit:</strong> <span id="upload_unit_name">-</span><br>
                                    <strong>Jenis Sampah:</strong> <span id="upload_waste_type">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Volume:</strong> <span id="upload_volume">-</span><br>
                                    <strong>Tanggal:</strong> <span id="upload_date">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-3">
                            <label for="evidence_file" class="form-label">
                                <i class="fas fa-file me-1"></i>Pilih File Bukti Dukung
                            </label>
                            <input type="file" class="form-control" id="evidence_file" name="evidence_file" 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Format yang diizinkan: JPG, PNG, PDF, DOC, DOCX. Maksimal 5 MB.
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div id="file_preview" class="mb-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-eye me-1"></i>Preview File</h6>
                                    <div id="preview_content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="evidence_description" class="form-label">
                                <i class="fas fa-comment me-1"></i>Keterangan (Opsional)
                            </label>
                            <textarea class="form-control" id="evidence_description" name="description" rows="3" 
                                      placeholder="Tambahkan keterangan tentang bukti dukung ini..."></textarea>
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload_progress" class="mb-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted mt-1">Mengunggah file...</small>
                        </div>

                        <!-- Error Messages -->
                        <div id="upload_error" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="error_message"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="upload_submit_btn" onclick="submitEvidenceUpload()">
                        <i class="fas fa-upload me-1"></i>Unggah Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function viewEvidence(buktiPath) {
        if (buktiPath) {
            const imageUrl = `<?= base_url() ?>${buktiPath}`;
            
            // Create professional modal
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-image me-2"></i>Bukti Dukung
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-4">
                            <img src="${imageUrl}" class="img-fluid rounded shadow" alt="Bukti Dukung" style="max-height: 70vh;">
                        </div>
                        <div class="modal-footer">
                            <a href="${imageUrl}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
            
            // Remove modal from DOM when hidden
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }
    }

    // Evidence Upload Functions
    function openEvidenceUpload(item) {
        // Populate modal with item data
        document.getElementById('upload_record_id').value = item.id;
        document.getElementById('upload_source_table').value = item.source_table || 'waste_management';
        document.getElementById('upload_indicator_key').value = item.indikator_key || '';
        
        document.getElementById('upload_unit_name').textContent = item.nama_unit || 'Unknown Unit';
        document.getElementById('upload_waste_type').textContent = item.jenis_sampah || 'Unknown';
        
        // Format volume display
        let volumeDisplay = '';
        if (item.volume_kg > 0) {
            volumeDisplay = `${item.volume_kg} kg`;
        }
        if (item.volume_l > 0) {
            volumeDisplay += (volumeDisplay ? ' + ' : '') + `${item.volume_l} L`;
        }
        document.getElementById('upload_volume').textContent = volumeDisplay || '0';
        
        document.getElementById('upload_date').textContent = formatDate(item.tanggal);
        
        // Update modal title
        const indicatorName = item.indikator_name || 'Unknown Indicator';
        document.getElementById('evidenceUploadModalLabel').innerHTML = 
            `<i class="fas fa-upload me-2"></i>Unggah Bukti Dukung - ${indicatorName}`;
        
        // Reset form
        document.getElementById('evidenceUploadForm').reset();
        document.getElementById('file_preview').style.display = 'none';
        document.getElementById('upload_progress').style.display = 'none';
        document.getElementById('upload_error').style.display = 'none';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('evidenceUploadModal'));
        modal.show();
    }

    function submitEvidenceUpload() {
        const form = document.getElementById('evidenceUploadForm');
        const fileInput = document.getElementById('evidence_file');
        const submitBtn = document.getElementById('upload_submit_btn');
        const progressDiv = document.getElementById('upload_progress');
        const errorDiv = document.getElementById('upload_error');
        
        // Validate file
        if (!fileInput.files[0]) {
            showUploadError('Silakan pilih file terlebih dahulu');
            return;
        }
        
        const file = fileInput.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            showUploadError('Ukuran file terlalu besar. Maksimal 5 MB.');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!allowedTypes.includes(file.type)) {
            showUploadError('Format file tidak didukung. Gunakan JPG, PNG, PDF, DOC, atau DOCX.');
            return;
        }
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Show progress
        submitBtn.disabled = true;
        progressDiv.style.display = 'block';
        errorDiv.style.display = 'none';
        
        // Upload file
        fetch('<?= base_url('admin-pusat/indikator-uigm/upload-evidence') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success
                showUploadSuccess('Bukti dukung berhasil diunggah!');
                
                // Close modal after delay and reload page
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('evidenceUploadModal')).hide();
                    location.reload(); // Reload to show updated evidence
                }, 1500);
            } else {
                showUploadError(data.message || 'Gagal mengunggah file');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showUploadError('Terjadi kesalahan saat mengunggah file');
        })
        .finally(() => {
            submitBtn.disabled = false;
            progressDiv.style.display = 'none';
        });
    }

    function showUploadError(message) {
        const errorDiv = document.getElementById('upload_error');
        const errorMessage = document.getElementById('error_message');
        
        errorDiv.className = 'alert alert-danger';
        errorMessage.textContent = message;
        errorDiv.style.display = 'block';
    }

    function showUploadSuccess(message) {
        const errorDiv = document.getElementById('upload_error');
        errorDiv.className = 'alert alert-success';
        errorDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
        errorDiv.style.display = 'block';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric'
        });
    }

    // File preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('evidence_file');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewDiv = document.getElementById('file_preview');
                const previewContent = document.getElementById('preview_content');
                
                if (file) {
                    // Show file info
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    let fileIcon = 'fas fa-file';
                    
                    if (file.type.startsWith('image/')) {
                        fileIcon = 'fas fa-image';
                    } else if (file.type === 'application/pdf') {
                        fileIcon = 'fas fa-file-pdf';
                    } else if (file.type.includes('word')) {
                        fileIcon = 'fas fa-file-word';
                    }
                    
                    previewContent.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="${fileIcon} fa-2x text-primary me-3"></i>
                            <div>
                                <strong>${file.name}</strong><br>
                                <small class="text-muted">Ukuran: ${fileSize} MB</small>
                            </div>
                        </div>
                    `;
                    
                    previewDiv.style.display = 'block';
                    
                    // Show image preview for image files
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewContent.innerHTML += `
                                <div class="mt-2">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                } else {
                    previewDiv.style.display = 'none';
                }
            });
        }
    });
    </script>
    <style>
        /* Professional Detail Page Styles */
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            background-color: #f8f9fc;
        }

        .detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .detail-header p {
            font-size: 16px;
            opacity: 0.9;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .header-actions .form-select {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            color: #333;
        }

        .header-actions .btn {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .header-actions .btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Professional Statistics Cards */
        .stats-grid-detail {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .stat-card-detail {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            border-left: 5px solid;
            position: relative;
            overflow: hidden;
        }

        .stat-card-detail::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), transparent);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .stat-card-detail:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card-detail.primary { border-left-color: #4e73df; }
        .stat-card-detail.success { border-left-color: #1cc88a; }
        .stat-card-detail.info { border-left-color: #36b9cc; }
        .stat-card-detail.warning { border-left-color: #f6c23e; }

        .stat-icon-detail {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            position: relative;
            z-index: 2;
        }

        .stat-card-detail.primary .stat-icon-detail { 
            background: linear-gradient(135deg, #4e73df, #224abe); 
        }
        .stat-card-detail.success .stat-icon-detail { 
            background: linear-gradient(135deg, #1cc88a, #17a673); 
        }
        .stat-card-detail.info .stat-icon-detail { 
            background: linear-gradient(135deg, #36b9cc, #2c9faf); 
        }
        .stat-card-detail.warning .stat-icon-detail { 
            background: linear-gradient(135deg, #f6c23e, #dda20a); 
        }

        .stat-content-detail h3 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #2c3e50;
        }

        .stat-content-detail p {
            margin: 0;
            color: #6c757d;
            font-weight: 600;
            font-size: 16px;
        }

        /* Professional Card Styles */
        .card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .shadow-lg {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        }

        .card-header h3 {
            font-size: 20px;
            font-weight: 600;
        }

        /* Professional Table Styles */
        .table-dark th {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            border: none;
            font-weight: 600;
            font-size: 14px;
            padding: 15px 12px;
        }

        .table-row-hover:hover {
            background-color: rgba(78, 115, 223, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #e3e6f0;
        }

        .user-info, .waste-info {
            line-height: 1.4;
        }

        .volume-badge .badge {
            font-size: 12px;
            padding: 6px 12px;
            margin: 2px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 30px;
            color: #6c757d;
        }

        .empty-icon {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #495057;
            margin-bottom: 10px;
        }

        /* Evidence Gallery */
        .evidence-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .evidence-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .evidence-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .evidence-image {
            position: relative;
            height: 200px;
            overflow: hidden;
            cursor: pointer;
        }

        .evidence-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .evidence-image:hover img {
            transform: scale(1.1);
        }

        .evidence-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .evidence-image:hover .evidence-overlay {
            opacity: 1;
        }

        .evidence-info {
            padding: 20px;
        }

        .evidence-info h6 {
            color: #2c3e50;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .detail-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .detail-header h1 {
                font-size: 24px;
            }

            .stats-grid-detail {
                grid-template-columns: 1fr;
            }

            .evidence-grid {
                grid-template-columns: 1fr;
            }

            .table-responsive {
                font-size: 12px;
            }
        }

        /* Modal Improvements */
        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }
    </style>
</body>
</html>