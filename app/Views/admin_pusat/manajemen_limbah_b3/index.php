<?php
$title = $title ?? 'Manajemen Master Limbah B3';
$limbah = $limbah ?? [];
$kategori_options = $kategori_options ?? [];
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
                <h1><i class="fas fa-biohazard"></i> <?= $title ?></h1>
                <p>Kelola data Limbah B3</p>
            </div>
            
            <div class="header-actions">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#limbahModal">
                    <i class="fas fa-plus"></i> Tambah Limbah B3
                </button>
                <a href="<?= base_url('/admin-pusat/manajemen-limbah-b3/logs') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-history"></i> Riwayat
                </a>
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
                <h3><i class="fas fa-table"></i> Daftar Limbah B3</h3>
            </div>
            <div class="card-body">
                <?php if (empty($limbah)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Belum ada data limbah B3
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="5%">Jenis</th>
                                <th>Nama Limbah</th>
                                <th width="15%">Kode</th>
                                <th width="15%">Kategori Bahaya</th>
                                <th>Karakteristik</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($limbah as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="text-center">
                                    <i class="fas fa-biohazard text-warning" title="Limbah B3"></i>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['nama_limbah'] ?? '-') ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($item['kode_limbah'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $kategori = $item['kategori_bahaya'] ?? '';
                                    $badgeClass = match($kategori) {
                                        'Bahaya 1' => 'danger',
                                        'Bahaya 2' => 'warning',
                                        'Bahaya 3' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= htmlspecialchars($kategori) ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php
                                        $char = $item['karakteristik'] ?? '';
                                        echo htmlspecialchars(substr($char, 0, 30));
                                        echo strlen($char) > 30 ? '...' : '';
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editLimbah(<?= htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="limbahModalLabel">
                        <i class="fas fa-plus-circle"></i> Tambah Limbah B3
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="limbahForm" action="<?= base_url('/admin-pusat/manajemen-limbah-b3/store') ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="namaLimbah" class="form-label">Nama Limbah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaLimbah" name="nama_limbah" 
                                placeholder="Contoh: Limbah Elektronik" required>
                        </div>

                        <div class="mb-3">
                            <label for="kodeLimbah" class="form-label">Kode Limbah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kodeLimbah" name="kode_limbah" 
                                placeholder="Contoh: B107d" required>
                        </div>

                        <div class="mb-3">
                            <label for="kategoriBahaya" class="form-label">Kategori Bahaya <span class="text-danger">*</span></label>
                            <select class="form-select" id="kategoriBahaya" name="kategori_bahaya" required>
                                <option value=""> Pilih Kategori </option>
                                <?php foreach ($kategori_options as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                        <label>Bentuk Fisik</label>
                        <select name="bentuk_fisik" class="form-control" required>
                            <option value="Cair">Cair</option>
                            <option value="Padat">Padat</option>
                            <option value="Sludge">Sludge (Lumpur)</option>
                        </select>
                    </div>

                        <div class="mb-3">
                            <label for="karakteristik" class="form-label">Karakteristik</label>
                            <textarea class="form-control" id="karakteristik" name="karakteristik" rows="3"
                                placeholder="Deskripsi karakteristik limbah..."></textarea>
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
                    <h5 class="modal-title"><i class="fas fa-trash"></i> Hapus Limbah B3</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus limbah ini?</p>
                    <p class="text-danger"><strong id="deleteName">-</strong></p>
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
            '<?= base_url('/admin-pusat/manajemen-limbah-b3/update') ?>/' + data.id;
        document.getElementById('namaLimbah').value = data.nama_limbah || '';
        document.getElementById('kodeLimbah').value = data.kode_limbah || '';
        document.getElementById('kategoriBahaya').value = data.kategori_bahaya || '';
        document.getElementById('karakteristik').value = data.karakteristik || '';
        
        document.getElementById('limbahModalLabel').innerHTML = '<i class="fas fa-edit"></i> Edit Limbah B3';
        document.getElementById('submitText').textContent = 'Perbarui';
        
        const modal = new bootstrap.Modal(document.getElementById('limbahModal'));
        modal.show();
    }

    function deleteLimbah(id, name) {
        document.getElementById('deleteName').textContent = name;
        document.getElementById('deleteForm').action = 
            '<?= base_url('/admin-pusat/manajemen-limbah-b3/delete') ?>/' + id;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    document.getElementById('limbahModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('limbahForm').action = 
            '<?= base_url('/admin-pusat/manajemen-limbah-b3/store') ?>';
        document.getElementById('limbahForm').reset();
        document.getElementById('limbahModalLabel').innerHTML = 
            '<i class="fas fa-plus-circle"></i> Tambah Limbah B3';
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
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
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
