<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16px;
            color: #667eea;
            margin-bottom: 3px;
        }
        
        .header h2 {
            font-size: 13px;
            color: #333;
            margin-bottom: 2px;
        }
        
        .header p {
            font-size: 9px;
            color: #666;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 4px;
            border-left: 3px solid #667eea;
        }
        
        .info-box table {
            width: 100%;
        }
        
        .info-box td {
            padding: 2px 4px;
            font-size: 9px;
        }
        
        .info-box td:first-child {
            font-weight: bold;
            width: 120px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        
        table.data-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        table.data-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            border: 1px solid #555;
            vertical-align: middle;
        }
        
        table.data-table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 8px;
            vertical-align: middle;
        }
        
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
        }
        
        .badge-draft {
            background: #6c757d;
            color: white;
        }
        
        .badge-menunggu {
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
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .summary-box {
            margin-top: 12px;
            padding: 8px;
            background-color: #e7f3ff;
            border: 1px solid #667eea;
            border-radius: 4px;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 3px 6px;
            font-size: 9px;
        }
        
        .summary-box td:first-child {
            font-weight: bold;
            width: 180px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA LIMBAH CAIR</h1>
        <h2>POLITEKNIK NEGERI BANDUNG</h2>
        <p>UI GreenMetric - Waste Management System (Admin Pusat)</p>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <table>
            <tr>
                <td>Dicetak oleh</td>
                <td>: <?= esc($admin) ?></td>
                <td>Tanggal Cetak</td>
                <td>: <?= $tanggal_cetak ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: <?= count($limbah_cair) ?> records</td>
                <td>Total Timbulan</td>
                <td>: <?= number_format($total_timbulan, 2, ',', '.') ?> Liter</td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <?php if (!empty($limbah_cair)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 7%;">Tanggal</th>
                <th style="width: 12%;">Unit/Jurusan</th>
                <th style="width: 14%;">Nama Limbah</th>
                <th style="width: 8%;">Kode</th>
                <th style="width: 9%;">Timbulan</th>
                <th style="width: 5%;">Satuan</th>
                <th style="width: 4%;">pH</th>
                <th style="width: 6%;">BOD</th>
                <th style="width: 6%;">COD</th>
                <th style="width: 6%;">TSS</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($limbah_cair as $data): 
                // Determine status
                $statusClass = 'badge-draft';
                $statusText = 'Draft';
                
                switch ($data['status']) {
                    case 'dikirim_ke_tps':
                        $statusClass = 'badge-menunggu';
                        $statusText = 'Menunggu';
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
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td class="text-center"><?= date('d/m/Y', strtotime($data['tanggal_input'])) ?></td>
                <td><?= esc($data['nama_unit'] ?? '-') ?></td>
                <td><?= esc($data['nama_limbah']) ?></td>
                <td class="text-center"><?= esc($data['kode_limbah']) ?></td>
                <td class="text-right"><?= number_format($data['timbulan'], 2, ',', '.') ?></td>
                <td class="text-center"><?= esc($data['satuan']) ?></td>
                <td class="text-center"><?= $data['ph'] ?? '-' ?></td>
                <td class="text-center"><?= $data['bod'] ? number_format($data['bod'], 2, ',', '.') : '-' ?></td>
                <td class="text-center"><?= $data['cod'] ? number_format($data['cod'], 2, ',', '.') : '-' ?></td>
                <td class="text-center"><?= $data['tss'] ? number_format($data['tss'], 2, ',', '.') : '-' ?></td>
                <td class="text-center">
                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Summary Box -->
    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Total Data Limbah Cair:</strong></td>
                <td><?= count($limbah_cair) ?> records</td>
            </tr>
            <tr>
                <td><strong>Total Timbulan:</strong></td>
                <td><?= number_format($total_timbulan, 2, ',', '.') ?> Liter</td>
            </tr>
        </table>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 30px; color: #999;">
        <p>Tidak ada data limbah cair yang tersedia</p>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem UI GreenMetric Management System - Politeknik Negeri Bandung</p>
        <p>Dicetak pada: <?= $tanggal_cetak ?> oleh <?= esc($admin) ?></p>
    </div>
</body>
</html>
