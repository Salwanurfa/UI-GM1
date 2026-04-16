<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building text-primary"></i>
                <?= $title ?>
            </h1>
            <p class="text-muted mb-0">Data luas kampus dan area parkir untuk indikator UIGM TR 5 & 6</p>
        </div>
        <a href="<?= base_url('/admin-pusat/infrastructure') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit"></i>
                        Form Data Infrastruktur
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/admin-pusat/infrastructure/save-infrastructure') ?>" method="POST">
                        <?= csrf_field() ?>
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="tahun_akademik" class="form-label">
                                        <i class="fas fa-calendar"></i> Tahun Akademik
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="tahun_akademik" 
                                           name="tahun_akademik" 
                                           placeholder="Contoh: 2025/2026" 
                                           value="<?= old('tahun_akademik', $edit_data['tahun_akademik'] ?? '') ?>" 
                                           required>
                                    <small class="text-muted">Format: YYYY/YYYY</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="luas_total_kampus" class="form-label">
                                        <i class="fas fa-map"></i> Luas Total Kampus (m²)
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="luas_total_kampus" 
                                           name="luas_total_kampus" 
                                           placeholder="Masukkan luas total kampus" 
                                           value="<?= old('luas_total_kampus', $edit_data['luas_total_kampus'] ?? '') ?>" 
                                           step="0.01"
                                           min="0"
                                           required>
                                    <small class="text-muted">Luas keseluruhan area kampus dalam meter persegi</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="luas_area_parkir_total" class="form-label">
                                        <i class="fas fa-parking"></i> Total Luas Area Parkir (m²)
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="luas_area_parkir_total" 
                                           name="luas_area_parkir_total" 
                                           placeholder="Masukkan total luas area parkir" 
                                           value="<?= old('luas_area_parkir_total', $edit_data['luas_area_parkir_total'] ?? '') ?>" 
                                           step="0.01"
                                           min="0"
                                           required>
                                    <small class="text-muted">Total luas semua area parkir di kampus</small>
                                </div>
                            </div>
                        </div>

                        <!-- Parking Breakdown (Optional) -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle text-info"></i>
                                    Detail Area Parkir (Opsional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="luas_parkir_terbuka" class="form-label">
                                                <i class="fas fa-sun"></i> Luas Parkir Terbuka (m²)
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="luas_parkir_terbuka" 
                                                   name="luas_parkir_terbuka" 
                                                   placeholder="Luas area parkir terbuka" 
                                                   value="<?= old('luas_parkir_terbuka', $edit_data['luas_parkir_terbuka'] ?? '') ?>" 
                                                   step="0.01"
                                                   min="0">
                                            <small class="text-muted">Area parkir tanpa atap/kanopi</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="luas_parkir_berkanopi" class="form-label">
                                                <i class="fas fa-home"></i> Luas Parkir Berkanopi/Gedung (m²)
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="luas_parkir_berkanopi" 
                                                   name="luas_parkir_berkanopi" 
                                                   placeholder="Luas area parkir berkanopi" 
                                                   value="<?= old('luas_parkir_berkanopi', $edit_data['luas_parkir_berkanopi'] ?? '') ?>" 
                                                   step="0.01"
                                                   min="0">
                                            <small class="text-muted">Area parkir dengan atap/kanopi atau dalam gedung</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-lightbulb"></i>
                                    <strong>Tips:</strong> Jika tidak diisi, sistem akan menganggap semua area parkir sebagai parkir terbuka.
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="keterangan" class="form-label">
                                <i class="fas fa-sticky-note"></i> Keterangan (Opsional)
                            </label>
                            <textarea class="form-control" 
                                      id="keterangan" 
                                      name="keterangan" 
                                      rows="3" 
                                      placeholder="Catatan tambahan tentang data infrastruktur..."><?= old('keterangan', $edit_data['keterangan'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $edit_data ? 'Perbarui Data' : 'Simpan Data' ?>
                            </button>
                            
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                            
                            <a href="<?= base_url('/admin-pusat/infrastructure') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-calculate parking breakdown
document.addEventListener('DOMContentLoaded', function() {
    const totalParkir = document.getElementById('luas_area_parkir_total');
    const parkirTerbuka = document.getElementById('luas_parkir_terbuka');
    const parkirBerkanopi = document.getElementById('luas_parkir_berkanopi');
    
    function updateBreakdown() {
        const total = parseFloat(totalParkir.value) || 0;
        const terbuka = parseFloat(parkirTerbuka.value) || 0;
        const berkanopi = parseFloat(parkirBerkanopi.value) || 0;
        
        if (total > 0 && (terbuka + berkanopi) > total) {
            alert('Total area parkir terbuka dan berkanopi tidak boleh melebihi total area parkir!');
        }
    }
    
    parkirTerbuka.addEventListener('input', updateBreakdown);
    parkirBerkanopi.addEventListener('input', updateBreakdown);
});
</script>
<?= $this->endSection() ?>