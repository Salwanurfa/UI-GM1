<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .header h1 {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            color: #555;
            margin-bottom: 3px;
        }
        
        .header p {
            font-size: 10px;
            color: #777;
        }
        
        .info-section {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        
        .info-section table {
            width: 100%;
        }
        
        .info-section td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .info-section td:first-child {
            width: 120px;
            font-weight: bold;
            color: #555;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .data-table thead {
            background-color: #667eea;
            color: white;
        }
        
        .data-table th {
            padding: 8px 5px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #667eea;
        }
        
        .data-table td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .data-table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-draft {
            background-color: #ffc107;
            color: #000;
        }
        
        .status-dikirim {
            background-color: #17a2b8;
            color: white;
        }
        
        .status-disetujui {
            background-color: #28a745;
            color: white;
        }
        
        .status-ditolak {
            background-color: #dc3545;
            color: white;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #777;
        }
        
        .summary-box {
            margin-top: 15px;
            padding: 10px;
            background-color: #e7f3ff;
            border: 1px solid #667eea;
            border-radius: 5px;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 4px 8px;
            font-size: 10px;
        }
        
        .summary-box td:first-child {
            font-weight: bold;
            width: 200px;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN DATA SAMPAH</h1>
        <h2>Politeknik Negeri Bandung</h2>
        <p>UI GreenMetric Management System</p>
    </div>
    
    <!-- Info Section -->
    <div class="info-section">
        <table>
            <tr>
                <td>Nama Pelapor</td>
                <td>: <?= esc($user['nama'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td>Unit/Bagian</td>
                <td>: <?= esc($unit['nama_unit'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: <?= esc($tanggal_cetak) ?></td>
            </tr>
            <tr>
                <td>Total Data</td>
                <td>: <?= count($waste_data) ?> record</td>
            </tr>
        </table>
    </div>
    
    <!-- Data Table -->
    <?php if (!empty($waste_data)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 30px;">No</th>
                    <th style="width: 80px;">Tanggal</th>
                    <th>Jenis Sampah</th>
                    <th class="text-right" style="width: 70px;">Berat (kg)</th>
                    <th style="width: 50px;">Satuan</th>
                    <th style="width: 90px;">Kategori</th>
                    <th class="text-right" style="width: 80px;">Nilai (Rp)</th>
                    <th class="text-center" style="width: 80px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalBerat = 0;
                $totalNilai = 0;
                foreach ($waste_data as $data): 
                    $totalBerat += $data['berat_kg'];
                    $totalNilai += $data['nilai_rupiah'] ?? 0;
                    
                    // Determine status class
                    $statusClass = 'status-draft';
                    $statusText = 'Draft';
                    
                    if (strpos($data['status'], 'disetujui') !== false) {
                        $statusClass = 'status-disetujui';
                        $statusText = 'Disetujui';
                    } elseif (strpos($data['status'], 'ditolak') !== false) {
                        $statusClass = 'status-ditolak';
                        $statusText = 'Ditolak';
                    } elseif (strpos($data['status'], 'dikirim') !== false) {
                        $statusClass = 'status-dikirim';
                        $statusText = 'Dikirim';
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= date('d/m/Y', strtotime($data['tanggal'])) ?></td>
                    <td><?= esc($data['jenis_sampah']) ?></td>
                    <td class="text-right"><?= number_format($data['berat_kg'], 2, ',', '.') ?></td>
                    <td><?= esc($data['satuan']) ?></td>
                    <td><?= esc(ucwords(str_replace('_', ' ', $data['kategori_sampah']))) ?></td>
                    <td class="text-right"><?= number_format($data['nilai_rupiah'] ?? 0, 0, ',', '.') ?></td>
                    <td class="text-center">
                        <span class="status-badge <?= $statusClass ?>">
                            <?= $statusText ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Summary Box -->
        <div class="summary-box">
            <table>
                <tr>
                    <td><strong>Total Berat Sampah:</strong></td>
                    <td><?= number_format($totalBerat, 2, ',', '.') ?> kg</td>
                </tr>
                <tr>
                    <td><strong>Total Nilai Ekonomis:</strong></td>
                    <td>Rp <?= number_format($totalNilai, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td><strong>Jumlah Record:</strong></td>
                    <td><?= count($waste_data) ?> data</td>
                </tr>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>Tidak ada data sampah yang tersedia untuk dicetak.</p>
        </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem UI GreenMetric Management System - Politeknik Negeri Bandung</p>
        <p>Dicetak pada: <?= esc($tanggal_cetak) ?></p>
    </div>
</body>
</html>
