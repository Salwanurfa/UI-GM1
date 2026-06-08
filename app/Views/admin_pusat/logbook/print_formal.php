<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Page Setup - LANDSCAPE MODE */
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            padding: 5mm;
            background: #fff;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 2px double #000;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .header .info {
            font-size: 9pt;
            margin-top: 2px;
        }
        
        /* Meta Information */
        .meta-info {
            margin-bottom: 8px;
            font-size: 9pt;
        }
        
        .meta-info table {
            width: 100%;
            border: none;
        }
        
        .meta-info td {
            padding: 1px 0;
            border: none;
        }
        
        .meta-info td:first-child {
            width: 140px;
            font-weight: bold;
        }
        
        /* Data Table - FIXED LAYOUT */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 9pt;
            table-layout: fixed;
        }
        
        table.data-table th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            vertical-align: top;
            word-wrap: break-word;
        }
        
        table.data-table td.center {
            text-align: center;
        }
        
        table.data-table td.right {
            text-align: right;
        }
        
        table.data-table tr.total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        /* Signature Section */
        .signature-section {
            margin-top: 12px;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        
        .signature-table td {
            width: 50%;
            padding: 5px 15px;
            vertical-align: top;
            border: none;
        }
        
        .signature-table td:first-child {
            text-align: left;
        }
        
        .signature-table td:last-child {
            text-align: right;
        }
        
        .signature-box .title {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .signature-box .space {
            height: 45px;
            margin: 5px 0;
        }
        
        .signature-box .name {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 170px;
            padding-top: 2px;
            margin-top: 2px;
        }
        
        .signature-box .nip {
            font-size: 8pt;
            margin-top: 2px;
        }
        
        /* Footer */
        .footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #000;
            font-size: 7pt;
            text-align: center;
            color: #666;
        }
        
        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
            font-size: 10pt;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .no-print, .print-button {
                display: none !important;
            }
            
            table.data-table th {
                background-color: #e0e0e0 !important;
                -webkit-print-color-adjust: exact;
            }
            
            table.data-table tr.total-row {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
            }
            
            table.data-table tr {
                page-break-inside: avoid;
            }
        }
        
        /* Screen Only - Print Button */
        @media screen {
            .print-button {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 24px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
                font-weight: bold;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 1000;
            }
            
            .print-button:hover {
                background: #5568d3;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Print Button (Screen Only) -->
    <button class="print-button no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Dokumen
    </button>

    <!-- Header -->
    <div class="header">
        <h1>POLITEKNIK NEGERI BANDUNG</h1>
        <h2><?= esc($title) ?></h2>
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
        <?php if ($category === '3r'): ?>
            <!-- TABEL PROGRAM 3R - TANPA CHECKBOX & TRANSAKSI -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 13%;">Jenis Sampah</th>
                        <th style="width: 18%;">Nama Sampah</th>
                        <th style="width: 15%;">Sumber/Unit</th>
                        <th style="width: 10%;">Berat (kg)</th>
                        <th style="width: 7%;">Satuan</th>
                        <th style="width: 13%;">Nilai Ekonomis (Rp)</th>
                        <th style="width: 10%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalBerat = 0;
                    $totalNilai = 0;
                    foreach ($data as $row): 
                        $totalBerat += $row['berat_kg'];
                        $totalNilai += $row['nilai_rupiah'];
                    ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td class="center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= esc($row['jenis_sampah']) ?></td>
                            <td><?= esc($row['nama_sampah']) ?></td>
                            <td><?= esc($row['nama_unit'] ?? 'N/A') ?></td>
                            <td class="right"><?= number_format($row['berat_kg'], 2, ',', '.') ?></td>
                            <td class="center"><?= esc($row['satuan']) ?></td>
                            <td class="right"><?= number_format($row['nilai_rupiah'], 0, ',', '.') ?></td>
                            <td class="center">
                                <?php
                                $status = $row['status'] ?? 'dikirim_ke_tps';
                                if ($status === 'disetujui' || $status === 'disetujui_admin') {
                                    echo 'Disetujui';
                                } elseif ($status === 'ditolak') {
                                    echo 'Ditolak';
                                } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                    echo 'Dikirim ke TPS';
                                } elseif ($status === 'menunggu_review') {
                                    echo 'Menunggu';
                                } else {
                                    echo ucfirst(str_replace('_', ' ', $status));
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <!-- Total Row -->
                    <tr class="total-row">
                        <td colspan="5" class="right">TOTAL:</td>
                        <td class="right"><?= number_format($totalBerat, 2, ',', '.') ?></td>
                        <td class="center">kg</td>
                        <td class="right"><?= number_format($totalNilai, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            
        <?php elseif ($category === 'b3'): ?>
            <!-- TABEL LIMBAH B3 - TANPA CHECKBOX & TRANSAKSI -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 10%;">Tanggal</th>
                        <th style="width: 20%;">Nama Limbah</th>
                        <th style="width: 10%;">Kode</th>
                        <th style="width: 12%;">Kategori Bahaya</th>
                        <th style="width: 12%;">Sumber/Unit</th>
                        <th style="width: 10%;">Timbulan (kg)</th>
                        <th style="width: 10%;">Lokasi</th>
                        <th style="width: 12%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalMasuk = 0;
                    foreach ($data as $row): 
                        $totalMasuk += $row['timbulan'];
                    ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td class="center"><?= date('d/m/Y', strtotime($row['tanggal_input'])) ?></td>
                            <td><?= esc($row['nama_limbah']) ?></td>
                            <td class="center"><?= esc($row['kode_limbah']) ?></td>
                            <td><?= esc($row['kategori_bahaya'] ?? '-') ?></td>
                            <td><?= esc($row['nama_unit'] ?? 'N/A') ?></td>
                            <td class="right"><?= number_format($row['timbulan'], 2, ',', '.') ?></td>
                            <td><?= esc($row['lokasi'] ?? '-') ?></td>
                            <td class="center">
                                <?php
                                $status = $row['status'] ?? 'dikirim_ke_tps';
                                if ($status === 'disetujui_admin' || $status === 'disetujui') {
                                    echo 'Disetujui';
                                } elseif ($status === 'ditolak_admin' || $status === 'ditolak') {
                                    echo 'Ditolak';
                                } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                    echo 'Dikirim ke TPS';
                                } elseif ($status === 'menunggu_review') {
                                    echo 'Menunggu';
                                } else {
                                    echo ucfirst(str_replace('_', ' ', $status));
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <!-- Total Row -->
                    <tr class="total-row">
                        <td colspan="6" class="right">TOTAL:</td>
                        <td class="right"><?= number_format($totalMasuk, 2, ',', '.') ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
            
        <?php else: // cair ?>
            <!-- TABEL LIMBAH CAIR - TANPA CHECKBOX & TRANSAKSI -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 9%;">Tanggal</th>
                        <th style="width: 16%;">Nama Limbah</th>
                        <th style="width: 9%;">Kode</th>
                        <th style="width: 12%;">Sumber/Unit</th>
                        <th style="width: 9%;">Volume (L)</th>
                        <th style="width: 7%;">pH</th>
                        <th style="width: 8%;">BOD</th>
                        <th style="width: 8%;">COD</th>
                        <th style="width: 8%;">TSS</th>
                        <th style="width: 10%;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalVolume = 0;
                    foreach ($data as $row): 
                        $totalVolume += $row['timbulan'];
                    ?>
                        <tr>
                            <td class="center"><?= $no++ ?></td>
                            <td class="center"><?= date('d/m/Y', strtotime($row['tanggal_input'])) ?></td>
                            <td><?= esc($row['nama_limbah']) ?></td>
                            <td class="center"><?= esc($row['kode_limbah']) ?></td>
                            <td><?= esc($row['nama_unit'] ?? 'N/A') ?></td>
                            <td class="right"><?= number_format($row['timbulan'], 2, ',', '.') ?></td>
                            <td class="center"><?= $row['ph'] ? number_format($row['ph'], 1) : '-' ?></td>
                            <td class="center"><?= $row['bod'] ? number_format($row['bod'], 1) : '-' ?></td>
                            <td class="center"><?= $row['cod'] ? number_format($row['cod'], 1) : '-' ?></td>
                            <td class="center"><?= $row['tss'] ? number_format($row['tss'], 1) : '-' ?></td>
                            <td class="center">
                                <?php
                                $status = $row['status'] ?? 'dikirim_ke_tps';
                                if ($status === 'disetujui_admin' || $status === 'disetujui') {
                                    echo 'Disetujui';
                                } elseif ($status === 'ditolak') {
                                    echo 'Ditolak';
                                } elseif ($status === 'dikirim_ke_tps' || $status === 'dikirim') {
                                    echo 'Dikirim ke TPS';
                                } elseif ($status === 'menunggu_review') {
                                    echo 'Menunggu';
                                } else {
                                    echo ucfirst(str_replace('_', ' ', $status));
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <!-- Total Row -->
                    <tr class="total-row">
                        <td colspan="5" class="right">TOTAL:</td>
                        <td class="right"><?= number_format($totalVolume, 2, ',', '.') ?></td>
                        <td colspan="5" class="center">-</td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="no-data">
            Tidak ada data untuk periode yang dipilih
        </div>
    <?php endif; ?>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-box">
                        <div class="title">Mengetahui,</div>
                        <div class="title">Kepala Unit Pengelola</div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                        <div class="nip">NIP. ........................</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div class="title">Petugas Pencatat,</div>
                        <div class="title">&nbsp;</div>
                        <div class="space"></div>
                        <div class="name">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</div>
                        <div class="nip">NIP. ........................</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak dari Sistem Informasi UI GreenMetric POLBAN | Dicetak: <?= date('d F Y, H:i:s') ?> WIB</p>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
