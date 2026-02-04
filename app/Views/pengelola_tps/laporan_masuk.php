<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan Masuk dari User' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .stats-card .number {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .stats-card .label {
            color: #6c757d;
            font-size: 0.9rem;
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

        .table {
            margin-bottom: 0;
        }

        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px 15px 0 0;
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
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-inbox"></i> Laporan Masuk dari User</h1>
                <p>Review dan kelola laporan sampah yang dikirim oleh user</p>
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card text-center" style="border-left: 4px solid #ffc107;">
                    <div class="icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="number"><?= $stats['pending_count'] ?? 0 ?></div>
                    <div class="label">Menunggu Review</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="border-left: 4px solid #28a745;">
                    <div class="icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="number"><?= $stats['approved_today'] ?? 0 ?></div>
                    <div class="label">Disetujui Hari Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="border-left: 4px solid #dc3545;">
                    <div class="icon text-danger">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="number"><?= $stats['rejected_today'] ?? 0 ?></div>
                    <div class="label">Ditolak Hari Ini</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center" style="border-left: 4px solid #17a2b8;">
                    <div class="icon text-info">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div class="number"><?= $stats['total_reviewed'] ?? 0 ?></div>
                    <div class="label">Total Direview</div>
                </div>
            </div>
        </div>

        <!-- Laporan Pending -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-hourglass-half"></i>
                    Laporan Menunggu Review (<?= count($laporan_pending) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($laporan_pending)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada laporan yang menunggu review</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Unit</th>
                                <th>Gedung</th>
                                <th>Jenis Sampah</th>
                                <th>Berat</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan_pending as $laporan): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($laporan['created_at'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($laporan['user_nama'] ?? 'N/A') ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($laporan['user_email'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($laporan['unit_nama'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($laporan['gedung_pelapor'] ?? $laporan['gedung'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($laporan['jenis_sampah'] ?? 'N/A') ?></td>
                                <td><?= number_format($laporan['berat_kg'] ?? 0, 2) ?> kg</td>
                                <td>Rp <?= number_format($laporan['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail(<?= $laporan['id'] ?>)">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="approveLaporan(<?= $laporan['id'] ?>)">
                                        <i class="fas fa-check"></i> Setujui
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectLaporan(<?= $laporan['id'] ?>)">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Laporan Reviewed -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history"></i>
                    Riwayat Review (<?= count($laporan_reviewed) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($laporan_reviewed)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada riwayat review</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal Review</th>
                                <th>User</th>
                                <th>Jenis Sampah</th>
                                <th>Berat</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laporan_reviewed as $laporan): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($laporan['tps_reviewed_at'])) ?></td>
                                <td><?= htmlspecialchars($laporan['user_nama'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($laporan['jenis_sampah'] ?? 'N/A') ?></td>
                                <td><?= number_format($laporan['berat_kg'] ?? 0, 2) ?> kg</td>
                                <td>
                                    <?php if ($laporan['status'] === 'disetujui_tps'): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php elseif ($laporan['status'] === 'ditolak_tps'): ?>
                                        <span class="badge bg-danger">Ditolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= ucfirst($laporan['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($laporan['tps_catatan'] ?? '-') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewDetail(<?= $laporan['id'] ?>)">
                                        <i class="fas fa-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-check-circle"></i> Setujui Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui laporan ini?</p>
                    <div class="mb-3">
                        <label for="approve_catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="approve_catatan" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    <input type="hidden" id="approve_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="confirmApprove()">
                        <i class="fas fa-check"></i> Ya, Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Tolak Laporan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak laporan ini?</p>
                    <div class="mb-3">
                        <label for="reject_catatan" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_catatan" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                    </div>
                    <input type="hidden" id="reject_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" onclick="confirmReject()">
                        <i class="fas fa-times"></i> Ya, Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function viewDetail(id) {
        $('#detailModal').modal('show');
        $('#detailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        
        $.ajax({
            url: '<?= base_url('/pengelola-tps/laporan-masuk/detail/') ?>' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-user"></i> Informasi User</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Nama:</strong></td><td>${data.user_nama || 'N/A'}</td></tr>
                                    <tr><td><strong>Email:</strong></td><td>${data.user_email || 'N/A'}</td></tr>
                                    <tr><td><strong>Unit:</strong></td><td>${data.unit_nama || 'N/A'}</td></tr>
                                    <tr><td><strong>Gedung:</strong></td><td>${data.gedung_pelapor || data.gedung || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary"><i class="fas fa-recycle"></i> Informasi Sampah</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Jenis:</strong></td><td>${data.jenis_sampah || 'N/A'}</td></tr>
                                    <tr><td><strong>Berat:</strong></td><td>${parseFloat(data.berat_kg || 0).toFixed(2)} kg</td></tr>
                                    <tr><td><strong>Kategori:</strong></td><td>${data.kategori_sampah || 'N/A'}</td></tr>
                                    <tr><td><strong>Nilai:</strong></td><td>Rp ${parseInt(data.nilai_rupiah || 0).toLocaleString('id-ID')}</td></tr>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-calendar"></i> Informasi Waktu</h6>
                                <table class="table table-sm">
                                    <tr><td><strong>Tanggal Input:</strong></td><td>${new Date(data.created_at).toLocaleString('id-ID')}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-warning">${data.status}</span></td></tr>
                                </table>
                            </div>
                        </div>
                        ${data.catatan ? `
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6 class="text-primary"><i class="fas fa-sticky-note"></i> Catatan</h6>
                                <p class="border p-3 rounded">${data.catatan}</p>
                            </div>
                        </div>
                        ` : ''}
                    `;
                    $('#detailContent').html(html);
                } else {
                    $('#detailContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#detailContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat detail</div>');
            }
        });
    }

    function approveLaporan(id) {
        $('#approve_id').val(id);
        $('#approve_catatan').val('');
        $('#approveModal').modal('show');
    }

    function confirmApprove() {
        const id = $('#approve_id').val();
        const catatan = $('#approve_catatan').val();
        
        // Disable button to prevent double click
        const btnApprove = $('#approveModal .btn-success');
        btnApprove.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        $.ajax({
            url: '<?= base_url('/pengelola-tps/laporan-masuk/approve/') ?>' + id,
            type: 'POST',
            data: {
                catatan: catatan,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#approveModal').modal('hide');
                    
                    // Show success message
                    showSuccessMessage(response.message);
                    
                    // Reload page after short delay to show message
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + response.message);
                    btnApprove.prop('disabled', false).html('<i class="fas fa-check"></i> Ya, Setujui');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menyetujui laporan');
                btnApprove.prop('disabled', false).html('<i class="fas fa-check"></i> Ya, Setujui');
            }
        });
    }

    function rejectLaporan(id) {
        $('#reject_id').val(id);
        $('#reject_catatan').val('');
        $('#rejectModal').modal('show');
    }

    function confirmReject() {
        const id = $('#reject_id').val();
        const catatan = $('#reject_catatan').val();
        
        if (!catatan || catatan.trim() === '') {
            alert('Alasan penolakan harus diisi');
            return;
        }
        
        // Disable button to prevent double click
        const btnReject = $('#rejectModal .btn-danger');
        btnReject.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
        
        $.ajax({
            url: '<?= base_url('/pengelola-tps/laporan-masuk/reject/') ?>' + id,
            type: 'POST',
            data: {
                catatan: catatan,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#rejectModal').modal('hide');
                    
                    // Show success message
                    showSuccessMessage(response.message);
                    
                    // Reload page after short delay to show message
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + response.message);
                    btnReject.prop('disabled', false).html('<i class="fas fa-times"></i> Ya, Tolak');
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menolak laporan');
                btnReject.prop('disabled', false).html('<i class="fas fa-times"></i> Ya, Tolak');
            }
        });
    }
    
    // Function to show success message
    function showSuccessMessage(message) {
        // Remove existing success messages
        $('.alert-success').remove();
        
        // Create new success message
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas fa-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert-success').fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    </script>
</body>
</html>
