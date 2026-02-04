<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>

<style>
        .nav-links {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .nav-link {
            background: white;
            color: #4a90e2;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: #4a90e2;
            color: white;
            transform: translateY(-2px);
        }

        .card-body {
            padding: 20px;
        }

        .unit-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .unit-info h3 {
            color: #5c8cbf;
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .category-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }

        .category-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-title {
            font-weight: 600;
            color: #333;
        }

        .category-code {
            background: #5c8cbf;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-body {
            padding: 20px;
        }

        .data-section {
            margin-bottom: 20px;
        }

        .data-label {
            font-weight: 600;
            color: #666;
            margin-bottom: 10px;
        }

        .data-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #5c8cbf;
            margin-bottom: 15px;
        }

        .review-section {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-control:focus {
            outline: none;
            border-color: #5c8cbf;
            box-shadow: 0 0 0 2px rgba(92, 140, 191, 0.2);
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #5c8cbf;
            color: white;
        }

        .btn-primary:hover {
            background: #4a7ba7;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #f5f5f5;
            color: #666;
        }

        .status-disetujui {
            background: #e8f5e8;
            color: #388e3c;
        }

        .status-perlu_revisi {
            background: #ffebee;
            color: #d32f2f;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 12px;
            font-weight: 600;
            color: white;
            text-align: center;
            line-height: 20px;
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

            .category-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

<!-- Navigation Links -->
<div class="nav-links">
    <a href="<?= base_url('/admin-pusat/dashboard') ?>" class="nav-link">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="<?= base_url('/admin-pusat/waste') ?>" class="nav-link">
        <i class="fas fa-trash-alt"></i> Waste Management
    </a>
    <a href="<?= base_url('/admin-pusat/laporan-waste') ?>" class="nav-link">
        <i class="fas fa-chart-pie"></i> Laporan Waste
    </a>
</div>

<!-- Unit Information -->
<div class="unit-info">
    <h3><i class="fas fa-building"></i> Informasi Pengiriman - <?= $pengiriman['nama_unit'] ?> (<?= $pengiriman['tahun'] ?>)</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Unit</div>
                    <div class="info-value"><?= $pengiriman['nama_unit'] ?> (<?= $pengiriman['kode_unit'] ?>)</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge status-<?= $pengiriman['status_pengiriman'] ?>">
                            <?= ucfirst(str_replace('_', ' ', $pengiriman['status_pengiriman'])) ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Progress</div>
                    <div class="info-value">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $pengiriman['progress_persen'] ?>%">
                                <div class="progress-text"><?= number_format($pengiriman['progress_persen'], 1) ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Kirim</div>
                    <div class="info-value">
                        <?= $pengiriman['tanggal_kirim'] ? date('d/m/Y H:i', strtotime($pengiriman['tanggal_kirim'])) : '-' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Container -->
        <div id="alert-container"></div>

        <!-- Categories Review -->
        <div class="category-grid">
            <?php foreach ($kategori as $kat): ?>
                <?php $review = $reviewData[$kat['id']] ?? null; ?>
                <div class="category-card">
                    <div class="category-header">
                        <div class="category-title"><?= $kat['nama_kategori'] ?></div>
                        <div class="category-code"><?= $kat['kode_kategori'] ?></div>
                    </div>
                    <div class="category-body">
                        <!-- Data Input -->
                        <div class="data-section">
                            <div class="data-label">Data yang Dikirim:</div>
                            <?php if ($review && !empty($review['data_input'])): ?>
                                <?php $dataInput = safe_json_decode($review['data_input'], true); ?>
                                <div class="data-content">
                                    <?php if (is_array($dataInput)): ?>
                                        <?php foreach ($dataInput as $key => $value): ?>
                                            <p><strong><?= ucfirst(str_replace('_', ' ', $key)) ?>:</strong> <?= htmlspecialchars($value) ?></p>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p><?= htmlspecialchars($dataInput) ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="data-content" style="color: #999;">
                                    <p><i class="fas fa-info-circle"></i> Belum ada data yang dikirim</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Review Section -->
                        <div class="review-section">
                            <div class="data-label">Review Admin Pusat:</div>

                            <div class="form-group">
                                <label>Status Review:</label>
                                <select class="form-control" id="status_<?= $kat['id'] ?>" onchange="toggleReviewFields(<?= $kat['id'] ?>)">
                                    <option value="pending" <?= ($review['status_review'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="disetujui" <?= ($review['status_review'] ?? '') === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                                    <option value="perlu_revisi" <?= ($review['status_review'] ?? '') === 'perlu_revisi' ? 'selected' : '' ?>>Perlu Revisi</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Catatan Review:</label>
                                <textarea class="form-control" id="catatan_<?= $kat['id'] ?>" rows="3" placeholder="Berikan catatan untuk kategori ini..."><?= htmlspecialchars($review['catatan_review'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group" id="skor_group_<?= $kat['id'] ?>" style="display: <?= ($review['status_review'] ?? '') === 'disetujui' ? 'block' : 'none' ?>;">
                                <label>Skor (0-100):</label>
                                <input type="number" class="form-control" id="skor_<?= $kat['id'] ?>" min="0" max="100" value="<?= $review['skor_review'] ?? '' ?>" placeholder="Masukkan skor...">
                            </div>

                            <button class="btn btn-primary btn-sm" onclick="saveReview(<?= $pengiriman['id'] ?>, <?= $kat['id'] ?>)">
                                <i class="fas fa-save"></i> Simpan Review
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Final Actions -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-check-circle"></i> Finalisasi Review
            </div>
            <div class="card-body">
                <p>Setelah semua kategori direview, sistem akan otomatis mengupdate status pengiriman.</p>
                <div style="margin-top: 15px;">
                    <a href="<?= base_url('/admin-pusat/dashboard') ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleReviewFields(kategoriId) {
            const status = document.getElementById(`status_${kategoriId}`).value;
            const skorGroup = document.getElementById(`skor_group_${kategoriId}`);

            if (status === 'disetujui') {
                skorGroup.style.display = 'block';
            } else {
                skorGroup.style.display = 'none';
                document.getElementById(`skor_${kategoriId}`).value = '';
            }
        }

        function saveReview(pengirimanId, indikatorId) {
            const status = document.getElementById(`status_${indikatorId}`).value;
            const catatan = document.getElementById(`catatan_${indikatorId}`).value;
            const skor = document.getElementById(`skor_${indikatorId}`).value;

            const formData = new FormData();
            formData.append('pengiriman_id', pengirimanId);
            formData.append('indikator_id', indikatorId);
            formData.append('status_review', status);
            formData.append('catatan_review', catatan);
            formData.append('skor_review', skor);

            fetch('/admin-pusat/update-review', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        // Refresh halaman setelah 1 detik
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showAlert('danger', data.message);
                    }
                })
                .catch(error => {
                    showAlert('danger', 'Terjadi kesalahan: ' + error.message);
                });
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alert-container');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';

            alertContainer.innerHTML = `
                <div class="alert ${alertClass}">
                    ${message}
                </div>
            `;

            setTimeout(() => {
                alertContainer.innerHTML = '';
            }, 5000);
        }

        // Initialize review fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($kategori as $kat): ?>
                toggleReviewFields(<?= $kat['id'] ?>);
            <?php endforeach; ?>
        });
    </script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>