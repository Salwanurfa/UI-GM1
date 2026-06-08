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
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }
        
        .header h1 {
            font-size: 18pt;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            color: #333;
            margin-bottom: 10px;
        }
        
        .header .info {
            font-size: 8pt;
            color: #666;
        }
        
        .meta-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
        }
        
        .meta-info p {
            margin: 3px 0;
            font-size: 8pt;
        }
        
        .meta-info strong {
            color: #667eea;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 8pt;
        }
        
        table thead {
            background-color: #667eea;
            color: white;
        }
        
        table thead th {
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #5568d3;
        }
        
        table tbody td {
            padding: 6px 5px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        table tbody tr:hover {
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
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 7pt;
            color: #666;
        }
        
        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #e7f3ff;
            border-left: 4px solid #667eea;
        }
        
        .summary h3 {
            font-size: 10pt;
            color: #667eea;
            margin-bottom: 8px;
        }
        
        .summary p {
            margin: 3px 0;
            font-size: 8pt;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>POLITEKNIK NEGERI BANDUNG</h1>
        <h2><?= esc($title) ?></h2>
        <div class="info">
            Sistem Informasi UI GreenMetric
        </div>
    </div>
    
    <!-- Meta Information -->
    <div class="meta-info">
        <?php if ($start_date && $end_date): ?>
            <p><strong>Periode:</strong> <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></p>
        <?php else: ?>
            <p><strong>Periode:</strong> Hari Ini (<?= date('d/m/Y') ?>)</p>
        <?php endif; ?>
        <p><strong>Dicetak pada:</strong> <?= esc($generated_at) ?></p>
        <p><strong>Dicetak oleh:</strong> <?= esc($generated_by) ?></p>
        <p><strong>Total Data:</strong> <?= count($data) ?> baris</p>
    </div>
    
    <!-- Data Table -->
    <?php if (!empty($data)): ?>
        <?php if ($category === '3r'): ?>
            <!-- Table for Program 3R -->
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">Jenis Sampah</th>
                        <th width="18%">Nama Sampah</th>
                        <th width="12%" class="text-right">Total Berat</th>
                        <th width="8%">Satuan</th>
                        <th width="12%" class="text-right">Total Nilai</th>
                        <th width="8%" class="text-center">Transaksi</th>
                        <th width="10%" class="text-center">Status</th>
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
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_ringkas'])) ?></td>
                            <td><?= esc($row['jenis_sampah']) ?></td>
                            <td><?= esc($row['nama_sampah']) ?></td>
                            <td class="text-right"><?= number_format($row['total_berat'], 2, ',', '.') ?></td>
                            <td><?= esc($row['satuan']) ?></td>
                            <td class="text-right">Rp <?= number_format($row['total_nilai'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $row['jumlah_transaksi'] ?>x</td>
                            <td class="text-center">
                                <?php
                                $statusClass = 'badge-info';
                                if ($row['status_terakhir'] === 'disetujui') $statusClass = 'badge-success';
                                elseif ($row['status_terakhir'] === 'ditolak') $statusClass = 'badge-danger';
                                elseif ($row['status_terakhir'] === 'menunggu_review') $statusClass = 'badge-warning';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $row['status_terakhir'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #e7f3ff; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td class="text-right"><?= number_format($totalBerat, 2, ',', '.') ?></td>
                        <td>kg</td>
                        <td class="text-right">Rp <?= number_format($totalNilai, 0, ',', '.') ?></td>
                        <td class="text-center"><?= $totalTransaksi ?>x</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
        <?php elseif ($category === 'b3'): ?>
            <!-- Table for Limbah B3 -->
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="20%">Nama Limbah</th>
                        <th width="12%">Kode Limbah</th>
                        <th width="15%">Kategori Bahaya</th>
                        <th width="12%" class="text-right">Total Timbulan</th>
                        <th width="8%">Satuan</th>
                        <th width="8%" class="text-center">Transaksi</th>
                        <th width="8%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalTimbulan = 0;
                    $totalTransaksi = 0;
                    foreach ($data as $row): 
                        $totalTimbulan += $row['total_timbulan'];
                        $totalTransaksi += $row['jumlah_transaksi'];
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_ringkas'])) ?></td>
                            <td><?= esc($row['nama_limbah']) ?></td>
                            <td><?= esc($row['kode_limbah']) ?></td>
                            <td><?= esc($row['kategori_bahaya']) ?></td>
                            <td class="text-right"><?= number_format($row['total_timbulan'], 2, ',', '.') ?></td>
                            <td><?= esc($row['satuan']) ?></td>
                            <td class="text-center"><?= $row['jumlah_transaksi'] ?>x</td>
                            <td class="text-center">
                                <?php
                                $statusClass = 'badge-info';
                                if ($row['status_terakhir'] === 'disetujui') $statusClass = 'badge-success';
                                elseif ($row['status_terakhir'] === 'ditolak') $statusClass = 'badge-danger';
                                elseif ($row['status_terakhir'] === 'menunggu_review') $statusClass = 'badge-warning';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $row['status_terakhir'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #e7f3ff; font-weight: bold;">
                        <td colspan="5" class="text-right">TOTAL:</td>
                        <td class="text-right"><?= number_format($totalTimbulan, 2, ',', '.') ?></td>
                        <td>kg</td>
                        <td class="text-center"><?= $totalTransaksi ?>x</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
        <?php elseif ($category === 'cair'): ?>
            <!-- Table for Limbah Cair -->
            <table>
                <thead>
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="10%">Tanggal</th>
                        <th width="18%">Nama Limbah</th>
                        <th width="10%">Kode</th>
                        <th width="10%" class="text-right">Timbulan</th>
                        <th width="7%">Satuan</th>
                        <th width="7%" class="text-center">pH</th>
                        <th width="7%" class="text-center">BOD</th>
                        <th width="7%" class="text-center">COD</th>
                        <th width="7%" class="text-center">TSS</th>
                        <th width="7%" class="text-center">Transaksi</th>
                        <th width="5%" class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $totalTimbulan = 0;
                    $totalTransaksi = 0;
                    foreach ($data as $row): 
                        $totalTimbulan += $row['total_timbulan'];
                        $totalTransaksi += $row['jumlah_transaksi'];
                    ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_ringkas'])) ?></td>
                            <td><?= esc($row['nama_limbah']) ?></td>
                            <td><?= esc($row['kode_limbah']) ?></td>
                            <td class="text-right"><?= number_format($row['total_timbulan'], 2, ',', '.') ?></td>
                            <td><?= esc($row['satuan']) ?></td>
                            <td class="text-center"><?= $row['rata_ph'] ? number_format($row['rata_ph'], 1) : '-' ?></td>
                            <td class="text-center"><?= $row['rata_bod'] ? number_format($row['rata_bod'], 1) : '-' ?></td>
                            <td class="text-center"><?= $row['rata_cod'] ? number_format($row['rata_cod'], 1) : '-' ?></td>
                            <td class="text-center"><?= $row['rata_tss'] ? number_format($row['rata_tss'], 1) : '-' ?></td>
                            <td class="text-center"><?= $row['jumlah_transaksi'] ?>x</td>
                            <td class="text-center">
                                <?php
                                $statusClass = 'badge-info';
                                if ($row['status_terakhir'] === 'disetujui') $statusClass = 'badge-success';
                                elseif ($row['status_terakhir'] === 'ditolak') $statusClass = 'badge-danger';
                                elseif ($row['status_terakhir'] === 'menunggu_review') $statusClass = 'badge-warning';
                                ?>
                                <span class="badge <?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $row['status_terakhir'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background-color: #e7f3ff; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td class="text-right"><?= number_format($totalTimbulan, 2, ',', '.') ?></td>
                        <td>L</td>
                        <td colspan="4"></td>
                        <td class="text-center"><?= $totalTransaksi ?>x</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        
        <!-- Summary -->
        <div class="summary">
            <h3>Ringkasan</h3>
            <p>Total baris data: <strong><?= count($data) ?></strong></p>
            <?php if ($category === '3r'): ?>
                <p>Total berat sampah: <strong><?= number_format($totalBerat, 2, ',', '.') ?> kg</strong></p>
                <p>Total nilai ekonomis: <strong>Rp <?= number_format($totalNilai, 0, ',', '.') ?></strong></p>
            <?php else: ?>
                <p>Total timbulan: <strong><?= number_format($totalTimbulan, 2, ',', '.') ?> <?= $category === 'cair' ? 'L' : 'kg' ?></strong></p>
            <?php endif; ?>
            <p>Total transaksi: <strong><?= $totalTransaksi ?> transaksi</strong></p>
        </div>
        
    <?php else: ?>
        <div class="no-data">
            <p>Tidak ada data untuk periode yang dipilih</p>
        </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis dari Sistem Informasi UI GreenMetric POLBAN</p>
        <p>Dicetak pada: <?= esc($generated_at) ?> oleh <?= esc($generated_by) ?></p>
    </div>
</body>
</html>
