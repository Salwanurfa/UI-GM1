<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-users text-success"></i>
                <?= $title ?>
            </h1>
            <p class="text-muted mb-0">Data populasi kampus untuk indikator UIGM TR 1 & 4</p>
        </div>
        <a href="<?= base_url('/admin-pusat/infrastructure') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-edit"></i>
                        Form Data Populasi
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

                    <form action="<?= base_url('/admin-pusat/infrastructure/save-population') ?>" method="POST">
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

                        <!-- Population Data -->
                        <div class="card bg-light mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-users text-success"></i>
                                    Data Populasi Kampus
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="jumlah_dosen" class="form-label">
                                                <i class="fas fa-chalkboard-teacher"></i> Jumlah Dosen
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="jumlah_dosen" 
                                                   name="jumlah_dosen" 
                                                   placeholder="Jumlah dosen aktif" 
                                                   value="<?= old('jumlah_dosen', $edit_data['jumlah_dosen'] ?? '') ?>" 
                                                   min="0"
                                                   required>
                                            <small class="text-muted">Dosen tetap dan tidak tetap</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="jumlah_mahasiswa" class="form-label">
                                                <i class="fas fa-user-graduate"></i> Jumlah Mahasiswa
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="jumlah_mahasiswa" 
                                                   name="jumlah_mahasiswa" 
                                                   placeholder="Jumlah mahasiswa aktif" 
                                                   value="<?= old('jumlah_mahasiswa', $edit_data['jumlah_mahasiswa'] ?? '') ?>" 
                                                   min="0"
                                                   required>
                                            <small class="text-muted">Mahasiswa aktif semua program</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="jumlah_tenaga_kependidikan" class="form-label">
                                                <i class="fas fa-user-tie"></i> Tenaga Kependidikan
                                            </label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="jumlah_tenaga_kependidikan" 
                                                   name="jumlah_tenaga_kependidikan" 
                                                   placeholder="Jumlah tendik/staff" 
                                                   value="<?= old('jumlah_tenaga_kependidikan', $edit_data['jumlah_tenaga_kependidikan'] ?? '') ?>" 
                                                   min="0"
                                                   required>
                                            <small class="text-muted">Staff administrasi dan teknis</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Total Population Display -->
                                <div class="alert alert-info">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <i class="fas fa-calculator"></i>
                                            <strong>Total Populasi akan dihitung otomatis:</strong>
                                            <br>
                                            <small>Dosen + Mahasiswa + Tenaga Kependidikan</small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="h4 mb-0" id="totalPopulasi">0</div>
                                            <small class="text-muted">Total Populasi</small>
                                        </div>
                                    </div>
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
                                      placeholder="Catatan tambahan tentang data populasi..."><?= old('keterangan', $edit_data['keterangan'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-success">
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
// Auto-calculate total population
document.addEventListener('DOMContentLoaded', function() {
    const dosenInput = document.getElementById('jumlah_dosen');
    const mahasiswaInput = document.getElementById('jumlah_mahasiswa');
    const tendikInput = document.getElementById('jumlah_tenaga_kependidikan');
    const totalDisplay = document.getElementById('totalPopulasi');
    
    function calculateTotal() {
        const dosen = parseInt(dosenInput.value) || 0;
        const mahasiswa = parseInt(mahasiswaInput.value) || 0;
        const tendik = parseInt(tendikInput.value) || 0;
        
        const total = dosen + mahasiswa + tendik;
        totalDisplay.textContent = total.toLocaleString();
    }
    
    // Calculate on input change
    dosenInput.addEventListener('input', calculateTotal);
    mahasiswaInput.addEventListener('input', calculateTotal);
    tendikInput.addEventListener('input', calculateTotal);
    
    // Calculate on page load
    calculateTotal();
});
</script>
<?= $this->endSection() ?>