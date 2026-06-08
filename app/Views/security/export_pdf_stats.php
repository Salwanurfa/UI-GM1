<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Statistik Transportasi Kampus</title>
    <style>
        /* Atur ukuran kertas dan margin keliling yang longgar agar tidak terlalu ke pinggir */
        @page {
            size: A4 landscape;
            margin: 45px 50px 45px 50px;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 11pt;
            margin: 0;
            padding: 0;
        }
        
        /* Desain KOP Surat Terpusat */
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h2 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .header h3 {
            margin: 5px 0 0 0;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0 0 0;
            font-size: 9pt;
            color: #555;
        }
        
        /* Tabel Informasi Dokumen */
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        /* Tabel Utama Data Kendaraan (Lebar dikunci 100% dari ruang halaman) */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        
        .report-table th {
            background-color: #f2f2f2;
            border: 1px solid #111;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
        }
        
        .report-table td {
            border: 1px solid #111;
            padding: 7px 6px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        /* Ruang Tanda Tangan Lapangan */
        .signature-container {
            width: 100%;
            margin-top: 40px;
            font-size: 10pt;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-space {
            height: 65px;
        }
        
        .footer-note {
            text-align: center;
            font-size: 8pt;
            color: #777;
            margin-top: 40px;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
        }
        
        /* Summary Box */
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #111;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        
        .summary-box table {
            width: 100%;
        }
        
        .summary-box td {
            padding: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Politeknik Negeri Bandung</h2>
        <h3>Laporan Statistik Transportasi Kampus</h3>
        <p>Jl. Gegerkalong Hilir, Ciwaruga, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat 40559</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;">Periode Laporan</td>
            <td style="width: 2%;">:</td>
            <td>Semua Data Statistik Transportasi</td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td><?= $generated_at ?> WIB</td>
        </tr>
        <tr>
            <td>Petugas Security</td>
            <td>:</td>
            <td><?= esc($user['nama_lengkap'] ?? 'N/A') ?> (<?= esc($user['username'] ?? 'N/A') ?>)</td>
        </tr>
        <tr>
            <td>Total Data</td>
            <td>:</td>
            <td><?= count($data) ?> record</td>
        </tr>
    </table>

    <?php if (!empty($data)): ?>
        <?php
        // Calculate summary statistics
        $totalKendaraan = 0;
        $totalZEV = 0;
        
        foreach ($data as $row) {
            $totalKendaraan += $row['jumlah_total'];
            if ($row['is_zev'] == 1) {
                $totalZEV += $row['jumlah_total'];
            }
        }
        ?>

        <div class="summary-box">
            <table>
                <tr>
                    <td style="width: 30%;" class="font-bold">Total Jumlah Kendaraan:</td>
                    <td class="font-bold"><?= number_format($totalKendaraan) ?> unit</td>
                </tr>
                <tr>
                    <td class="font-bold">Total Kendaraan ZEV (Zero Emission):</td>
                    <td class="font-bold"><?= number_format($totalZEV) ?> unit (<?= $totalKendaraan > 0 ? number_format(($totalZEV / $totalKendaraan) * 100, 1) : 0 ?>%)</td>
                </tr>
                <tr>
                    <td class="font-bold">Total Record Data:</td>
                    <td class="font-bold"><?= count($data) ?> record</td>
                </tr>
            </table>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 22%;">Jenis Kendaraan</th>
                    <th style="width: 16%;">Bahan Bakar</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 8%;">ZEV</th>
                    <th style="width: 8%;">Shuttle</th>
                    <th style="width: 12%;">Tanggal Input</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center">
                            <?php if ($row['periode'] === 'Harian' && isset($row['tanggal_pencatatan']) && $row['tanggal_pencatatan']): ?>
                                <?= date('d/m/Y', strtotime($row['tanggal_pencatatan'])) ?>
                            <?php elseif ($row['periode'] === 'Mingguan (Back-up)'): ?>
                                <?= isset($row['tanggal_mulai']) && $row['tanggal_mulai'] ? date('d/m/Y', strtotime($row['tanggal_mulai'])) : '-' ?>
                                →
                                <?= isset($row['tanggal_selesai']) && $row['tanggal_selesai'] ? date('d/m/Y', strtotime($row['tanggal_selesai'])) : '-' ?>
                            <?php elseif ($row['periode'] === 'Bulanan (Back-up)'): ?>
                                <?= isset($row['bulan']) ? esc($row['bulan']) : '-' ?>
                                <?= isset($row['tahun']) ? esc($row['tahun']) : '' ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= esc($row['kategori_kendaraan'] ?? 'Tidak Diketahui') ?></td>
                        <td><?= esc($row['jenis_bahan_bakar']) ?></td>
                        <td class="text-center font-bold"><?= number_format($row['jumlah_total']) ?></td>
                        <td class="text-center"><?= $row['is_zev'] == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td class="text-center"><?= $row['is_shuttle'] == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="4" class="text-right font-bold">TOTAL KENDARAAN:</td>
                    <td class="text-center font-bold"><?= number_format($totalKendaraan) ?></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>

    <?php else: ?>
        <table class="report-table">
            <tbody>
                <tr>
                    <td class="text-center">Belum ada data statistik transportasi.</td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>

    <table class="signature-container">
        <tr>
            <td class="signature-box">
                <p class="font-bold">Mengetahui,</p>
                <p class="font-bold">Komandan Security</p>
                <div class="signature-space"></div>
                <p>( ___________________________ )</p>
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <p class="font-bold">Petugas Security,</p>
                <p class="font-bold"><?= esc($user['nama_lengkap'] ?? 'Security') ?></p>
                <div class="signature-space"></div>
                <p>( ___________________________ )</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen Laporan Statistik Transportasi - UI GreenMetric POLBAN
    </div>
</body>
</html>
