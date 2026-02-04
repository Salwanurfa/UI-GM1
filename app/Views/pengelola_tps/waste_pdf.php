<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Sampah TPS - <?= $tps_info['nama_unit'] ?? 'TPS' ?></title>
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
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .stat-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        .stat-item .value {
            font-size: 16px;
            font-weight: bold;
            color: #333;
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
        .status-dikirim { background: #2196F3; color: white; }
        .status-review { background: #FF9800; color: white; }
        .status-disetujui { background: #4CAF50; color: white; }
        .status-perlu_revisi { background: #f44336; color: white; }
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
        .monthly-summary-box {
            background: #fff3cd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .monthly-summary-box h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #856404;
        }
        .monthly-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .month-item {
            display: table-cell;
            width: 8.33%;
            text-align: center;
            padding: 8px 5px;
            border: 1px solid #ddd;
            background: white;
        }
        .month-name {
            font-weight: bold;
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
        }
        .month-data {
            font-size: 9px;
            color: #666;
        }
        .month-data div {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN DATA SAMPAH TPS</h1>
        <p><?= $tps_info['nama_unit'] ?? 'TPS' ?></p>
        <p style="font-size: 10px;">Dicetak pada: <?= $generated_at ?></p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td>TPS</td>
                <td>: <?= $tps_info['nama_unit'] ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Pengelola</td>
                <td>: <?= $user['nama_lengkap'] ?? 'N/A' ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: <?= count($waste_list) ?> data</td>
            </tr>
        </table>
    </div>

    <div class="stats-grid">
        <div class="stat-item">
            <div class="label">Draft</div>
            <div class="value"><?= $status_count['draft'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Dikirim</div>
            <div class="value"><?= $status_count['dikirim'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Review</div>
            <div class="value"><?= $status_count['review'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Disetujui</div>
            <div class="value"><?= $status_count['disetujui'] ?></div>
        </div>
        <div class="stat-item">
            <div class="label">Perlu Revisi</div>
            <div class="value"><?= $status_count['perlu_revisi'] ?></div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 20%;">Jenis Sampah</th>
                <th style="width: 12%;">Berat</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 18%;">Nilai (Rp)</th>
                <th style="width: 13%;">Status</th>
                <th style="width: 10%;">Kategori</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($waste_list as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d/m/Y', strtotime($item['created_at'])) ?></td>
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
                <td><?= $item['kategori_sampah'] ?? 'N/A' ?></td>
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
                <td><?= count($waste_list) ?> data</td>
            </tr>
        </table>
    </div>

    <?php if (!empty($monthly_summary)): ?>
    <div class="monthly-summary-box">
        <h3>RINGKASAN BULANAN <?= date('Y') ?></h3>
        <div class="monthly-grid">
            <?php 
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $monthlyData = [];
            foreach ($monthly_summary as $data) {
                $monthlyData[$data['month']] = $data;
            }
            ?>
            <?php for ($i = 1; $i <= 12; $i++): ?>
            <div class="month-item">
                <div class="month-name"><?= $months[$i-1] ?></div>
                <div class="month-data">
                    <div><?= $monthlyData[$i]['count'] ?? 0 ?> data</div>
                    <div><?= number_format($monthlyData[$i]['total_weight'] ?? 0, 0, ',', '.') ?> kg</div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem UI GreenMetric POLBAN</p>
        <p>Dicetak pada: <?= $generated_at ?></p>
    </div>
</body>
</html>
