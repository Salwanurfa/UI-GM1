<?php
/**
 * Admin Pusat - Laporan Monitoring Kendaraan - Ringkasan Bulanan
 * Halaman Informatif dengan Grafik dan Tabel Ringkasan
 */

// Helper function untuk mapping kategori ke standar formal
function getStandardCategory($kategori) {
    // Mapping untuk backward compatibility dengan data lama
    $mapping = [
        // Data lama (backward compatibility)
        'Roda Dua' => 'Sepeda Motor (Kategori L)',
        'Motor Listrik' => 'Kendaraan Bermotor Listrik (KBL)',
        'Roda Empat' => 'Mobil Penumpang (Kategori M1)',
        'Bus' => 'Mobil Bus (Kategori M2/M3)',
        'Sepeda' => 'Kendaraan Tidak Bermotor (Sepeda)',
        'Kendaraan Umum' => 'Mobil Penumpang (Kategori M1)',
        
        // Data baru (sudah sesuai standar)
        'Sepeda Motor (Kategori L)' => 'Sepeda Motor (Kategori L)',
        'Mobil Penumpang (Kategori M1)' => 'Mobil Penumpang (Kategori M1)',
        'Mobil Bus (Kategori M2/M3)' => 'Mobil Bus (Kategori M2/M3)',
        'Kendaraan Tidak Bermotor (Sepeda)' => 'Kendaraan Tidak Bermotor (Sepeda)',
        'Kendaraan Bermotor Listrik (KBL)' => 'Kendaraan Bermotor Listrik (KBL)'
    ];
    
    return $mapping[$kategori] ?? $kategori;
}

// Helper function untuk nama bulan Indonesia
function getIndonesianMonth($monthNum) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    return $months[$monthNum] ?? '';
}

// Helper function untuk format bulan tahun
function formatMonthYear($monthNum, $year) {
    return getIndonesianMonth($monthNum) . ' ' . $year;
}

