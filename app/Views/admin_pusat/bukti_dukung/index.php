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
                                <label for="kategori" class="form-label fw-bold mb-2">
                                    <i class="fas fa-tags me-2 text-primary"></i>Kategori
                                </label>
                                <select class="form-select" 
                                        id="kategori" 
                                        name="kategori" 
                                        required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="Limbah 3R">Limbah 3R</option>
                                    <option value="Kertas dan Plastik">Kertas dan Plastik</option>
                                    <option value="Limbah Organik">Limbah Organik</option>
                                    <option value="Limbah Anorganik">Limbah Anorganik</option>
                                    <option value="Limbah B3">Limbah B3</option>
                                    <option value="Limbah Cair">Limbah Cair</option>
                                  </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Pilih kategori yang sesuai dengan jenis bukti dukung
                                </small>
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
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Daftar Bukti Dukung
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="<?= base_url('admin-pusat/bukti-dukung') ?>" class="d-flex justify-content-end">
                                <div class="input-group input-group-sm" style="max-width: 300px;">
                                    <label class="input-group-text" for="filterKategori">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </label>
                                    <select class="form-select" id="filterKategori" name="kategori" onchange="this.form.submit()">
                                        <option value="semua" <?= ($filter_kategori ?? 'semua') === 'semua' ? 'selected' : '' ?>>Semua Kategori</option>
                                        <option value="Limbah 3R" <?= ($filter_kategori ?? '') === 'Limbah 3R' ? 'selected' : '' ?>>Limbah 3R</option>
                                        <option value="Kertas dan Plastik" <?= ($filter_kategori ?? '') === 'Kertas dan Plastik' ? 'selected' : '' ?>>Kertas dan Plastik</option>
                                        <option value="Limbah Organik" <?= ($filter_kategori ?? '') === 'Limbah Organik' ? 'selected' : '' ?>>Limbah Organik</option>
                                        <option value="Limbah Anorganik" <?= ($filter_kategori ?? '') === 'Limbah Anorganik' ? 'selected' : '' ?>>Limbah Anorganik</option>
                                        <option value="Limbah B3" <?= ($filter_kategori ?? '') === 'Limbah B3' ? 'selected' : '' ?>>Limbah B3</option>
                                        <option value="Limbah Cair" <?= ($filter_kategori ?? '') === 'Limbah Cair' ? 'selected' : '' ?>>Limbah Cair</option>
                                        <option value="Limbah Lainnya" <?= ($filter_kategori ?? '') === 'Limbah Lainnya' ? 'selected' : '' ?>>Limbah Lainnya</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($bukti_dukung_list)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Judul</th>
                                        <th width="10%">Periode</th>
                                        <th width="12%">Kategori</th>
                                        <th width="15%">File</th>
                                        <th width="13%">Upload</th>
                                        <th width="20%">Aksi</th>
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
                                                <?php
                                                $kategori = $item['kategori'] ?? 'Tidak ada kategori';
                                                $badgeClass = 'secondary';
                                                
                                                // Set badge color based on category
                                                switch ($kategori) {
                                                    case 'Limbah 3R':
                                                        $badgeClass = 'success';
                                                        break;
                                                    case 'Kertas dan Plastik':
                                                        $badgeClass = 'info';
                                                        break;
                                                    case 'Limbah Organik':
                                                        $badgeClass = 'success';
                                                        break;
                                                    case 'Limbah Anorganik':
                                                        $badgeClass = 'warning';
                                                        break;
                                                    case 'Limbah B3':
                                                        $badgeClass = 'danger';
                                                        break;
                                                    case 'Limbah Cair':
                                                        $badgeClass = 'primary';
                                                        break;
                                                    case 'Limbah Lainnya':
                                                        $badgeClass = 'dark';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge bg-<?= $badgeClass ?>"><?= esc($kategori) ?></span>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="text-muted"><?= esc($item['nama_file']) ?></small><br>
                                                    <small class="text-success"><?= esc($item['ukuran_file']) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= isset($item['uploaded_at']) && $item['uploaded_at'] ? date('d/m/Y H:i', strtotime($item['uploaded_at'])) : (isset($item['created_at']) && $item['created_at'] ? date('d/m/Y H:i', strtotime($item['created_at'])) : 'N/A') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button"
                                                            class="btn btn-info btn-sm"
                                                            onclick="previewFile(<?= $item['id'] ?>, '<?= esc($item['nama_file']) ?>', '<?= esc($item['judul']) ?>')"
                                                            title="Preview File">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="<?= base_url('admin-pusat/bukti-dukung/delete/' . $item['id']) ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Yakin ingin menghapus bukti dukung ini?')"
                                                       title="Hapus">
                                                        <i class="fas fa-trash"></i>
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

    <!-- Modal Preview File -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="previewModalLabel">
                        <i class="fas fa-eye me-2"></i>Preview File: <span id="previewFileName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="min-height: 500px; max-height: 80vh; overflow: auto;">
                    <!-- Container untuk preview -->
                    <div id="previewContainer" class="d-flex justify-content-center align-items-center" style="min-height: 500px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="downloadLink" href="#" class="btn btn-success btn-sm" download>
                        <i class="fas fa-download me-2"></i>Download File
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
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

        // Function to preview file
        function previewFile(fileId, fileName, fileTitle) {
            // Set modal title
            document.getElementById('previewFileName').textContent = fileTitle;
            
            // Build preview URL using controller route
            const previewUrl = '<?= base_url('/admin-pusat/bukti-dukung/preview/') ?>' + fileId;
            const downloadUrl = '<?= base_url('/admin-pusat/bukti-dukung/download/') ?>' + fileId;
            
            // Set download link
            document.getElementById('downloadLink').href = downloadUrl;
            
            // Get file extension
            const extension = fileName.split('.').pop().toLowerCase();
            
            // Get preview container
            const container = document.getElementById('previewContainer');
            
            // Show loading spinner
            container.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
            
            // Determine file type and render preview
            setTimeout(() => {
                if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                    // Image preview
                    container.innerHTML = `
                        <div class="p-4 w-100 text-center">
                            <img src="${previewUrl}" 
                                 alt="${fileTitle}" 
                                 class="img-fluid rounded shadow"
                                 style="max-width: 100%; max-height: 70vh; object-fit: contain;"
                                 onerror="this.parentElement.innerHTML='<div class=\\'alert alert-danger m-4\\'><i class=\\'fas fa-exclamation-triangle me-2\\'></i>Gagal memuat gambar. URL: ${previewUrl}</div>'">
                        </div>
                    `;
                } else if (extension === 'pdf') {
                    // PDF preview using iframe
                    container.innerHTML = `
                        <iframe src="${previewUrl}" 
                                style="width: 100%; height: 70vh; border: none;"
                                title="${fileTitle}">
                            <p>Browser Anda tidak mendukung preview PDF. 
                               <a href="${downloadUrl}" class="btn btn-primary">Download PDF</a>
                            </p>
                        </iframe>
                    `;
                } else if (['doc', 'docx', 'xls', 'xlsx'].includes(extension)) {
                    // Office documents - use Google Docs Viewer
                    const fullUrl = window.location.origin + previewUrl;
                    container.innerHTML = `
                        <div class="p-4 text-center">
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Preview Dokumen Office</strong><br>
                                <small>Dokumen Word/Excel akan dibuka menggunakan Google Docs Viewer</small>
                            </div>
                            <iframe src="https://docs.google.com/viewer?url=${encodeURIComponent(fullUrl)}&embedded=true" 
                                    style="width: 100%; height: 65vh; border: 1px solid #ddd;"
                                    title="${fileTitle}">
                                <p>Browser Anda tidak mendukung preview dokumen. 
                                   <a href="${downloadUrl}" class="btn btn-primary">Download File</a>
                                </p>
                            </iframe>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Jika preview tidak muncul, silakan download file untuk melihat isinya
                                </small>
                            </div>
                        </div>
                    `;
                } else {
                    // Unsupported file type
                    container.innerHTML = `
                        <div class="p-5 text-center">
                            <i class="fas fa-file-alt text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mb-3">Preview tidak tersedia untuk tipe file ini</h5>
                            <p class="text-muted mb-4">File: <strong>${fileName}</strong></p>
                            <a href="${downloadUrl}" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>Download File
                            </a>
                        </div>
                    `;
                }
            }, 300);
        }
    </script>
</body>
</html>