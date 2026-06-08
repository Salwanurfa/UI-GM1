<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Analisis Infrastruktur & Parkir' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <style>
        .main-content {
            margin-left: 280px;
            padding: 25px 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
            background: #f4f6f9;
        }
        
        .page-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .page-header h1 {
            font-size: 26px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .card-header {
            padding: 18px 25px;
            font-weight: 600;
        }
        
        .ratio-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        
        .ratio-card h2 {
            font-size: 48px;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .table thead th {
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
        }
        
        .badge {
            font-size: 12px;
            padding: 5px 12px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1><i class="fas fa-parking"></i> Analisis Infrastruktur & Parkir (TR 5 & TR 8)</h1>
                <p>Kelola data area parkir dan jalur pedestrian untuk UI GreenMetric</p>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- TR 5: Area Parkir -->
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h3><i class="fas fa-square-parking"></i> TR 5: Data Area Parkir</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/admin-pusat/transportation/simpan-parkir') ?>">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="luas_parkir" class="form-label">
                                    <i class="fas fa-ruler-combined"></i> Luas Area Parkir (m²)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="luas_parkir" 
                                       name="luas_parkir" value="<?= $parkir['luas_parkir'] ?? '' ?>" 
                                       placeholder="Contoh: 5000" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="luas_kampus" class="form-label">
                                    <i class="fas fa-map"></i> Total Luas Area Kampus (m²)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="luas_kampus" 
                                       name="luas_kampus" value="<?= $parkir['luas_kampus'] ?? '' ?>" 
                                       placeholder="Contoh: 100000" required>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="tahun_parkir" class="form-label">
                                    <i class="fas fa-calendar"></i> Tahun Data
                                </label>
                                <select class="form-select" id="tahun_parkir" name="tahun" required>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++): 
                                    ?>
                                        <option value="<?= $i ?>" <?= (isset($parkir['tahun']) && $parkir['tahun'] == $i) || $i == $currentYear ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-save"></i> Simpan Data Parkir
                            </button>
                        </div>
                    </form>
                    
                    <!-- Rasio Display -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="ratio-card">
                                <p><i class="fas fa-percentage"></i> Rasio Area Parkir</p>
                                <h2><?= $rasio_parkir ?>%</h2>
                                <p>dari Total Luas Kampus</p>
                                <hr style="border-color: rgba(255,255,255,0.3);">
                                <small>
                                    Formula: (<?= number_format($parkir['luas_parkir'] ?? 0) ?> m² / <?= number_format($parkir['luas_kampus'] ?? 1) ?> m²) × 100%
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

            <!-- TR 8: Jalur Pedestrian -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3><i class="fas fa-walking"></i> TR 8: Pendataan Jalur Pedestrian</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('/admin-pusat/transportation/simpan-pedestrian') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" id="pedestrian_id" name="id" value="">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="nama_jalur" class="form-label">
                                    <i class="fas fa-road"></i> Nama/Lokasi Jalur
                                </label>
                                <input type="text" class="form-control" id="nama_jalur" 
                                       name="nama_jalur" placeholder="Contoh: Jalur Gedung A-B" required>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="panjang_jalur" class="form-label">
                                    <i class="fas fa-ruler-horizontal"></i> Panjang (m)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="panjang_jalur" 
                                       name="panjang_jalur" placeholder="Contoh: 150" required>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="lebar_jalur" class="form-label">
                                    <i class="fas fa-arrows-alt-h"></i> Lebar (m)
                                </label>
                                <input type="number" step="0.01" class="form-control" id="lebar_jalur" 
                                       name="lebar_jalur" placeholder="Contoh: 2.5" required>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="kondisi" class="form-label">
                                    <i class="fas fa-check-circle"></i> Kondisi
                                </label>
                                <select class="form-select" id="kondisi" name="kondisi" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success" id="btnSubmitPedestrian">
                                        <i class="fas fa-plus"></i> Tambah Jalur
                                    </button>
                                    <button type="button" class="btn btn-secondary d-none" id="btnCancelPedestrian" onclick="resetPedestrianForm()">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
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
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-warning" 
                                                            onclick="editPedestrian(<?= htmlspecialchars(json_encode($ped), ENT_QUOTES, 'UTF-8') ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger" 
                                                            onclick="deletePedestrian(<?= $ped['id'] ?>, '<?= esc($ped['nama_jalur']) ?>')">
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
                                        <td colspan="2"></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada data jalur pedestrian</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Catatan UI GreenMetric:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>TR 5:</strong> Rasio area parkir terhadap total luas kampus (semakin rendah semakin baik)</li>
                    <li><strong>TR 8:</strong> Ketersediaan dan kualitas jalur pedestrian (semakin panjang dan baik semakin tinggi skornya)</li>
                    <li>Data harus diperbarui setiap tahun untuk akurasi penilaian</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            $('#pedestrianTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json' },
                pageLength: 10
            });
        });

        function editPedestrian(data) {
            $('#pedestrian_id').val(data.id);
            $('#nama_jalur').val(data.nama_jalur);
            $('#panjang_jalur').val(data.panjang_jalur);
            $('#lebar_jalur').val(data.lebar_jalur);
            $('#kondisi').val(data.kondisi);
            
            $('#btnSubmitPedestrian').html('<i class="fas fa-save"></i> Update').removeClass('btn-success').addClass('btn-warning');
            $('#btnCancelPedestrian').removeClass('d-none');
            
            $('html, body').animate({
                scrollTop: $('#nama_jalur').offset().top - 100
            }, 500);
        }

        function resetPedestrianForm() {
            $('#pedestrian_id').val('');
            $('#nama_jalur').val('');
            $('#panjang_jalur').val('');
            $('#lebar_jalur').val('');
            $('#kondisi').val('');
            
            $('#btnSubmitPedestrian').html('<i class="fas fa-plus"></i> Tambah Jalur').removeClass('btn-warning').addClass('btn-success');
            $('#btnCancelPedestrian').addClass('d-none');
        }

        function deletePedestrian(id, nama) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `Yakin ingin menghapus jalur:<br><strong>"${nama}"</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('/admin-pusat/transportation/hapus-pedestrian/') ?>' + id;
                }
            });
        }

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