// Helper function untuk short month label (untuk chart)
function getShortMonthLabel($monthNum, $year) {
    $shortMonths = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    
    return $shortMonths[$monthNum] . ' ' . $year;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laporan Monitoring Kendaraan' ?> - UI GreenMetric POLBAN</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            background: #f8f9fc;
            color: #5a5c69;
        }

        /* ===== MAIN CONTENT WRAPPER ===== */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            padding: 0;
        }

        .page-container {
            padding: 2rem;
            max-width: 100%;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.5rem 2rem rgba(102, 126, 234, 0.3);
            position: relative;
            width: 100%;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
        }

        .page-header .badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 600;
            margin-top: 1rem;
            display: inline-block;
        }

        /* ===== STATISTICS CARDS ===== */
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            width: 100%;
        }

        .stat-card-modern {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
            border-left: 4px solid;
            width: 100%;
        }

        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem rgba(58, 59, 69, 0.25);
        }

        .stat-card-modern.primary { border-left-color: #4e73df; }
        .stat-card-modern.success { border-left-color: #1cc88a; }
        .stat-card-modern.warning { border-left-color: #f6c23e; }
        .stat-card-modern.info { border-left-color: #36b9cc; }

        .stat-card-modern h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            color: #2c3e50;
        }

        .stat-card-modern p {
            margin: 0.5rem 0 0 0;
            color: #858796;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-modern small {
            color: #b7b9cc;
            font-size: 0.75rem;
        }

        /* ===== CHART SECTION ===== */
        .chart-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            margin-bottom: 2rem;
            width: 100%;
        }

        .chart-card h5 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-card h5 i {
            color: #667eea;
        }

        #monthlyTrendChart {
            max-height: 400px;
        }

        /* ===== TABLE SECTION ===== */
        .table-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
            overflow: hidden;
            width: 100%;
        }

        .table-card-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            padding: 1.5rem 2rem;
            border-bottom: 2px solid #e3e6f0;
        }

        .table-card-header h5 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .table-responsive {
            padding: 0;
        }

        .table-modern {
            margin: 0;
            font-size: 0.875rem;
        }

        .table-modern thead th {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            padding: 1.25rem 1rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #5a5c69;
            white-space: nowrap;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background: #f8f9fc;
        }

        .table-modern tbody td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }

        /* ===== MONTH GROUP HEADER ===== */
        .month-group-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 1rem 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== BADGES ===== */
        .badge-category {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-motor {
            background: #e3f2fd;
            color: #1565c0;
        }

        .badge-motor-listrik {
            background: #e0f2f1;
            color: #00695c;
        }

        .badge-mobil {
            background: #f3e5f5;
            color: #6a1b9a;
        }

        .badge-bus {
            background: #fff3e0;
            color: #e65100;
        }

        .badge-sepeda {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .badge-umum {
            background: #fce4ec;
            color: #c2185b;
        }

        /* ===== PROGRESS BAR ===== */
        .zev-progress {
            height: 1.5rem;
            border-radius: 0.5rem;
            background: #e9ecef;
            overflow: hidden;
            position: relative;
        }

        .zev-progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            transition: width 0.6s ease;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-success-gradient {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            border: none;
            color: white;
        }

        .btn-danger-gradient {
            background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
            border: none;
            color: white;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 5rem;
            color: #d1d3e2;
            margin-bottom: 1.5rem;
        }

        .empty-state p {
            color: #858796;
            font-size: 1.125rem;
            margin: 0;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }

            .page-container {
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1><i class="fas fa-chart-line me-2"></i>Laporan Monitoring Kendaraan</h1>
                <p>Ringkasan Bulanan - Analisis Komprehensif Data Kendaraan Kampus</p>
                <span class="badge">
                    <i class="fas fa-calendar-alt me-2"></i>Periode: <?= date('F Y') ?>
                </span>
            </div>

            <!-- Statistics Overview -->
            <div class="stats-overview">
                <div class="stat-card-modern primary">
                    <h3><?= number_format($summary_stats['total_vehicles'] ?? 0) ?></h3>
                    <p>Total Kendaraan</p>
                    <small>Akumulasi seluruh data</small>
                </div>

                <div class="stat-card-modern success">
                    <h3><?= number_format($summary_stats['total_zev'] ?? 0) ?></h3>
                    <p>Zero Emission (ZEV)</p>
                    <small><?= $summary_stats['zev_percentage'] ?? 0 ?>% dari total</small>
                </div>

                <div class="stat-card-modern warning">
                    <h3><?= number_format($summary_stats['total_entries'] ?? 0) ?></h3>
                    <p>Total Data Entry</p>
                    <small>Record terdaftar</small>
                </div>

                <div class="stat-card-modern info">
                    <h3><?= number_format($summary_stats['total_shuttle'] ?? 0) ?></h3>
                    <p>Shuttle Kampus</p>
                    <small><?= $summary_stats['shuttle_percentage'] ?? 0 ?>% dari total</small>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="<?= base_url('admin-pusat/transportation') ?>" class="btn btn-primary-gradient btn-modern">
                    <i class="fas fa-arrow-left"></i> Kembali ke Manajemen
                </a>
                <button onclick="window.print()" class="btn btn-success-gradient btn-modern">
                    <i class="fas fa-print"></i> Cetak Laporan
                </button>
                <a href="<?= base_url('admin-pusat/transportation/export-excel') ?>" class="btn btn-danger-gradient btn-modern">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>

            <!-- Chart Section -->
            <div class="chart-card">
                <h5>
                    <i class="fas fa-chart-bar"></i>
                    Tren Jumlah Kendaraan Per Bulan (12 Bulan Terakhir)
                </h5>
                <canvas id="monthlyTrendChart"></canvas>
            </div>

            <!-- Monthly Summary Table -->
            <div class="table-card">
                <div class="table-card-header">
                    <h5>
                        <i class="fas fa-table"></i>
                        Ringkasan Bulanan - Breakdown Per Kategori
                    </h5>
                </div>
                <div class="table-responsive">
                    <?php if (!empty($monthly_summary)): ?>
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Periode (Bulan - Tahun)</th>
                                    <th>Kategori Kendaraan</th>
                                    <th class="text-center">Total Unit</th>
                                    <th class="text-center">Total ZEV</th>
                                    <th>Persentase ZEV</th>
                                    <th class="text-center">Jumlah Entry</th>
                                </tr>
                            </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $currentMonth = '';
                                    foreach ($monthly_summary as $row): 
                                        // Format bulan tahun dari bulan_num dan tahun
                                        $monthYear = formatMonthYear($row['bulan_num'], $row['tahun']);
                                        
                                        // Tampilkan header grup bulan jika berbeda
                                        if ($currentMonth !== $monthYear):
                                            $currentMonth = $monthYear;
                                    ?>
                                        <tr>
                                            <td colspan="7" class="month-group-header">
                                                <i class="fas fa-calendar-alt me-2"></i>
                                                <?= $monthYear ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <td>
                                            <small class="text-muted"><?= $monthYear ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $kategori = strtolower($row['kategori_kendaraan']);
                                            $badgeClass = 'badge-mobil';
                                            
                                            if (strpos($kategori, 'motor listrik') !== false) {
                                                $badgeClass = 'badge-motor-listrik';
                                            } elseif (strpos($kategori, 'roda dua') !== false) {
                                                $badgeClass = 'badge-motor';
                                            } elseif (strpos($kategori, 'roda empat') !== false) {
                                                $badgeClass = 'badge-mobil';
                                            } elseif (strpos($kategori, 'bus') !== false) {
                                                $badgeClass = 'badge-bus';
                                            } elseif (strpos($kategori, 'sepeda') !== false) {
                                                $badgeClass = 'badge-sepeda';
                                            } elseif (strpos($kategori, 'kendaraan umum') !== false) {
                                                $badgeClass = 'badge-umum';
                                            }
                                            ?>
                                            <span class="badge-category <?= $badgeClass ?>">
                                                <?= getStandardCategory($row['kategori_kendaraan']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-primary" style="font-size: 1.125rem;">
                                                <?= number_format($row['total_unit']) ?>
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            <strong class="text-success">
                                                <?= number_format($row['total_zev']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <div class="zev-progress">
                                                <div class="zev-progress-bar" style="width: <?= $row['persentase_zev'] ?? 0 ?>%">
                                                    <?= $row['persentase_zev'] ?? 0 ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                <?= $row['jumlah_entry'] ?> entry
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada data untuk ditampilkan</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Prepare chart data
        const chartData = <?= json_encode($chart_data) ?>;
        
        // Monthly Trend Chart
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        
        // Format labels dari tahun dan bulan
        const labels = chartData.monthly_trend.map(item => {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return monthNames[item.bulan - 1] + ' ' + item.tahun;
        });
        
        const data = chartData.monthly_trend.map(item => parseInt(item.total_kendaraan));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Kendaraan',
                    data: data,
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    hoverBackgroundColor: 'rgba(118, 75, 162, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Inter',
                                size: 14,
                                weight: '600'
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            family: 'Inter',
                            size: 14,
                            weight: '700'
                        },
                        bodyFont: {
                            family: 'Inter',
                            size: 13
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return 'Total: ' + context.parsed.y.toLocaleString('id-ID') + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 12
                            },
                            callback: function(value) {
                                return value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                family: 'Inter',
                                size: 12,
                                weight: '600'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
