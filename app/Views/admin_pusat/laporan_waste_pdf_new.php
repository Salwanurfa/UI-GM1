<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Waste Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }
        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 3px 0;
        }
        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            padding: 8px;
            background-color: #2c3e50;
            color: white;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
        }
        table.data-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            text-align: center;
        }
        .summary-item h4 {
            margin: 0;
            font-size: 18px;
            color: #1976d2;
        }
        .summary-item p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: right;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN WASTE MANAGEMENT</h1>
        <p>UI GreenMetric POLBAN - Sistem Informasi Pengelolaan Sampah</p>
        <p>Dicetak pada: <?= $generated_at ?></p>
    </div>

    <?php if (!empty($filters['start_date']) || !empty($filters['end_date'])): ?>
    <div class="info-section">
        <table>
            <tr>
                <td>Periode</td>
                <td>: <?= $filters['start_date'] ? date('d/m/Y', strtotime($filters['start_date'])) : '-' ?> s/d <?= $filters['end_date'] ? date('d/m/Y', strtotime($filters['end_date'])) : '-' ?></td>
            </tr>
            <?php if (!empty($filters['status'])): ?>
            <tr>
                <td>Status</td>
                <td>: <?= ucfirst($filters['status']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <div class="section-title">RINGKASAN</div>
    <div class="summary-grid">
        <div class="summary-item">
            <h4><?= $data['summary']['total_transaksi'] ?? 0 ?></h4>
            <p>Total Transaksi</p>
        </div>
        <div class="summary-item">
            <h4><?= $data['summary']['total_disetujui'] ?? 0 ?></h4>
            <p>Disetujui</p>
        </div>
        <div class="summary-item">
            <h4><?= $data['summary']['total_ditolak'] ?? 0 ?></h4>
            <p>Ditolak</p>
        </div>
        <div class="summary-item">
            <h4><?= number_format($data['summary']['total_berat_disetujui'] ?? 0, 2) ?> kg</h4>
            <p>Total Berat</p>
        </div>
    </div>

    <div class="section-title">REKAP PER JENIS SAMPAH</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Sampah</th>
                <th class="text-center">Total</th>
                <th class="text-center">Disetujui</th>
                <th class="text-center">Ditolak</th>
                <th class="text-right">Berat (kg)</th>
                <th class="text-right">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['rekap_jenis'])): ?>
                <?php foreach ($data['rekap_jenis'] as $index => $item): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= $item['jenis_sampah'] ?></td>
                    <td class="text-center"><?= $item['total_transaksi'] ?></td>
                    <td class="text-center"><?= $item['total_disetujui'] ?></td>
                    <td class="text-center"><?= $item['total_ditolak'] ?></td>
                    <td class="text-right"><?= number_format($item['total_berat_disetujui'], 2) ?></td>
                    <td class="text-right"><?= number_format($item['total_nilai_disetujui'], 0) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">REKAP PER UNIT</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Unit</th>
                <th class="text-center">Total</th>
                <th class="text-center">Disetujui</th>
                <th class="text-center">Ditolak</th>
                <th class="text-right">Berat (kg)</th>
                <th class="text-right">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data['rekap_unit'])): ?>
                <?php foreach ($data['rekap_unit'] as $index => $item): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= $item['nama_unit'] ?? 'N/A' ?></td>
                    <td class="text-center"><?= $item['total_transaksi'] ?></td>
                    <td class="text-center"><?= $item['total_disetujui'] ?></td>
                    <td class="text-center"><?= $item['total_ditolak'] ?></td>
                    <td class="text-right"><?= number_format($item['total_berat_disetujui'], 2) ?></td>
                    <td class="text-right"><?= number_format($item['total_nilai_disetujui'], 0) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- DATA DISETUJUI -->
    <?php if (!empty($data['data_disetujui'])): ?>
    <div class="section-title">DATA DISETUJUI (<?= count($data['data_disetujui']) ?>)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 18%;">Unit</th>
                <th style="width: 15%;">Jenis Sampah</th>
                <th style="width: 10%;" class="text-right">Berat (kg)</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 15%;" class="text-right">Nilai (Rp)</th>
                <th style="width: 12%;">Direview Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['data_disetujui'] as $index => $item): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= date('d/m/Y H:i', strtotime($item['tanggal_input'] ?? $item['created_at'])) ?></td>
                <td><?= $item['nama_unit'] ?? 'N/A' ?></td>
                <td><?= $item['jenis_sampah'] ?? 'N/A' ?></td>
                <td class="text-right"><?= number_format($item['berat_kg'] ?? 0, 2) ?></td>
                <td><?= $item['satuan'] ?? 'kg' ?></td>
                <td class="text-right"><?= $item['nilai_rupiah'] ? 'Rp ' . number_format($item['nilai_rupiah'], 0, ',', '.') : 'Rp 0' ?></td>
                <td><?= $item['reviewed_by_name'] ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- DATA DITOLAK -->
    <?php if (!empty($data['data_ditolak'])): ?>
    <div class="section-title">DATA DITOLAK (<?= count($data['data_ditolak']) ?>)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 18%;">Unit</th>
                <th style="width: 15%;">Jenis Sampah</th>
                <th style="width: 10%;" class="text-right">Berat (kg)</th>
                <th style="width: 8%;">Satuan</th>
                <th style="width: 15%;">Alasan Ditolak</th>
                <th style="width: 12%;">Direview Oleh</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['data_ditolak'] as $index => $item): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><?= date('d/m/Y H:i', strtotime($item['tanggal_input'] ?? $item['created_at'])) ?></td>
                <td><?= $item['nama_unit'] ?? 'N/A' ?></td>
                <td><?= $item['jenis_sampah'] ?? 'N/A' ?></td>
                <td class="text-right"><?= number_format($item['berat_kg'] ?? 0, 2) ?></td>
                <td><?= $item['satuan'] ?? 'kg' ?></td>
                <td><?= $item['keterangan'] ?? '-' ?></td>
                <td><?= $item['reviewed_by_name'] ?? '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh sistem pada <?= $generated_at ?></p>
        <p>UI GreenMetric POLBAN - Waste Management System</p>
    </div>
</body>
</html>
