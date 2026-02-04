<?php
$widgetInfo = $available_widgets[$widget['widget_key']] ?? [];
$isActive = $widget['is_active'];
?>

<div class="widget-config-card <?= !$isActive ? 'inactive' : '' ?>" 
     data-widget-id="<?= $widget['id'] ?>" 
     data-widget-key="<?= $widget['widget_key'] ?>"
     data-custom-label="<?= htmlspecialchars($widget['custom_label']) ?>"
     data-custom-color="<?= $widget['custom_color'] ?>">
     
    <div class="d-flex align-items-center">
        <!-- Drag Handle -->
        <div class="widget-drag-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        
        <!-- Widget Info -->
        <div class="flex-grow-1 p-3">
            <div class="d-flex justify-content-between align-items-start">
                <div class="widget-info">
                    <h6 class="mb-1">
                        <i class="<?= $widgetInfo['icon'] ?? 'fas fa-puzzle-piece' ?>" 
                           style="color: <?= $widget['custom_color'] ?>"></i>
                        <?= $widget['custom_label'] ?: ($widgetInfo['name'] ?? $widget['widget_key']) ?>
                    </h6>
                    <small class="text-muted">
                        <?= $widgetInfo['description'] ?? 'Widget kustom' ?>
                    </small>
                    <div class="widget-meta mt-2">
                        <span class="badge bg-secondary">Urutan: <?= $widget['urutan'] ?></span>
                        <span class="badge" style="background-color: <?= $widget['custom_color'] ?>; color: white;">
                            <?= $widget['custom_color'] ?>
                        </span>
                        <?php if (!empty($widget['widget_config'])): ?>
                        <span class="badge bg-info">
                            <i class="fas fa-cog"></i> Dikonfigurasi
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Widget Actions -->
                <div class="widget-actions">
                    <div class="btn-group" role="group">
                        <!-- Toggle Active/Inactive -->
                        <button type="button" 
                                class="btn btn-sm btn-outline-secondary widget-toggle" 
                                onclick="toggleWidget(<?= $widget['id'] ?>)"
                                title="<?= $isActive ? 'Nonaktifkan' : 'Aktifkan' ?> Widget">
                            <i class="fas <?= $isActive ? 'fa-toggle-on text-success' : 'fa-toggle-off text-muted' ?>"></i>
                        </button>
                        
                        <!-- Configure Widget -->
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary" 
                                onclick="configureWidget(<?= $widget['id'] ?>)"
                                title="Konfigurasi Widget">
                            <i class="fas fa-cog"></i>
                        </button>
                        
                        <!-- Preview Widget -->
                        <button type="button" 
                                class="btn btn-sm btn-outline-info" 
                                onclick="previewWidget('<?= $widget['widget_key'] ?>', '<?= $role ?>')"
                                title="Preview Widget">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Widget Configuration Summary -->
            <?php if (!empty($widget['widget_config'])): ?>
            <div class="widget-config-summary mt-2">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Konfigurasi:</strong>
                    <?php
                    $configSummary = [];
                    foreach ($widget['widget_config'] as $key => $value) {
                        if (is_bool($value)) {
                            $configSummary[] = $key . ': ' . ($value ? 'Ya' : 'Tidak');
                        } else {
                            $configSummary[] = $key . ': ' . $value;
                        }
                    }
                    echo implode(', ', array_slice($configSummary, 0, 3));
                    if (count($configSummary) > 3) {
                        echo ' ...';
                    }
                    ?>
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function previewWidget(widgetKey, role) {
    // Create a simple preview modal or popup
    const previewContent = getWidgetPreviewContent(widgetKey, role);
    
    // Create modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Widget: ${widgetKey}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ${previewContent}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal after hide
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

function getWidgetPreviewContent(widgetKey, role) {
    const previews = {
        'stat_cards': `
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <h3>25</h3>
                        <p>Data Disetujui</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-edit"></i></div>
                    <div class="stat-content">
                        <h3>5</h3>
                        <p>Perlu Revisi</p>
                    </div>
                </div>
            </div>
        `,
        'waste_summary': `
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-recycle"></i> Ringkasan Waste Management</h5>
                </div>
                <div class="card-body">
                    <div class="waste-grid">
                        <div class="waste-item">
                            <div class="waste-header">
                                <h6>Plastik</h6>
                                <span class="waste-total">15 data</span>
                            </div>
                            <div class="waste-stats">
                                <div class="waste-stat">
                                    <span class="stat-label">Disetujui</span>
                                    <span class="stat-value text-success">12</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        'recent_activity': `
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon"><i class="fas fa-plus"></i></div>
                            <div class="activity-content">
                                <p>Data plastik 5kg berhasil ditambahkan</p>
                                <small class="text-muted">2 jam yang lalu</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        'quick_actions': `
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt"></i> Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <button class="btn btn-primary"><i class="fas fa-plus"></i> Input Data</button>
                        <button class="btn btn-success"><i class="fas fa-download"></i> Export</button>
                    </div>
                </div>
            </div>
        `,
        'price_info': `
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-money-bill-wave"></i> Informasi Harga</h5>
                </div>
                <div class="card-body">
                    <div class="price-grid">
                        <div class="price-item">
                            <h6>Plastik</h6>
                            <div class="price-value">
                                <span class="price-amount">Rp 2.000</span>
                                <small class="price-unit">per kg</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        'tps_operations': `
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-industry"></i> Operasional TPS</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="tps-info-card">
                                <h6><i class="fas fa-weight-hanging"></i> Kapasitas</h6>
                                <div class="capacity-text">
                                    <span class="capacity-current">75</span>
                                    <span class="capacity-separator">/</span>
                                    <span class="capacity-max">100</span>
                                    <span class="capacity-unit">ton</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `
    };
    
    return previews[widgetKey] || '<p class="text-muted">Preview tidak tersedia untuk widget ini.</p>';
}
</script>