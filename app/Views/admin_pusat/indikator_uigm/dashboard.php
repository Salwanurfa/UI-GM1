<?php
$title = $title ?? 'Manajemen Indikator UIGM';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-chart-line"></i> Manajemen Indikator UIGM</h1>
                <p>Data dikelola berdasarkan standar UI GreenMetric dan regulasi lingkungan hidup yang berlaku</p>
                <div class="reference-actions mt-2">
                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#referenceModal">
                        <i class="fas fa-book-open me-1"></i> Lihat Dasar Klasifikasi
                    </button>
                </div>
            </div>
            
            <div class="header-actions">
                <form method="GET" class="d-flex align-items-center gap-2">
                    <select name="tahun" id="tahun" class="form-select form-select-sm" style="width: 120px;">
                        <?php if (isset($year_options)): ?>
                            <?php foreach ($year_options as $year => $label): ?>
                                <option value="<?= $year ?>" <?= ($year == $selected_year) ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </form>
                
                <!-- Data Management Actions -->
                <div class="d-flex gap-2 ms-3">
                    <button type="button" class="btn btn-danger btn-sm" onclick="cleanOrphanedData()" title="Bersihkan data yang tidak valid">
                        <i class="fas fa-trash-alt me-1"></i>Bersihkan
                    </button>
                    <button type="button" class="btn btn-info btn-sm" onclick="checkOrphanedData()" title="Periksa data yang tidak valid">
                        <i class="fas fa-search me-1"></i>Periksa
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        <!-- UIGM Indicators Summary -->
        <div class="stats-grid mb-4">
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <div class="stat-content">
                    <h3 id="program-3r-total">0 Kg</h3>
                    <p>Program 3R 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada Standar UI GreenMetric (Waste Management) - Program Reduce, Reuse, Recycle"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="stat-content">
                    <h3 id="kertas-plastik-total">0 Kg</h3>
                    <p>Pengurangan Kertas & Plastik 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada Standar UI GreenMetric (Waste Management) - Upaya pengurangan konsumsi kertas dan plastik"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-content">
                    <h3 id="organik-total">0 Kg</h3>
                    <p>Limbah Organik 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada UU No. 18/2008 tentang Pengelolaan Sampah - Limbah yang dapat terurai secara alami"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-cube"></i>
                </div>
                <div class="stat-content">
                    <h3 id="anorganik-total">0 Kg</h3>
                    <p>Limbah Anorganik 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada UU No. 18/2008 tentang Pengelolaan Sampah - Limbah yang dapat didaur ulang"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-biohazard"></i>
                </div>
                <div class="stat-content">
                    <h3 id="b3-total">0 Kg</h3>
                    <p>Limbah B3 
                        <i class="fas fa-info-circle text-warning ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada PP No. 22 Tahun 2021 tentang Pengelolaan Limbah B3 - Bahan Berbahaya dan Beracun"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <div class="stat-content">
                    <h3 id="cair-total">0 L</h3>
                    <p>Limbah Cair 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada Standar Pengolahan Air Limbah Domestik - Peraturan Menteri LHK"></i>
                    </p>
                </div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3 id="daur-ulang-persen">0%</h3>
                    <p>Persentase Daur Ulang 
                        <i class="fas fa-info-circle text-info ms-1" 
                           data-bs-toggle="tooltip" 
                           data-bs-placement="top" 
                           title="Merujuk pada Standar UI GreenMetric - Perhitungan persentase limbah yang dapat didaur ulang"></i>
                    </p>
                </div>
            </div>
        </div>

        <!-- 7 UIGM Indicators Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar"></i> 7 Indikator UIGM (UI GreenMetric)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed-layout">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center align-middle">
                                    <span class="header-text">No</span>
                                </th>
                                <th width="30%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        <span class="header-text">Indikator</span>
                                    </div>
                                </th>
                                <th width="15%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-weight-hanging me-2"></i>
                                        <span class="header-text">Total (Kg)</span>
                                    </div>
                                </th>
                                <th width="15%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-flask me-2"></i>
                                        <span class="header-text">Total (L)</span>
                                    </div>
                                </th>
                                <th width="10%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-list-ol me-2"></i>
                                        <span class="header-text">Records</span>
                                    </div>
                                </th>
                                <th width="10%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-source me-2"></i>
                                        <span class="header-text">Sumber</span>
                                    </div>
                                </th>
                                <th width="15%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-eye me-2"></i>
                                        <span class="header-text">Aksi</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="uigmIndicatorTable">
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Detailed Recap Table -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-table"></i> Tabel Rekapan Data User (Sinkronisasi Otomatis)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-fixed-layout">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center align-middle">
                                    <span class="header-text">No</span>
                                </th>
                                <th width="18%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-user me-2"></i>
                                        <span class="header-text">Nama User/Unit</span>
                                    </div>
                                </th>
                                <th width="17%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-trash me-2"></i>
                                        <span class="header-text">Jenis Sampah</span>
                                    </div>
                                </th>
                                <th width="15%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-weight-hanging me-2"></i>
                                        <span class="header-text">Jumlah/Volume</span>
                                    </div>
                                </th>
                                <th width="18%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-chart-line me-2"></i>
                                        <span class="header-text">Kategori Indikator</span>
                                    </div>
                                </th>
                                <th width="12%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-calendar me-2"></i>
                                        <span class="header-text">Waktu Input</span>
                                    </div>
                                </th>
                                <th width="8%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-file me-2"></i>
                                        <span class="header-text">Bukti</span>
                                    </div>
                                </th>
                                <th width="12%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-building me-2"></i>
                                        <span class="header-text">Gedung</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="detailedRecapTable">
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Standardized Category Analysis Table -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-layer-group"></i> Standardisasi Kategori Limbah 
                    <i class="fas fa-info-circle text-white-50 ms-2" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top" 
                       title="Sistem kategorisasi berdasarkan regulasi resmi pemerintah dan standar internasional"></i>
                </h3>
                <p class="mb-0 text-white-50">Kategori berdasarkan Standar UI GreenMetric & Regulasi Lingkungan Hidup</p>
            </div>
            <div class="card-body">
                <!-- Reference Information -->
                <div class="alert alert-info mb-3">
                    <h6><i class="fas fa-info-circle me-2"></i>Rujukan Standar:</h6>
                    <small>
                        • <strong>UI GreenMetric Ranking System</strong> - Standar penilaian universitas berkelanjutan<br>
                        • <strong>UU No. 18/2008</strong> tentang Pengelolaan Sampah - Dasar hukum pengelolaan sampah nasional<br>
                        • <strong>PP No. 22/2021</strong> tentang Pengelolaan Limbah B3 - Regulasi limbah berbahaya dan beracun<br>
                        • <strong>Peraturan Menteri LHK</strong> tentang Pengelolaan Limbah - Pedoman teknis pengelolaan<br>
                        • <strong>ISO 14001</strong> Environmental Management Standards - Standar manajemen lingkungan internasional
                    </small>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-fixed-layout">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%" class="text-center align-middle">
                                    <span class="header-text">No</span>
                                </th>
                                <th width="22%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-user me-2"></i>
                                        <span class="header-text">Nama Penginput</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Unit atau individu yang menginput data limbah ke sistem"></i>
                                    </div>
                                </th>
                                <th width="18%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-trash me-2"></i>
                                        <span class="header-text">Jenis Sampah</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Kategori sampah berdasarkan input user sebelum standardisasi"></i>
                                    </div>
                                </th>
                                <th width="25%" class="align-middle">
                                    <div class="header-content">
                                        <i class="fas fa-certificate me-2"></i>
                                        <span class="header-text">Kategori Standar</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Kategori hasil standardisasi berdasarkan regulasi pemerintah dengan rujukan peraturan"></i>
                                    </div>
                                </th>
                                <th width="12%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-weight-hanging me-2"></i>
                                        <span class="header-text">Jumlah</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Volume limbah dalam kilogram (kg) atau liter (L)"></i>
                                    </div>
                                </th>
                                <th width="11%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-source me-2"></i>
                                        <span class="header-text">Asal Data</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Sumber data: User Umum, Unit TPS, atau Admin Unit"></i>
                                    </div>
                                </th>
                                <th width="10%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-calendar me-2"></i>
                                        <span class="header-text">Waktu Input</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Tanggal dan waktu data diinput ke sistem"></i>
                                    </div>
                                </th>
                                <th width="10%" class="text-center align-middle">
                                    <div class="header-content justify-content-center">
                                        <i class="fas fa-file me-2"></i>
                                        <span class="header-text">Bukti Dukung</span>
                                        <i class="fas fa-info-circle text-white-50 ms-2" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Dokumen pendukung untuk validasi data"></i>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="standardizedCategoryTable">
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin"></i> Memuat data standardisasi...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Category Summary Cards -->
        <div class="card mt-4">
            <div class="card-header">
                <h3><i class="fas fa-chart-pie"></i> Ringkasan 7 Indikator UIGM</h3>
                <p class="mb-0 text-muted">Data dikelompokkan berdasarkan standar UI GreenMetric dan regulasi lingkungan hidup</p>
            </div>
            <div class="card-body">
                <div id="categorySummaryCards" class="row">
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-spinner fa-spin"></i> Memuat ringkasan 7 indikator UIGM...
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reference Classification Modal -->
    <div class="modal fade" id="referenceModal" tabindex="-1" aria-labelledby="referenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="referenceModalLabel">
                        <i class="fas fa-book-open me-2"></i>Dasar Klasifikasi Limbah
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <h6><i class="fas fa-certificate me-2"></i>Landasan Teoritis Sistem</h6>
                                <p class="mb-0">Sistem kategorisasi limbah ini dibangun berdasarkan regulasi resmi pemerintah Indonesia dan standar internasional yang diakui secara global untuk memastikan akurasi dan kepatuhan hukum.</p>
                            </div>
                        </div>
                    </div>

                    <!-- UI GreenMetric Standards -->
                    <div class="reference-section mb-4">
                        <h6 class="text-primary"><i class="fas fa-globe me-2"></i>UI GreenMetric Ranking System</h6>
                        <div class="card border-primary">
                            <div class="card-body">
                                <p><strong>Sumber:</strong> Universitas Indonesia - UI GreenMetric World University Rankings</p>
                                <p><strong>Kategori yang Dirujuk:</strong></p>
                                <ul class="mb-2">
                                    <li><span class="badge bg-success me-2">Program 3R</span> Reduce, Reuse, Recycle initiatives</li>
                                    <li><span class="badge bg-warning me-2">Kertas & Plastik</span> Paper and plastic reduction efforts</li>
                                    <li><span class="badge bg-warning me-2">Daur Ulang</span> Recycling percentage calculations</li>
                                </ul>
                                <p class="text-muted small">UI GreenMetric adalah sistem pemeringkatan universitas berkelanjutan yang diakui secara internasional, dengan fokus pada pengelolaan limbah sebagai salah satu indikator utama.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Indonesian National Law -->
                    <div class="reference-section mb-4">
                        <h6 class="text-success"><i class="fas fa-balance-scale me-2"></i>Undang-Undang Republik Indonesia</h6>
                        <div class="card border-success">
                            <div class="card-body">
                                <p><strong>UU No. 18 Tahun 2008 tentang Pengelolaan Sampah</strong></p>
                                <p><strong>Kategori yang Dirujuk:</strong></p>
                                <ul class="mb-2">
                                    <li><span class="badge bg-success me-2">Limbah Organik</span> Pasal 2: Sampah yang dapat terurai secara alami</li>
                                    <li><span class="badge bg-info me-2">Limbah Anorganik</span> Pasal 2: Sampah yang tidak dapat terurai secara alami</li>
                                    <li><span class="badge bg-secondary me-2">Residu</span> Pasal 19: Sampah sisa yang tidak dapat diolah kembali</li>
                                </ul>
                                <p class="text-muted small">Undang-undang ini menjadi dasar hukum pengelolaan sampah di Indonesia dan mengatur klasifikasi sampah berdasarkan karakteristik fisik dan kimia.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Government Regulation B3 -->
                    <div class="reference-section mb-4">
                        <h6 class="text-danger"><i class="fas fa-biohazard me-2"></i>Peraturan Pemerintah Limbah B3</h6>
                        <div class="card border-danger">
                            <div class="card-body">
                                <p><strong>PP No. 22 Tahun 2021 tentang Pengelolaan Limbah Bahan Berbahaya dan Beracun</strong></p>
                                <p><strong>Kategori yang Dirujuk:</strong></p>
                                <ul class="mb-2">
                                    <li><span class="badge bg-danger me-2">Limbah B3</span> Pasal 1: Bahan Berbahaya dan Beracun</li>
                                </ul>
                                <p><strong>Karakteristik B3 (Pasal 3):</strong></p>
                                <ul class="small mb-2">
                                    <li>Mudah meledak (explosive)</li>
                                    <li>Mudah terbakar (flammable)</li>
                                    <li>Bersifat reaktif (reactive)</li>
                                    <li>Beracun (toxic)</li>
                                    <li>Menyebabkan infeksi (infectious)</li>
                                    <li>Bersifat korosif (corrosive)</li>
                                </ul>
                                <p class="text-muted small">Regulasi khusus untuk limbah berbahaya yang memerlukan penanganan dan pembuangan khusus untuk melindungi kesehatan manusia dan lingkungan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Ministry Regulation -->
                    <div class="reference-section mb-4">
                        <h6 class="text-primary"><i class="fas fa-flask me-2"></i>Peraturan Menteri Lingkungan Hidup</h6>
                        <div class="card border-primary">
                            <div class="card-body">
                                <p><strong>Peraturan Menteri LHK tentang Pengelolaan Air Limbah Domestik</strong></p>
                                <p><strong>Kategori yang Dirujuk:</strong></p>
                                <ul class="mb-2">
                                    <li><span class="badge bg-primary me-2">Limbah Cair</span> Air limbah domestik dan industri</li>
                                </ul>
                                <p><strong>Jenis Air Limbah:</strong></p>
                                <ul class="small mb-2">
                                    <li>Air limbah domestik (rumah tangga)</li>
                                    <li>Air limbah perkantoran</li>
                                    <li>Air limbah komersial</li>
                                    <li>Air limbah laboratorium</li>
                                </ul>
                                <p class="text-muted small">Mengatur standar kualitas air limbah dan metode pengolahannya sebelum dibuang ke lingkungan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- ISO Standards -->
                    <div class="reference-section mb-4">
                        <h6 class="text-info"><i class="fas fa-certificate me-2"></i>Standar Internasional ISO</h6>
                        <div class="card border-info">
                            <div class="card-body">
                                <p><strong>ISO 14001:2015 Environmental Management Systems</strong></p>
                                <p><strong>Prinsip yang Diterapkan:</strong></p>
                                <ul class="mb-2">
                                    <li>Identifikasi dan evaluasi aspek lingkungan</li>
                                    <li>Pengelolaan limbah berdasarkan hierarki pengelolaan</li>
                                    <li>Monitoring dan pengukuran kinerja lingkungan</li>
                                    <li>Peningkatan berkelanjutan (continuous improvement)</li>
                                </ul>
                                <p class="text-muted small">Standar manajemen lingkungan internasional yang memberikan kerangka kerja untuk pengelolaan limbah yang efektif dan berkelanjutan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Implementation Notes -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-cogs me-2"></i>Implementasi dalam Sistem</h6>
                        <p class="mb-2">Sistem ini menggunakan algoritma cerdas untuk mengkategorikan limbah berdasarkan:</p>
                        <ul class="small mb-2">
                            <li><strong>Keyword matching:</strong> Pencocokan kata kunci berdasarkan regulasi</li>
                            <li><strong>Priority classification:</strong> Prioritas keamanan (B3 > Cair > Organik > Anorganik > Residu)</li>
                            <li><strong>Regulatory compliance:</strong> Kepatuhan terhadap semua regulasi yang berlaku</li>
                            <li><strong>Audit trail:</strong> Jejak audit untuk verifikasi dan evaluasi</li>
                        </ul>
                        <p class="mb-0 small text-muted">Setiap kategori limbah yang dihasilkan sistem dapat dipertanggungjawabkan secara hukum dan ilmiah.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printReference()">
                        <i class="fas fa-print me-1"></i> Cetak Referensi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Evidence Upload Modal -->
    <div class="modal fade" id="evidenceUploadModal" tabindex="-1" aria-labelledby="evidenceUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="evidenceUploadModalLabel">
                        <i class="fas fa-upload me-2"></i>Unggah Bukti Dukung
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="evidenceUploadForm" enctype="multipart/form-data">
                        <input type="hidden" id="upload_record_id" name="record_id">
                        <input type="hidden" id="upload_source_table" name="source_table">
                        <input type="hidden" id="upload_indicator_key" name="indicator_key">
                        
                        <!-- Record Information -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Informasi Data</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Unit:</strong> <span id="upload_unit_name">-</span><br>
                                    <strong>Jenis Sampah:</strong> <span id="upload_waste_type">-</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Volume:</strong> <span id="upload_volume">-</span><br>
                                    <strong>Tanggal:</strong> <span id="upload_date">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-3">
                            <label for="evidence_file" class="form-label">
                                <i class="fas fa-file me-1"></i>Pilih File Bukti Dukung
                            </label>
                            <input type="file" class="form-control" id="evidence_file" name="evidence_file" 
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Format yang diizinkan: JPG, PNG, PDF, DOC, DOCX. Maksimal 5 MB.
                            </div>
                        </div>

                        <!-- File Preview -->
                        <div id="file_preview" class="mb-3" style="display: none;">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-eye me-1"></i>Preview File</h6>
                                    <div id="preview_content"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="evidence_description" class="form-label">
                                <i class="fas fa-comment me-1"></i>Keterangan (Opsional)
                            </label>
                            <textarea class="form-control" id="evidence_description" name="description" rows="3" 
                                      placeholder="Tambahkan keterangan tentang bukti dukung ini..."></textarea>
                        </div>

                        <!-- Upload Progress -->
                        <div id="upload_progress" class="mb-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted mt-1">Mengunggah file...</small>
                        </div>

                        <!-- Error Messages -->
                        <div id="upload_error" class="alert alert-danger" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="error_message"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="upload_submit_btn" onclick="submitEvidenceUpload()">
                        <i class="fas fa-upload me-1"></i>Unggah Bukti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Load data on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Check if we can reach the debug endpoint
        console.log('Loading UIGM Dashboard...');
        
        // Test debug endpoint first
        fetch(`<?= base_url('admin-pusat/indikator-uigm/debug-data') ?>?tahun=${document.getElementById('tahun').value}`)
            .then(response => response.json())
            .then(data => {
                console.log('Debug data:', data);
                if (data.success) {
                    console.log(`Found ${data.test_result.total_records} total records, ${data.test_result.approved_records} approved`);
                    console.log('Available jenis_sampah:', data.test_result.jenis_sampah_list);
                }
            })
            .catch(error => console.error('Debug error:', error));
        
        loadUIGMIndicatorData();
        loadDetailedRecapData();
        loadStandardizedCategoryData();
        loadCategorySummary();
    });

    function loadUIGMIndicatorData() {
        const tahun = document.getElementById('tahun').value;
        
        // Show loading state
        showLoadingState();
        
        // Fetch UIGM indicator data
        fetch(`<?= base_url('admin-pusat/indikator-uigm/get-uigm-indicator-data') ?>?tahun=${tahun}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateUIGMIndicatorTable(data.data);
                    updateSummaryCards(data.data);
                } else {
                    console.error('Error loading UIGM indicator data:', data.message);
                    showErrorState('Gagal memuat data indikator: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorState('Terjadi kesalahan saat memuat data indikator');
            });
    }
    function loadDetailedRecapData() {
        const tahun = document.getElementById('tahun').value;
        
        // Show loading for detailed table
        document.getElementById('detailedRecapTable').innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data...
                </td>
            </tr>
        `;
        
        // Fetch detailed recap data
        fetch(`<?= base_url('admin-pusat/indikator-uigm/get-detailed-recap-data') ?>?tahun=${tahun}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateDetailedRecapTable(data.data);
                } else {
                    console.error('Error loading detailed recap data:', data.message);
                    showDetailedRecapError('Gagal memuat data rekapan: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showDetailedRecapError('Terjadi kesalahan saat memuat data rekapan');
            });
    }

    function loadStandardizedCategoryData() {
        const tahun = document.getElementById('tahun').value;
        
        // Show loading for standardized table
        document.getElementById('standardizedCategoryTable').innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data standardisasi...
                </td>
            </tr>
        `;
        
        // Fetch standardized categorized data
        fetch(`<?= base_url('admin-pusat/indikator-uigm/get-standardized-categorized-data') ?>?tahun=${tahun}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateStandardizedCategoryTable(data.data);
                } else {
                    console.error('Error loading standardized data:', data.message);
                    showStandardizedCategoryError('Gagal memuat data standardisasi: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStandardizedCategoryError('Terjadi kesalahan saat memuat data standardisasi');
            });
    }

    function loadCategorySummary() {
        const tahun = document.getElementById('tahun').value;
        
        // Show loading for category summary
        document.getElementById('categorySummaryCards').innerHTML = `
            <div class="col-12 text-center py-4">
                <i class="fas fa-spinner fa-spin"></i> Memuat ringkasan 7 indikator UIGM...
            </div>
        `;
        
        // Fetch UIGM indicator data instead of category summary
        fetch(`<?= base_url('admin-pusat/indikator-uigm/get-uigm-indicator-data') ?>?tahun=${tahun}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateCategorySummaryCards(data.data);
                } else {
                    console.error('Error loading UIGM indicator data:', data.message);
                    showCategorySummaryError('Gagal memuat ringkasan indikator UIGM: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCategorySummaryError('Terjadi kesalahan saat memuat ringkasan indikator UIGM');
            });
    }

    function showLoadingState() {
        // Show loading indicators for summary cards
        const loadingElements = ['program-3r-total', 'kertas-plastik-total', 'organik-total', 'anorganik-total', 'b3-total', 'cair-total', 'daur-ulang-persen'];
        loadingElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        });

        // Show loading for indicator table
        document.getElementById('uigmIndicatorTable').innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin"></i> Memuat data indikator...
                </td>
            </tr>
        `;
    }

    function showErrorState(message) {
        // Reset summary cards to 0 with error indication
        document.getElementById('program-3r-total').textContent = '0 Kg';
        document.getElementById('kertas-plastik-total').textContent = '0 Kg';
        document.getElementById('organik-total').textContent = '0 Kg';
        document.getElementById('anorganik-total').textContent = '0 Kg';
        document.getElementById('b3-total').textContent = '0 Kg';
        document.getElementById('cair-total').textContent = '0 L';
        document.getElementById('daur-ulang-persen').textContent = '0%';

        // Show error in indicator table
        document.getElementById('uigmIndicatorTable').innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p><strong>Gagal Memuat Data</strong></p>
                        <small>${message}</small>
                        <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="location.reload()">
                            <i class="fas fa-refresh me-1"></i>Muat Ulang
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showDetailedRecapError(message) {
        document.getElementById('detailedRecapTable').innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p><strong>Gagal Memuat Data Rekapan</strong></p>
                        <small>${message}</small>
                        <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadDetailedRecapData()">
                            <i class="fas fa-refresh me-1"></i>Coba Lagi
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showStandardizedCategoryError(message) {
        document.getElementById('standardizedCategoryTable').innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <p><strong>Gagal Memuat Data Standardisasi</strong></p>
                        <small>${message}</small>
                        <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadStandardizedCategoryData()">
                            <i class="fas fa-refresh me-1"></i>Coba Lagi
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function showCategorySummaryError(message) {
        document.getElementById('categorySummaryCards').innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p><strong>Gagal Memuat Ringkasan Indikator UIGM</strong></p>
                    <small>${message}</small>
                    <br><button class="btn btn-sm btn-outline-primary mt-2" onclick="loadCategorySummary()">
                        <i class="fas fa-refresh me-1"></i>Coba Lagi
                    </button>
                </div>
            </div>
        `;
    }

    function updateStandardizedCategoryTable(data) {
        const tableBody = document.getElementById('standardizedCategoryTable');
        let html = '';

        if (!data || data.length === 0) {
            html = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Belum ada data untuk tahun ini</p>
                            <small>Data akan muncul setelah user menginput dan TPS menyetujui</small>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            data.forEach((item, index) => {
                const categoryColor = item.category_color || 'secondary';
                const asalDataColor = getAsalDataColor(item.user_role);
                const asalDataText = getAsalDataText(item.user_role);
                
                const volumeDisplay = item.volume_kg > 0 ? 
                    `${parseFloat(item.volume_kg).toFixed(2)} kg` : 
                    `${parseFloat(item.volume_l).toFixed(2)} L`;
                
                html += `
                    <tr>
                        <td class="text-center fw-bold">${index + 1}</td>
                        <td>
                            <div class="fw-bold text-dark">${item.nama_unit || 'Unknown Unit'}</div>
                            ${item.nama_user ? `<small class="text-muted"><i class="fas fa-user me-1"></i>${item.nama_user}</small>` : ''}
                        </td>
                        <td>
                            <div class="fw-bold">${item.jenis_sampah || 'Unknown'}</div>
                            ${item.nama_sampah_detail && item.nama_sampah_detail !== item.jenis_sampah ? 
                                `<small class="text-muted">${item.nama_sampah_detail}</small>` : ''}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">${item.standard_category_name}</div>
                            <small class="text-muted">${item.category_reference}</small>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">${volumeDisplay}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-${asalDataColor} rounded-pill" 
                                  data-bs-toggle="tooltip" 
                                  data-bs-placement="top" 
                                  title="Data diinput oleh ${asalDataText}">
                                <i class="fas fa-${getAsalDataIcon(asalDataText)} me-1"></i>${asalDataText}
                            </span>
                        </td>
                        <td class="text-center">
                            <small class="text-muted">${formatDate(item.tanggal)}</small>
                        </td>
                        <td class="text-center">
                            ${item.bukti_foto ? 
                                `<button class="btn btn-sm btn-success" onclick="viewEvidence('${item.bukti_foto}')" title="Lihat Bukti">
                                    <i class="fas fa-eye"></i>
                                </button>` : 
                                `<button class="btn btn-sm btn-outline-primary" onclick="openEvidenceUpload(${JSON.stringify(item).replace(/"/g, '&quot;')})" title="Unggah Bukti">
                                    <i class="fas fa-upload"></i>
                                </button>`
                            }
                        </td>
                    </tr>
                `;
            });
        }

        tableBody.innerHTML = html;
        
        // Reinitialize tooltips for new content
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }

    function updateCategorySummaryCards(data) {
        const container = document.getElementById('categorySummaryCards');
        let html = '';

        // Define the 7 UIGM indicators with their display properties
        const uigmIndicators = [
            {
                key: 'indikator_1',
                name: 'Program 3R',
                description: 'Kegiatan Reduce, Reuse, Recycle',
                color: 'success',
                icon: 'recycle',
                reference: 'UI GreenMetric Standard'
            },
            {
                key: 'indikator_2', 
                name: 'Pengurangan Kertas & Plastik',
                description: 'Upaya mengurangi penggunaan kertas dan plastik',
                color: 'warning',
                icon: 'leaf',
                reference: 'UI GreenMetric Standard'
            },
            {
                key: 'indikator_3',
                name: 'Limbah Organik',
                description: 'Limbah yang dapat terurai secara alami',
                color: 'success',
                icon: 'seedling',
                reference: 'UU No. 18/2008'
            },
            {
                key: 'indikator_4',
                name: 'Limbah Anorganik', 
                description: 'Limbah non-organik umum',
                color: 'info',
                icon: 'cube',
                reference: 'UU No. 18/2008'
            },
            {
                key: 'indikator_5',
                name: 'Limbah B3',
                description: 'Bahan Berbahaya dan Beracun',
                color: 'danger',
                icon: 'biohazard',
                reference: 'PP No. 22/2021'
            },
            {
                key: 'indikator_6',
                name: 'Limbah Cair',
                description: 'Air limbah dan cairan buangan',
                color: 'primary',
                icon: 'flask',
                reference: 'Peraturan Menteri LHK'
            },
            {
                key: 'indikator_7',
                name: 'Persentase Daur Ulang',
                description: 'Persentase limbah yang dapat didaur ulang',
                color: 'warning',
                icon: 'percentage',
                reference: 'UI GreenMetric Standard'
            }
        ];

        uigmIndicators.forEach(indicator => {
            const indicatorData = data[indicator.key] || {
                total_kg: 0,
                total_l: 0,
                total_records: 0,
                recycling_percentage: 0
            };
            
            // Special handling for recycling percentage
            let displayValue = '';
            if (indicator.key === 'indikator_7') {
                displayValue = `${indicatorData.recycling_percentage || 0}%`;
            } else if (indicator.key === 'indikator_6') {
                displayValue = `${(indicatorData.total_l || 0).toFixed(2)} L`;
            } else {
                displayValue = `${(indicatorData.total_kg || 0).toFixed(2)} kg`;
            }
            
            html += `
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <div class="card border-left-${indicator.color} shadow h-100 py-2" 
                         data-bs-toggle="tooltip" 
                         data-bs-placement="top" 
                         title="${indicator.description} - ${indicator.reference}">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-${indicator.color} text-uppercase mb-1">
                                        ${indicator.name}
                                        <i class="fas fa-info-circle text-muted ms-1" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="right" 
                                           title="${indicator.reference}"></i>
                                    </div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        ${displayValue}
                                    </div>
                                    <small class="text-muted">${indicatorData.total_records || 0} records</small>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-${indicator.icon} fa-2x text-${indicator.color}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        
        // Reinitialize tooltips for new content
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }

    function getAsalDataText(userRole) {
        const roleMapping = {
            'pengelola_tps': 'TPS',
            'user': 'USER',
            'admin_unit': 'USER',
            'admin_pusat': 'ADMIN PUSAT',
            'super_admin': 'ADMIN PUSAT'
        };
        return roleMapping[userRole] || 'USER';
    }

    function updateCategorizedDataTable(data) {
        const tableBody = document.getElementById('categorizedDataTable');
        let html = '';

        if (!data || data.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Belum ada data untuk tahun ini</p>
                            <small>Data akan muncul setelah user menginput dan TPS menyetujui</small>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            data.forEach((item, index) => {
                const categoryColor = getCategoryColor(item.kategori_utama);
                const indicatorColor = getIndicatorBadgeColor(item.indikator_key);
                const asalDataColor = getAsalDataColor(item.asal_data);
                
                html += `
                    <tr>
                        <td class="text-center fw-bold">${index + 1}</td>
                        <td>
                            <div class="fw-bold text-dark">${item.nama_penginput || 'Unknown Unit'}</div>
                            ${item.nama_user ? `<small class="text-muted"><i class="fas fa-user me-1"></i>${item.nama_user}</small>` : ''}
                        </td>
                        <td>
                            <div class="fw-bold">${item.jenis_sampah || 'Unknown'}</div>
                            ${item.nama_sampah_detail && item.nama_sampah_detail !== item.jenis_sampah ? 
                                `<small class="text-muted">${item.nama_sampah_detail}</small>` : ''}
                        </td>
                        <td>
                            <span class="badge bg-${categoryColor} rounded-pill">
                                <i class="fas fa-layer-group me-1"></i>${item.kategori_utama}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-${indicatorColor} rounded-pill">
                                ${item.kategori_indikator}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold text-primary">${item.jumlah_display}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-${asalDataColor} rounded-pill">
                                <i class="fas fa-${getAsalDataIcon(item.asal_data)} me-1"></i>${item.asal_data}
                            </span>
                        </td>
                        <td class="text-center">
                            <small class="text-muted">${formatDate(item.waktu_input)}</small>
                        </td>
                    </tr>
                `;
            });
        }

        tableBody.innerHTML = html;
    }

    function getCategoryColor(category) {
        const colors = {
            'Organik': 'success',
            'Anorganik': 'info',
            'B3': 'danger',
            'Cair': 'primary',
            'Residu': 'secondary'
        };
        return colors[category] || 'secondary';
    }

    function getAsalDataColor(asalData) {
        const colors = {
            'TPS': 'primary',
            'USER': 'secondary',
            'ADMIN PUSAT': 'info'
        };
        return colors[asalData] || 'secondary';
    }

    function getAsalDataIcon(asalData) {
        const icons = {
            'User Umum': 'user',
            'Unit TPS': 'recycle',
            'Admin Unit': 'user-tie',
            'Sistem': 'cog'
        };
        return icons[asalData] || 'question';
    }

    function getAsalDataColor(userRole) {
        const colors = {
            'pengelola_tps': 'success',
            'user': 'info',
            'admin_unit': 'warning',
            'admin_pusat': 'primary',
            'super_admin': 'danger'
        };
        return colors[userRole] || 'secondary';
    }
    function updateSummaryCards(data) {
        // Update summary cards with UIGM indicator data
        document.getElementById('program-3r-total').textContent = (data.indikator_1?.total_kg || 0) + ' Kg';
        document.getElementById('kertas-plastik-total').textContent = (data.indikator_2?.total_kg || 0) + ' Kg';
        document.getElementById('organik-total').textContent = (data.indikator_3?.total_kg || 0) + ' Kg';
        document.getElementById('anorganik-total').textContent = (data.indikator_4?.total_kg || 0) + ' Kg';
        document.getElementById('b3-total').textContent = (data.indikator_5?.total_kg || 0) + ' Kg';
        document.getElementById('cair-total').textContent = (data.indikator_6?.total_l || 0) + ' L';
        
        // Enhanced recycling percentage display
        const recyclingData = data.indikator_7 || {};
        const percentage = recyclingData.recycling_percentage || 0;
        const recyclingElement = document.getElementById('daur-ulang-persen');
        
        recyclingElement.textContent = percentage + '%';
        
        // Add tooltip with calculation details
        if (recyclingData.calculation_formula) {
            recyclingElement.setAttribute('data-bs-toggle', 'tooltip');
            recyclingElement.setAttribute('data-bs-placement', 'top');
            recyclingElement.setAttribute('title', 
                `Perhitungan: ${recyclingData.calculation_formula}\n` +
                `Organik: ${recyclingData.organic_kg || 0} kg\n` +
                `Anorganik: ${recyclingData.inorganic_kg || 0} kg\n` +
                `Total Padat: ${recyclingData.total_solid_kg || 0} kg`
            );
        }
        
        // Reinitialize tooltips
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }

    function updateUIGMIndicatorTable(data) {
        const tableBody = document.getElementById('uigmIndicatorTable');
        let html = '';

        const indicators = [
            { key: 'indikator_1', name: 'Program 3R', icon: 'recycle', color: 'success' },
            { key: 'indikator_2', name: 'Pengurangan Kertas & Plastik', icon: 'leaf', color: 'warning' },
            { key: 'indikator_3', name: 'Limbah Organik', icon: 'seedling', color: 'success' },
            { key: 'indikator_4', name: 'Limbah Anorganik', icon: 'cube', color: 'info' },
            { key: 'indikator_5', name: 'Limbah B3', icon: 'biohazard', color: 'danger' },
            { key: 'indikator_6', name: 'Limbah Cair', icon: 'flask', color: 'primary' },
            { key: 'indikator_7', name: 'Persentase Daur Ulang', icon: 'percentage', color: 'warning' }
        ];

        indicators.forEach((indicator, index) => {
            const indicatorData = data[indicator.key] || {};
            const totalKg = indicatorData.total_kg || 0;
            const totalL = indicatorData.total_l || 0;
            const records = indicatorData.total_records || 0;
            const sources = indicatorData.sources || 0;

            // Special handling for different indicator types
            let volumeDisplay = '';
            let additionalInfo = '';
            
            if (indicator.key === 'indikator_6') {
                // Liquid waste - show liters primarily
                volumeDisplay = `${totalL.toFixed(2)} L`;
                if (totalKg > 0) {
                    volumeDisplay += ` + ${totalKg.toFixed(2)} kg`;
                }
                additionalInfo = 'Limbah cair diukur dalam liter';
            } else if (indicator.key === 'indikator_7') {
                // Recycling percentage - show percentage
                const percentage = indicatorData.recycling_percentage || 0;
                volumeDisplay = `${percentage}%`;
                additionalInfo = `Formula: ${indicatorData.calculation_formula || 'Belum ada data'}`;
            } else {
                // Solid waste - show kg primarily
                volumeDisplay = `${totalKg.toFixed(2)} kg`;
                if (totalL > 0) {
                    volumeDisplay += ` + ${totalL.toFixed(2)} L`;
                }
                additionalInfo = 'Limbah padat diukur dalam kilogram';
            }

            html += `
                <tr class="table-${indicator.color}">
                    <td class="text-center fw-bold">${index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="waste-icon bg-${indicator.color} me-3">
                                <i class="fas fa-${indicator.icon}"></i>
                            </div>
                            <div>
                                <strong>${indicator.name}</strong>
                                <br><small class="text-muted">${indicatorData.config?.description || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center" data-bs-toggle="tooltip" title="${additionalInfo}">
                        <span class="fw-bold text-${indicator.color}">${totalKg.toFixed(2)}</span>
                    </td>
                    <td class="text-center" data-bs-toggle="tooltip" title="${additionalInfo}">
                        <span class="fw-bold text-${indicator.color}">${totalL.toFixed(2)}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info">${records}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">${sources}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="viewIndicatorDetails('${indicator.key}')"
                                data-bs-toggle="tooltip" 
                                title="Lihat detail ${indicator.name}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;
        
        // Reinitialize tooltips for new content
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }
    function updateDetailedRecapTable(data) {
        const tableBody = document.getElementById('detailedRecapTable');
        let html = '';

        if (!data || data.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p><strong>Belum ada data disetujui</strong></p>
                            <small>Data akan muncul setelah user menginput dan TPS menyetujui.<br>
                            Pastikan status data sudah 'disetujui', 'disetujui_tps', atau 'approved'.</small>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            data.forEach((item, index) => {
                // Use the formatted display values from backend
                const volumeDisplay = item.volume_display || 
                    (item.volume_kg > 0 ? `${parseFloat(item.volume_kg).toFixed(2)} kg` : `${parseFloat(item.volume_l).toFixed(2)} L`);
                
                const indicatorBadgeColor = getIndicatorBadgeColor(item.indikator_key);
                const sourceDisplay = item.source_display || getAsalDataText(item.user_role);
                const categoryDisplay = item.category_display || item.standard_category_name || 'Unknown';
                
                // Add special styling for liquid waste
                const isLiquidWaste = item.is_liquid_waste || item.standard_category === 'limbah_cair';
                const volumeClass = isLiquidWaste ? 'text-primary' : 'text-success';
                const volumeIcon = isLiquidWaste ? 'fas fa-flask' : 'fas fa-weight-hanging';
                
                html += `
                    <tr>
                        <td class="text-center fw-bold">${index + 1}</td>
                        <td>
                            <div class="fw-bold text-dark">${item.nama_unit || 'Unknown Unit'}</div>
                            ${item.nama_user ? `<small class="text-muted"><i class="fas fa-user me-1"></i>${item.nama_user}</small>` : ''}
                        </td>
                        <td>
                            <div class="fw-bold">${item.jenis_sampah || 'Unknown'}</div>
                            ${item.nama_sampah_detail && item.nama_sampah_detail !== item.jenis_sampah ? 
                                `<small class="text-muted">${item.nama_sampah_detail}</small>` : ''}
                            ${item.source_table === 'limbah_b3' ? 
                                `<span class="badge bg-danger badge-sm ms-1">B3</span>` : ''}
                        </td>
                        <td class="text-center">
                            <span class="fw-bold ${volumeClass}" 
                                  data-bs-toggle="tooltip" 
                                  title="${isLiquidWaste ? 'Limbah cair diukur dalam liter' : 'Limbah padat diukur dalam kilogram'}">
                                <i class="${volumeIcon} me-1"></i>${volumeDisplay}
                            </span>
                            ${item.processing_notes ? 
                                `<br><small class="text-muted">${item.processing_notes}</small>` : ''}
                        </td>
                        <td>
                            <span class="badge bg-${indicatorBadgeColor} rounded-pill" 
                                  data-bs-toggle="tooltip" 
                                  title="${item.indikator_description}">
                                ${item.indikator_name || 'Unknown'}
                            </span>
                        </td>
                        <td class="text-center">
                            <small class="text-muted">${formatDate(item.tanggal)}</small>
                        </td>
                        <td class="text-center">
                            ${item.bukti_foto ? 
                                `<button class="btn btn-sm btn-success" onclick="viewEvidence('${item.bukti_foto}')" title="Lihat Bukti">
                                    <i class="fas fa-eye"></i> Lihat Bukti
                                </button>` : 
                                `<button class="btn btn-sm btn-outline-primary" onclick="openEvidenceUpload(${JSON.stringify(item).replace(/"/g, '&quot;')})" title="Unggah Bukti">
                                    <i class="fas fa-upload"></i> Upload
                                </button>`
                            }
                        </td>
                        <td>
                            <small class="text-muted">${item.gedung || 'Unknown'}</small>
                            <br><small class="text-info">${sourceDisplay}</small>
                        </td>
                    </tr>
                `;
            });
        }

        tableBody.innerHTML = html;
        
        // Reinitialize tooltips for new content
        setTimeout(() => {
            initializeTooltips();
        }, 100);
    }

    function getIndicatorBadgeColor(indicatorKey) {
        const colors = {
            'indikator_1': 'success',
            'indikator_2': 'warning', 
            'indikator_3': 'success',
            'indikator_4': 'info',
            'indikator_5': 'danger',
            'indikator_6': 'primary',
            'indikator_7': 'warning'
        };
        return colors[indicatorKey] || 'secondary';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric'
        });
    }

    function viewIndicatorDetails(indicatorKey) {
        // Redirect to detailed view for specific indicator
        const tahun = document.getElementById('tahun').value;
        window.location.href = `<?= base_url('admin-pusat/indikator-uigm/detail') ?>/${indicatorKey}?tahun=${tahun}`;
    }

    function viewEvidence(buktiPath) {
        // Open evidence image in modal or new window
        if (buktiPath) {
            const imageUrl = `<?= base_url() ?>${buktiPath}`;
            window.open(imageUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        }
    }

    // Reload data when year filter changes
    document.getElementById('tahun').addEventListener('change', function() {
        loadUIGMIndicatorData();
        loadDetailedRecapData();
        loadStandardizedCategoryData();
        loadCategorySummary();
    });

    // Initialize Bootstrap tooltips
    function initializeTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true,
                delay: { show: 300, hide: 100 }
            });
        });
    }

    // Initialize tooltips when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTooltips();
    });

    // Print reference function
    function printReference() {
        const modalContent = document.querySelector('#referenceModal .modal-body').innerHTML;
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Dasar Klasifikasi Limbah - POLBAN Green Metric</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .reference-section { margin-bottom: 20px; }
                    .badge { display: inline-block; padding: 0.25em 0.6em; font-size: 0.75em; }
                    .bg-success { background-color: #198754 !important; color: white; }
                    .bg-warning { background-color: #ffc107 !important; color: black; }
                    .bg-info { background-color: #0dcaf0 !important; color: black; }
                    .bg-danger { background-color: #dc3545 !important; color: white; }
                    .bg-primary { background-color: #0d6efd !important; color: white; }
                    .bg-secondary { background-color: #6c757d !important; color: white; }
                    @media print {
                        .no-print { display: none; }
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="container-fluid">
                    <div class="text-center mb-4">
                        <h2>Dasar Klasifikasi Limbah</h2>
                        <h4>Sistem Manajemen Indikator UIGM</h4>
                        <p class="text-muted">Politeknik Negeri Bandung</p>
                        <hr>
                    </div>
                    ${modalContent}
                    <div class="text-center mt-4 text-muted">
                        <small>Dicetak pada: ${new Date().toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</small>
                    </div>
                </div>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    // Evidence Upload Functions
    function openEvidenceUpload(item) {
        // Populate modal with item data
        document.getElementById('upload_record_id').value = item.id;
        document.getElementById('upload_source_table').value = item.source_table || 'waste_management';
        document.getElementById('upload_indicator_key').value = item.indikator_key || '';
        
        document.getElementById('upload_unit_name').textContent = item.nama_unit || 'Unknown Unit';
        document.getElementById('upload_waste_type').textContent = item.jenis_sampah || 'Unknown';
        document.getElementById('upload_volume').textContent = item.volume_display || 
            (item.volume_kg > 0 ? `${item.volume_kg} kg` : `${item.volume_l} L`);
        document.getElementById('upload_date').textContent = formatDate(item.tanggal);
        
        // Update modal title
        const indicatorName = item.indikator_name || 'Unknown Indicator';
        document.getElementById('evidenceUploadModalLabel').innerHTML = 
            `<i class="fas fa-upload me-2"></i>Unggah Bukti Dukung - ${indicatorName}`;
        
        // Reset form
        document.getElementById('evidenceUploadForm').reset();
        document.getElementById('file_preview').style.display = 'none';
        document.getElementById('upload_progress').style.display = 'none';
        document.getElementById('upload_error').style.display = 'none';
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('evidenceUploadModal'));
        modal.show();
    }

    function submitEvidenceUpload() {
        const form = document.getElementById('evidenceUploadForm');
        const fileInput = document.getElementById('evidence_file');
        const submitBtn = document.getElementById('upload_submit_btn');
        const progressDiv = document.getElementById('upload_progress');
        const errorDiv = document.getElementById('upload_error');
        
        // Validate file
        if (!fileInput.files[0]) {
            showUploadError('Silakan pilih file terlebih dahulu');
            return;
        }
        
        const file = fileInput.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file.size > maxSize) {
            showUploadError('Ukuran file terlalu besar. Maksimal 5 MB.');
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf', 
                             'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!allowedTypes.includes(file.type)) {
            showUploadError('Format file tidak didukung. Gunakan JPG, PNG, PDF, DOC, atau DOCX.');
            return;
        }
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Show progress
        submitBtn.disabled = true;
        progressDiv.style.display = 'block';
        errorDiv.style.display = 'none';
        
        // Upload file
        fetch('<?= base_url('admin-pusat/indikator-uigm/upload-evidence') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Success
                showUploadSuccess('Bukti dukung berhasil diunggah!');
                
                // Close modal after delay
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('evidenceUploadModal')).hide();
                    
                    // Reload data to show updated evidence
                    loadDetailedRecapData();
                    loadStandardizedCategoryData();
                }, 1500);
            } else {
                showUploadError(data.message || 'Gagal mengunggah file');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showUploadError('Terjadi kesalahan saat mengunggah file');
        })
        .finally(() => {
            submitBtn.disabled = false;
            progressDiv.style.display = 'none';
        });
    }

    function showUploadError(message) {
        const errorDiv = document.getElementById('upload_error');
        const errorMessage = document.getElementById('error_message');
        
        errorMessage.textContent = message;
        errorDiv.style.display = 'block';
    }

    function showUploadSuccess(message) {
        const errorDiv = document.getElementById('upload_error');
        errorDiv.className = 'alert alert-success';
        errorDiv.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
        errorDiv.style.display = 'block';
    }

    // File preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('evidence_file');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewDiv = document.getElementById('file_preview');
                const previewContent = document.getElementById('preview_content');
                
                if (file) {
                    // Show file info
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    let fileIcon = 'fas fa-file';
                    
                    if (file.type.startsWith('image/')) {
                        fileIcon = 'fas fa-image';
                    } else if (file.type === 'application/pdf') {
                        fileIcon = 'fas fa-file-pdf';
                    } else if (file.type.includes('word')) {
                        fileIcon = 'fas fa-file-word';
                    }
                    
                    previewContent.innerHTML = `
                        <div class="d-flex align-items-center">
                            <i class="${fileIcon} fa-2x text-primary me-3"></i>
                            <div>
                                <strong>${file.name}</strong><br>
                                <small class="text-muted">Ukuran: ${fileSize} MB</small>
                            </div>
                        </div>
                    `;
                    
                    previewDiv.style.display = 'block';
                    
                    // Show image preview for image files
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewContent.innerHTML += `
                                <div class="mt-2">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                                </div>
                            `;
                        };
                        reader.readAsDataURL(file);
                    }
                } else {
                    previewDiv.style.display = 'none';
                }
            });
        }
    });
    </script>
    <style>
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .header-content h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 28px;
            font-weight: 700;
        }

        .header-content p {
            color: #6c757d;
            margin: 0 0 10px 0;
            font-size: 16px;
        }

        .reference-actions {
            margin-top: 8px;
        }

        .reference-actions .btn {
            font-size: 13px;
            padding: 6px 12px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.primary { border-left-color: #007bff; }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.info { border-left-color: #17a2b8; }
        .stat-card.danger { border-left-color: #dc3545; }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-card.primary .stat-icon { background: #007bff; }
        .stat-card.success .stat-icon { background: #28a745; }
        .stat-card.warning .stat-icon { background: #ffc107; }
        .stat-card.info .stat-icon { background: #17a2b8; }
        .stat-card.danger .stat-icon { background: #dc3545; }

        /* Bootstrap card border-left styles for UIGM indicator cards */
        .card.border-left-primary { border-left: 4px solid #007bff !important; }
        .card.border-left-success { border-left: 4px solid #28a745 !important; }
        .card.border-left-warning { border-left: 4px solid #ffc107 !important; }
        .card.border-left-info { border-left: 4px solid #17a2b8 !important; }
        .card.border-left-danger { border-left: 4px solid #dc3545 !important; }

        /* UIGM indicator cards responsive layout */
        #categorySummaryCards .card {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        #categorySummaryCards .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }

        #categorySummaryCards .text-xs {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        #categorySummaryCards .h6 {
            font-size: 1.1rem;
            font-weight: 700;
        }

        /* Responsive grid for 7 cards */
        @media (min-width: 1200px) {
            #categorySummaryCards .col-xl-3:nth-child(4n+1) {
                clear: left;
            }
        }

        @media (max-width: 1199px) and (min-width: 768px) {
            #categorySummaryCards .col-md-4:nth-child(3n+1) {
                clear: left;
            }
        }

        .stat-content h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }

        .stat-content p {
            margin: 0;
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 20px 25px;
        }

        .card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .table th {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }

        .table td {
            vertical-align: middle;
            font-size: 14px;
        }

        /* Professional Table Header Styles */
        .table-fixed-layout {
            table-layout: fixed;
            width: 100%;
        }

        .table-dark th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            border: none !important;
            color: white !important;
            font-weight: 600;
            font-size: 13px;
            padding: 15px 12px;
            vertical-align: middle;
            white-space: nowrap;
            position: relative;
        }

        .table-dark th .header-content {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            white-space: nowrap;
            line-height: 1.2;
        }

        .table-dark th .header-content.justify-content-center {
            justify-content: center;
        }

        .table-dark th .header-text {
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
            display: inline-block;
        }

        .table-dark th i.fas {
            font-size: 12px;
            flex-shrink: 0;
        }

        .table-dark th i.fa-info-circle {
            opacity: 0.7;
            transition: opacity 0.3s ease;
            cursor: help;
        }

        .table-dark th i.fa-info-circle:hover {
            opacity: 1;
        }

        /* Consistent spacing for icons */
        .table-dark th .me-2 {
            margin-right: 8px !important;
        }

        .table-dark th .ms-2 {
            margin-left: 8px !important;
        }

        /* Ensure proper alignment */
        .table-dark th.align-middle {
            vertical-align: middle !important;
        }

        .table-dark th.text-center .header-content {
            justify-content: center;
        }

        /* Responsive table improvements */
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table body styling improvements */
        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .table tbody td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        /* Additional professional styling */
        .table-dark th:first-child {
            border-top-left-radius: 10px;
        }

        .table-dark th:last-child {
            border-top-right-radius: 10px;
        }

        /* Ensure consistent icon sizing */
        .table-dark th i.fas:not(.fa-info-circle) {
            width: 14px;
            text-align: center;
        }

        /* Improve tooltip styling */
        .tooltip {
            font-size: 12px;
        }

        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            background-color: #2c3e50;
            border-radius: 6px;
            padding: 8px 12px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .table-dark th .header-text {
                font-size: 11px;
            }
            
            .table-dark th i.fas {
                font-size: 10px;
            }
            
            .table-dark th {
                padding: 10px 8px;
            }
        }

        /* Category badge styling improvements */
        .table tbody td .badge {
            font-size: 11px;
            padding: 6px 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            text-align: center;
        }

        .table tbody td .badge i {
            font-size: 10px;
            margin-right: 4px;
        }

        /* Ensure centered badges in Kategori Standar column */
        .table tbody td.text-center .badge {
            margin: 0 auto;
        }

        /* Kategori Standar column text formatting */
        .table tbody td .fw-bold {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .table tbody td small.text-muted {
            font-size: 11px;
            color: #6c757d;
            line-height: 1.2;
            display: block;
            margin-top: 2px;
        }

        .waste-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }

            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-actions {
                width: 100%;
            }

            .header-actions form {
                width: 100%;
            }

            .table {
                font-size: 12px;
            }

            .waste-icon {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
        }

        /* Tooltip Enhancements */
        .tooltip {
            font-size: 12px;
        }

        .tooltip-inner {
            max-width: 300px;
            text-align: left;
            background-color: #2c3e50;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #2c3e50;
        }

        .bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: #2c3e50;
        }

        .bs-tooltip-start .tooltip-arrow::before {
            border-left-color: #2c3e50;
        }

        .bs-tooltip-end .tooltip-arrow::before {
            border-right-color: #2c3e50;
        }

        /* Modal Enhancements */
        .reference-section {
            margin-bottom: 1.5rem;
        }

        .reference-section h6 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }

        .reference-section .card {
            border-width: 2px;
        }

        .reference-section .badge {
            font-size: 0.75em;
            padding: 0.35em 0.65em;
        }

        /* Info icon hover effects */
        .fas.fa-info-circle {
            cursor: help;
            transition: all 0.2s ease;
        }

        .fas.fa-info-circle:hover {
            transform: scale(1.1);
            opacity: 0.8;
        }
    </style>

    <script>
        // Data cleanup functions
        function checkOrphanedData() {
            if (!confirm('Periksa data yang tidak valid? Ini akan menampilkan informasi tentang data yang bermasalah.')) {
                return;
            }
            
            fetch('<?= base_url('admin-pusat/indikator-uigm/check-orphaned-data') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let message = `Data yang diperiksa:\n`;
                        message += `- Data sampah valid: ${data.valid_waste_count}\n`;
                        message += `- Data B3 valid: ${data.valid_b3_count}\n`;
                        message += `- Data tidak valid: ${data.total_orphaned}\n\n`;
                        
                        if (data.total_orphaned > 0) {
                            message += `Ditemukan ${data.total_orphaned} data yang tidak valid (orphaned data).\n`;
                            message += `Data ini mungkin berasal dari testing atau user yang sudah dihapus.\n\n`;
                            message += `Klik "Bersihkan" untuk menghapus data yang tidak valid.`;
                        } else {
                            message += `Semua data valid. Tidak ada data yang perlu dibersihkan.`;
                        }
                        
                        alert(message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memeriksa data');
                });
        }
        
        function cleanOrphanedData() {
            if (!confirm('Hapus semua data yang tidak valid?\n\nData yang akan dihapus adalah data sampah yang tidak terhubung dengan user yang valid.\n\nTindakan ini tidak dapat dibatalkan!')) {
                return;
            }
            
            fetch('<?= base_url('admin-pusat/indikator-uigm/clean-orphaned-data') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Pembersihan selesai!\n\n${data.message}\n\nHalaman akan dimuat ulang untuk menampilkan data yang sudah dibersihkan.`);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat membersihkan data');
                });
        }
    </script>
</body>
</html>