<!-- Area Parkir -->
<div class="card">
    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-square-parking"></i> Data Area Parkir</h3>
        <button type="button" class="btn btn-light btn-sm" onclick="openParkirModal()">
            <i class="fas fa-edit"></i> Update Data Parkir
        </button>
    </div>
    <div class="card-body">
        <!-- Rasio Display -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="ratio-display">
                    <p><i class="fas fa-percentage"></i> Rasio Area Parkir</p>
                    <h2><?= $rasio_parkir ?>%</h2>
                    <p>dari Total Luas Kampus</p>
                    <hr style="border-color: rgba(255,255,255,0.3);">
                    <small class="fst-italic" style="opacity: 0.9;">
                        <i class="fas fa-calculator"></i> Formula: (<?= number_format($parkir['luas_parkir'] ?? 0) ?> m² / <?= number_format($parkir['luas_kampus'] ?? 1) ?> m²) × 100%
                    </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> Standar UI GreenMetric</h5>
                    <ul class="mb-0">
                        <li>Rasio parkir yang <strong>lebih rendah</strong> mendapat skor lebih tinggi</li>
                        <li>Target ideal: <strong>&lt; 5%</strong> dari total luas kampus</li>
                        <li>Kampus dengan rasio parkir rendah menunjukkan komitmen terhadap transportasi berkelanjutan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jalur Pedestrian -->
<div class="card">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h3 class="mb-0"><i class="fas fa-walking"></i> Pendataan Jalur Pedestrian</h3>
        <button type="button" class="btn btn-light btn-sm" onclick="openPedestrianModal()">
            <i class="fas fa-plus-circle"></i> Tambah Jalur Baru
        </button>
    </div>
    <div class="card-body">
        <!-- Tabel Jalur Pedestrian -->
        <div class="table-responsive mt-4">
            <table class="table table-hover table-sm" id="pedestrianTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama/Lokasi Jalur</th>
                        <th>Panjang (m)</th>
                        <th>Lebar (m)</th>
                        <th>Luas (m²)</th>
                        <th>Kondisi</th>
                        <th>Foto Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pedestrian_data)): ?>
                        <?php $no = 1; $total_panjang = 0; $total_luas = 0; ?>
                        <?php foreach ($pedestrian_data as $ped): ?>
                            <?php 
                            $luas = $ped['panjang_jalur'] * $ped['lebar_jalur'];
                            $total_panjang += $ped['panjang_jalur'];
                            $total_luas += $luas;
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= esc($ped['nama_jalur']) ?></strong></td>
                                <td><?= number_format($ped['panjang_jalur'], 2) ?> m</td>
                                <td><?= number_format($ped['lebar_jalur'], 2) ?> m</td>
                                <td><?= number_format($luas, 2) ?> m²</td>
                                <td>
                                    <?php if ($ped['kondisi'] == 'Baik'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Baik
                                        </span>
                                    <?php elseif ($ped['kondisi'] == 'Rusak Ringan'): ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-exclamation"></i> Rusak Ringan
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Rusak Berat
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($ped['foto_kondisi'])): ?>
                                        <img src="<?= base_url('uploads/pedestrian/' . $ped['foto_kondisi']) ?>" 
                                             class="img-thumbnail pedestrian-thumbnail" 
                                             style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; cursor: pointer;"
                                             onclick="showPhotoModal('<?= base_url('uploads/pedestrian/' . $ped['foto_kondisi']) ?>', '<?= esc($ped['nama_jalur']) ?>')"
                                             title="Klik untuk melihat foto">
                                    <?php else: ?>
                                        <span class="text-muted">
                                            <i class="fas fa-image"></i> Belum ada
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <?php if (!empty($ped['foto_kondisi'])): ?>
                                            <button type="button" class="btn btn-info" 
                                                    onclick="showPhotoModal('<?= base_url('uploads/pedestrian/' . $ped['foto_kondisi']) ?>', '<?= esc($ped['nama_jalur']) ?>')"
                                                    title="Preview Foto">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-warning" 
                                                onclick="editPedestrian(<?= htmlspecialchars(json_encode($ped), ENT_QUOTES, 'UTF-8') ?>)"
                                                title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" 
                                                onclick="deletePedestrian(<?= $ped['id'] ?>, '<?= esc($ped['nama_jalur']) ?>')"
                                                title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-info">
                            <td colspan="2"><strong>TOTAL</strong></td>
                            <td><strong><?= number_format($total_panjang, 2) ?> m</strong></td>
                            <td colspan="1"></td>
                            <td><strong><?= number_format($total_luas, 2) ?> m²</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada data jalur pedestrian</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if (!empty($pedestrian_data)): ?>
            <div class="mt-2 ms-2">
                <small class="text-muted fst-italic">
                    <i class="fas fa-calculator"></i> Formula Luas: Panjang (m) × Lebar (m). Total Luas adalah akumulasi dari seluruh data jalur.
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Preview Foto -->
<div class="modal fade" id="photoPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-image"></i> Preview Foto - <span id="previewJalurName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <img id="previewImage" src="" class="img-fluid rounded shadow">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- CSS for Thumbnail Hover Effect -->
<style>
.pedestrian-thumbnail {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.pedestrian-thumbnail:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    z-index: 10;
}
</style>

