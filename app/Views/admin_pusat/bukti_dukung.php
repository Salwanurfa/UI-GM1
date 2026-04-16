<?php
$title = $title ?? 'Bukti Dukung';
?>

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
        <!-- Page Header -->
        <div class="page-header mb-4 ms-3">
            <h4><i class="fas fa-file-alt me-2"></i>Bukti Dukung</h4>
            <p>Kelola dokumen bukti dukung untuk sistem UI GreenMetric</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Main Content Card -->
        <div class="card shadow-sm border-0 p-4">
            <!-- Header Petunjuk -->
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fas fa-info-circle me-3 fs-5"></i>
                <div>
                    <strong>Petunjuk Penggunaan</strong><br>
                    <small>Upload dokumen bukti dukung untuk mendukung data UI GreenMetric. Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Maksimal ukuran file 10MB.</small>
                </div>
            </div>

            <!-- Form Upload Bukti Dukung -->
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm border-0 p-3">
                        <h5 class="mb-3">
                            <i class="fas fa-upload me-2 text-primary"></i>Upload Bukti Dukung
                        </h5>
                        
                        <form action="<?= base_url('/admin-pusat/bukti-dukung/upload') ?>" method="POST" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="judul" class="form-label fw-bold mb-2">
                                    <i class="fas fa-heading me-2 text-primary"></i>Judul Bukti Dukung
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="judul" 
                                       name="judul" 
                                       placeholder="Contoh: Laporan Waste Management Q1 2026" 
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="periode" class="form-label fw-bold mb-2">
                                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Periode
                                </label>
                                <input type="month" 
                                       class="form-control" 
                                       id="periode" 
                                       name="periode" 
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="file_bukti" class="form-label fw-bold mb-2">
                                    <i class="fas fa-file me-2 text-primary"></i>File Bukti Dukung
                                </label>
                                <input type="file" 
                                       class="form-control" 
                                       id="file_bukti" 
                                       name="file_bukti" 
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" 
                                       required>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG. Maksimal 10MB
                                </small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti Dukung
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Bukti Dukung -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Bukti Dukung
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($bukti_dukung_list)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Judul</th>
                                        <th width="15%">Periode</th>
                                        <th width="20%">File</th>
                                        <th width="15%">Upload</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bukti_dukung_list as $index => $item): ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark"><?= $index + 1 ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt text-primary me-2"></i>
                                                    <strong><?= esc($item['judul']) ?></strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= esc($item['periode']) ?></span>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted"><?= esc($item['nama_file']) ?></small><br>
                                                    <small class="text-success"><?= esc($item['ukuran_file']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($item['uploaded_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('/admin-pusat/bukti-dukung/download/' . $item['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <a href="<?= base_url('/admin-pusat/bukti-dukung/delete/' . $item['id']) ?>" 
                                                       class="btn btn-outline-danger btn-sm"
                                                       onclick="return confirm('Yakin ingin menghapus bukti dukung ini?')"
                                                       title="Hapus">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada bukti dukung</h5>
                            <p class="text-muted">Upload dokumen bukti dukung pertama Anda menggunakan form di atas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // File validation
        document.getElementById('file_bukti').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 10MB');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/jpeg',
                    'image/png'
                ];
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG');
                    this.value = '';
                    return;
                }
            }
        });
    </script>
</body>
</html>