<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PENCATATAN PENGELOLAAN LIMBAH CAIR (LOG BOOK)</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 10mm;
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
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .header .info {
            font-size: 9pt;
            margin-top: 5px;
        }
        
        .meta-info {
            margin-bottom: 10px;
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
            font-size: 8pt;
        }
        
        table.data-table th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: top;
        }
        
        table.data-table td.center {
            text-align: center;
        }
        
        table.data-table td.right {
            text-align: right;
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        
        .signature-table td {
            width: 50%;
            padding: 10px;
            vertical-align: top;
            border: none;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-box .space {
            height: 60px;
            margin: 10px 0;
        }
        
        .signature-box .name {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .signature-box .nip {
            font-size: 9pt;
            margin-top: 3px;
        }
        
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            font-style: italic;
            color: #666;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>POLITEKNIK NEGERI BANDUNG</h1>
        <h2>PENCATATAN PENGELOLAAN LIMBAH CAIR (LOG BOOK)</h2>
        <div class="info">
            Jl. Gegerkalong Hilir, Ciwaruga, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat 40559
        </div>
    </div>
    
    <!-- Meta Information -->
    <div class="meta-info">
        <table>
            <tr>
                <td>Periode Pencatatan</td>
                <td>: <?php if ($start_date && $end_date): ?>
                        <?= date('d F Y', strtotime($start_date)) ?> s/d <?= date('d F Y', strtotime($end_date)) ?>
                    <?php else: ?>
                        <?= date('d F Y') ?> (Hari Ini)
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: <?= date('d F Y, H:i') ?> WIB</td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td>
                <td>: <?= esc($generated_by) ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Data Table -->
    <?php if (!empty($data)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 3%;">No</th>
                    <th rowspan="2" style="width: 7%;">Tanggal</th>
                    <th rowspan="2" style="width: 12%;">Jenis Limbah Cair</th>
                    <th rowspan="2" style="width: 8%;">Kode Limbah</th>
                    <th rowspan="2" style="width: 10%;">Sumber/Unit</th>
                    <th rowspan="2" style="width: 8%;">Volume (Liter)</th>
                    <th colspan="4">Parameter Kualitas</th>
                    <th rowspan="2" style="width: 6%;">Jumlah Transaksi</th>
                    <th rowspan="2" style="width: 10%;">Keterangan</th>
                </tr>
                <tr>
                    <th style="width: 6%;">pH</th>
                    <th style="width: 6%;">BOD (mg/L)</th>
                    <th style="width: 6%;">COD (mg/L)</th>
                    <th style="width: 6%;">TSS (mg/L)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalVolume = 0;
                $totalTransaksi = 0;
                foreach ($data as $row): 
                    $totalVolume += $row['total_timbulan'];
                    $totalTransaksi += $row['jumlah_transaksi'];
                ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td class="center"><?= date('d/m/Y', strtotime($row['tanggal_ringkas'])) ?></td>
                        <td><?= esc($row['nama_limbah']) ?></td>
                        <td class="center"><?= esc($row['kode_limbah']) ?></td>
                        <td><?= esc($row['nama_units'] ?? 'Unit Kerja') ?></td>
                        <td class="right"><?= number_format($row['total_timbulan'], 2, ',', '.') ?></td>
                        <td class="center"><?= $row['rata_ph'] ? number_format($row['rata_ph'], 1) : '-' ?></td>
                        <td class="center"><?= $row['rata_bod'] ? number_format($row['rata_bod'], 1) : '-' ?></td>
                        <td class="center"><?= $row['rata_cod'] ? number_format($row['rata_cod'], 1) : '-' ?></td>
                        <td class="center"><?= $row['rata_tss'] ? number_format($row['rata_tss'], 1) : '-' ?></td>
                        <td class="center"><?= $row['jumlah_transaksi'] ?>x</td>
                        <td>
                            <?php
                            $status = $row['status_terakhir'];
                            if ($status === 'disetujui' || $status === 'disetujui_admin') {
                                echo 'Disetujui';
                            } elseif ($status === 'menunggu_review') {
                                echo 'Menunggu Review';
                            } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                echo 'Dikirim ke TPS';
                            } elseif ($status === 'draft') {
                                echo 'Draft';
                            } else {
                                echo ucfirst(str_replace('_', ' ', $status));
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Total Row -->
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td colspan="5" class="right">TOTAL:</td>
                    <td class="right"><?= number_format($totalVolume, 2, ',', '.') ?></td>
                    <td colspan="4" class="center">-</td>
                    <td class="center"><?= $totalTransaksi ?>x</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Catatan -->
        <div style="margin-bottom: 15px; font-size: 9pt;">
            <strong>Catatan:</strong>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li>Data yang ditampilkan merupakan akumulasi harian dari transaksi limbah cair</li>
                <li>Parameter kualitas (pH, BOD, COD, TSS) merupakan nilai rata-rata dari seluruh transaksi</li>
                <li>Baku mutu limbah cair mengacu pada Peraturan Menteri Lingkungan Hidup</li>
                <li>Limbah cair harus diolah terlebih dahulu sebelum dibuang ke lingkungan</li>
            </ul>
        </div>
        
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 7%;">Tanggal</th>
                    <th style="width: 12%;">Jenis Limbah</th>
                    <th style="width: 8%;">Kode</th>
                    <th style="width: 10%;">Sumber</th>
                    <th style="width: 8%;">Volume (L)</th>
                    <th style="width: 6%;">pH</th>
                    <th style="width: 6%;">BOD</th>
                    <th style="width: 6%;">COD</th>
                    <th style="width: 6%;">TSS</th>
                    <th style="width: 6%;">Transaksi</th>
                    <th style="width: 10%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="12" class="no-data">
                        Tidak ada data limbah cair untuk periode yang dipilih
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
                        <div class="title">Kepala Unit Pengelola Limbah Cair</div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                        <div class="nip">NIP. ...................................</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div class="title">Petugas Pencatat,</div>
                        <div class="title">&nbsp;</div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
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
