<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Security POLBAN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e3c72;
            margin-bottom: 10px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #1e3c72;
            color: white;
            font-weight: bold;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-masuk {
            color: #27ae60;
            font-weight: bold;
        }
        
        .status-keluar {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $title ?></h1>
        <p>Politeknik Negeri Bandung</p>
        <p>UI GreenMetric Transportation Data</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="150"><strong>Petugas Security:</strong></td>
            <td><?= esc($user['nama_lengkap']) ?></td>
        </tr>
        <tr>
            <td><strong>Username:</strong></td>
            <td><?= esc($user['username']) ?></td>
        </tr>
        <tr>
            <td><strong>Tanggal Export:</strong></td>
            <td><?= $generated_at ?></td>
        </tr>
        <tr>
            <td><strong>Total Data:</strong></td>
            <td><?= count($data) ?> record</td>
        </tr>
    </table>

    <?php if (!empty($data)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="12%">Tanggal</th>
                    <th width="10%">Waktu</th>
                    <th width="15%">Jenis Kendaraan</th>
                    <th width="15%">Plat Nomor</th>
                    <th width="20%">Tujuan</th>
                    <th width="10%">Status</th>
                    <th width="13%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= date('H:i', strtotime($row['waktu'])) ?></td>
                        <td><?= ucfirst($row['jenis_kendaraan']) ?></td>
                        <td><?= esc($row['plat_nomor']) ?></td>
                        <td><?= esc($row['tujuan']) ?></td>
                        <td class="status-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></td>
                        <td><?= esc($row['keterangan']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data transportasi untuk ditampilkan.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem UI GreenMetric POLBAN</p>
        <p>© <?= date('Y') ?> Politeknik Negeri Bandung</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>