<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #5c8cbf 0%, #4a7ba7 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-link {
            background: white;
            color: #5c8cbf;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #5c8cbf;
            color: white;
            transform: translateY(-2px);
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: #5c8cbf;
            color: white;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .card-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #5c8cbf;
            box-shadow: 0 0 0 2px rgba(92, 140, 191, 0.2);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn-primary {
            background: #5c8cbf;
            color: white;
        }

        .btn-primary:hover {
            background: #4a7ba7;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }

        .export-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .export-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border-left: 4px solid #5c8cbf;
            text-align: center;
        }

        .export-card .icon {
            font-size: 48px;
            color: #5c8cbf;
            margin-bottom: 15px;
        }

        .export-card h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .export-card p {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .preview-section {
            margin-top: 30px;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .preview-table th,
        .preview-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .preview-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .preview-table tr:hover {
            background: #f8f9fa;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .nav-links {
                flex-direction: column;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .export-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div>
                <h1><i class="fas fa-file-export"></i> Laporan UIGM</h1>
                <p>Export dan cetak laporan penilaian UI GreenMetric</p>
            </div>
            <div class="header-info">
                <span><?= $user['nama_lengkap'] ?></span>
                <a href="/auth/logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Navigation Links -->
        <div class="nav-links">
            <?php if ($user['role'] === 'super_admin'): ?>
                <a href="/super-admin/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="/admin-pusat/dashboard" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php endif; ?>
            <a href="/report" class="nav-link active">
                <i class="fas fa-file-export"></i> Laporan
            </a>
        </div>

        <!-- Filter Section -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Laporan
            </div>
            <div class="card-body">
                <form id="reportForm">
                    <div class="filter-grid">
                        <div class="form-group">
                            <label for="tahun_id">Tahun Penilaian</label>
                            <select id="tahun_id" name="tahun_id" class="form-control">
                                <?php foreach ($tahunList as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= $tahun['status_aktif'] ? 'selected' : '' ?>>
                                        <?= $tahun['tahun'] ?> - <?= $tahun['nama_periode'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="unit_id">Unit (Opsional)</label>
                            <select id="unit_id" name="unit_id" class="form-control">
                                <option value="">Semua Unit</option>
                                <?php foreach ($unitList as $unit): ?>
                                    <option value="<?= $unit['id'] ?>"><?= $unit['nama_unit'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-info" onclick="previewData()">
                            <i class="fas fa-eye"></i> Preview Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export Options -->
        <div class="export-options">
            <div class="export-card">
                <div class="icon"><i class="fas fa-file-csv"></i></div>
                <h3>Export CSV</h3>
                <p>Download data dalam format CSV untuk analisis di Excel atau aplikasi spreadsheet lainnya</p>
                <button class="btn btn-success" onclick="exportCSV()">
                    <i class="fas fa-download"></i> Download CSV
                </button>
            </div>

            <div class="export-card">
                <div class="icon"><i class="fas fa-file-pdf"></i></div>
                <h3>Generate PDF</h3>
                <p>Buat laporan dalam format PDF yang siap untuk dicetak atau dibagikan</p>
                <button class="btn btn-primary" onclick="generatePDF()">
                    <i class="fas fa-file-pdf"></i> Generate PDF
                </button>
            </div>
        </div>

        <!-- Preview Section -->
        <div id="previewSection" class="card" style="display: none;">
            <div class="card-header">
                <i class="fas fa-eye"></i> Preview Data
            </div>
            <div class="card-body">
                <div id="previewContent">
                    <!-- Preview content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewData() {
            const tahunId = document.getElementById('tahun_id').value;
            const unitId = document.getElementById('unit_id').value;

            // Show loading
            const previewSection = document.getElementById('previewSection');
            const previewContent = document.getElementById('previewContent');

            previewSection.style.display = 'block';
            previewContent.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>';

            // Simulate API call (replace with actual API endpoint)
            fetch(`/api/unit-progress?tahun_id=${tahunId}&unit_id=${unitId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayPreview(data.progress);
                    } else {
                        previewContent.innerHTML = '<div class="alert alert-danger">Gagal memuat data</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    previewContent.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>';
                });
        }

        function displayPreview(data) {
            const previewContent = document.getElementById('previewContent');

            if (!data || data.length === 0) {
                previewContent.innerHTML = '<div style="text-align: center; padding: 20px; color: #666;">Tidak ada data untuk ditampilkan</div>';
                return;
            }

            let html = `
                <table class="preview-table">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Kode</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Tanggal Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.forEach(item => {
                const statusClass = getStatusClass(item.status_pengiriman);
                const statusText = getStatusText(item.status_pengiriman);

                html += `
                    <tr>
                        <td>${item.nama_unit}</td>
                        <td>${item.kode_unit}</td>
                        <td><span class="badge ${statusClass}">${statusText}</span></td>
                        <td>${parseFloat(item.progress_persen).toFixed(1)}%</td>
                        <td>${item.tanggal_kirim ? formatDate(item.tanggal_kirim) : '-'}</td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            previewContent.innerHTML = html;
        }

        function getStatusClass(status) {
            const classes = {
                'draft': 'badge-secondary',
                'dikirim': 'badge-info',
                'review': 'badge-warning',
                'disetujui': 'badge-success',
                'perlu_revisi': 'badge-danger'
            };
            return classes[status] || 'badge-secondary';
        }

        function getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'dikirim': 'Dikirim',
                'review': 'Review',
                'disetujui': 'Disetujui',
                'perlu_revisi': 'Perlu Revisi'
            };
            return texts[status] || status;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        function exportCSV() {
            const tahunId = document.getElementById('tahun_id').value;
            const unitId = document.getElementById('unit_id').value;

            let url = '/report/export-csv?tahun_id=' + tahunId;
            if (unitId) {
                url += '&unit_id=' + unitId;
            }

            window.open(url, '_blank');
        }

        function generatePDF() {
            const tahunId = document.getElementById('tahun_id').value;
            const unitId = document.getElementById('unit_id').value;

            let url = '/report/generate-pdf?tahun_id=' + tahunId;
            if (unitId) {
                url += '&unit_id=' + unitId;
            }

            window.open(url, '_blank');
        }

        // Auto preview on page load
        document.addEventListener('DOMContentLoaded', function() {
            previewData();
        });
    </script>
</body>

</html>