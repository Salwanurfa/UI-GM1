<!-- Action Button -->
<div class="mb-3">
    <button type="button" class="btn btn-primary btn-lg" onclick="openUploadModal()">
        <i class="fas fa-cloud-upload-alt"></i> Upload Dokumen Baru
    </button>
</div>

<!-- Dokumen Program Pengurangan Area Parkir -->
<div class="card">
    <div class="card-header bg-warning text-white">
        <h3><i class="fas fa-folder"></i> Program Pengurangan Area Parkir</h3>
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
                    $tr6_docs = array_filter($documents ?? [], function($doc) {
                        return strpos($doc['kategori'], 'Pengurangan Area Parkir') !== false;
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
                                        <i class="fas fa-download"></i>
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
                                <p class="text-muted">Belum ada dokumen Program Pengurangan Area Parkir</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dokumen Inisiatif Pengurangan Kendaraan Pribadi -->
<div class="card">
    <div class="card-header bg-success text-white">
        <h3><i class="fas fa-folder"></i> Inisiatif Pengurangan Kendaraan Pribadi</h3>
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
                    $tr7_docs = array_filter($documents ?? [], function($doc) {
                        return strpos($doc['kategori'], 'Pengurangan Kendaraan Pribadi') !== false;
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
                                        <i class="fas fa-download"></i>
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
                                <p class="text-muted">Belum ada dokumen Inisiatif Pengurangan Kendaraan Pribadi</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info Box -->
<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle"></i>
    <strong>Catatan UI GreenMetric:</strong>
    <ul class="mb-0 mt-2">
        <li><strong>Program Pengurangan Area Parkir:</strong> Dokumen program/kebijakan pengurangan area parkir (SK, Proposal, Laporan Implementasi)</li>
        <li><strong>Inisiatif Pengurangan Kendaraan Pribadi:</strong> Dokumen inisiatif pengurangan kendaraan pribadi (Program Carpool, Bike Sharing, Kampanye, dll)</li>
        <li>Format file yang diterima: PDF, JPG, PNG (Max 5MB)</li>
        <li>Dokumen harus jelas, terbaca, dan relevan dengan kategori</li>
    </ul>
</div>

<!-- Modal Upload Dokumen -->
<div class="modal fade" id="modalUpload" tabindex="-1" aria-labelledby="modalUploadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="modalUploadLabel">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Dokumen Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUpload" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_kategori" class="form-label fw-bold">
                                <i class="fas fa-tag"></i> Kategori Dokumen <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="modal_kategori" name="kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Program Pengurangan Area Parkir">Program Pengurangan Area Parkir</option>
                                <option value="Inisiatif Pengurangan Kendaraan Pribadi">Inisiatif Pengurangan Kendaraan Pribadi</option>
                            </select>
                            <small class="text-muted">Pilih kategori sesuai jenis dokumen</small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="modal_tahun_dokumen" class="form-label fw-bold">
                                <i class="fas fa-calendar"></i> Tahun Dokumen <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="modal_tahun_dokumen" name="tahun" required>
                                <?php 
                                $currentYear = date('Y');
                                for ($i = $currentYear - 5; $i <= $currentYear + 1; $i++): 
                                ?>
                                    <option value="<?= $i ?>" <?= $i == $currentYear ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <small class="text-muted">Tahun pembuatan dokumen</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label for="modal_nama_dokumen" class="form-label fw-bold">
                                <i class="fas fa-file-alt"></i> Nama Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="modal_nama_dokumen" 
                                   name="nama_dokumen" placeholder="Contoh: Surat Keputusan Rektor tentang Pengurangan Area Parkir" required>
                            <small class="text-muted">Nama lengkap dokumen</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label for="modal_deskripsi" class="form-label fw-bold">
                                <i class="fas fa-align-left"></i> Deskripsi/Keterangan
                            </label>
                            <textarea class="form-control" id="modal_deskripsi" name="deskripsi" 
                                      rows="3" placeholder="Jelaskan isi dokumen secara singkat..."></textarea>
                            <small class="text-muted">Deskripsi singkat tentang isi dokumen</small>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <label for="modal_file_dokumen" class="form-label fw-bold">
                                <i class="fas fa-paperclip"></i> File Dokumen <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="modal_file_dokumen" 
                                   name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: PDF, JPG, PNG | Max: 5MB</small>
                        </div>
                    </div>
                    
                    <!-- Preview File Info -->
                    <div class="alert alert-light mt-3" id="filePreview" style="display: none;">
                        <h6 class="mb-2"><i class="fas fa-file"></i> File yang Dipilih:</h6>
                        <p class="mb-0" id="fileInfo"></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="uploadDokumen()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="fas fa-upload"></i> Upload Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#tr6Table')) {
            $('#tr6Table').DataTable().destroy();
        }
        if ($.fn.DataTable.isDataTable('#tr7Table')) {
            $('#tr7Table').DataTable().destroy();
        }
        
        $('#tr6Table, #tr7Table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
            pageLength: 10,
            order: [[5, 'desc']]
        });
        
        // Preview file info when selected
        $('#modal_file_dokumen').on('change', function(e) {
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
                    $('#filePreview').hide();
                    return;
                }
                
                // Validate file type
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file PDF, JPG, JPEG, dan PNG yang diperbolehkan',
                        confirmButtonColor: '#d33'
                    });
                    $(this).val('');
                    $('#filePreview').hide();
                    return;
                }
                
                // Show file info
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileType = file.type.split('/')[1].toUpperCase();
                $('#fileInfo').html(`<strong>${file.name}</strong><br>Ukuran: ${fileSize} MB | Tipe: ${fileType}`);
                $('#filePreview').show();
            } else {
                $('#filePreview').hide();
            }
        });
    });
    
    // Open Upload Modal
    function openUploadModal() {
        // Reset form
        document.getElementById('formUpload').reset();
        $('#filePreview').hide();
        
        var modal = new bootstrap.Modal(document.getElementById('modalUpload'));
        modal.show();
    }
    
    // Upload Dokumen
    function uploadDokumen() {
        const form = document.getElementById('formUpload');
        
        // Validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Mengupload Dokumen...',
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
        fetch('<?= base_url('/admin-pusat/transportation/upload-dokumen') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalUpload'));
            modal.hide();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Dokumen berhasil diupload',
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
                text: 'Terjadi kesalahan saat mengupload dokumen',
                confirmButtonText: 'OK'
            });
        });
    }

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
</script>

<style>
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
</style>
