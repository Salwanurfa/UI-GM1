<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pencatatan Keluar Masuk Kendaraan</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h2>Politeknik Negeri Bandung</h2>
        <h3>Pencatatan Keluar Masuk Kendaraan (Log Harian)</h3>
        <p>Jl. Gegerkalong Hilir, Ciwaruga, Kec. Parongpong, Kabupaten Bandung Barat, Jawa Barat 40559</p>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 18%;">Periode Laporan</td>
            <td style="width: 2%;">:</td>
            <td>
                <?php if (!empty($start_date) && !empty($end_date)): ?>
                    <?= date('d M Y', strtotime($start_date)) ?> s/d <?= date('d M Y', strtotime($end_date)) ?>
                <?php else: ?>
                    Semua Data Pencatatan
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Tanggal Cetak</td>
            <td>:</td>
            <td><?= date('d M Y, H:i') ?> WIB</td>
        </tr>
        <tr>
            <td>Petugas Penginput</td>
            <td>:</td>
            <td><?= esc($security['nama_lengkap'] ?? 'Security Gate') ?></td>
        </tr>
    </table>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 25%;">Jenis Kendaraan</th>
                <th style="width: 15%;">Jumlah Masuk</th>
                <th style="width: 15%;">Jumlah Keluar</th>
                <th style="width: 20%;">Total Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php 
                $no = 1; 
                $tot_masuk = 0; 
                $tot_keluar = 0; 
                $tot_aktivitas = 0; 
                ?>
                <?php foreach ($logs as $row): ?>
                    <?php 
                    $tot_masuk += $row['jumlah_masuk'];
                    $tot_keluar += $row['jumlah_keluar'];
                    $tot_aktivitas += ($row['jumlah_masuk'] + $row['jumlah_keluar']);
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= esc($row['jenis_kendaraan']) ?></td>
                        <td class="text-center"><?= $row['jumlah_masuk'] ?></td>
                        <td class="text-center"><?= $row['jumlah_keluar'] ?></td>
                        <td class="text-center"><?= ($row['jumlah_masuk'] + $row['jumlah_keluar']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="3" class="text-right font-bold">TOTAL AKUMULASI:</td>
                    <td class="text-center font-bold"><?= $tot_masuk ?></td>
                    <td class="text-center font-bold"><?= $tot_keluar ?></td>
                    <td class="text-center font-bold"><?= $tot_aktivitas ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Belum ada data pencatatan kendaraan.</td>
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
                <p class="font-bold">Petugas Lapangan,</p>
                <p class="font-bold">Security</p>
                <div class="signature-space"></div>
                <p>( ___________________________ )</p>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen Laporan Keluar Masuk Kendaraan - UI GreenMetric POLBAN
    </div>
</body>
</html>
