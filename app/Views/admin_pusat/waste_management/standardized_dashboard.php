<?= $this->extend('layouts/admin_pusat_new') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-recycle"></i> Dashboard Waste Management Standar</h1>
            <p>Monitoring limbah berdasarkan kategori standar UI GreenMetric</p>
        </div>
        <div class="header-actions">
            <select class="form-select" id="yearFilter" onchange="filterByYear()">
                <?php for($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == ($selected_year ?? date('Y')) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="button" class="btn btn-success ms-2" onclick="exportStandardizedData()">
                <i class="fas fa-download"></i> Export
            </button>
        </div>
    </div>
</div>

<!-- UIGM Compliance Cards -->
<div class="row mb-4">
    <?php if (isset($uigm_indicators)): ?>
        <?php foreach ($uigm_indicators as $code => $indicator): ?>
            <div class="col-md-3">
                <div class="card border-left-<?= $indicator['compliance'] === 'compliant' ? 'success' : 'warning' ?>">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-<?= $indicator['compliance'] === 'compliant' ? 'success' : 'warning' ?> text-uppercase mb-1">
                            <?= $code ?> - <?= $indicator['name'] ?>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($indicator['processing_rate'], 1) ?>%
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-<?= $indicator['compliance'] === 'compliant' ? 'success' : 'warning' ?>" 
                                 style="width: <?= min(100, ($indicator['processing_rate'] / $indicator['target']) * 100) ?>%"></div>
                        </div>
                        <small class="text-muted">Target: <?= $indicator['target'] ?>%</small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Category Breakdown -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie"></i> Breakdown Kategori Limbah Standar</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Sub-Kategori</th>
                                <th class="text-center">Total Volume</th>
                                <th class="text-center">Terolah</th>
                                <th class="text-center">% Pengolahan</th>
                                <th class="text-center">UIGM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($category_data)): ?>
                                <?php foreach ($category_data as $category): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?= $this->getCategoryColor($category['waste_category_standard']) ?>">
                                                <?= strtoupper($category['waste_category_standard']) ?>
                                            </span>
                                        </td>
                                        <td><?= $category['category_name'] ?></td>
                                        <td class="text-center">
                                            <?= number_format($category['total_volume'], 2) ?> <?= $category['default_unit'] ?>
                                        </td>
                                        <td class="text-center">
                                            <?= number_format($category['processed_volume'], 2) ?> <?= $category['default_unit'] ?>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $rate = $category['total_volume'] > 0 ? ($category['processed_volume'] / $category['total_volume']) * 100 : 0;
                                            ?>
                                            <span class="badge bg-<?= $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger') ?>">
                                                <?= number_format($rate, 1) ?>%
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $category['uigm_mapping'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Sumber Data</h5>
            </div>
            <div class="card-body">
                <?php if (isset($source_breakdown)): ?>
                    <?php foreach ($source_breakdown as $source => $data): ?>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong><?= ucwords(str_replace('_', ' ', $source)) ?></strong>
                                <br>
                                <small class="text-muted"><?= $data['contributors'] ?> kontributor</small>
                            </div>
                            <div class="text-end">
                                <div class="h6 mb-0"><?= number_format($data['total_volume'], 1) ?> kg</div>
                                <small class="text-muted"><?= $data['total_entries'] ?> entri</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-trophy"></i> Skor Kepatuhan UIGM</h5>
            </div>
            <div class="card-body text-center">
                <div class="display-4 font-weight-bold text-<?= ($summary['compliance_score'] ?? 0) >= 80 ? 'success' : (($summary['compliance_score'] ?? 0) >= 60 ? 'warning' : 'danger') ?>">
                    <?= number_format($summary['compliance_score'] ?? 0, 1) ?>%
                </div>
                <p class="text-muted">Skor Kepatuhan Total</p>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-<?= ($summary['compliance_score'] ?? 0) >= 80 ? 'success' : (($summary['compliance_score'] ?? 0) >= 60 ? 'warning' : 'danger') ?>" 
                         style="width: <?= $summary['compliance_score'] ?? 0 ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Data Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-table"></i> Data Detail Limbah per Kategori</h5>
            <div>
                <button class="btn btn-outline-primary btn-sm" onclick="showMappingModal()">
                    <i class="fas fa-cog"></i> Mapping Kategori
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="wasteDetailTable">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pengirim</th>
                        <th>Kategori Standar</th>
                        <th>Jenis Limbah</th>
                        <th>Volume</th>
                        <th>Metode Pengolahan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($waste_details)): ?>
                        <?php foreach ($waste_details as $waste): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($waste['tanggal_input'])) ?></td>
                                <td>
                                    <strong><?= $waste['nama_pelapor'] ?? 'N/A' ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $waste['gedung'] ?? 'N/A' ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $this->getCategoryColor($waste['waste_category_standard']) ?>">
                                        <?= strtoupper($waste['waste_category_standard'] ?? 'N/A') ?>
                                    </span>
                                    <br>
                                    <small><?= $waste['waste_subcategory'] ?? 'N/A' ?></small>
                                </td>
                                <td><?= $waste['jenis_sampah'] ?></td>
                                <td>
                                    <?= number_format($waste['volume_standardized'] ?? $waste['berat_kg'], 2) ?> 
                                    <?= $waste['volume_unit'] ?? 'kg' ?>
                                </td>
                                <td>
                                    <?php if ($waste['processing_method_standard']): ?>
                                        <span class="badge bg-info"><?= ucwords(str_replace('_', ' ', $waste['processing_method_standard'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Belum ditentukan</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->getStatusBadge($waste['status']) ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="editWasteMapping(<?= $waste['id'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Mapping Modal -->
<div class="modal fade" id="mappingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mapping Kategori Limbah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="mappingForm">
                    <input type="hidden" id="waste_id" name="waste_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori Standar</label>
                                <select class="form-select" id="waste_category_standard" name="waste_category_standard" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="organik">Limbah Organik</option>
                                    <option value="anorganik">Limbah Anorganik</option>
                                    <option value="b3">Limbah B3</option>
                                    <option value="cair">Limbah Cair</option>
                                    <option value="residu">Limbah Residu</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sub-Kategori</label>
                                <select class="form-select" id="waste_subcategory" name="waste_subcategory" required>
                                    <option value="">Pilih Sub-Kategori</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Volume Standar</label>
                                <input type="number" class="form-control" id="volume_standardized" name="volume_standardized" step="0.001" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <select class="form-select" id="volume_unit" name="volume_unit" required>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="m3">Meter Kubik (m³)</option>
                                    <option value="liter">Liter</option>
                                    <option value="unit">Unit</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Metode Pengolahan</label>
                        <select class="form-select" id="processing_method_standard" name="processing_method_standard">
                            <option value="">Pilih Metode</option>
                            <option value="daur_ulang">Daur Ulang</option>
                            <option value="kompos">Kompos</option>
                            <option value="biogas">Biogas</option>
                            <option value="reuse">Reuse</option>
                            <option value="reduce">Reduce</option>
                            <option value="landfill">Landfill</option>
                            <option value="incineration">Incineration</option>
                            <option value="treatment">Treatment</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveMapping()">Simpan Mapping</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Category color mapping
function getCategoryColor(category) {
    const colors = {
        'organik': 'success',
        'anorganik': 'primary', 
        'b3': 'danger',
        'cair': 'info',
        'residu': 'secondary'
    };
    return colors[category] || 'secondary';
}

// Filter by year
function filterByYear() {
    const year = document.getElementById('yearFilter').value;
    window.location.href = `<?= base_url('/admin-pusat/waste-standardized') ?>?year=${year}`;
}

// Show mapping modal
function showMappingModal() {
    const modal = new bootstrap.Modal(document.getElementById('mappingModal'));
    modal.show();
}

// Edit waste mapping
function editWasteMapping(wasteId) {
    // Load waste data and show modal
    fetch(`<?= base_url('/admin-pusat/waste/get/') ?>${wasteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('waste_id').value = wasteId;
                document.getElementById('waste_category_standard').value = data.waste.waste_category_standard || '';
                document.getElementById('waste_subcategory').value = data.waste.waste_subcategory || '';
                document.getElementById('volume_standardized').value = data.waste.volume_standardized || data.waste.berat_kg;
                document.getElementById('volume_unit').value = data.waste.volume_unit || 'kg';
                document.getElementById('processing_method_standard').value = data.waste.processing_method_standard || '';
                
                showMappingModal();
            }
        });
}

// Save mapping
function saveMapping() {
    const formData = new FormData(document.getElementById('mappingForm'));
    
    fetch('<?= base_url('/admin-pusat/waste/update-mapping') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Mapping berhasil disimpan');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Export standardized data
function exportStandardizedData() {
    const year = document.getElementById('yearFilter').value;
    window.location.href = `<?= base_url('/admin-pusat/waste-standardized/export') ?>?year=${year}`;
}

// Update subcategory options based on category
document.getElementById('waste_category_standard').addEventListener('change', function() {
    const category = this.value;
    const subcategorySelect = document.getElementById('waste_subcategory');
    
    // Clear existing options
    subcategorySelect.innerHTML = '<option value="">Pilih Sub-Kategori</option>';
    
    const subcategories = {
        'organik': ['Sisa Makanan', 'Dedaunan', 'Limbah Taman', 'Sampah Dapur'],
        'anorganik': ['Plastik', 'Kertas', 'Logam', 'Kaca', 'Kardus'],
        'b3': ['Baterai', 'Lampu Neon', 'Limbah Medis', 'Elektronik', 'Oli Bekas', 'Limbah Laboratorium'],
        'cair': ['Air Limbah Domestik', 'Air Limbah Laboratorium', 'Air Limbah Kantin'],
        'residu': ['Sampah Campur', 'Sampah Non-Recyclable']
    };
    
    if (subcategories[category]) {
        subcategories[category].forEach(sub => {
            const option = document.createElement('option');
            option.value = sub;
            option.textContent = sub;
            subcategorySelect.appendChild(option);
        });
    }
});
</script>
<?= $this->endSection() ?>

<?php
// Helper functions for view
function getCategoryColor($category) {
    $colors = [
        'organik' => 'success',
        'anorganik' => 'primary', 
        'b3' => 'danger',
        'cair' => 'info',
        'residu' => 'secondary'
    ];
    return $colors[$category] ?? 'secondary';
}

function getStatusBadge($status) {
    $badges = [
        'draft' => 'bg-secondary',
        'dikirim_ke_tps' => 'bg-warning',
        'disetujui_tps' => 'bg-info',
        'ditolak_tps' => 'bg-danger',
        'disetujui' => 'bg-success'
    ];
    
    $class = $badges[$status] ?? 'bg-secondary';
    $text = ucwords(str_replace('_', ' ', $status));
    
    return "<span class=\"badge {$class}\">{$text}</span>";
}
?>