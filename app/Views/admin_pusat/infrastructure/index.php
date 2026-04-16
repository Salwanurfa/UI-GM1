<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building text-primary"></i>
                Manajemen Infrastruktur & Populasi
            </h1>
            <p class="text-muted mb-0">Data master untuk indikator UIGM TR 1, 2, 4, 5, dan 6</p>
        </div>
    </div>

    <!-- UIGM Ratios Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                TR 1: Rasio Kendaraan/Populasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $uigm_ratios['vehicle_population_ratio'] ?>%
                            </div>
                            <div class="text-xs text-muted">
                                <?= number_format($uigm_ratios['total_vehicles']) ?> / <?= number_format($uigm_ratios['total_population']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                TR 4: Rasio ZEV
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $uigm_ratios['zev_ratio'] ?>%
                            </div>
                            <div class="text-xs text-muted">
                                <?= number_format($uigm_ratios['total_zev']) ?> ZEV dari <?= number_format($uigm_ratios['total_vehicles']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-leaf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                TR 5 & 6: Rasio Area Parkir
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $uigm_ratios['parking_ratio'] ?>%
                            </div>
                            <div class="text-xs text-muted">
                                <?= number_format($uigm_ratios['parking_area']) ?> m² / <?= number_format($uigm_ratios['campus_area']) ?> m²
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-parking fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                TR 2: Layanan Shuttle
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $uigm_ratios['shuttle_ratio'] ?>%
                            </div>
                            <div class="text-xs text-muted">
                                <?= number_format($uigm_ratios['total_shuttle']) ?> shuttle untuk <?= number_format($uigm_ratios['total_population']) ?> populasi
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infrastructure & Population Data Management -->
    <div class="row">
        <!-- Infrastructure Data -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i>
                        Data Infrastruktur Kampus
                    </h6>
                    <a href="<?= base_url('/admin-pusat/infrastructure/infrastructure-form') ?>" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
                <div class="card-body">
                    <!-- Current Infrastructure Stats -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="font-weight-bold mb-2">Data Aktif: <?= $infrastructure_stats['tahun_akademik'] ?></h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Luas Total Kampus:</small>
                                <div class="font-weight-bold"><?= number_format($infrastructure_stats['luas_total_kampus']) ?> m²</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Luas Area Parkir:</small>
                                <div class="font-weight-bold"><?= number_format($infrastructure_stats['luas_area_parkir_total']) ?> m²</div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($infrastructure_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun Akademik</th>
                                        <th>Luas Kampus (m²)</th>
                                        <th>Luas Parkir (m²)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($infrastructure_data as $item): ?>
                                        <tr>
                                            <td><?= esc($item['tahun_akademik']) ?></td>
                                            <td><?= number_format($item['luas_total_kampus']) ?></td>
                                            <td><?= number_format($item['luas_area_parkir_total']) ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('/admin-pusat/infrastructure/infrastructure-form/' . $item['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            onclick="confirmDelete('infrastructure', <?= $item['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada data infrastruktur</h6>
                            <p class="text-muted">Tambahkan data luas kampus dan area parkir</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Population Data -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i>
                        Data Populasi Kampus
                    </h6>
                    <a href="<?= base_url('/admin-pusat/infrastructure/population-form') ?>" 
                       class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                </div>
                <div class="card-body">
                    <!-- Current Population Stats -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="font-weight-bold mb-2">Data Aktif: <?= $population_stats['tahun_akademik'] ?></h6>
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted">Dosen:</small>
                                <div class="font-weight-bold"><?= number_format($population_stats['jumlah_dosen']) ?></div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Mahasiswa:</small>
                                <div class="font-weight-bold"><?= number_format($population_stats['jumlah_mahasiswa']) ?></div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Tendik:</small>
                                <div class="font-weight-bold"><?= number_format($population_stats['jumlah_tenaga_kependidikan']) ?></div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="text-center">
                            <small class="text-muted">Total Populasi:</small>
                            <div class="h5 font-weight-bold text-primary"><?= number_format($population_stats['total_populasi']) ?></div>
                        </div>
                    </div>

                    <?php if (!empty($population_data)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun Akademik</th>
                                        <th>Total Populasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($population_data as $item): ?>
                                        <tr>
                                            <td><?= esc($item['tahun_akademik']) ?></td>
                                            <td>
                                                <strong><?= number_format($item['total_populasi']) ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    D: <?= number_format($item['jumlah_dosen']) ?> | 
                                                    M: <?= number_format($item['jumlah_mahasiswa']) ?> | 
                                                    T: <?= number_format($item['jumlah_tenaga_kependidikan']) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('/admin-pusat/infrastructure/population-form/' . $item['id']) ?>" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            onclick="confirmDelete('population', <?= $item['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada data populasi</h6>
                            <p class="text-muted">Tambahkan data jumlah dosen, mahasiswa, dan tendik</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Forms -->
<form id="deleteInfrastructureForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<form id="deletePopulationForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(type, id) {
    const typeName = type === 'infrastructure' ? 'infrastruktur' : 'populasi';
    
    if (confirm(`Apakah Anda yakin ingin menghapus data ${typeName} ini?`)) {
        const form = document.getElementById(`delete${type.charAt(0).toUpperCase() + type.slice(1)}Form`);
        form.action = `<?= base_url('/admin-pusat/infrastructure/delete-') ?>${type}/${id}`;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>