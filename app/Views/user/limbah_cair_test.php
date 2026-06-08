<!DOCTYPE html>
<html>
<head>
    <title>TEST LIMBAH CAIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>TEST DATA LIMBAH CAIR</h1>
        
        <div class="alert alert-info">
            <strong>Total data:</strong> <?= count($limbah_cair ?? []) ?>
        </div>
        
        <?php if (!empty($limbah_cair)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Nama Limbah</th>
                        <th>Lokasi</th>
                        <th>Timbulan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($limbah_cair as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['nama_limbah'] ?></td>
                        <td><?= $row['lokasi'] ?></td>
                        <td><?= $row['timbulan'] ?></td>
                        <td><?= $row['status'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-danger">
                <strong>ERROR:</strong> Variabel $limbah_cair KOSONG!
            </div>
        <?php endif; ?>
        
        <hr>
        <h3>RAW DATA:</h3>
        <pre><?php print_r($limbah_cair ?? 'TIDAK ADA'); ?></pre>
    </div>
</body>
</html>
