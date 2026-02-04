<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Data Sampah TPS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="bg-dark min-vh-100">
                    <div class="p-3">
                        <h5 class="text-white">
                            <i class="fas fa-recycle me-2"></i>
                            TPS System
                        </h5>
                        <hr class="text-white">
                        <nav class="nav flex-column">
                            <a class="nav-link text-white active" href="<?= base_url('/tps/waste') ?>">
                                <i class="fas fa-list me-2"></i>
                                Data Sampah
                            </a>
                            <a class="nav-link text-white" href="<?= base_url('/tps/waste/create') ?>">
                                <i class="fas fa-plus me-2"></i>
                                Input Data
                            </a>
                            <a class="nav-link text-white" href="<?= base_url('/auth/logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><i class="fas fa-list text-primary me-2"></i><?= $title ?></h2>
                            <p class="text-muted">Daftar data sampah yang telah diinput</p>
                        </div>
                        <div>
                            <a href="<?= base_url('/tps/waste/create') ?>" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>
                                Input Data Baru
                            </a>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Data Table -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-table me-2"></i>
                                Data Sampah TPS
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($tps_data)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data sampah</h5>
                                <p class="text-muted">Klik tombol "Input Data Baru" untuk menambahkan data</p>
                                <a href="<?= base_url('/tps/waste/create') ?>" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>
                                    Input Data Pertama
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Gedung Asal</th>
                                            <th>Jumlah</th>
                                            <th>Satuan</th>
                                            <th>Nilai Rupiah</th>
                                            <th>Status</th>
                                            <th>Dibuat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tps_data as $index => $data): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td>
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($data['tanggal'])) ?>
                                            </td>
                                            <td>
                                                <i class="fas fa-building me-1"></i>
                                                <?= $data['sampah_dari_gedung'] ?>
                                            </td>
                                            <td class="text-end">
                                                <strong><?= number_format($data['jumlah_berat'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= $data['satuan'] ?></span>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($data['nilai_rupiah']): ?>
                                                <span class="text-success">
                                                    <i class="fas fa-money-bill me-1"></i>
                                                    Rp <?= number_format($data['nilai_rupiah'], 0, ',', '.') ?>
                                                </span>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    'pending' => 'bg-warning',
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger'
                                                ];
                                                $statusIcon = [
                                                    'pending' => 'fas fa-clock',
                                                    'approved' => 'fas fa-check',
                                                    'rejected' => 'fas fa-times'
                                                ];
                                                $statusText = [
                                                    'pending' => 'Pending',
                                                    'approved' => 'Disetujui',
                                                    'rejected' => 'Ditolak'
                                                ];
                                                ?>
                                                <span class="badge <?= $statusClass[$data['status']] ?? 'bg-secondary' ?>">
                                                    <i class="<?= $statusIcon[$data['status']] ?? 'fas fa-question' ?> me-1"></i>
                                                    <?= $statusText[$data['status']] ?? ucfirst($data['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($data['created_at'])) ?>
                                                </small>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Summary Statistics -->
                            <div class="row mt-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-list fa-2x mb-2"></i>
                                            <h5><?= count($tps_data) ?></h5>
                                            <small>Total Data</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-weight fa-2x mb-2"></i>
                                            <h5><?= number_format(array_sum(array_column($tps_data, 'jumlah_berat')), 2) ?></h5>
                                            <small>Total Berat</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-money-bill fa-2x mb-2"></i>
                                            <h5>Rp <?= number_format(array_sum(array_filter(array_column($tps_data, 'nilai_rupiah'))), 0, ',', '.') ?></h5>
                                            <small>Total Nilai</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clock fa-2x mb-2"></i>
                                            <h5><?= count(array_filter($tps_data, function($item) { return $item['status'] === 'pending'; })) ?></h5>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
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