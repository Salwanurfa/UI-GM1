<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            color: #333;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .info-box td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        table.data-table thead {
            background-color: #667eea;
        }
        
        table.data-table th {
            background-color: #667eea !important;
            color: #ffffff !important;
            padding: 10px 5px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
            border: 2px solid #333333;
            vertical-align: middle;
            line-height: 1.4;
        }
        
        table.data-table td {
            padding: 6px 5px;
            border: 1px solid #333333;
            font-size: 9px;
            vertical-align: middle;
        }
        
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table.data-table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-draft {
            background: #6c757d;
            color: white;
        }
        
        .badge-dikirim {
            background: #ffc107;
            color: #000;
        }
        
        .badge-disetujui {
            background: #28a745;
            color: white;
        }
        
        .badge-ditolak {
            background: #dc3545;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
        }
        
        .footer .signature {
            margin-top: 60px;
            border-top: 1px solid #000;
            display: inline-block;
            padding-top: 5px;
            min-width: 200px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA LIMBAH CAIR</h1>
        <h2>POLITEKNIK NEGERI BANDUNG</h2>
        <p>UI GreenMetric - Waste Management System</p>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <table>
            <tr>
                <td>Unit</td>
                <td>: <?= esc($unit['nama_unit'] ?? 'Unit') ?></td>
                <td>Tanggal Cetak</td>
                <td>: <?= $tanggal_cetak ?></td>
            </tr>
            <tr>
                <td>Nama User</td>
                <td>: <?= esc($user['nama'] ?? 'User') ?></td>
                <td>Total Data</td>
                <td>: <?= count($limbah_cair) ?> records</td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <?php if (!empty($limbah_cair)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Tanggal Input</th>
                <th style="width: 12%;">Unit/Jurusan</th>
                <th style="width: 15%;">Nama Limbah</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 10%;">Volume/Timbulan (L/bulan)</th>
                <th style="width: 6%;">Satuan</th>
                <th style="width: 5%;">pH</th>
                <th style="width: 7%;">BOD (mg/L)</th>
                <th style="width: 7%;">COD (mg/L)</th>
                <th style="width: 7%;">TSS (mg/L)</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($limbah_cair as $data): ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($data['tanggal_input'])) ?></td>
                <td><?= esc($data['lokasi']) ?></td>
                <td><?= esc($data['nama_limbah']) ?></td>
                <td class="text-center"><?= esc($data['kode_limbah']) ?></td>
                <td class="text-right"><?= number_format($data['timbulan'], 2, ',', '.') ?></td>
                <td class="text-center"><?= esc($data['satuan']) ?></td>
                <td class="text-center"><?= $data['ph'] ?? '-' ?></td>
                <td class="text-center"><?= $data['bod'] ? number_format($data['bod'], 2, ',', '.') : '-' ?></td>
                <td class="text-center"><?= $data['cod'] ? number_format($data['cod'], 2, ',', '.') : '-' ?></td>
                <td class="text-center"><?= $data['tss'] ? number_format($data['tss'], 2, ',', '.') : '-' ?></td>
                <td class="text-center">
                    <?php
                    $statusClass = 'badge-draft';
                    $statusText = 'Draft';
                    
                    switch ($data['status']) {
                        case 'dikirim_ke_tps':
                            $statusClass = 'badge-dikirim';
                            $statusText = 'Dikirim';
                            break;
                        case 'disetujui_tps':
                        case 'disetujui_admin':
                            $statusClass = 'badge-disetujui';
                            $statusText = 'Disetujui';
                            break;
                        case 'ditolak_tps':
                        case 'ditolak_admin':
                            $statusClass = 'badge-ditolak';
                            $statusText = 'Ditolak';
                            break;
                    }
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <p>Tidak ada data limbah cair yang tersedia</p>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Bandung, <?= date('d F Y') ?></p>
        <p>Penanggung Jawab,</p>
        <div class="signature">
            <strong><?= esc($user['nama'] ?? 'User') ?></strong>
        </div>
    </div>
</body>
</html>
