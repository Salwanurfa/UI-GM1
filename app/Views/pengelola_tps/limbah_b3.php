<?php
/**
 * TPS Limbah B3 Verification
 */

$limbah_list = $limbah_list ?? [];
$tps_info    = $tps_info ?? ['nama_unit' => 'TPS'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limbah B3 TPS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/toast-notification.css') ?>" rel="stylesheet">
    <link href="<?= base_url('/css/loading-state.css') ?>" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/sidebar') ?>

<div class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-skull-crossbones"></i> Limbah B3 TPS</h1>
        <p>Verifikasi Limbah B3 yang masuk ke <?= esc($tps_info['nama_unit'] ?? 'TPS') ?></p>
    </div>

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

    <?php
    $pending   = array_filter($limbah_list, fn($r) => ($r['status'] ?? '') === 'dikirim_ke_tps');
    $approved  = array_filter($limbah_list, fn($r) => ($r['status'] ?? '') === 'disetujui_tps');
    $rejected  = array_filter($limbah_list, fn($r) => ($r['status'] ?? '') === 'ditolak_tps');
    ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-list"></i> Data Limbah B3</h3>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#b3-pending-tab">
                        <i class="fas fa-clock text-warning"></i> Menunggu Verifikasi
                        <span class="badge bg-warning"><?= count($pending) ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#b3-approved-tab">
                        <i class="fas fa-check-circle text-success"></i> Disetujui
                        <span class="badge bg-success"><?= count($approved) ?></span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#b3-rejected-tab">
                        <i class="fas fa-times-circle text-danger"></i> Ditolak
                        <span class="badge bg-danger"><?= count($rejected) ?></span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="b3-pending-tab">
                    <?php if (!empty($pending)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Input</th>
                                    <th>Unit Asal</th>
                                    <th>Nama Limbah</th>
                                    <th>Lokasi</th>
                                    <th>Timbulan</th>
                                    <th>Bentuk Fisik</th>
                                    <th>Kemasan</th>
                                    <th>Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; foreach ($pending as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? $row['created_at'])) ?></td>
                                        <td><?= esc($row['nama_unit'] ?? '-') ?></td>
                                        <td><span class="badge bg-danger"><?= esc($row['nama_limbah'] ?? '-') ?></span></td>
                                        <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                        <td><?= number_format($row['timbulan'] ?? 0, 3, ',', '.') . ' ' . esc($row['satuan'] ?? '') ?></td>
                                        <td><?= esc($row['bentuk_fisik'] ?? '-') ?></td>
                                        <td><?= esc($row['kemasan'] ?? '-') ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-success" onclick="approveB3(<?= (int) $row['id'] ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="rejectB3(<?= (int) $row['id'] ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada Limbah B3 yang menunggu verifikasi.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="b3-approved-tab">
                    <?php if (!empty($approved)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Input</th>
                                    <th>Unit Asal</th>
                                    <th>Nama Limbah</th>
                                    <th>Lokasi</th>
                                    <th>Timbulan</th>
                                    <th>Keterangan</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; foreach ($approved as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? $row['created_at'])) ?></td>
                                        <td><?= esc($row['nama_unit'] ?? '-') ?></td>
                                        <td><span class="badge bg-success"><?= esc($row['nama_limbah'] ?? '-') ?></span></td>
                                        <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                        <td><?= number_format($row['timbulan'] ?? 0, 3, ',', '.') . ' ' . esc($row['satuan'] ?? '') ?></td>
                                        <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada Limbah B3 yang disetujui.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="b3-rejected-tab">
                    <?php if (!empty($rejected)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Input</th>
                                    <th>Unit Asal</th>
                                    <th>Nama Limbah</th>
                                    <th>Lokasi</th>
                                    <th>Timbulan</th>
                                    <th>Alasan Penolakan</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $no = 1; foreach ($rejected as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_input'] ?? $row['created_at'])) ?></td>
                                        <td><?= esc($row['nama_unit'] ?? '-') ?></td>
                                        <td><span class="badge bg-danger"><?= esc($row['nama_limbah'] ?? '-') ?></span></td>
                                        <td><?= esc($row['lokasi'] ?? '-') ?></td>
                                        <td><?= number_format($row['timbulan'] ?? 0, 3, ',', '.') . ' ' . esc($row['satuan'] ?? '') ?></td>
                                        <td><?= esc($row['keterangan'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-times-circle fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Belum ada Limbah B3 yang ditolak.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal konfirmasi TPS -->
<div class="modal fade" id="tpsReviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tpsReviewModalTitle">Verifikasi Limbah B3</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tpsReviewForm">
                <?= csrf_field() ?>
                <input type="hidden" id="tps_review_id" name="id">
                <input type="hidden" id="tps_review_action" name="action">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Keterangan (opsional untuk approve, wajib untuk reject)</label>
                        <textarea class="form-control" name="keterangan" id="tps_review_keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?= base_url('/js/toast-notification.js') ?>"></script>
<script src="<?= base_url('/js/loading-state.js') ?>"></script>
<script>
    function openReviewModal(id, action) {
        document.getElementById('tps_review_id').value = id;
        document.getElementById('tps_review_action').value = action;
        document.getElementById('tps_review_keterangan').value = '';
        const title = action === 'approve'
            ? 'Setujui Limbah B3'
            : 'Tolak Limbah B3';
        document.getElementById('tpsReviewModalTitle').textContent = title;
        const modal = new bootstrap.Modal(document.getElementById('tpsReviewModal'));
        modal.show();
    }

    function approveB3(id) {
        openReviewModal(id, 'approve');
    }

    function rejectB3(id) {
        openReviewModal(id, 'reject');
    }

    document.getElementById('tpsReviewForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('tps_review_id').value;
        const action = document.getElementById('tps_review_action').value;
        const keterangan = document.getElementById('tps_review_keterangan').value;

        if (action === 'reject' && !keterangan.trim()) {
            alert('Keterangan wajib diisi saat menolak Limbah B3');
            return;
        }

        const fd = new FormData(this);
        let url = '';
        if (action === 'approve') {
            url = '<?= base_url('/pengelola-tps/limbah-b3/approve/') ?>' + id;
        } else {
            url = '<?= base_url('/pengelola-tps/limbah-b3/reject/') ?>' + id;
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            if (data.success) {
                toast.success(data.message || 'Berhasil memproses Limbah B3');
                setTimeout(() => location.reload(), 800);
            } else {
                toast.error(data.message || 'Gagal memproses Limbah B3');
            }
        } catch (err) {
            console.error(err);
            toast.error('Terjadi kesalahan saat memproses Limbah B3');
        }
    });
</script>

<style>
    .main-content {
        margin-left: 280px;
        padding: 30px;
        min-height: 100vh;
        max-width: calc(100vw - 280px);
        overflow-x: hidden;
    }
    .page-header {
        margin-bottom: 30px;
        padding: 20px 0;
        border-bottom: 2px solid #e9ecef;
    }
    .page-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 28px;
        font-weight: 700;
    }
    .page-header p {
        color: #6c757d;
        font-size: 16px;
        margin: 0;
    }
    .card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        border: none;
    }
    .card-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 15px 20px;
    }
    .card-body {
        padding: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
    }
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            max-width: 100vw;
        }
    }
</style>

</body>
</html>