<!-- Info Box -->
<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle"></i>
    <strong>Catatan UI GreenMetric:</strong>
    <ul class="mb-0 mt-2">
        <li><strong>Area Parkir:</strong> Rasio area parkir terhadap total luas kampus (semakin rendah semakin baik)</li>
        <li><strong>Jalur Pedestrian:</strong> Ketersediaan dan kualitas jalur pedestrian (semakin panjang dan baik semakin tinggi skornya)</li>
        <li>Data harus diperbarui setiap tahun untuk akurasi penilaian</li>
    </ul>
</div>

<!-- Modal Input Data Parkir -->
<div class="modal fade" id="modalParkir" tabindex="-1" aria-labelledby="modalParkirLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                <h5 class="modal-title" id="modalParkirLabel">
                    <i class="fas fa-square-parking"></i> Update Data Area Parkir
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formParkir">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_luas_parkir" class="form-label fw-bold">
                                <i class="fas fa-ruler-combined"></i> Luas Area Parkir (m²) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" class="form-control" id="modal_luas_parkir" 
                                   name="luas_parkir" value="<?= $parkir['luas_parkir'] ?? '' ?>" 
                                   placeholder="Contoh: 5000" required>
                            <small class="text-muted">Total luas area parkir di kampus</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="modal_luas_kampus" class="form-label fw-bold">
                                <i class="fas fa-map"></i> Total Luas Area Kampus (m²) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" class="form-control" id="modal_luas_kampus" 
                                   name="luas_kampus" value="<?= $parkir['luas_kampus'] ?? '' ?>" 
                                   placeholder="Contoh: 100000" required>
                            <small class="text-muted">Total luas keseluruhan kampus</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label for="modal_tahun_parkir" class="form-label fw-bold">
                                <i class="fas fa-calendar"></i> Tahun Data <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="modal_tahun_parkir" name="tahun" required>
                                <?php 
                                $currentYear = date('Y');
                                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= (isset($parkir['tahun']) && $parkir['tahun'] == $i) || $i == $currentYear ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Tahun data area parkir</small>
                        </div>
                    </div>
                    
                    <!-- Preview Rasio -->
                    <div class="alert alert-warning mt-3">
                        <h6 class="mb-2"><i class="fas fa-calculator"></i> Preview Rasio Area Parkir:</h6>
                        <h3 class="mb-0" id="preview_rasio_parkir">0%</h3>
                        <small class="text-muted">Target ideal: &lt; 5% dari total luas kampus</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-warning" onclick="simpanParkir()" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Jalur Pedestrian -->
