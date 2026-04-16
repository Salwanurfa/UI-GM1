<?php
$title = $title ?? 'LogBook Kegiatan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header mb-4 ms-3 d-flex justify-content-between align-items-center">
            <div>
                <h4><i class="fas fa-book me-2"></i>LogBook Kegiatan</h4>
                <p>Kelola catatan logbook harian untuk sistem UI GreenMetric dengan kategori Program 3R, Limbah B3, dan Limbah Cair</p>
            </div>
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
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs mb-4" id="logbookTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="program3r-tab" data-bs-toggle="tab" data-bs-target="#program3r" type="button" role="tab">
                        <i class="fas fa-recycle me-2"></i>Program 3R
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="limbahb3-tab" data-bs-toggle="tab" data-bs-target="#limbahb3" type="button" role="tab">
                        <i class="fas fa-skull-crossbones me-2"></i>Limbah B3
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="limbahcair-tab" data-bs-toggle="tab" data-bs-target="#limbahcair" type="button" role="tab">
                        <i class="fas fa-tint me-2"></i>Limbah Cair
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="logbookTabContent">
                
                <!-- Program 3R Tab -->
                <div class="tab-pane fade show active" id="program3r" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card shadow-sm border-0 p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">
                                        <i class="fas fa-recycle me-2 text-success"></i>Input Program 3R (Reuse, Reduce, Recycle)
                                    </h5>
                                </div>
                                
                                <form action="<?= base_url('admin-pusat/logbook/save') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="kategori" value="3R">
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="tanggal_3r" class="form-label fw-bold">
                                                <i class="fas fa-calendar-alt me-2 text-primary"></i>Tanggal Kegiatan
                                            </label>
                                            <input type="date" class="form-control" id="tanggal_3r" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="sumber_sampah_3r" class="form-label fw-bold">
                                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>Sumber Sampah
                                            </label>
                                            <input type="text" class="form-control" id="sumber_sampah_3r" name="sumber_sampah" placeholder="Contoh: Kantin, Gedung A, Taman" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="jenis_material_3r" class="form-label fw-bold">
                                                <i class="fas fa-cube me-2 text-primary"></i>Jenis Material
                                            </label>
                                            <select class="form-control" id="jenis_material_3r" name="jenis_material" required>
                                                <option value="">Pilih Jenis Material</option>
                                                <option value="Kertas">Kertas</option>
                                                <option value="Plastik">Plastik</option>
                                                <option value="Logam">Logam</option>
                                                <option value="Kaca">Kaca</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="berat_terkumpul_3r" class="form-label fw-bold">
                                                <i class="fas fa-weight me-2 text-primary"></i>Berat Terkumpul (kg)
                                            </label>
                                            <input type="number" step="0.01" class="form-control" id="berat_terkumpul_3r" name="berat_terkumpul" placeholder="0.00" required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tindakan_3r" class="form-label fw-bold">
                                            <i class="fas fa-cogs me-2 text-primary"></i>Tindakan
                                        </label>
                                        <select class="form-control" id="tindakan_3r" name="tindakan" required>
                                            <option value="">Pilih Tindakan</option>
                                            <option value="Didaur ulang">Didaur ulang</option>
                                            <option value="Digunakan kembali">Digunakan kembali</option>
                                            <option value="Dijual ke Bank Sampah">Dijual ke Bank Sampah</option>
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-save me-2"></i>Simpan Program 3R
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Riwayat Program 3R -->
                    <div class="card shadow-sm border-0 mt-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Riwayat Program 3R
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%">Tanggal</th>
                                            <th width="20%">Sumber</th>
                                            <th width="15%">Material</th>
                                            <th width="15%">Berat (kg)</th>
                                            <th width="20%">Tindakan</th>
                                            <th width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($riwayat_3r)): ?>
                                            <?php foreach ($riwayat_3r as $row): ?>
                                                <tr>
                                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                                    <td><?= esc($row['sumber_sampah']) ?></td>
                                                    <td><?= esc($row['jenis_material']) ?></td>
                                                    <td><?= number_format($row['berat_terkumpul'], 2) ?> kg</td>
                                                    <td><?= esc($row['tindakan']) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary btn-sm" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>Belum ada data Program 3R
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Limbah B3 Tab -->
                <div class="tab-pane fade" id="limbahb3" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-skull-crossbones fa-3x text-muted mb-3"></i>
                        <h5>Fitur Limbah B3 Segera Hadir</h5>
                        <p class="text-muted">Modul untuk pengelolaan limbah B3 sedang dalam pengembangan</p>
                    </div>
                </div>

                <!-- Limbah Cair Tab -->
                <div class="tab-pane fade" id="limbahcair" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-tint fa-3x text-muted mb-3"></i>
                        <h5>Fitur Limbah Cair Segera Hadir</h5>
                        <p class="text-muted">Modul untuk pengelolaan limbah cair sedang dalam pengembangan</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-set today's date for all date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('tanggal_3r').value = today;
        });
    </script>
</body>
</html>