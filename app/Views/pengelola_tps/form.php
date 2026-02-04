<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Form Input Sampah TPS' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-plus-circle"></i> Form Input Data Sampah TPS</h1>
                <p>Input data sampah yang diterima TPS dari berbagai gedung</p>
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

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Form Input Sampah TPS -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-recycle text-success"></i>
                    Form Input Data Sampah TPS
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= base_url('/pengelola-tps/waste/save') ?>" id="tpsWasteForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <!-- Nama Pelapor (Auto-fill, Read-only) -->
                            <div class="mb-3">
                                <label for="nama_pelapor" class="form-label">
                                    <i class="fas fa-user"></i> Nama Pelapor <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor" 
                                       value="<?= session()->get('user')['nama_lengkap'] ?? '' ?>" readonly 
                                       style="background-color: #e9ecef;">
                                <div class="form-text">Otomatis dari akun yang login</div>
                            </div>

                            <!-- Gedung Pelapor (Manual Input) -->
                            <div class="mb-3">
                                <label for="gedung_pelapor" class="form-label">
                                    <i class="fas fa-building"></i> Gedung Pelapor <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="gedung_pelapor" name="gedung_pelapor" 
                                       value="<?= old('gedung_pelapor') ?>" placeholder="Contoh: Gedung A, Kantin, Lab Komputer" required maxlength="255">
                                <div class="form-text">Nama gedung atau lokasi asal sampah</div>
                            </div>

                            <!-- Bukti Foto (Upload) -->
                            <div class="mb-3">
                                <label for="bukti_foto" class="form-label">
                                    <i class="fas fa-camera"></i> Bukti Foto <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" id="bukti_foto" name="bukti_foto" 
                                       accept="image/jpeg,image/jpg,image/png" required>
                                <div class="form-text">Format: JPG/PNG, Maksimal 5MB</div>
                                <!-- Preview Container -->
                                <div id="preview_container" style="display: none; margin-top: 10px;">
                                    <img id="preview_image" src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
                                </div>
                            </div>

                            <!-- Tanggal dan Waktu (Auto-fill, Read-only) -->
                            <div class="mb-3">
                                <label for="tanggal_waktu" class="form-label">
                                    <i class="fas fa-clock"></i> Tanggal dan Waktu <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" class="form-control" id="tanggal_waktu" name="tanggal_waktu" 
                                       value="<?= date('Y-m-d\TH:i') ?>" readonly 
                                       style="background-color: #e9ecef;">
                                <div class="form-text">Otomatis saat form dibuka</div>
                            </div>

                            <!-- Unit Pengirim -->
                            <div class="mb-3">
                                <label for="unit_pengirim" class="form-label">
                                    <i class="fas fa-university"></i> Unit Pengirim <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-unit" id="unit_pengirim" name="unit_pengirim" required>
                                    <option value="">-- Pilih Unit Pengirim --</option>
                                    <?php if (!empty($unitList)): ?>
                                        <?php foreach ($unitList as $unit): ?>
                                            <option value="<?= $unit['id'] ?>" <?= old('unit_pengirim') == $unit['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($unit['nama_unit']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Pilih unit/fakultas/jurusan pengirim sampah</div>
                            </div>

                            <!-- Jenis Sampah -->
                            <div class="mb-3">
                                <label for="jenis_sampah" class="form-label">
                                    <i class="fas fa-trash-alt"></i> Jenis Sampah <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-jenis" id="jenis_sampah" name="jenis_sampah" required onchange="updateKategoriSampah()">
                                    <option value="">-- Pilih Jenis Sampah --</option>
                                    <?php if (!empty($allCategories)): ?>
                                        <?php foreach ($allCategories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                    data-kategori="<?= htmlspecialchars($category['jenis_sampah']) ?>"
                                                    data-harga="<?= $category['harga_per_satuan'] ?>"
                                                    data-dapat-dijual="<?= $category['dapat_dijual'] ?>"
                                                    <?= old('jenis_sampah') == $category['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['jenis_sampah']) ?> - <?= htmlspecialchars($category['nama_jenis']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Fallback jika tidak ada data -->
                                        <option value="">Tidak ada data jenis sampah</option>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Pilih jenis sampah yang diterima</div>
                            </div>

                            <!-- Jumlah dan Satuan -->
                            <div class="row">
                                <div class="col-8">
                                    <div class="mb-3">
                                        <label for="jumlah" class="form-label">
                                            <i class="fas fa-weight"></i> Jumlah <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" 
                                               value="<?= old('jumlah') ?>" step="0.01" min="0.01" placeholder="0.00" required
                                               onchange="calculateNilaiRupiah()">
                                        <div class="form-text">Berat sampah yang diterima</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-3">
                                        <label for="satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                                        <select class="form-select" id="satuan" name="satuan" required onchange="calculateNilaiRupiah()">
                                            <option value="kg" <?= old('satuan') === 'kg' ? 'selected' : '' ?>>kg</option>
                                            <option value="ton" <?= old('satuan') === 'ton' ? 'selected' : '' ?>>ton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <!-- Kategori Sampah -->
                            <div class="mb-3">
                                <label for="kategori_sampah" class="form-label">
                                    <i class="fas fa-tags"></i> Kategori Sampah <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kategori_sampah" name="kategori_sampah" required onchange="toggleNilaiRupiah()">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="bisa_dijual" <?= old('kategori_sampah') === 'bisa_dijual' ? 'selected' : '' ?>>Bisa Dijual</option>
                                    <option value="tidak_bisa_dijual" <?= old('kategori_sampah') === 'tidak_bisa_dijual' ? 'selected' : '' ?>>Tidak Bisa Dijual</option>
                                </select>
                                <div class="form-text">Apakah sampah ini memiliki nilai ekonomis?</div>
                            </div>

                            <!-- Nilai Rupiah Preview (Read-only) -->
                            <div class="mb-3" id="nilai_rupiah_preview" style="display: none;">
                                <label class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Nilai Rupiah (Otomatis)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="preview_nilai" readonly 
                                           placeholder="0" style="background-color: #f8f9fa;">
                                </div>
                                <div class="form-text text-success">
                                    <i class="fas fa-info-circle"></i> 
                                    Nilai dihitung otomatis berdasarkan harga pasar
                                </div>
                            </div>

                            <!-- Harga Per Kg Info -->
                            <div class="mb-3" id="harga_info" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Harga Pasar Saat Ini:</strong>
                                    <div id="harga_detail"></div>
                                </div>
                            </div>

                            <!-- Catatan (Opsional) -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label">
                                    <i class="fas fa-sticky-note"></i> Catatan (Opsional)
                                </label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                          placeholder="Catatan tambahan tentang sampah ini..."><?= old('catatan') ?></textarea>
                                <div class="form-text">Informasi tambahan jika diperlukan</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="<?= base_url('/pengelola-tps/dashboard') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" name="action" value="draft" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-save"></i> Simpan sebagai Draft
                                    </button>
                                    <button type="submit" name="action" value="kirim" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> Kirim ke Admin
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Tips Input Data</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Pastikan tanggal sesuai dengan waktu penerimaan sampah</li>
                            <li>Tulis nama gedung dengan jelas dan konsisten</li>
                            <li>Timbang sampah dengan akurat sebelum input</li>
                            <li>Pilih kategori "Bisa Dijual" hanya untuk sampah yang layak jual</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Data yang sudah dikirim tidak dapat diedit</li>
                            <li>Pastikan data sudah benar sebelum mengirim</li>
                            <li>Draft dapat diedit kapan saja</li>
                            <li>Admin akan review data yang dikirim</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // Initialize Select2
    $(document).ready(function() {
        // Initialize Select2 for Unit dropdown
        $('.select2-unit').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Unit Pengirim --',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for Jenis Sampah dropdown
        $('.select2-jenis').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Jenis Sampah --',
            allowClear: true,
            width: '100%'
        });

        // Trigger update when jenis sampah changes
        $('.select2-jenis').on('change', function() {
            updateKategoriSampah();
        });
        
        // Initialize image preview
        document.getElementById('bukti_foto').addEventListener('change', handleImagePreview);
    });

    function handleImagePreview(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (5MB = 5242880 bytes)
            if (file.size > 5242880) {
                alert('Ukuran file maksimal 5MB');
                e.target.value = '';
                document.getElementById('preview_container').style.display = 'none';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                alert('File harus berformat JPG atau PNG');
                e.target.value = '';
                document.getElementById('preview_container').style.display = 'none';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_image').src = e.target.result;
                document.getElementById('preview_container').style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('preview_container').style.display = 'none';
        }
    }

    function updateKategoriSampah() {
        const jenisSampahSelect = document.getElementById('jenis_sampah');
        const selectedOption = jenisSampahSelect.options[jenisSampahSelect.selectedIndex];
        const kategoriSelect = document.getElementById('kategori_sampah');
        
        if (selectedOption && selectedOption.value) {
            const dapatDijual = selectedOption.getAttribute('data-dapat-dijual');
            const harga = selectedOption.getAttribute('data-harga');
            const kategori = selectedOption.getAttribute('data-kategori');
            
            if (dapatDijual === '1') {
                // Bisa dijual - enable both options
                kategoriSelect.innerHTML = `
                    <option value="">-- Pilih Kategori --</option>
                    <option value="bisa_dijual" selected>Bisa Dijual</option>
                    <option value="tidak_bisa_dijual">Tidak Bisa Dijual</option>
                `;
                
                // Show harga info
                showHargaInfo(kategori, harga);
            } else {
                // Tidak bisa dijual - only one option
                kategoriSelect.innerHTML = `
                    <option value="tidak_bisa_dijual" selected>Tidak Bisa Dijual</option>
                `;
                
                hideHargaInfo();
            }
        } else {
            kategoriSelect.innerHTML = `
                <option value="">-- Pilih Kategori --</option>
                <option value="bisa_dijual">Bisa Dijual</option>
                <option value="tidak_bisa_dijual">Tidak Bisa Dijual</option>
            `;
            hideHargaInfo();
        }
        
        toggleNilaiRupiah();
    }

    function showHargaInfo(jenisSampah, harga) {
        const hargaInfo = document.getElementById('harga_info');
        const hargaDetail = document.getElementById('harga_detail');
        
        const hargaFormatted = new Intl.NumberFormat('id-ID').format(harga);
        hargaDetail.innerHTML = `${jenisSampah}: Rp ${hargaFormatted}/kg`;
        hargaInfo.style.display = 'block';
    }

    function hideHargaInfo() {
        document.getElementById('harga_info').style.display = 'none';
    }

    function toggleNilaiRupiah() {
        const jenisSampahSelect = document.getElementById('jenis_sampah');
        const selectedOption = jenisSampahSelect.options[jenisSampahSelect.selectedIndex];
        const kategori = document.getElementById('kategori_sampah').value;
        const nilaiPreview = document.getElementById('nilai_rupiah_preview');
        
        if (selectedOption && selectedOption.value && kategori === 'bisa_dijual') {
            const dapatDijual = selectedOption.getAttribute('data-dapat-dijual');
            
            if (dapatDijual === '1') {
                nilaiPreview.style.display = 'block';
                calculateNilaiRupiah();
            } else {
                nilaiPreview.style.display = 'none';
                document.getElementById('preview_nilai').value = '';
            }
        } else {
            nilaiPreview.style.display = 'none';
            document.getElementById('preview_nilai').value = '';
        }
    }

    function calculateNilaiRupiah() {
        const jenisSampahSelect = document.getElementById('jenis_sampah');
        const selectedOption = jenisSampahSelect.options[jenisSampahSelect.selectedIndex];
        const kategori = document.getElementById('kategori_sampah').value;
        const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
        const satuan = document.getElementById('satuan').value;
        
        if (selectedOption && selectedOption.value && kategori === 'bisa_dijual' && jumlah > 0) {
            const dapatDijual = selectedOption.getAttribute('data-dapat-dijual');
            
            if (dapatDijual === '1') {
                let jumlahKg = jumlah;
                
                // Convert to kg if needed
                if (satuan === 'ton') {
                    jumlahKg = jumlah * 1000;
                }
                
                const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;
                const nilaiTotal = jumlahKg * harga;
                
                document.getElementById('preview_nilai').value = new Intl.NumberFormat('id-ID').format(nilaiTotal);
            } else {
                document.getElementById('preview_nilai').value = '';
            }
        } else {
            document.getElementById('preview_nilai').value = '';
        }
    }

    // Form validation
    document.getElementById('tpsWasteForm').addEventListener('submit', function(e) {
        const namaPelapor = document.getElementById('nama_pelapor').value;
        const gedungPelapor = document.getElementById('gedung_pelapor').value;
        const buktiFoto = document.getElementById('bukti_foto').files[0];
        const unitPengirim = document.getElementById('unit_pengirim').value;
        const jenisSampah = document.getElementById('jenis_sampah').value;
        const jumlah = parseFloat(document.getElementById('jumlah').value);
        
        if (!namaPelapor) {
            e.preventDefault();
            alert('Nama pelapor harus diisi');
            return false;
        }
        
        if (!gedungPelapor || gedungPelapor.trim() === '') {
            e.preventDefault();
            alert('Gedung pelapor harus diisi');
            document.getElementById('gedung_pelapor').focus();
            return false;
        }
        
        if (!buktiFoto) {
            e.preventDefault();
            alert('Bukti foto harus diupload');
            document.getElementById('bukti_foto').focus();
            return false;
        }
        
        if (!unitPengirim) {
            e.preventDefault();
            alert('Unit pengirim harus dipilih');
            return false;
        }
        
        if (!jenisSampah) {
            e.preventDefault();
            alert('Jenis sampah harus dipilih');
            return false;
        }
        
        if (jumlah <= 0) {
            e.preventDefault();
            alert('Jumlah sampah harus lebih dari 0');
            return false;
        }
        
        // Confirm before submit
        const action = e.submitter.value;
        const message = action === 'kirim' ? 
            'Apakah Anda yakin ingin mengirim data ini ke Admin? Data yang sudah dikirim tidak dapat diedit.' :
            'Apakah Anda yakin ingin menyimpan data ini sebagai draft?';
            
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        updateKategoriSampah();
    });
    </script>
</body>
</html>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.page-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
}

.page-header p {
    margin: 10px 0 0 0;
    opacity: 0.9;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    padding: 15px 20px;
}

.card-body {
    padding: 25px;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn {
    border-radius: 8px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.alert {
    border: none;
    border-radius: 8px;
    padding: 15px 20px;
}

.text-danger {
    color: #dc3545 !important;
}

.form-text {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 5px;
}

.input-group-text {
    background-color: #e9ecef;
    border: 2px solid #e9ecef;
    border-right: none;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
}
</style>