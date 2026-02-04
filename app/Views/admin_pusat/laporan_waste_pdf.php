<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info-box {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 5px;
        }
        .info-box td:first-child {
            width: 150px;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-box {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e9;
            border: 2px solid #4CAF50;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #2e7d32;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box td {
            padding: 5px;
        }
        .summary-box td:first-child {
            width: 200px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN WASTE MANAGEMENT</h1>
        <p>Sistem Informasi Pengelolaan Sampah</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>Periode Laporan</td>
                <td>: <?= $periode ?></td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: <?= date('d/m/Y H:i:s') ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: <?= $summary['total_data'] ?> data</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 80px;">Tanggal</th>
                <th>Unit</th>
                <th>Jenis Sampah</th>
                <th class="text-right" style="width: 80px;">Berat (kg)</th>
                <th class="text-right" style="width: 100px;">Nilai (Rp)</th>
                <th>Pelapor</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($wasteData)): ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; ?>
                <?php foreach ($wasteData as $waste): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($waste['tanggal'])) ?></td>
                        <td><?= esc($waste['nama_unit'] ?? '-') ?></td>
                        <td><?= esc($waste['jenis_sampah']) ?></td>
                        <td class="text-right"><?= number_format($waste['berat'], 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format($waste['nilai_ekonomis'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= esc($waste['user_name'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary-box">
        <h3>RINGKASAN</h3>
        <table>
            <tr>
                <td>Total Data Waste</td>
                <td>: <?= $summary['total_data'] ?> data</td>
            </tr>
            <tr>
                <td>Total Berat Keseluruhan</td>
                <td>: <?= number_format($summary['total_berat'], 2, ',', '.') ?> kg</td>
            </tr>
            <tr>
                <td>Total Nilai Ekonomis</td>
                <td>: Rp <?= number_format($summary['total_nilai'], 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>Sistem Informasi Waste Management</p>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
