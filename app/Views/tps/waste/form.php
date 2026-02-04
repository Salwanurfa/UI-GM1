<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Input Data Sampah TPS' ?></title>
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
                            <a class="nav-link text-white" href="<?= base_url('/tps/waste') ?>">
                                <i class="fas fa-list me-2"></i>
                                Data Sampah
                            </a>
                            <a class="nav-link text-white active" href="<?= base_url('/tps/waste/create') ?>">
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
                            <h2><i class="fas fa-plus-circle text-success me-2"></i><?= $title ?></h2>
                            <p class="text-muted">Form khusus untuk pengelola TPS</p>
                        </div>
                        <div>
                            <span class="badge bg-success">Pengelola TPS</span>
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

                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Form Input TPS -->
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                Form Input Data Sampah TPS
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('/tps/waste/store') ?>" method="POST" id="tpsWasteForm">
                                <?= csrf_field() ?>
                                
                                <div class="row">
                                    <!-- Tanggal -->
                                    <div class="col-md-6 mb-3">
                                        <label for="tanggal" class="form-label">
                                            <i class="fas fa-calendar me-1"></i>
                                            Tanggal <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control <?= session()->getFlashdata('errors')['tanggal'] ?? '' ? 'is-invalid' : '' ?>" 
                                               id="tanggal" 
                                               name="tanggal" 
                                               value="<?= old('tanggal', date('Y-m-d')) ?>" 
                                               required>
                                        <?php if (isset(session()->getFlashdata('errors')['tanggal'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['tanggal'] ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Sampah Dari Gedung -->
                                    <div class="col-md-6 mb-3">
                                        <label for="sampah_dari_gedung" class="form-label">
                                            <i class="fas fa-building me-1"></i>
                                            Sampah Dari Gedung <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select <?= session()->getFlashdata('errors')['sampah_dari_gedung'] ?? '' ? 'is-invalid' : '' ?>" 
                                                id="sampah_dari_gedung" 
                                                name="sampah_dari_gedung" 
                                                required>
                                            <option value="">-- Pilih Gedung --</option>
                                            <?php foreach ($gedung_options as $gedung): ?>
                                            <option value="<?= $gedung ?>" <?= old('sampah_dari_gedung') === $gedung ? 'selected' : '' ?>>
                                                <?= $gedung ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset(session()->getFlashdata('errors')['sampah_dari_gedung'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['sampah_dari_gedung'] ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Jumlah Berat -->
                                    <div class="col-md-4 mb-3">
                                        <label for="jumlah_berat" class="form-label">
                                            <i class="fas fa-weight me-1"></i>
                                            Jumlah Berat <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control <?= session()->getFlashdata('errors')['jumlah_berat'] ?? '' ? 'is-invalid' : '' ?>" 
                                               id="jumlah_berat" 
                                               name="jumlah_berat" 
                                               value="<?= old('jumlah_berat') ?>" 
                                               step="0.01" 
                                               min="0.01" 
                                               placeholder="Contoh: 25.5"
                                               required>
                                        <?php if (isset(session()->getFlashdata('errors')['jumlah_berat'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['jumlah_berat'] ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Satuan -->
                                    <div class="col-md-4 mb-3">
                                        <label for="satuan" class="form-label">
                                            <i class="fas fa-balance-scale me-1"></i>
                                            Satuan <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select <?= session()->getFlashdata('errors')['satuan'] ?? '' ? 'is-invalid' : '' ?>" 
                                                id="satuan" 
                                                name="satuan" 
                                                required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <?php foreach ($satuan_options as $value => $label): ?>
                                            <option value="<?= $value ?>" <?= old('satuan') === $value ? 'selected' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (isset(session()->getFlashdata('errors')['satuan'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['satuan'] ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Nilai Rupiah -->
                                    <div class="col-md-4 mb-3">
                                        <label for="nilai_rupiah" class="form-label">
                                            <i class="fas fa-money-bill me-1"></i>
                                            Nilai Rupiah <span class="text-muted">(Opsional)</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" 
                                                   class="form-control <?= session()->getFlashdata('errors')['nilai_rupiah'] ?? '' ? 'is-invalid' : '' ?>" 
                                                   id="nilai_rupiah" 
                                                   name="nilai_rupiah" 
                                                   value="<?= old('nilai_rupiah') ?>" 
                                                   step="0.01" 
                                                   min="0" 
                                                   placeholder="Kosongkan jika tidak bisa dijual">
                                        </div>
                                        <small class="form-text text-muted">
                                            Isi hanya jika sampah bisa dijual
                                        </small>
                                        <?php if (isset(session()->getFlashdata('errors')['nilai_rupiah'])): ?>
                                        <div class="invalid-feedback">
                                            <?= session()->getFlashdata('errors')['nilai_rupiah'] ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <a href="<?= base_url('/tps/waste') ?>" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                Kembali
                                            </a>
                                            <div>
                                                <button type="reset" class="btn btn-outline-warning me-2">
                                                    <i class="fas fa-undo me-2"></i>
                                                    Reset
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-save me-2"></i>
                                                    Simpan Data
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card mt-4 border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informasi Penting
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Form ini khusus untuk <strong>pengelola TPS</strong></li>
                                <li>Semua field bertanda <span class="text-danger">*</span> wajib diisi</li>
                                <li><strong>Nilai Rupiah</strong> hanya diisi jika sampah bisa dijual</li>
                                <li>Data yang disimpan akan berstatus <span class="badge bg-warning">Pending</span> menunggu approval</li>
                            </ul>
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

        // Form validation enhancement
        document.getElementById('tpsWasteForm').addEventListener('submit', function(e) {
            const jumlahBerat = document.getElementById('jumlah_berat').value;
            const nilaiRupiah = document.getElementById('nilai_rupiah').value;
            
            if (parseFloat(jumlahBerat) <= 0) {
                e.preventDefault();
                alert('Jumlah berat harus lebih dari 0');
                return false;
            }
            
            if (nilaiRupiah && parseFloat(nilaiRupiah) < 0) {
                e.preventDefault();
                alert('Nilai rupiah tidak boleh negatif');
                return false;
            }
        });
    </script>
</body>
</html>