<?php
/**
 * Security Transportation Input - UI GreenMetric POLBAN
 * Form input statistik transportasi untuk security
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title><?= $title ?? 'Input Statistik Transportasi' ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/dashboard.css') ?>?v=<?= time() ?>" rel="stylesheet">
    
    <style>
        /* ===== FORM STYLES (SAMA DENGAN WASTE) ===== */
        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }

        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .card-header {
            background: white;
            border-bottom: 2px solid #e9ecef;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
        }

        .btn {
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }

        .info-box {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .info-box h6 {
            color: #667eea;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .info-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .info-box li {
            color: #495057;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        /* Button group spacing */
        .d-flex.gap-2 {
            gap: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 24px;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-car-side me-2"></i>
                <?= isset($edit_data) ? 'Edit' : 'Input' ?> Statistik Transportasi
            </h1>
            <p>Form input data kendaraan untuk monitoring transportasi kampus</p>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="info-box">
            <h6><i class="fas fa-info-circle me-2"></i>Informasi Penting</h6>
            <ul>
                <li>Klik tombol <strong>"+ Tambah Data Kendaraan"</strong> untuk menginput data baru</li>
                <li>Pilih tipe pencatatan: <strong>Harian</strong>, <strong>Mingguan (Back-up)</strong>, atau <strong>Bulanan (Back-up)</strong></li>
                <li>Data akan otomatis tersimpan dan dapat diedit kembali dari tabel</li>
            </ul>
        </div>

        <!-- Data History Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Data yang Sudah Diinput</h5>
                <div class="d-flex gap-2">
                    <?php if (!empty($available_periods) && is_array($available_periods)): ?>
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm" onclick="bulkDelete()" style="display: none;">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openAddModal()">
                        <i class="fas fa-plus me-1"></i> Tambah Data Kendaraan
                    </button>
                    <a href="<?= base_url('security/transportation/export-excel') ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                    <a href="<?= base_url('security/transportation/export-pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($available_periods) && is_array($available_periods)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input" style="cursor: pointer;" title="Pilih Semua">
                                </th>
                                <th style="width: 50px;">No</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 18%; white-space: nowrap;">Jenis Kendaraan</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 15%;">Bahan Bakar</th>
                                <th class="text-center" style="width: 10%;">Jumlah</th>
                                <th class="text-center" style="width: 12%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($available_periods as $period): ?>
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input row-checkbox" data-id="<?= $period['id'] ?? 0 ?>" style="cursor: pointer;">
                                    </td>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if ($period['periode'] === 'Harian' && isset($period['tanggal_pencatatan'])): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                <?= date('d/m/Y', strtotime($period['tanggal_pencatatan'])) ?>
                                            </small>
                                        <?php elseif ($period['periode'] === 'Mingguan (Back-up)'): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-week me-1"></i>
                                                <?= isset($period['tanggal_mulai']) ? date('d/m/Y', strtotime($period['tanggal_mulai'])) : '-' ?>
                                                <br>
                                                <i class="fas fa-arrow-right me-1"></i>
                                                <?= isset($period['tanggal_selesai']) ? date('d/m/Y', strtotime($period['tanggal_selesai'])) : '-' ?>
                                            </small>
                                        <?php elseif ($period['periode'] === 'Bulanan (Back-up)'): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                <?= isset($period['bulan']) ? esc($period['bulan']) : '-' ?>
                                                <?= isset($period['tahun']) ? esc($period['tahun']) : '' ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Tampilkan kategori kendaraan apa adanya dari database (teks saja)
                                        // TIDAK ADA FALLBACK - langsung ambil dari kategori_kendaraan
                                        ?>
                                        <?= esc($period['kategori_kendaraan'] ?? 'Belum Diisi') ?>
                                    </td>
                                    <td>
                                        <?php
                                        // Get status kendaraan from database
                                        $statusKendaraan = $period['status_kendaraan'] ?? null;
                                        
                                        // Normalize for comparison (case-insensitive, trim whitespace)
                                        $statusLower = strtolower(trim($statusKendaraan ?? ''));
                                        
                                        // Set default values
                                        $statusColor = 'secondary';
                                        $statusIcon = '<i class="fas fa-question-circle me-1"></i>';
                                        $statusText = 'Belum Diisi';
                                        
                                        // Determine badge based on status (flexible matching)
                                        if (!empty($statusKendaraan) && $statusLower !== 'tidak diketahui') {
                                            $statusText = $statusKendaraan;
                                            
                                            // Check using strpos for flexible matching
                                            if (strpos($statusLower, 'universitas') !== false) {
                                                $statusColor = 'success';
                                                $statusIcon = '<i class="fas fa-university me-1"></i>';
                                            } elseif (strpos($statusLower, 'pribadi') !== false) {
                                                $statusColor = 'info';
                                                $statusIcon = '<i class="fas fa-user me-1"></i>';
                                            } elseif (strpos($statusLower, 'sewa') !== false) {
                                                $statusColor = 'warning text-dark';
                                                $statusIcon = '<i class="fas fa-key me-1"></i>';
                                            } elseif (strpos($statusLower, 'umum') !== false) {
                                                $statusColor = 'warning text-dark';
                                                $statusIcon = '<i class="fas fa-bus me-1"></i>';
                                            }
                                        }
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>">
                                            <?= $statusIcon ?>
                                            <?= esc($statusText) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $fuelType = $period['jenis_bahan_bakar'] ?? '';
                                        $fuelIcon = match($fuelType) {
                                            'Listrik' => '<i class="fas fa-bolt text-success me-1"></i>',
                                            'Bensin' => '<i class="fas fa-gas-pump text-warning me-1"></i>',
                                            'Diesel' => '<i class="fas fa-gas-pump text-danger me-1"></i>',
                                            'Non-BBM' => '<i class="fas fa-bicycle text-info me-1"></i>',
                                            default => '<i class="fas fa-question-circle me-1"></i>'
                                        };
                                        ?>
                                        <?= $fuelIcon ?>
                                        <small><?= esc($fuelType) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <strong class="text-primary"><?= number_format($period['jumlah_total'] ?? 0) ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                onclick="openEditModal(<?= $period['id'] ?? 0 ?>)" 
                                                title="Edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum Ada Data</h5>
                    <p class="text-muted">Klik tombol "Tambah Data Kendaraan" untuk mulai menginput data</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Data Kendaraan -->
    <div class="modal fade" id="transportModal" tabindex="-1" aria-labelledby="transportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: #2c3e50; color: white;">
                    <h5 class="modal-title" id="transportModalLabel">
                        <i class="fas fa-car-side"></i> <span id="modalTitle">Tambah Data Kendaraan</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url('security/transportation/save') ?>" method="POST" id="transportForm">
                    <div class="modal-body">
                        <?= csrf_field() ?>
                        <input type="hidden" name="edit_id" id="edit_id" value="">
                        
                        <!-- Alert Info ZEV -->
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Info:</strong> Pilih jenis kendaraan dan bahan bakar. Kategori akan terisi otomatis.
                        </div>

                        <!-- ROW 1: TIPE PENCATATAN & TANGGAL -->
                        <div class="row mb-3">
                            <!-- Tipe Pencatatan -->
                            <div class="col-md-6">
                                <label for="tipe_pencatatan" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Tipe Pencatatan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="tipe_pencatatan" name="tipe_pencatatan" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="Harian">Harian</option>
                                    <option value="Mingguan (Back-up)">Mingguan (Back-up)</option>
                                    <option value="Bulanan (Back-up)">Bulanan (Back-up)</option>
                                </select>
                                <small class="form-text text-muted">Pilih tipe pencatatan data</small>
                            </div>

                            <!-- Tanggal Kejadian (Harian) -->
                            <div class="col-md-6" id="input_harian">
                                <label for="tanggal_pencatatan" class="form-label fw-bold">
                                    <i class="fas fa-calendar-day me-1"></i>
                                    Tanggal Kejadian <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_pencatatan" 
                                       name="tanggal_pencatatan" 
                                       value="<?= date('Y-m-d') ?>" 
                                       max="<?= date('Y-m-d') ?>"
                                       required>
                                <small class="form-text text-muted">Tanggal kejadian transportasi</small>
                            </div>
                        </div>

                        <!-- RENTANG TANGGAL (Mingguan Back-up) - Hidden by default -->
                        <div class="row mb-3" id="input_rentang" style="display: none;">
                            <div class="col-md-6">
                                <label for="tanggal_mulai" class="form-label fw-bold">
                                    <i class="fas fa-calendar-week me-1"></i>
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_mulai" 
                                       name="tanggal_mulai" 
                                       max="<?= date('Y-m-d') ?>">
                                <small class="form-text text-muted">Tanggal awal periode mingguan</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tanggal_selesai" class="form-label fw-bold">
                                    <i class="fas fa-calendar-check me-1"></i>
                                    Tanggal Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal_selesai" 
                                       name="tanggal_selesai" 
                                       max="<?= date('Y-m-d') ?>">
                                <small class="form-text text-muted">Tanggal akhir periode mingguan</small>
                            </div>
                        </div>

                        <!-- BULANAN (Dropdown Bulan & Tahun) - Hidden by default -->
                        <div class="row mb-3" id="input_bulanan" style="display: none;">
                            <div class="col-md-6">
                                <label for="bulan" class="form-label fw-bold">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Pilih Bulan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="bulan" name="bulan">
                                    <option value="">Pilih Bulan</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                                <small class="form-text text-muted">Bulan periode bulanan</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tahun" class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>
                                    Pilih Tahun <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="tahun" name="tahun">
                                    <option value="">Pilih Tahun</option>
                                    <?php for ($year = 2024; $year <= 2030; $year++): ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                                <small class="form-text text-muted">Tahun periode bulanan</small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ROW 2: JENIS KENDARAAN & KATEGORI -->
                        <div class="row mb-3">
                            <!-- Jenis Kendaraan -->
                            <div class="col-md-6">
                                <label for="kategori_kendaraan" class="form-label fw-bold">
                                    <i class="fas fa-car me-1"></i>
                                    Jenis Kendaraan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kategori_kendaraan" name="kategori_kendaraan" required>
                                    <option value="">Pilih Jenis</option>
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= esc($cat['nama_kategori']) ?>" 
                                                    data-zev="<?= $cat['is_zev'] ?? 0 ?>">
                                                <?= esc($cat['nama_kategori']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="form-text text-muted">Contoh: Sepeda Motor, Mobil Penumpang, Bus</small>
                            </div>
                            
                            <!-- Status Kendaraan -->
                            <div class="col-md-6">
                                <label for="status_kendaraan" class="form-label fw-bold">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Status Kendaraan
                                </label>
                                <select class="form-select" id="status_kendaraan" name="status_kendaraan">
                                    <option value="">Pilih Status</option>
                                    <option value="Milik Universitas">🏛️ Milik Universitas</option>
                                    <option value="Milik Pribadi">👤 Milik Pribadi</option>
                                    <option value="Kendaraan Sewa">🔑 Kendaraan Sewa</option>
                                    <option value="Kendaraan Umum">🚌 Kendaraan Umum</option>
                                </select>
                                <small class="form-text text-muted">Kepemilikan atau status kendaraan</small>
                            </div>
                        </div>
                        
                        <!-- Hidden field for kategori_sederhana (auto-filled by backend) -->
                        <input type="hidden" id="kategori_sederhana" name="kategori_sederhana" value="">

                        <!-- ROW 3: BAHAN BAKAR & JUMLAH -->
                        <div class="row mb-3">
                            <!-- Jenis Bahan Bakar -->
                            <div class="col-md-6">
                                <label for="jenis_bahan_bakar" class="form-label fw-bold">
                                    <i class="fas fa-gas-pump me-1"></i>
                                    Bahan Bakar <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="jenis_bahan_bakar" name="jenis_bahan_bakar" required disabled>
                                    <option value="">Pilih jenis kendaraan dulu</option>
                                </select>
                                <small class="form-text text-muted">Pilihan muncul setelah jenis dipilih</small>
                            </div>

                            <!-- Jumlah Total -->
                            <div class="col-md-6">
                                <label for="jumlah_total" class="form-label fw-bold">
                                    <i class="fas fa-calculator me-1"></i>
                                    Jumlah Kendaraan <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="jumlah_total" name="jumlah_total" 
                                       value="1" min="1" required>
                                <small class="form-text text-muted">Total jumlah kendaraan yang tercatat</small>
                            </div>
                        </div>

                        <!-- ROW 4: SHUTTLE CHECKBOX -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold d-block">
                                    <i class="fas fa-bus me-1"></i>
                                    Jenis Kendaraan Khusus
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_shuttle" name="is_shuttle" value="1">
                                    <label class="form-check-label" for="is_shuttle">
                                        <strong>Kendaraan Shuttle Kampus</strong>
                                        <small class="text-muted d-block">Centang jika ini adalah shuttle/bus kampus</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== FORM DEBUG ===');
            console.log('Form exists:', document.getElementById('transportForm') !== null);
            console.log('Tipe field exists:', document.getElementById('tipe_pencatatan') !== null);
            console.log('Tanggal field exists:', document.getElementById('tanggal_pencatatan') !== null);
            console.log('==================');
            
            // Set default date if empty
            const tanggalInput = document.getElementById('tanggal_pencatatan');
            if (tanggalInput && !tanggalInput.value) {
                const today = new Date().toISOString().split('T')[0];
                tanggalInput.value = today;
                console.log('Set default date:', today);
            }
            
            // Initialize tipe pencatatan toggle
            initTipePencatatanToggle();
            
            // Initialize kategori kendaraan to bahan bakar mapping
            initKategoriToBahanBakarMapping();
            
            // Initialize kategori sederhana auto-fill
            initKategoriSederhanaAutoFill();
        });

        // Open Add Modal
        function openAddModal() {
            // Reset form
            document.getElementById('transportForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('tanggal_pencatatan').value = '<?= date('Y-m-d') ?>';
            document.getElementById('jumlah_total').value = '1';
            document.getElementById('tipe_pencatatan').value = '';
            document.getElementById('is_shuttle').checked = false;
            
            // Reset visibility
            document.getElementById('input_harian').style.display = 'block';
            document.getElementById('input_rentang').style.display = 'none';
            document.getElementById('input_bulanan').style.display = 'none';
            
            // Reset kategori and bahan bakar
            document.getElementById('kategori_kendaraan').value = '';
            document.getElementById('jenis_bahan_bakar').value = '';
            document.getElementById('jenis_bahan_bakar').disabled = true;
            
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Tambah Data Kendaraan';
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('transportModal'));
            modal.show();
        }

        // Open Edit Modal
        function openEditModal(id) {
            // Redirect to edit URL - the page will auto-open modal with data
            window.location.href = '<?= base_url('security/transportation') ?>?edit=' + id;
        }

        // Auto-open modal if editing
        <?php if (isset($edit_data) && !empty($edit_data)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Fill form with edit data
            document.getElementById('edit_id').value = '<?= $edit_data['id'] ?? '' ?>';
            document.getElementById('tipe_pencatatan').value = '<?= $edit_data['periode'] ?? '' ?>';
            document.getElementById('kategori_kendaraan').value = '<?= $edit_data['kategori_kendaraan'] ?? '' ?>';
            document.getElementById('jumlah_total').value = '<?= $edit_data['jumlah_total'] ?? '1' ?>';
            document.getElementById('is_shuttle').checked = <?= (!empty($edit_data['is_shuttle']) && $edit_data['is_shuttle'] == 1) ? 'true' : 'false' ?>;
            
            // Set status kendaraan if available
            <?php if (!empty($edit_data['status_kendaraan'])): ?>
                document.getElementById('status_kendaraan').value = '<?= $edit_data['status_kendaraan'] ?>';
            <?php endif; ?>
            
            // Set date fields based on periode
            <?php if ($edit_data['periode'] === 'Harian' && !empty($edit_data['tanggal_pencatatan'])): ?>
                document.getElementById('tanggal_pencatatan').value = '<?= $edit_data['tanggal_pencatatan'] ?>';
            <?php elseif ($edit_data['periode'] === 'Mingguan (Back-up)'): ?>
                document.getElementById('tanggal_mulai').value = '<?= $edit_data['tanggal_mulai'] ?? '' ?>';
                document.getElementById('tanggal_selesai').value = '<?= $edit_data['tanggal_selesai'] ?? '' ?>';
            <?php elseif ($edit_data['periode'] === 'Bulanan (Back-up)'): ?>
                document.getElementById('bulan').value = '<?= $edit_data['bulan'] ?? '' ?>';
                document.getElementById('tahun').value = '<?= $edit_data['tahun'] ?? '' ?>';
            <?php endif; ?>
            
            // Trigger tipe pencatatan change to show correct fields
            const tipePencatatanEvent = new Event('change');
            document.getElementById('tipe_pencatatan').dispatchEvent(tipePencatatanEvent);
            
            // Trigger kategori change to populate bahan bakar
            const kategoriEvent = new Event('change');
            document.getElementById('kategori_kendaraan').dispatchEvent(kategoriEvent);
            
            // Set bahan bakar after kategori is processed
            setTimeout(function() {
                document.getElementById('jenis_bahan_bakar').value = '<?= $edit_data['jenis_bahan_bakar'] ?? '' ?>';
            }, 200);
            
            // Update modal title
            document.getElementById('modalTitle').textContent = 'Edit Data Kendaraan';
            
            // Show modal
            var modal = new bootstrap.Modal(document.getElementById('transportModal'));
            modal.show();
        });
        <?php endif; ?>

        // Reset Form when modal is closed
        $('#transportModal').on('hidden.bs.modal', function () {
            // If we were editing, redirect to clear the edit parameter
            <?php if (isset($edit_data) && !empty($edit_data)): ?>
                window.location.href = '<?= base_url('security/transportation') ?>';
            <?php else: ?>
                document.getElementById('transportForm').reset();
                document.getElementById('edit_id').value = '';
                document.getElementById('tanggal_pencatatan').value = '<?= date('Y-m-d') ?>';
                document.getElementById('jumlah_total').value = '1';
                document.getElementById('is_shuttle').checked = false;
            <?php endif; ?>
        });
        /**
         * Auto-fill Kategori Sederhana (Hidden Field) based on Bahan Bakar and Jenis Kendaraan
         * This runs in background - user doesn't see it but data is still saved to database
         */
        function initKategoriSederhanaAutoFill() {
            const kategoriSederhana = document.getElementById('kategori_sederhana');
            const jenisBahanBakar = document.getElementById('jenis_bahan_bakar');
            const kategoriKendaraan = document.getElementById('kategori_kendaraan');
            
            if (!kategoriSederhana || !jenisBahanBakar || !kategoriKendaraan) return;
            
            // Function to auto-determine kategori sederhana
            function autoFillKategoriSederhana() {
                const bahanBakar = jenisBahanBakar.value;
                const jenisKendaraan = kategoriKendaraan.value.toLowerCase();
                
                // Rule 1: Listrik atau Non-BBM → Fasilitas Kampus
                if (bahanBakar === 'Listrik' || bahanBakar === 'Non-BBM' || jenisKendaraan.includes('sepeda')) {
                    kategoriSederhana.value = 'Fasilitas Kampus';
                    console.log('Auto-fill: Fasilitas Kampus (Listrik/Non-BBM/Sepeda)');
                }
                // Rule 2: Motor → Roda Dua
                else if (jenisKendaraan.includes('motor') || jenisKendaraan.includes('roda dua') || jenisKendaraan.includes('roda 2')) {
                    kategoriSederhana.value = 'Roda Dua';
                    console.log('Auto-fill: Roda Dua (Motor)');
                }
                // Rule 3: Mobil, Bus, Truck → Roda Empat
                else if (jenisKendaraan.includes('mobil') || jenisKendaraan.includes('bus') || 
                         jenisKendaraan.includes('truck') || jenisKendaraan.includes('roda empat') || 
                         jenisKendaraan.includes('roda 4')) {
                    kategoriSederhana.value = 'Roda Empat';
                    console.log('Auto-fill: Roda Empat (Mobil/Bus/Truck)');
                }
                // Default: Bensin → Roda Dua, Diesel → Roda Empat
                else if (bahanBakar === 'Bensin') {
                    kategoriSederhana.value = 'Roda Dua';
                    console.log('Auto-fill: Roda Dua (Bensin default)');
                } else if (bahanBakar === 'Diesel') {
                    kategoriSederhana.value = 'Roda Empat';
                    console.log('Auto-fill: Roda Empat (Diesel default)');
                }
            }
            
            // Listen to changes
            jenisBahanBakar.addEventListener('change', autoFillKategoriSederhana);
            kategoriKendaraan.addEventListener('change', function() {
                // Delay to allow bahan bakar to update first
                setTimeout(autoFillKategoriSederhana, 200);
            });
            
            // If editing, restore kategori sederhana value
            <?php if (isset($edit_data) && !empty($edit_data['kategori_sederhana'])): ?>
                setTimeout(function() {
                    kategoriSederhana.value = '<?= $edit_data['kategori_sederhana'] ?>';
                    console.log('Restored kategori sederhana:', '<?= $edit_data['kategori_sederhana'] ?>');
                }, 300);
            <?php endif; ?>
        }
        
        // Dynamic Bahan Bakar based on Kategori Kendaraan
        function initKategoriToBahanBakarMapping() {
            const kategoriKendaraan = document.getElementById('kategori_kendaraan');
            const jenisBahanBakar = document.getElementById('jenis_bahan_bakar');
            
            if (!kategoriKendaraan || !jenisBahanBakar) return;
            
            // Build fuel options from database (passed from controller)
            const allFuels = <?= json_encode($fuels ?? []) ?>;
            const allCategories = <?= json_encode($categories ?? []) ?>;
            
            // Build mapping: Kategori -> Bahan Bakar options
            const bahanBakarMapping = {};
            const defaultBahanBakar = {};
            
            allCategories.forEach(function(cat) {
                const categoryName = cat.nama_kategori;
                
                // Get allowed fuels for this category (from kategori_bahan_bakar field)
                let allowedFuels = [];
                if (cat.kategori_bahan_bakar) {
                    allowedFuels = cat.kategori_bahan_bakar.split(',').map(f => f.trim());
                }
                
                // Build fuel options for this category
                bahanBakarMapping[categoryName] = [];
                allFuels.forEach(function(fuel) {
                    // If category has specific allowed fuels, filter by them
                    if (allowedFuels.length > 0) {
                        if (allowedFuels.includes(fuel.nama_bahan_bakar)) {
                            bahanBakarMapping[categoryName].push({
                                value: fuel.nama_bahan_bakar,
                                label: fuel.nama_bahan_bakar + (fuel.is_zev == 1 ? ' (ZEV)' : '')
                            });
                        }
                    } else {
                        // If no specific fuels defined, show all fuels
                        bahanBakarMapping[categoryName].push({
                            value: fuel.nama_bahan_bakar,
                            label: fuel.nama_bahan_bakar + (fuel.is_zev == 1 ? ' (ZEV)' : '')
                        });
                    }
                });
                
                // Set default fuel (first in the list)
                if (bahanBakarMapping[categoryName].length > 0) {
                    defaultBahanBakar[categoryName] = bahanBakarMapping[categoryName][0].value;
                }
            });
            
            // Function to update bahan bakar options
            function updateBahanBakarOptions() {
                const selectedKategori = kategoriKendaraan.value;
                
                // Clear current options
                jenisBahanBakar.innerHTML = '';
                
                if (!selectedKategori) {
                    // No category selected - disable and show placeholder
                    jenisBahanBakar.disabled = true;
                    const placeholderOption = document.createElement('option');
                    placeholderOption.value = '';
                    placeholderOption.textContent = 'Pilih kategori kendaraan terlebih dahulu';
                    jenisBahanBakar.appendChild(placeholderOption);
                    console.log('Bahan Bakar: Disabled (no category selected)');
                } else {
                    // Category selected - enable and populate options
                    jenisBahanBakar.disabled = false;
                    
                    // Add default option
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Pilih Bahan Bakar --';
                    jenisBahanBakar.appendChild(defaultOption);
                    
                    // Add options based on selected category
                    const options = bahanBakarMapping[selectedKategori] || [];
                    options.forEach(function(option) {
                        const optionElement = document.createElement('option');
                        optionElement.value = option.value;
                        optionElement.textContent = option.label;
                        jenisBahanBakar.appendChild(optionElement);
                    });
                    
                    // ========================================
                    // SMART AUTO-FILL: Pilih bahan bakar berdasarkan jenis kendaraan
                    // ========================================
                    let smartDefaultValue = null;
                    const kategoriLower = selectedKategori.toLowerCase();
                    
                    // Rule 1: Sepeda → Non-BBM (jika ada, fallback ke Listrik)
                    if (kategoriLower.includes('sepeda') && !kategoriLower.includes('motor')) {
                        smartDefaultValue = findFuelOption(options, ['Non-BBM', 'Listrik']);
                        console.log('🚲 Smart Auto-fill: Sepeda → Non-BBM/Listrik');
                    }
                    // Rule 2: Motor Listrik / Kendaraan Listrik → Listrik
                    else if (kategoriLower.includes('listrik')) {
                        smartDefaultValue = findFuelOption(options, ['Listrik']);
                        console.log('⚡ Smart Auto-fill: Kendaraan Listrik → Listrik');
                    }
                    // Rule 3: Sepeda Motor → Bensin
                    else if (kategoriLower.includes('motor') || kategoriLower.includes('roda dua') || kategoriLower.includes('kategori l')) {
                        smartDefaultValue = findFuelOption(options, ['Bensin', 'Listrik']);
                        console.log('🏍️ Smart Auto-fill: Sepeda Motor → Bensin');
                    }
                    // Rule 4: Shuttle Bus / Bus → Diesel (fallback Bensin)
                    else if (kategoriLower.includes('bus') || kategoriLower.includes('shuttle')) {
                        smartDefaultValue = findFuelOption(options, ['Diesel', 'Bensin', 'Listrik']);
                        console.log('🚌 Smart Auto-fill: Bus → Diesel');
                    }
                    // Rule 5: Truk → Diesel
                    else if (kategoriLower.includes('truk') || kategoriLower.includes('truck')) {
                        smartDefaultValue = findFuelOption(options, ['Diesel', 'Bensin']);
                        console.log('🚚 Smart Auto-fill: Truk → Diesel');
                    }
                    // Rule 6: Mobil Pribadi / Mobil Penumpang → Bensin (fallback Diesel)
                    else if (kategoriLower.includes('mobil')) {
                        smartDefaultValue = findFuelOption(options, ['Bensin', 'Diesel', 'Listrik']);
                        console.log('🚗 Smart Auto-fill: Mobil → Bensin');
                    }
                    // Default: Gunakan default dari mapping database
                    else {
                        smartDefaultValue = defaultBahanBakar[selectedKategori];
                        console.log('📋 Smart Auto-fill: Default dari database');
                    }
                    
                    // Set the smart default value
                    if (smartDefaultValue) {
                        jenisBahanBakar.value = smartDefaultValue;
                        console.log('✅ Bahan Bakar: Auto-filled to', smartDefaultValue, 'for', selectedKategori);
                    }
                    
                    // SPECIAL CASE: Auto-lock if only one fuel option available
                    if (options.length === 1) {
                        jenisBahanBakar.setAttribute('readonly', 'readonly');
                        jenisBahanBakar.style.backgroundColor = '#e9ecef';
                        jenisBahanBakar.style.cursor = 'not-allowed';
                        console.log('🔒 Bahan Bakar: Locked (only one option available)');
                    } else {
                        jenisBahanBakar.removeAttribute('readonly');
                        jenisBahanBakar.style.backgroundColor = '';
                        jenisBahanBakar.style.cursor = '';
                    }
                    
                    console.log('Bahan Bakar: Updated for', selectedKategori, '- Options:', options.length);
                }
            }
            
            // Helper function: Find fuel option from priority list
            function findFuelOption(options, priorityList) {
                for (let i = 0; i < priorityList.length; i++) {
                    const fuel = priorityList[i];
                    const found = options.find(opt => opt.value === fuel);
                    if (found) {
                        return found.value;
                    }
                }
                // If no match found, return first available option
                return options.length > 0 ? options[0].value : null;
            }
            
            // Listen to kategori kendaraan change
            kategoriKendaraan.addEventListener('change', updateBahanBakarOptions);
            
            // Initialize on page load
            updateBahanBakarOptions();
            
            // If editing, restore the selected bahan bakar value
            <?php if (isset($edit_data) && !empty($edit_data['jenis_bahan_bakar'])): ?>
                setTimeout(function() {
                    jenisBahanBakar.value = '<?= $edit_data['jenis_bahan_bakar'] ?>';
                    console.log('Restored bahan bakar value:', '<?= $edit_data['jenis_bahan_bakar'] ?>');
                }, 100);
            <?php endif; ?>
        }
        
        // Toggle between Harian, Mingguan, and Bulanan
        function initTipePencatatanToggle() {
            const tipePencatatan = document.getElementById('tipe_pencatatan');
            const inputHarian = document.getElementById('input_harian');
            const inputRentang = document.getElementById('input_rentang');
            const inputBulanan = document.getElementById('input_bulanan');
            const tanggalPencatatan = document.getElementById('tanggal_pencatatan');
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            const bulan = document.getElementById('bulan');
            const tahun = document.getElementById('tahun');
            
            if (!tipePencatatan) return;
            
            // Function to toggle inputs
            function toggleInputs() {
                const tipe = tipePencatatan.value;
                
                if (tipe === 'Harian') {
                    // Show Harian input only
                    inputHarian.style.display = 'block';
                    inputRentang.style.display = 'none';
                    inputBulanan.style.display = 'none';
                    
                    // Set required
                    tanggalPencatatan.required = true;
                    tanggalMulai.required = false;
                    tanggalSelesai.required = false;
                    bulan.required = false;
                    tahun.required = false;
                    
                    // Clear other values
                    tanggalMulai.value = '';
                    tanggalSelesai.value = '';
                    bulan.value = '';
                    tahun.value = '';
                    
                    console.log('Mode: Harian');
                } else if (tipe === 'Mingguan (Back-up)') {
                    // Show Rentang input for Mingguan
                    inputHarian.style.display = 'none';
                    inputRentang.style.display = 'flex';
                    inputBulanan.style.display = 'none';
                    
                    // Set required
                    tanggalPencatatan.required = false;
                    tanggalMulai.required = true;
                    tanggalSelesai.required = true;
                    bulan.required = false;
                    tahun.required = false;
                    
                    // Clear other values
                    tanggalPencatatan.value = '';
                    bulan.value = '';
                    tahun.value = '';
                    
                    console.log('Mode: Mingguan (Back-up)');
                } else if (tipe === 'Bulanan (Back-up)') {
                    // Show Bulanan input (Bulan & Tahun dropdowns)
                    inputHarian.style.display = 'none';
                    inputRentang.style.display = 'none';
                    inputBulanan.style.display = 'flex';
                    
                    // Set required
                    tanggalPencatatan.required = false;
                    tanggalMulai.required = false;
                    tanggalSelesai.required = false;
                    bulan.required = true;
                    tahun.required = true;
                    
                    // Clear other values
                    tanggalPencatatan.value = '';
                    tanggalMulai.value = '';
                    tanggalSelesai.value = '';
                    
                    console.log('Mode: Bulanan (Back-up)');
                } else {
                    // Hide all if no selection
                    inputHarian.style.display = 'block';
                    inputRentang.style.display = 'none';
                    inputBulanan.style.display = 'none';
                }
            }
            
            // Listen to change event
            tipePencatatan.addEventListener('change', toggleInputs);
            
            // Initialize on page load
            toggleInputs();
        }
        
        // Confirm delete function
        function confirmDelete(id, periode) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Yakin ingin menghapus data "${periode}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('security/transportation/delete/') ?>' + id;
                }
            });
        }

        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // ===== BULK DELETE FUNCTIONALITY =====
        
        /**
         * Bind checkbox event listeners
         */
        function bindCheckboxEvents() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            
            if (!selectAllCheckbox) return;
            
            // Select All checkbox handler
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                rowCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
                updateBulkDeleteButton();
            });
            
            // Individual checkbox handlers
            rowCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateSelectAllCheckbox();
                    updateBulkDeleteButton();
                });
            });
        }
        
        /**
         * Update Select All checkbox state
         */
        function updateSelectAllCheckbox() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            
            if (!selectAllCheckbox || rowCheckboxes.length === 0) return;
            
            const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
            const totalCount = rowCheckboxes.length;
            
            // Update select all checkbox state
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        /**
         * Update bulk delete button visibility and count
         */
        function updateBulkDeleteButton() {
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const selectedCount = document.getElementById('selectedCount');
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            
            if (!btnBulkDelete || !selectedCount) return;
            
            const count = checkedBoxes.length;
            
            if (count > 0) {
                btnBulkDelete.style.display = 'inline-block';
                selectedCount.textContent = count;
            } else {
                btnBulkDelete.style.display = 'none';
                selectedCount.textContent = '0';
            }
        }
        
        /**
         * Bulk delete selected items
         */
        function bulkDelete() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Data Terpilih',
                    text: 'Silakan pilih data yang ingin dihapus terlebih dahulu.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Collect IDs
            const ids = [];
            checkedBoxes.forEach(function(checkbox) {
                const id = parseInt(checkbox.getAttribute('data-id'));
                if (id > 0) {
                    ids.push(id);
                }
            });
            
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID data tidak valid.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            
            // Confirmation dialog
            Swal.fire({
                title: 'Konfirmasi Hapus Massal',
                html: `Yakin ingin menghapus <strong>${ids.length}</strong> data kendaraan yang dipilih?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt me-1"></i> Ya, Hapus!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus Data...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send AJAX request
                    $.ajax({
                        url: '<?= base_url('security/transportation/bulk-delete') ?>',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ ids: ids }),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message || 'Data berhasil dihapus',
                                    confirmButtonColor: '#28a745',
                                    timer: 1500,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    // PERBAIKAN: Cek apakah semua data akan terhapus
                                    const totalRows = document.querySelectorAll('tbody tr').length;
                                    
                                    if (totalRows === ids.length) {
                                        // Semua data terhapus - reload halaman untuk menampilkan "Belum Ada Data"
                                        window.location.reload();
                                    } else {
                                        // Masih ada data tersisa - hapus baris secara individual
                                        checkedBoxes.forEach(function(checkbox) {
                                            const row = checkbox.closest('tr');
                                            if (row) {
                                                // Animasi fade out sebelum hapus
                                                $(row).fadeOut(300, function() {
                                                    $(this).remove();
                                                    
                                                    // Update nomor urut setelah baris dihapus
                                                    updateRowNumbers();
                                                    
                                                    // Reset checkbox dan button
                                                    const selectAllCheckbox = document.getElementById('selectAll');
                                                    if (selectAllCheckbox) {
                                                        selectAllCheckbox.checked = false;
                                                    }
                                                    updateBulkDeleteButton();
                                                });
                                            }
                                        });
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: response.message || 'Terjadi kesalahan saat menghapus data',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Bulk delete error:', error);
                            console.error('Response:', xhr.responseText);
                            
                            let errorMessage = 'Terjadi kesalahan saat menghapus data.';
                            
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                errorMessage = 'Server error: ' + error;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMessage,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }
        
        /**
         * Update row numbers after deletion
         */
        function updateRowNumbers() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(function(row, index) {
                const noCell = row.querySelector('td:nth-child(2)'); // Kolom No (setelah checkbox)
                if (noCell) {
                    noCell.textContent = index + 1;
                }
            });
        }
        
        // Initialize bulk delete functionality on page load
        document.addEventListener('DOMContentLoaded', function() {
            bindCheckboxEvents();
            updateBulkDeleteButton();
        });
        
        // ===== END BULK DELETE FUNCTIONALITY =====

        // Form validation
        (function() {
            'use strict';
            const forms = document.querySelectorAll('form');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
