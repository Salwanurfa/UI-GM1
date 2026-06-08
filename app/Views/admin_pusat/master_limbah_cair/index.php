<?php
$title = $title ?? 'Manajemen Master Limbah Cair';
$master_limbah = $master_limbah ?? [];
$tingkat_bahaya_options = $tingkat_bahaya_options ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-tint"></i> <?= $title ?></h1>
                <p>Kelola data master limbah cair untuk referensi input user</p>
            </div>
            
            <div class="header-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#limbahModal">
                    <i class="fas fa-plus"></i> Tambah Limbah Cair
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Daftar Master Limbah Cair</h3>
            </div>
            <div class="card-body">
                <?php if (empty($master_limbah)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada data master limbah cair
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="20%">Nama Limbah Cair</th>
                                <th width="10%">Kode Limbah</th>
                                <th width="12%">Tingkat Bahaya</th>
                                <th width="20%">Karakteristik</th>
                                <th width="20%">Pengolahan</th>
                                <th width="13%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($master_limbah as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($item['nama_limbah'] ?? '-') ?></strong></td>
                                <td class="text-center">
                                    <?php if (!empty($item['kode_limbah']) && $item['kode_limbah'] !== '-'): ?>
                                    <span class="badge bg-warning text-dark">
                                        <?= htmlspecialchars($item['kode_limbah']) ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $badgeClass = 'bg-secondary';
                                    $tingkat = strtolower($item['tingkat_bahaya'] ?? '');
                                    if (strpos($tingkat, 'bahaya 1') !== false) {
                                        $badgeClass = 'bg-danger';
                                    } elseif (strpos($tingkat, 'bahaya 2') !== false) {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif (strpos($tingkat, 'rendah') !== false) {
                                        $badgeClass = 'bg-success';
                                    }
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($item['tingkat_bahaya'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($item['karakteristik'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($item['pengolahan'] ?? '-') ?></small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary mb-1" 
                                        onclick="editLimbah(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger mb-1"
                                        onclick="deleteLimbah(<?= $item['id'] ?>, '<?= htmlspecialchars($item['nama_limbah'], ENT_QUOTES) ?>')">
                                        <i class="fas fa-trash"></i> Hapus
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

    <!-- Modal Tambah/Edit -->
    <div class="modal fade" id="limbahModal" tabindex="-1" aria-labelledby="limbahModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="limbahModalLabel">
                        <i class="fas fa-plus-circle"></i> Tambah Master Limbah Cair
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="limbahForm" action="<?= base_url('/admin-pusat/master-limbah-cair/store') ?>" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="namaLimbah" class="form-label">Nama Limbah Cair <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="namaLimbah" name="nama_limbah" 
                                        placeholder="Contoh: Limbah Cair Laboratorium Kimia" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="kodeLimbah" class="form-label">Kode Limbah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="kodeLimbah" name="kode_limbah" 
                                        placeholder="Contoh: A106d" required>
                                    <small class="text-muted">Gunakan "-" untuk non-B3</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tingkatBahaya" class="form-label">Tingkat Bahaya <span class="text-danger">*</span></label>
                            <select class="form-select" id="tingkatBahaya" name="tingkat_bahaya" required>
                                <option value=""> Pilih Tingkat Bahaya </option>
                                <?php foreach ($tingkat_bahaya_options as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="karakteristik" class="form-label">Karakteristik <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="karakteristik" name="karakteristik" rows="3"
                                placeholder="Contoh: Beracun, Korosif" required></textarea>
                            <small class="text-muted">Jelaskan sifat atau karakteristik limbah (pisahkan dengan koma)</small>
                        </div>

                        <div class="mb-3">
                            <label for="pengolahan" class="form-label">Pengolahan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pengolahan" name="pengolahan" rows="3"
                                placeholder="Contoh: Netralisasi & Penampungan Terpisah" required></textarea>
                            <small class="text-muted">Jelaskan metode pengolahan atau penanganan limbah ini</small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <span id="submitText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-trash"></i> Hapus Limbah Cair</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus limbah cair ini?</p>
                    <p class="text-danger"><strong id="deleteName">-</strong></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <small>Data yang sudah diinput user tidak akan terhapus</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function editLimbah(data) {
        document.getElementById('limbahForm').action = 
            '<?= base_url('/admin-pusat/master-limbah-cair/update') ?>/' + data.id;
        document.getElementById('namaLimbah').value = data.nama_limbah || '';
        document.getElementById('kodeLimbah').value = data.kode_limbah || '';
        document.getElementById('tingkatBahaya').value = data.tingkat_bahaya || '';
        document.getElementById('karakteristik').value = data.karakteristik || '';
        document.getElementById('pengolahan').value = data.pengolahan || '';
        
        document.getElementById('limbahModalLabel').innerHTML = '<i class="fas fa-edit"></i> Edit Master Limbah Cair';
        document.getElementById('submitText').textContent = 'Perbarui';
        
        const modal = new bootstrap.Modal(document.getElementById('limbahModal'));
        modal.show();
    }

    function deleteLimbah(id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = 
            '<?= base_url('/admin-pusat/master-limbah-cair/delete') ?>/' + id;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    document.getElementById('limbahModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('limbahForm').action = 
            '<?= base_url('/admin-pusat/master-limbah-cair/store') ?>';
        document.getElementById('limbahForm').reset();
        document.getElementById('limbahModalLabel').innerHTML = 
            '<i class="fas fa-plus-circle"></i> Tambah Master Limbah Cair';
        document.getElementById('submitText').textContent = 'Simpan';
    });
    </script>

    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .card-body {
            padding: 20px;
        }

        .table th {
            background-color: #667eea;
            color: white;
            font-weight: 600;
            border: 1px solid #5568d3;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td {
            vertical-align: middle;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                flex-direction: column;
            }

            .header-actions .btn {
                width: 100%;
            }

            .table {
                font-size: 12px;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</body>
</html>
