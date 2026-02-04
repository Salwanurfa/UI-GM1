<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1><?= $title ?></h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Penilaian Data Status 'Dikirim' (<?= count($penilaian_dikirim) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($penilaian_dikirim)): ?>
                            <p class="text-muted">Tidak ada data penilaian dengan status 'dikirim'</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Unit</th>
                                            <th>Kategori</th>
                                            <th>Indikator</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($penilaian_dikirim as $item): ?>
                                        <tr>
                                            <td><?= $item['id'] ?></td>
                                            <td><?= $item['nama_unit'] ?></td>
                                            <td><?= $item['kategori_uigm'] ?></td>
                                            <td><?= $item['indikator'] ?></td>
                                            <td><span class="badge bg-warning"><?= $item['status'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Waste Data Status 'Dikirim' (<?= count($waste_dikirim) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($waste_dikirim)): ?>
                            <p class="text-muted">Tidak ada data waste dengan status 'dikirim'</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Unit</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($waste_dikirim as $item): ?>
                                        <tr>
                                            <td><?= $item['id'] ?></td>
                                            <td><?= $item['nama_unit'] ?></td>
                                            <td><?= $item['jenis_sampah'] ?></td>
                                            <td><?= $item['jumlah'] ?> <?= $item['satuan'] ?></td>
                                            <td><span class="badge bg-warning"><?= $item['status'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Status Count Penilaian</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($status_count)): ?>
                            <p class="text-muted">Tidak ada data penilaian</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($status_count as $status): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= ucfirst($status['status']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= $status['count'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>All Units (<?= count($all_units) ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($all_units)): ?>
                            <p class="text-muted">Tidak ada unit</p>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($all_units as $unit): ?>
                                <li class="list-group-item">
                                    <strong><?= $unit['nama_unit'] ?></strong>
                                    <br><small class="text-muted">ID: <?= $unit['id'] ?> | Status: <?= $unit['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?></small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="/debug/create-sample-data" class="btn btn-primary">Create Sample Data</a>
            <a href="/admin-pusat/review" class="btn btn-success">Go to Admin Review</a>
        </div>
    </div>
</body>
</html>