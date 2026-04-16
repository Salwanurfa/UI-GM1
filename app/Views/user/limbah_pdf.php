<?php
/**
 * Template PDF Laporan Limbah B3
 * Digunakan oleh service untuk generate PDF dengan Dompdf
 */

$user = $user ?? [];
$unit = $unit ?? ['nama_unit' => 'Unit'];
$limbah_list = $limbah_list ?? [];
$total_timbulan = $total_timbulan ?? 0;
$status_count = $status_count ?? [];
$generated_at = $generated_at ?? date('d/m/Y H:i:s');?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPORAN DATA LIMBAH B3</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.2;
            color: #333;
            background: #fff;
            font-size: 10pt;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
            background: white;
        }

        /* Header / Kop Surat */
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 16pt;
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 10pt;
            color: #666;
            margin: 2px 0;
        }

        .info-section {
            margin-bottom: 15px;
            font-size: 9pt;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .info-column {
            flex: 1;
            min-width: 200px;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
            color: #1e3c72;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        /* Table Styling */
        .table-wrapper {
            margin-bottom: 15px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .table thead {
            background: #1e3c72;
            color: white;
        }

        .table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #ddd;
            font-size: 8pt;
            word-wrap: break-word;
        }

        .table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            border-bottom: 1px solid #e0e0e0;
            font-size: 8pt;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .table tfoot {
            background: #ecf0f1;
            font-weight: 600;
        }

        .table tfoot td {
            padding: 8px 4px;
            border: 1px solid #ddd;
            color: #1e3c72;
            font-size: 9pt;
        }

        /* Column widths for landscape */
        .col-no { width: 4%; }
        .col-tanggal { width: 10%; }
        .col-nama-limbah { width: 20%; }
        .col-kode { width: 8%; }
        .col-lokasi { width: 18%; }
        .col-timbulan { width: 12%; }
        .col-karakteristik { width: 18%; }
        .col-bentuk-fisik { width: 10%; }

        /* Text Alignment */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #1e3c72;
            border-radius: 4px;
            font-size: 9pt;
        }

        .summary-title {
            font-weight: 700;
            color: #1e3c72;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        .summary-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .summary-item {
            flex: 1;
            min-width: 120px;
            padding: 6px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .summary-item-label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 3px;
        }

        .summary-item-value {
            font-size: 12pt;
            font-weight: 700;
            color: #1e3c72;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }

        .footer-note {
            font-style: italic;
            margin-bottom: 5px;
        }

        .generated-date {
            margin-top: 3px;
            color: #999;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        /* Long text handling */
        .long-text {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        /* Page Break */
        @media print {
            .container {
                page-break-after: always;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header / Kop Surat -->
        <div class="header">
            <h1>📋 LAPORAN DATA LIMBAH B3</h1>
            <p>Sistem Manajemen Limbah Bahan Berbahaya dan Beracun</p>
            <p>UI GreenMetric POLBAN</p>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-column">
                <div class="info-row">
                    <div class="info-label">Unit/Departemen</div>
                    <div class="info-value">: <strong><?= esc($unit['nama_unit'] ?? 'Unit') ?></strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Pengguna</div>
                    <div class="info-value">: <strong><?= esc($user['nama_lengkap'] ?? 'User') ?></strong></div>
                </div>
            </div>
            <div class="info-column">
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">: <?= esc($user['email'] ?? '-') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Laporan</div>
                    <div class="info-value">: <?= $generated_at ?></div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-no text-center">No</th>
                        <th class="col-tanggal text-center">Tanggal Input</th>
                        <th class="col-nama-limbah">Nama Limbah</th>
                        <th class="col-kode text-center">Kode</th>
                        <th class="col-lokasi">Lokasi</th>
                        <th class="col-timbulan text-right">Timbulan</th>
                        <th class="col-karakteristik">Karakteristik</th>
                        <th class="col-bentuk-fisik text-center">Bentuk Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($limbah_list)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($limbah_list as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= format_datetime_wib($item['tanggal_input'], 'd/m/Y H:i') ?></td>
                                <td class="long-text"><?= esc($item['nama_limbah'] ?? '-') ?></td>
                                <td class="text-center"><?= esc($item['kode_limbah'] ?? '-') ?></td>
                                <td class="long-text"><?= esc($item['lokasi'] ?? '-') ?></td>
                                <td class="text-right"><?= number_format($item['timbulan'] ?? 0, 2, '.', '') ?> <?= esc($item['satuan']) ?></td>
                                <td class="long-text"><?= esc($item['karakteristik'] ?? '-') ?></td>
                                <td class="long-text"><?= esc($item['bentuk_fisik'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="empty-state">
                                Tidak ada data Limbah B3 untuk ditampilkan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">
                            <strong>TOTAL TIMBULAN:</strong>
                        </td>
                        <td class="text-right">
                            <strong><?= number_format($total_timbulan, 2, '.', '') ?> Unit</strong>
                        </td>
                        <td class="text-center">
                            <strong><?= count($limbah_list) ?> Data</strong>
                        </td>
                        <td class="text-center">
                            <strong>-</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Summary Section -->
        <?php if (!empty($status_count)): ?>
        <div class="summary-section">
            <div class="summary-title">📊 RINGKASAN STATUS DATA</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-item-label">Draft</div>
                    <div class="summary-item-value"><?= $status_count['draft'] ?? 0 ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-label">Menunggu Review</div>
                    <div class="summary-item-value"><?= $status_count['dikirim_ke_tps'] ?? 0 ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-label">Disetujui</div>
                    <div class="summary-item-value"><?= ($status_count['disetujui_tps'] ?? 0) + ($status_count['disetujui_admin'] ?? 0) ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-label">Ditolak</div>
                    <div class="summary-item-value"><?= $status_count['ditolak_tps'] ?? 0 ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-label">Total Item</div>
                    <div class="summary-item-value"><?= count($limbah_list) ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-item-label">Total Timbulan</div>
                    <div class="summary-item-value"><?= number_format($total_timbulan, 1) ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-note">
                ℹ️ Laporan ini adalah dokumen resmi untuk internal sistem manajemen limbah B3.
                Untuk informasi lebih lanjut, hubungi Tim Manajemen Limbah B3 POLBAN.
            </div>
            <div class="generated-date">
                Laporan Dibuat: <?= $generated_at ?>
            </div>
        </div>
    </div>
</body>
</html>
