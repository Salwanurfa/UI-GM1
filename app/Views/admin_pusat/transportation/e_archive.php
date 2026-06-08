<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'E-Archive Dokumen Transportasi' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            background: #f4f6f9;
        }
        
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .page-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 18px 25px;
            font-weight: 600;
        }
        
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        
        .upload-area:hover {
            border-color: #007bff;
            background: #e7f3ff;
        }
        
        .upload-area i {
            font-size: 48px;
            color: #007bff;
            margin-bottom: 15px;
        }
        
        .file-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .file-icon.pdf {
            color: #dc3545;
        }
        
        .file-icon.jpg, .file-icon.jpeg, .file-icon.png {
            color: #28a745;
        }
        
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
        }
        
        .badge {
            font-size: 12px;
            padding: 5px 12px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-folder-open"></i> E-Archive Dokumen Transportasi (TR 6 & TR 7)</h1>
                <p>Upload dan kelola dokumen bukti fisik untuk UI GreenMetric</p>
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

            <!-- Upload Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-cloud-upload-alt"></i> Upload Dokumen Baru</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/admin-pusat/transportation/upload-dokumen') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="kategori" class="form-label">
                                    <i class="fas fa-tag"></i> Kategori Dokumen
                                </label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="TR 6 - Program Pengurangan Area Parkir">TR 6 - Program Pengurangan Area Parkir</option>
                                    <option value="TR 7 - Inisiatif Pengurangan Kendaraan Pribadi">TR 7 - Inisiatif Pengurangan Kendaraan Pribadi</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="nama_dokumen" class="form-label">
                                    <i class="fas fa-file-alt"></i> Nama Dokumen
                                </label>
                                <input type="text" class="form-control" id="nama_dokumen" 
                                       name="nama_dokumen" placeholder="Contoh: Surat Keputusan Rektor" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="tahun_dokumen" class="form-label">
                                    <i class="fas fa-calendar"></i> Tahun Dokumen
                                </label>
                                <select class="form-select" id="tahun_dokumen" name="tahun" required>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++): 
                                    ?>
                                        <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-8">
                                <label for="deskripsi" class="form-label">
                                    <i class="fas fa-align-left"></i> Deskripsi/Keterangan
                                </label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" 
                                          rows="3" placeholder="Jelaskan isi dokumen secara singkat..."></textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="file_dokumen" class="form-label">
                                    <i class="fas fa-paperclip"></i> File Dokumen (PDF/JPG/PNG)
                                </label>
                                <input type="file" class="form-control" id="file_dokumen" 
                                       name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-muted">Max: 5MB</small>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload"></i> Upload Dokumen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dokumen TR 6 -->
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h3><i class="fas fa-folder"></i> TR 6: Program Pengurangan Area Parkir</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="tr6Table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Dokumen</th>
                                    <th style="width: 100px;">Tahun</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 100px;">Tipe File</th>
                                    <th style="width: 120px;">Tanggal Upload</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $tr6_docs = array_filter($documents, function($doc) {
                                    return strpos($doc['kategori'], 'TR 6') !== false;
                                });
                                ?>
                                <?php if (!empty($tr6_docs)): ?>
                                    <?php foreach ($tr6_docs as $doc): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="fas fa-file file-icon <?= strtolower($doc['tipe_file']) ?>"></i>
                                            <strong><?= esc($doc['nama_dokumen']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-calendar"></i> <?= $doc['tahun'] ?>
                                            </span>
                                        </td>
                                        <td><?= esc($doc['deskripsi']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper($doc['tipe_file']) ?></span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('/admin-pusat/transportation/download-dokumen/' . $doc['id']) ?>" 
                                                   class="btn btn-info" title="Download">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="deleteDokumen(<?= $doc['id'] ?>, '<?= esc($doc['nama_dokumen']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada dokumen TR 6</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dokumen TR 7 -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3><i class="fas fa-folder"></i> TR 7: Inisiatif Pengurangan Kendaraan Pribadi</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="tr7Table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Nama Dokumen</th>
                                    <th style="width: 100px;">Tahun</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 100px;">Tipe File</th>
                                    <th style="width: 120px;">Tanggal Upload</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $tr7_docs = array_filter($documents, function($doc) {
                                    return strpos($doc['kategori'], 'TR 7') !== false;
                                });
                                ?>
                                <?php if (!empty($tr7_docs)): ?>
                                    <?php foreach ($tr7_docs as $doc): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="fas fa-file file-icon <?= strtolower($doc['tipe_file']) ?>"></i>
                                            <strong><?= esc($doc['nama_dokumen']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">
                                                <i class="fas fa-calendar"></i> <?= $doc['tahun'] ?>
                                            </span>
                                        </td>
                                        <td><?= esc($doc['deskripsi']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= strtoupper($doc['tipe_file']) ?></span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('/admin-pusat/transportation/download-dokumen/' . $doc['id']) ?>" 
                                                   class="btn btn-info" title="Download">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="deleteDokumen(<?= $doc['id'] ?>, '<?= esc($doc['nama_dokumen']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada dokumen TR 7</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan UI GreenMetric:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>TR 6:</strong> Dokumen program/kebijakan pengurangan area parkir (SK, Proposal, Laporan Implementasi)</li>
                    <li><strong>TR 7:</strong> Dokumen inisiatif pengurangan kendaraan pribadi (Program Carpool, Bike Sharing, Kampanye, dll)</li>
                    <li>Format file yang diterima: PDF, JPG, PNG (Max 5MB)</li>
                    <li>Dokumen harus jelas, terbaca, dan relevan dengan kategori</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#tr6Table, #tr7Table').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                pageLength: 10,
                order: [[5, 'desc']] // Sort by upload date
            });
        });

        function deleteDokumen(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin ingin menghapus dokumen:<br><strong>"${nama}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('/admin-pusat/transportation/hapus-dokumen/') ?>' + id;
                }
            });
        }

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
