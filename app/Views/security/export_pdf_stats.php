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
        
        .kategori-roda-dua { color: #27ae60; font-weight: bold; }
        .kategori-roda-empat { color: #3498db; font-weight: bold; }
        .kategori-sepeda { color: #9b59b6; font-weight: bold; }
        .kategori-kendaraan-umum { color: #f39c12; font-weight: bold; }
        
        .bahan-bakar-bensin { color: #e74c3c; font-weight: bold; }
        .bahan-bakar-diesel { color: #34495e; font-weight: bold; }
        .bahan-bakar-listrik { color: #f1c40f; font-weight: bold; }
        .bahan-bakar-non-bbm { color: #27ae60; font-weight: bold; }
        
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
        <p>UI GreenMetric Transportation Statistics</p>
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
                    <th width="25%">Periode</th>
                    <th width="20%">Kategori Kendaraan</th>
                    <th width="20%">Jenis Bahan Bakar</th>
                    <th width="15%">Jumlah Total</th>
                    <th width="15%">Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($row['periode']) ?></td>
                        <td class="kategori-<?= strtolower(str_replace(' ', '-', $row['kategori_kendaraan'])) ?>">
                            <?= esc($row['kategori_kendaraan']) ?>
                        </td>
                        <td class="bahan-bakar-<?= strtolower(str_replace('-', '-', $row['jenis_bahan_bakar'])) ?>">
                            <?= esc($row['jenis_bahan_bakar']) ?>
                        </td>
                        <td style="text-align: center; font-weight: bold;">
                            <?= number_format($row['jumlah_total']) ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>Tidak ada data statistik transportasi untuk ditampilkan.</p>
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