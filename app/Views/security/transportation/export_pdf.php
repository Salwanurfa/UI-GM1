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
            <td><?= $export_date ?> WIB</td>
        </tr>
        <tr>
            <td>Petugas Security</td>
            <td>:</td>
            <td><?= esc($security['nama_lengkap'] ?? 'N/A') ?> (<?= esc($security['username'] ?? 'N/A') ?>)</td>
        </tr>
        <tr>
            <td>Total Data</td>
            <td>:</td>
            <td><?= number_format($summary['total_records']) ?> record</td>
        </tr>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td style="width: 30%;" class="font-bold">Total Jumlah Kendaraan:</td>
                <td class="font-bold"><?= number_format($summary['total_kendaraan']) ?> unit</td>
            </tr>
            <tr>
                <td class="font-bold">Total Data ZEV (Zero Emission):</td>
                <td class="font-bold"><?= number_format($summary['total_zev']) ?> record</td>
            </tr>
            <tr>
                <td class="font-bold">Total Data Shuttle Kampus:</td>
                <td class="font-bold"><?= number_format($summary['total_shuttle']) ?> record</td>
            </tr>
        </table>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 20%;">Jenis Kendaraan</th>
                <th style="width: 14%;">Bahan Bakar</th>
                <th style="width: 10%;">Jumlah</th>
                <th style="width: 8%;">ZEV</th>
                <th style="width: 8%;">Shuttle</th>
                <th style="width: 12%;">Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($records)): ?>
                <?php $no = 1; ?>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center">
                            <?php if ($record['periode'] === 'Harian' && !empty($record['tanggal_pencatatan'])): ?>
                                <?= date('d/m/Y', strtotime($record['tanggal_pencatatan'])) ?>
                            <?php elseif ($record['periode'] === 'Mingguan (Back-up)'): ?>
                                <?= date('d/m/Y', strtotime($record['tanggal_mulai'])) ?> - 
                                <?= date('d/m/Y', strtotime($record['tanggal_selesai'])) ?>
                            <?php elseif ($record['periode'] === 'Bulanan (Back-up)'): ?>
                                <?= esc($record['bulan'] ?? '-') ?> <?= esc($record['tahun'] ?? '') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= esc($record['kategori_kendaraan'] ?? 'Tidak Diketahui') ?></td>
                        <td><?= esc($record['jenis_bahan_bakar']) ?></td>
                        <td class="text-center font-bold"><?= number_format($record['jumlah_total']) ?></td>
                        <td class="text-center"><?= $record['is_zev'] == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td class="text-center"><?= $record['is_shuttle'] == 1 ? 'Ya' : 'Tidak' ?></td>
                        <td class="text-center"><?= date('d/m/Y H:i', strtotime($record['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="4" class="text-right font-bold">TOTAL KENDARAAN:</td>
                    <td class="text-center font-bold"><?= number_format($summary['total_kendaraan']) ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Belum ada data statistik transportasi.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

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
                <p class="font-bold"><?= esc($security['nama_lengkap'] ?? 'Security') ?></p>
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
