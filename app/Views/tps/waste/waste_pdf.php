<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Sampah TPS</title>
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
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 0;
        }
        .info-box td:first-child {
            width: 150px;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.data-table th {
            background: #4CAF50;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table.data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        .status-draft { background: #9e9e9e; color: white; }
        .status-dikirim_ke_admin { background: #2196F3; color: white; }
        .status-disetujui_admin { background: #4CAF50; color: white; }
        .status-ditolak_admin { background: #f44336; color: white; }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .summary-box {
            background: #e8f5e9;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #2e7d32;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-box td {
            padding: 5px 0;
        }
        .summary-box td:last-child {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA SAMPAH TPS</h1>
        <p><?= $unit['nama_unit'] ?? 'TPS' ?></p>
        <p style="font-size: 10px;">Dicetak pada: <?= $tanggal_cetak ?></p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>TPS</td>
                <td>: <?= $unit['nama_unit'] ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Pengelola</td>
                <td>: <?= $user['nama_lengkap'] ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: <?= count($waste_data) ?> data</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 25%;">Jenis Sampah</th>
                <th style="width: 12%;">Berat (kg)</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 18%;">Nilai (Rp)</th>
                <th style="width: 18%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php $total_berat = 0; ?>
            <?php $total_nilai = 0; ?>
            <?php foreach ($waste_data as $item): ?>
            <?php 
                $total_berat += $item['berat_kg'];
                $total_nilai += $item['nilai_rupiah'] ?? 0;
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($item['tanggal'])) ?></td>
                <td><?= $item['jenis_sampah'] ?? 'N/A' ?></td>
                <td><?= number_format($item['berat_kg'], 2) ?></td>
                <td><?= $item['satuan'] ?? 'kg' ?></td>
                <td><?= number_format($item['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                <td>
                    <?php
                    $status = $item['status'] ?? 'draft';
                    $statusText = ucfirst(str_replace('_', ' ', $status));
                    ?>
                    <span class="status-badge status-<?= $status ?>"><?= $statusText ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="summary-box">
        <h3>RINGKASAN</h3>
        <table>
            <tr>
                <td>Total Berat Sampah</td>
                <td><?= number_format($total_berat, 2) ?> kg</td>
            </tr>
            <tr>
                <td>Total Nilai</td>
                <td>Rp <?= number_format($total_nilai, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td><?= count($waste_data) ?> data</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem UI GreenMetric POLBAN</p>
        <p>Dicetak pada: <?= $tanggal_cetak ?></p>
    </div>
</body>
</html>
