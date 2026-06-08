<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PENCATATAN PROGRAM 3R (REDUCE, REUSE, RECYCLE)</title>
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
            font-size: 9pt;
        }
        
        table.data-table th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }
        
        table.data-table td {
            border: 1px solid #000;
            padding: 5px 4px;
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
        <h2>PENCATATAN PROGRAM 3R (REDUCE, REUSE, RECYCLE)</h2>
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
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Tanggal</th>
                    <th style="width: 12%;">Jenis Sampah</th>
                    <th style="width: 15%;">Nama Sampah</th>
                    <th style="width: 15%;">Sumber/Unit</th>
                    <th style="width: 10%;">Berat (kg)</th>
                    <th style="width: 6%;">Satuan</th>
                    <th style="width: 12%;">Nilai Ekonomis (Rp)</th>
                    <th style="width: 7%;">Jumlah Transaksi</th>
                    <th style="width: 12%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalBerat = 0;
                $totalNilai = 0;
                $totalTransaksi = 0;
                foreach ($data as $row): 
                    $totalBerat += $row['total_berat'];
                    $totalNilai += $row['total_nilai'];
                    $totalTransaksi += $row['jumlah_transaksi'];
                ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td class="center"><?= date('d/m/Y', strtotime($row['tanggal_ringkas'])) ?></td>
                        <td><?= esc($row['jenis_sampah']) ?></td>
                        <td><?= esc($row['nama_sampah']) ?></td>
                        <td><?= esc($row['nama_units'] ?? 'Unit Kerja') ?></td>
                        <td class="right"><?= number_format($row['total_berat'], 2, ',', '.') ?></td>
                        <td class="center"><?= esc($row['satuan']) ?></td>
                        <td class="right"><?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
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
                    <td class="right"><?= number_format($totalBerat, 2, ',', '.') ?></td>
                    <td class="center">kg</td>
                    <td class="right"><?= number_format($totalNilai, 0, ',', '.') ?></td>
                    <td class="center"><?= $totalTransaksi ?>x</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <!-- Catatan -->
        <div style="margin-bottom: 15px; font-size: 9pt;">
            <strong>Catatan:</strong>
            <ul style="margin-left: 20px; margin-top: 5px;">
                <li>Data yang ditampilkan merupakan akumulasi harian dari transaksi sampah 3R</li>
                <li>Nilai ekonomis dihitung berdasarkan harga jual sampah per kilogram</li>
                <li>Program 3R bertujuan mengurangi, menggunakan kembali, dan mendaur ulang sampah</li>
            </ul>
        </div>
        
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Tanggal</th>
                    <th style="width: 12%;">Jenis Sampah</th>
                    <th style="width: 15%;">Nama Sampah</th>
                    <th style="width: 15%;">Sumber/Unit</th>
                    <th style="width: 10%;">Berat (kg)</th>
                    <th style="width: 6%;">Satuan</th>
                    <th style="width: 12%;">Nilai Ekonomis (Rp)</th>
                    <th style="width: 7%;">Transaksi</th>
                    <th style="width: 12%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="10" class="no-data">
                        Tidak ada data Program 3R untuk periode yang dipilih
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
                        <div class="title">Kepala Unit Pengelola Sampah</div>
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