<div class="modal fade" id="modalPedestrian" tabindex="-1" aria-labelledby="modalPedestrianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                <h5 class="modal-title" id="modalPedestrianLabel">
                    <i class="fas fa-walking"></i> <span id="pedestrianModalTitle">Tambah Jalur Pedestrian</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPedestrian" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" id="modal_pedestrian_id" name="id" value="">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_nama_jalur" class="form-label fw-bold">
                                <i class="fas fa-road"></i> Nama/Lokasi Jalur <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="modal_nama_jalur" 
                                   name="nama_jalur" placeholder="Contoh: Jalur Gedung A-B" required>
                            <small class="text-muted">Nama atau lokasi jalur pedestrian</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="modal_panjang_jalur" class="form-label fw-bold">
                                <i class="fas fa-ruler-horizontal"></i> Panjang (m) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" class="form-control" id="modal_panjang_jalur" 
                                   name="panjang_jalur" placeholder="150" required>
                            <small class="text-muted">Panjang jalur dalam meter</small>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="modal_lebar_jalur" class="form-label fw-bold">
                                <i class="fas fa-arrows-alt-h"></i> Lebar (m) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" class="form-control" id="modal_lebar_jalur" 
                                   name="lebar_jalur" placeholder="2.5" required>
                            <small class="text-muted">Lebar jalur dalam meter</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label for="modal_kondisi" class="form-label fw-bold">
                                <i class="fas fa-check-circle"></i> Kondisi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="modal_kondisi" name="kondisi" required>
                                <option value="">-- Pilih Kondisi --</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                            <small class="text-muted">Kondisi jalur saat ini</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="modal_foto_jalur" class="form-label fw-bold">
                                <i class="fas fa-camera"></i> Foto Kondisi (Opsional)
                            </label>
                            <input type="file" class="form-control" id="modal_foto_jalur" 
                                   name="foto_jalur" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">JPG/PNG, Max 5MB</small>
                        </div>
                    </div>
                    
                    <!-- Preview Luas -->
                    <div class="alert alert-success mt-3">
                        <h6 class="mb-2"><i class="fas fa-calculator"></i> Preview Luas Jalur:</h6>
                        <h3 class="mb-0" id="preview_luas_jalur">0 m²</h3>
                        <small class="text-muted">Panjang × Lebar</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-success" onclick="simpanPedestrian()" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none;">
                    <i class="fas fa-save"></i> <span id="btnSavePedestrianText">Simpan Data</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const baseUrl = '<?= base_url() ?>';
    
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#pedestrianTable')) {
            $('#pedestrianTable').DataTable().destroy();
        }
        $('#pedestrianTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10
        });
        
        // Auto-update preview for Parkir
        $('#modal_luas_parkir, #modal_luas_kampus').on('input', function() {
            updatePreviewParkir();
        });
        
        // Auto-update preview for Pedestrian
        $('#modal_panjang_jalur, #modal_lebar_jalur').on('input', function() {
            updatePreviewPedestrian();
        });
        
        // Preview image before upload in modal
        $('#modal_foto_jalur').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB',
                        confirmButtonColor: '#d33'
                    });
                    $(this).val('');
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan',
                        confirmButtonColor: '#d33'
                    });
                    $(this).val('');
                    return;
                }
            }
        });
    });
    
    // Open Parkir Modal
    function openParkirModal() {
        var modal = new bootstrap.Modal(document.getElementById('modalParkir'));
        modal.show();
        updatePreviewParkir();
    }
    
    // Update Preview Rasio Parkir
    function updatePreviewParkir() {
        const luasParkir = parseFloat($('#modal_luas_parkir').val()) || 0;
        const luasKampus = parseFloat($('#modal_luas_kampus').val()) || 1;
        const rasio = (luasParkir / luasKampus) * 100;
        $('#preview_rasio_parkir').text(rasio.toFixed(2) + '%');
    }
    
    // Simpan Data Parkir
    function simpanParkir() {
        const form = document.getElementById('formParkir');
        
        // Validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan Data...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit via AJAX
        fetch('<?= base_url('/admin-pusat/transportation/simpan-parkir') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalParkir'));
            modal.hide();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data area parkir berhasil disimpan',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page to update statistics
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan data',
                confirmButtonText: 'OK'
            });
        });
    }
    
    // Open Pedestrian Modal
    function openPedestrianModal() {
        // Reset form
        document.getElementById('formPedestrian').reset();
        $('#modal_pedestrian_id').val('');
        $('#pedestrianModalTitle').text('Tambah Jalur Pedestrian');
        $('#btnSavePedestrianText').text('Simpan Data');
        
        var modal = new bootstrap.Modal(document.getElementById('modalPedestrian'));
        modal.show();
        updatePreviewPedestrian();
    }
    
    // Update Preview Luas Pedestrian
    function updatePreviewPedestrian() {
        const panjang = parseFloat($('#modal_panjang_jalur').val()) || 0;
        const lebar = parseFloat($('#modal_lebar_jalur').val()) || 0;
        const luas = panjang * lebar;
        $('#preview_luas_jalur').text(luas.toFixed(2) + ' m²');
    }
    
    // Simpan Data Pedestrian
    function simpanPedestrian() {
        const form = document.getElementById('formPedestrian');
        
        // Validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan Data...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Get form data
        const formData = new FormData(form);
        
        // Submit via AJAX
        fetch('<?= base_url('/admin-pusat/transportation/simpan-pedestrian') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalPedestrian'));
            modal.hide();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data jalur pedestrian berhasil disimpan',
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload page to update table
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menyimpan data',
                confirmButtonText: 'OK'
            });
        });
    }

    function showPhotoModal(photoUrl, namaJalur) {
        document.getElementById('previewImage').src = photoUrl;
        document.getElementById('previewJalurName').textContent = namaJalur;
        
        const modal = new bootstrap.Modal(document.getElementById('photoPreviewModal'));
        modal.show();
    }

    function editPedestrian(data) {
        // Fill form with data
        $('#modal_pedestrian_id').val(data.id);
        $('#modal_nama_jalur').val(data.nama_jalur);
        $('#modal_panjang_jalur').val(data.panjang_jalur);
        $('#modal_lebar_jalur').val(data.lebar_jalur);
        $('#modal_kondisi').val(data.kondisi);
        
        // Update modal title
        $('#pedestrianModalTitle').text('Edit Jalur Pedestrian');
        $('#btnSavePedestrianText').text('Update Data');
        
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('modalPedestrian'));
        modal.show();
        updatePreviewPedestrian();
    }

    function deletePedestrian(id, nama) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Yakin ingin menghapus jalur:<br><strong>"${nama}"</strong>?<br><br><small class="text-danger">Data dan foto (jika ada) akan dihapus permanen!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
            cancelButtonText: '<i class="fas fa-times"></i> Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = baseUrl + '/admin-pusat/transportation/hapus-pedestrian/' + id;
            }
        });
    }
</script>
