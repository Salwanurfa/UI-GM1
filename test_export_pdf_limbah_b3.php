<?php
/**
 * Test file untuk Export PDF Limbah B3
 * Jalankan file ini di browser untuk test tampilan PDF
 */

// Simulasi data untuk testing
$dataLimbah = [
    [
        'id' => 1,
        'tanggal_input' => '2026-03-01 10:00:00',
        'nama_limbah' => 'Oli Bekas Motor',
        'kode_limbah' => 'B105d',
        'lokasi' => 'Laboratorium Teknik Mesin',
        'timbulan' => 15.5,
        'karakteristik' => 'Beracun, mudah terbakar',
        'bentuk_fisik' => 'Cair kental berwarna hitam',
        'nama_user' => 'John Doe',
        'nama_unit' => 'Teknik Mesin'
    ],
    [
        'id' => 2,
        'tanggal_input' => '2026-03-01 14:30:00',
        'nama_limbah' => 'Kain Bekas Pembersih',
        'kode_limbah' => 'B109d',
        'lokasi' => 'Workshop Otomotif',
        'timbulan' => 2.3,
        'karakteristik' => 'Terkontaminasi oli dan bahan kimia',
        'bentuk_fisik' => 'Padat - kain bekas',
        'nama_user' => 'Jane Smith',
        'nama_unit' => 'Teknik Otomotif'
    ],
    [
        'id' => 3,
        'tanggal_input' => '2026-03-02 09:15:00',
        'nama_limbah' => 'Limbah Asam Sulfat',
        'kode_limbah' => 'B225d',
        'lokasi' => 'Laboratorium Kimia Analitik',
        'timbulan' => 8.7,
        'karakteristik' => 'Korosif, sangat berbahaya',
        'bentuk_fisik' => 'Cair tidak berwarna',
        'nama_user' => 'Dr. Ahmad',
        'nama_unit' => 'Teknik Kimia'
    ]
];

$totalTimbulan = array_sum(array_column($dataLimbah, 'timbulan'));

$data = [
    'title' => 'Laporan Data Limbah B3',
    'dataLimbah' => $dataLimbah,
    'totalTimbulan' => $totalTimbulan,
    'generated_at' => date('d/m/Y H:i:s'),
    'generated_by' => 'Admin Test'
];

// Include template
include 'app/Views/admin_pusat/manajemen_limbah_b3/export_pdf.php';
?>