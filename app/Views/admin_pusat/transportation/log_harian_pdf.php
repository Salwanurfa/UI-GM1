<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PENCATATAN KELUAR MASUK KENDARAAN (LOG HARIAN)</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.5cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
        }
        
        .header h1 {
            font-size: 15pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .header h2 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .header .info {
            font-size: 9pt;
            margin-top: 6px;
            line-height: 1.4;
        }
        
        .meta-info {
            margin-bottom: 12px;
            font-size: 9pt;
        }
        
        .meta-info table {
            width: 100%;
            border: none;
        }
        
        .meta-info td {
            padding: 2px 0;
            border: none;
        }
        
        .meta-info td:first-child {
            width: 150px;
            font-weight: bold;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        
        table.data-table th {
            background-color: #e8e8e8;
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            line-height: 1.3;
        }
        
        table.data-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            vertical-align: middle;
            line-height: 1.3;
        }
        
        table.data-table td.center {
            text-align: center;
        }
        
        table.data-table td.right {
            text-align: right;
            padding-right: 10px;
        }
        
        table.data-table tr.total-row {
            font-weight: bold;
            background-color: #f5f5f5;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        table.data-table tr.total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 6px 8px;
        }
        
        .signature-section {
            margin-top: 35px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        
        .signature-table td {
            width: 50%;
            padding: 0;
            vertical-align: top;
            border: none;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 5px;
            line-height: 1.4;
        }
        
        .signature-box .space {
            height: 70px;
            margin: 12px 0;
        }
        
        .signature-box .name {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 220px;
            padding-top: 5px;
            margin-top: 5px;
            font-weight: normal;
        }
        
        .signature-box .nip {
            font-size: 9pt;
            margin-top: 4px;
            font-weight: normal;
        }
        
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #333;
            font-size: 8pt;
            text-align: center;
            color: #555;
            line-height: 1.4;
        }
        
        .no-data {
            text-align: center;
            padding: 35px;
            font-style: italic;
            color: #666;
        }
        
        /* Print-specific styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>POLITEKNIK NEGERI BANDUNG</h1>
        <h2>PENCATATAN KELUAR MASUK KENDARAAN (LOG HARIAN)</h2>
        <div class="info">
            Jl. Gegerkalong Hilir, Ciwaruga, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat 40559
        </div>
    </div>
    
    <!-- Meta Information -->
    <div class="meta-info">
        <table>
            <tr>
                <td>Periode Pencatatan</td>
                <td>: <?php if (!empty($start_date) && !empty($end_date)): ?>
                        <?= date('d F Y', strtotime($start_date)) ?> s/d <?= date('d F Y', strtotime($end_date)) ?>
                    <?php else: ?>
                        Semua Data
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: <?= date('d F Y, H:i') ?> WIB</td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td>
                <td>: <?= esc($admin['nama'] ?? $admin['username'] ?? 'Admin') ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Data Table -->
    <?php if (!empty($logs)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 15%;">Jenis Kendaraan</th>
                    <th style="width: 12%;">Jumlah Masuk</th>
                    <th style="width: 12%;">Jumlah Keluar</th>
                    <th style="width: 13%;">Total Aktivitas</th>
                    <th style="width: 33%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalMasuk = 0;
                $totalKeluar = 0;
                $totalAktivitas = 0;
                foreach ($logs as $log): 
                    $aktivitas = $log['jumlah_masuk'] + $log['jumlah_keluar'];
                    $totalMasuk += $log['jumlah_masuk'];
                    $totalKeluar += $log['jumlah_keluar'];
                    $totalAktivitas += $aktivitas;
                ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td class="center"><?= date('d/m/Y', strtotime($log['tanggal'])) ?></td>
                        <td><?= esc($log['jenis_kendaraan']) ?></td>
                        <td class="right"><?= number_format($log['jumlah_masuk'], 0, ',', '.') ?></td>
                        <td class="right"><?= number_format($log['jumlah_keluar'], 0, ',', '.') ?></td>
                        <td class="right"><?= number_format($aktivitas, 0, ',', '.') ?></td>
                        <td><?= esc($log['keterangan'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="3" class="right" style="padding-right: 10px;">TOTAL:</td>
                    <td class="right"><?= number_format($totalMasuk, 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($totalKeluar, 0, ',', '.') ?></td>
                    <td class="right"><?= number_format($totalAktivitas, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 11%;">Tanggal</th>
                    <th style="width: 15%;">Jenis Kendaraan</th>
                    <th style="width: 12%;">Jumlah Masuk</th>
                    <th style="width: 12%;">Jumlah Keluar</th>
                    <th style="width: 13%;">Total Aktivitas</th>
                    <th style="width: 33%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="no-data">
                        Tidak ada data kendaraan untuk periode yang dipilih
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        <div class="title">Mengetahui,</div>
                        <div class="title">Kepala Unit Pengelola Transportasi</div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                        <div class="nip">NIP. ...................................</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div class="title">Petugas Pencatat,</div>
                        <div class="title"><?= esc($admin['nama'] ?? $admin['username'] ?? 'Admin') ?></div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                        <div class="nip">NIP. ...................................</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Informasi UI GreenMetric POLBAN</p>
        <p>Dicetak pada: <?= date('d F Y, H:i:s') ?> WIB</p>
    </div>
</body>
</html>
