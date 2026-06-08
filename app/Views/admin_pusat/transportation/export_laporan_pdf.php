<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Transportasi Kampus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            color: #007bff;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            color: #333;
            margin-bottom: 3px;
            font-weight: normal;
        }
        
        .header p {
            font-size: 10px;
            color: #666;
            margin: 2px 0;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
            border-radius: 3px;
        }
        
        .info-box p {
            margin: 3px 0;
            font-size: 10px;
        }
        
        .info-box strong {
            color: #007bff;
        }
        
        .section-title {
            background: #007bff;
            color: white;
            padding: 8px 10px;
            margin: 20px 0 10px 0;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 3px;
        }
        
        .section-title.green {
            background: #28a745;
        }
        
        .section-title.cyan {
            background: #17a2b8;
        }
        
        .section-title.orange {
            background: #fd7e14;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .summary-item {
            display: table-row;
        }
        
        .summary-item div {
            display: table-cell;
            padding: 8px;
            border: 1px solid #dee2e6;
        }
        
        .summary-item .label {
            background: #f8f9fa;
            font-weight: bold;
            width: 40%;
            color: #495057;
        }
        
        .summary-item .value {
            background: white;
            color: #007bff;
            font-weight: bold;
            font-size: 13px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        table thead {
            background: #007bff;
            color: white;
        }
        
        table thead th {
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #0056b3;
        }
        
        table tbody td {
            padding: 6px 8px;
            border: 1px solid #dee2e6;
            font-size: 10px;
        }
        
        table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        table tbody tr:hover {
            background: #e9ecef;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        
        .badge-success {
            background: #28a745;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #333;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-style: italic;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .section-title {
                page-break-after: avoid;
            }
            
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Data Transportasi Kampus</h1>
        <h2>Politeknik Negeri Bandung</h2>
        <p>UI GreenMetric - Transportation Criteria</p>
        <p>Dicetak pada: <?= $generated_at ?></p>
    </div>

    <!-- Filter Info -->
    <?php if (!empty($filters['start_date']) || !empty($filters['end_date'])): ?>
    <div class="info-box">
        <p><strong>Filter Periode:</strong></p>
        <?php if (!empty($filters['start_date']) && !empty($filters['end_date'])): ?>
            <p>Tanggal: <?= date('d/m/Y', strtotime($filters['start_date'])) ?> - <?= date('d/m/Y', strtotime($filters['end_date'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($filters['kategori_kendaraan'])): ?>
            <p>Kategori: <?= esc($filters['kategori_kendaraan']) ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Summary Statistics -->
    <div class="section-title orange">Ringkasan Statistik</div>
    <div class="summary-grid">
        <div class="summary-item">
            <div class="label">Total Kendaraan</div>
            <div class="value"><?= number_format($report_data['summary']['total_kendaraan']) ?> Unit</div>
        </div>
        <div class="summary-item">
            <div class="label">Total ZEV (Zero Emission Vehicle)</div>
            <div class="value"><?= number_format($report_data['summary']['total_zev']) ?> Unit</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Non-ZEV (Kendaraan BBM)</div>
            <div class="value"><?= number_format($report_data['summary']['total_non_zev']) ?> Unit</div>
        </div>
        <div class="summary-item">
            <div class="label">Persentase Keberlanjutan</div>
            <div class="value"><?= number_format($report_data['summary']['persentase_keberlanjutan'], 2) ?>%</div>
        </div>
    </div>

    <!-- Rekap per Kategori Kendaraan -->
    <div class="section-title">Rekapitulasi per Kategori Kendaraan</div>
    <?php if (!empty($report_data['rekap_kategori'])): ?>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">No</th>
                <th style="width: 60%;">Kategori Kendaraan</th>
                <th style="width: 25%;" class="text-center">Total Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report_data['rekap_kategori'] as $index => $row): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><strong><?= esc($row['kategori']) ?></strong></td>
                <td class="text-center"><strong><?= number_format($row['total_unit']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">Tidak ada data untuk ditampilkan</div>
    <?php endif; ?>

    <!-- Rekap per Jenis Bahan Bakar -->
    <div class="section-title green">Rekapitulasi per Jenis Bahan Bakar</div>
    <?php if (!empty($report_data['rekap_bahan_bakar'])): ?>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">No</th>
                <th style="width: 60%;">Jenis Bahan Bakar</th>
                <th style="width: 25%;" class="text-center">Total Unit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report_data['rekap_bahan_bakar'] as $index => $row): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td>
                    <strong><?= esc($row['bahan_bakar']) ?></strong>
                    <?php if ($row['bahan_bakar'] === 'Listrik' || $row['bahan_bakar'] === 'Non-BBM'): ?>
                        <span class="badge badge-success">ZEV</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><strong><?= number_format($row['total_unit']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">Tidak ada data untuk ditampilkan</div>
    <?php endif; ?>

    <!-- Rekap Bulanan -->
    <div class="section-title cyan">Rekapitulasi Bulanan</div>
    <?php if (!empty($report_data['rekap_bulanan'])): ?>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">No</th>
                <th style="width: 60%;">Periode</th>
                <th style="width: 25%;" class="text-center">Total Kendaraan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($report_data['rekap_bulanan'] as $index => $row): ?>
            <tr>
                <td class="text-center"><?= $index + 1 ?></td>
                <td><strong><?= esc($row['periode']) ?></strong></td>
                <td class="text-center"><strong><?= number_format($row['total_kendaraan']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="no-data">Tidak ada data untuk ditampilkan</div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Politeknik Negeri Bandung</strong></p>
        <p>Jl. Gegerkalong Hilir, Ds. Ciwaruga, Bandung 40559</p>
        <p>Telp: (022) 2013789, 2013164 | Fax: (022) 2013889</p>
        <p style="margin-top: 10px;">Dokumen ini dicetak secara otomatis dari Sistem UI GreenMetric POLBAN</p>
    </div>
</body>
</html>
