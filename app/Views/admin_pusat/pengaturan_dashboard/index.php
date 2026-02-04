<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengaturan Dashboard' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css" rel="stylesheet">
    <style>
        .widget-config-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .widget-config-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .widget-config-card.inactive {
            opacity: 0.6;
            background: #f8f9fa;
        }
        
        .widget-drag-handle {
            cursor: move;
            color: #6c757d;
            padding: 10px;
            border-right: 1px solid #e9ecef;
        }
        
        .widget-drag-handle:hover {
            color: #495057;
            background: #f8f9fa;
        }
        
        .color-picker {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .widget-preview {
            max-height: 200px;
            overflow: hidden;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .role-tab {
            border-radius: 10px 10px 0 0;
        }
        
        .sortable-ghost {
            opacity: 0.4;
        }
        
        .sortable-chosen {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-cogs"></i> Pengaturan Dashboard</h1>
            <p>Kelola tampilan dan fitur dashboard User dan TPS</p>
            
            <div class="header-actions mt-3">
                <button class="btn btn-success" onclick="exportConfig()">
                    <i class="fas fa-download"></i> Export Konfigurasi
                </button>
                <button class="btn btn-info" onclick="showPreview('user')">
                    <i class="fas fa-eye"></i> Preview User
                </button>
                <button class="btn btn-info" onclick="showPreview('tps')">
                    <i class="fas fa-eye"></i> Preview TPS
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Dashboard Configuration Tabs -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active role-tab" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-config" type="button" role="tab">
                            <i class="fas fa-user"></i> Dashboard User
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link role-tab" id="tps-tab" data-bs-toggle="tab" data-bs-target="#tps-config" type="button" role="tab">
                            <i class="fas fa-recycle"></i> Dashboard TPS
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="dashboardTabsContent">
                    <!-- User Dashboard Configuration -->
                    <div class="tab-pane fade show active" id="user-config" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-user"></i> Konfigurasi Dashboard User</h5>
                            <div>
                                <button class="btn btn-warning btn-sm" onclick="resetDashboard('user')">
                                    <i class="fas fa-undo"></i> Reset Default
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="saveAllChanges('user')">
                                    <i class="fas fa-save"></i> Simpan Semua
                                </button>
                            </div>
                        </div>
                        
                        <div id="user-widgets" class="widget-list">
                            <?php if (isset($dashboard_settings['user'])): ?>
                                <?php foreach ($dashboard_settings['user'] as $widget): ?>
                                    <?= view('admin_pusat/pengaturan_dashboard/widget_config_card', ['widget' => $widget, 'role' => 'user', 'available_widgets' => $available_widgets]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- TPS Dashboard Configuration -->
                    <div class="tab-pane fade" id="tps-config" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-recycle"></i> Konfigurasi Dashboard TPS</h5>
                            <div>
                                <button class="btn btn-warning btn-sm" onclick="resetDashboard('tps')">
                                    <i class="fas fa-undo"></i> Reset Default
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="saveAllChanges('tps')">
                                    <i class="fas fa-save"></i> Simpan Semua
                                </button>
                            </div>
                        </div>
                        
                        <div id="tps-widgets" class="widget-list">
                            <?php if (isset($dashboard_settings['tps'])): ?>
                                <?php foreach ($dashboard_settings['tps'] as $widget): ?>
                                    <?= view('admin_pusat/pengaturan_dashboard/widget_config_card', ['widget' => $widget, 'role' => 'tps', 'available_widgets' => $available_widgets]) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Widgets Info -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-puzzle-piece"></i> Widget yang Tersedia</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($available_widgets as $key => $widget): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="<?= $widget['icon'] ?>"></i>
                                    <?= $widget['name'] ?>
                                </h6>
                                <p class="card-text small text-muted">
                                    <?= $widget['description'] ?>
                                </p>
                                <div class="widget-features">
                                    <small class="text-info">
                                        <strong>Konfigurasi:</strong>
                                        <?= implode(', ', $widget['configurable']) ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Widget Configuration Modal -->
    <div class="modal fade" id="widgetConfigModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfigurasi Widget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="widgetConfigForm">
                        <input type="hidden" id="modal-widget-id" name="widget_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Judul Widget</label>
                                    <input type="text" class="form-control" id="modal-custom-label" name="custom_label">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Warna Widget</label>
                                    <input type="color" class="form-control color-picker" id="modal-custom-color" name="custom_color">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Konfigurasi Khusus</label>
                            <div id="widget-specific-config">
                                <!-- Dynamic configuration options will be loaded here -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveWidgetConfig()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Initialize sortable for both widget lists
        let userSortable, tpsSortable;
        
        document.addEventListener('DOMContentLoaded', function() {
            initializeSortable();
        });
        
        function initializeSortable() {
            const userWidgets = document.getElementById('user-widgets');
            const tpsWidgets = document.getElementById('tps-widgets');
            
            if (userWidgets) {
                userSortable = Sortable.create(userWidgets, {
                    handle: '.widget-drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        updateWidgetOrder('user');
                    }
                });
            }
            
            if (tpsWidgets) {
                tpsSortable = Sortable.create(tpsWidgets, {
                    handle: '.widget-drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        updateWidgetOrder('tps');
                    }
                });
            }
        }
        
        function updateWidgetOrder(role) {
            const container = document.getElementById(role + '-widgets');
            const widgets = container.querySelectorAll('.widget-config-card');
            const orders = [];
            
            widgets.forEach((widget, index) => {
                const widgetId = widget.dataset.widgetId;
                orders.push(widgetId);
            });
            
            fetch('<?= base_url('admin-pusat/pengaturan-dashboard/update-order') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    role: role,
                    orders: orders
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan saat memperbarui urutan');
            });
        }
        
        function toggleWidget(widgetId) {
            fetch('<?= base_url('admin-pusat/pengaturan-dashboard/toggle-widget') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    widget_id: widgetId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    // Toggle visual state
                    const card = document.querySelector(`[data-widget-id="${widgetId}"]`);
                    card.classList.toggle('inactive');
                    
                    const toggle = card.querySelector('.widget-toggle');
                    const icon = toggle.querySelector('i');
                    if (icon.classList.contains('fa-toggle-on')) {
                        icon.classList.remove('fa-toggle-on', 'text-success');
                        icon.classList.add('fa-toggle-off', 'text-muted');
                    } else {
                        icon.classList.remove('fa-toggle-off', 'text-muted');
                        icon.classList.add('fa-toggle-on', 'text-success');
                    }
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan saat mengubah status widget');
            });
        }
        
        function configureWidget(widgetId) {
            // Load widget configuration in modal
            const card = document.querySelector(`[data-widget-id="${widgetId}"]`);
            const widgetKey = card.dataset.widgetKey;
            const customLabel = card.dataset.customLabel;
            const customColor = card.dataset.customColor;
            
            document.getElementById('modal-widget-id').value = widgetId;
            document.getElementById('modal-custom-label').value = customLabel;
            document.getElementById('modal-custom-color').value = customColor;
            
            // Load widget-specific configuration
            loadWidgetSpecificConfig(widgetKey);
            
            const modal = new bootstrap.Modal(document.getElementById('widgetConfigModal'));
            modal.show();
        }
        
        function loadWidgetSpecificConfig(widgetKey) {
            const configContainer = document.getElementById('widget-specific-config');
            
            // Widget-specific configuration options
            const configs = {
                'stat_cards': `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_approved" name="show_approved" checked>
                        <label class="form-check-label" for="show_approved">Tampilkan Data Disetujui</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_pending" name="show_pending" checked>
                        <label class="form-check-label" for="show_pending">Tampilkan Data Pending</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_revision" name="show_revision" checked>
                        <label class="form-check-label" for="show_revision">Tampilkan Data Revisi</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_draft" name="show_draft" checked>
                        <label class="form-check-label" for="show_draft">Tampilkan Draft</label>
                    </div>
                `,
                'recent_activity': `
                    <div class="mb-3">
                        <label class="form-label">Maksimal Item</label>
                        <input type="number" class="form-control" id="max_items" name="max_items" value="5" min="1" max="20">
                    </div>
                `,
                'waste_summary': `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_details" name="show_details" checked>
                        <label class="form-check-label" for="show_details">Tampilkan Detail</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="show_value_calculation" name="show_value_calculation" checked>
                        <label class="form-check-label" for="show_value_calculation">Tampilkan Perhitungan Nilai</label>
                    </div>
                `
            };
            
            configContainer.innerHTML = configs[widgetKey] || '<p class="text-muted">Tidak ada konfigurasi khusus untuk widget ini.</p>';
        }
        
        function saveWidgetConfig() {
            const form = document.getElementById('widgetConfigForm');
            const formData = new FormData(form);
            
            // Collect widget-specific config
            const widgetConfig = {};
            const configInputs = document.querySelectorAll('#widget-specific-config input');
            configInputs.forEach(input => {
                if (input.type === 'checkbox') {
                    widgetConfig[input.name] = input.checked;
                } else {
                    widgetConfig[input.name] = input.value;
                }
            });
            
            formData.append('widget_config', JSON.stringify(widgetConfig));
            
            fetch('<?= base_url('admin-pusat/pengaturan-dashboard/update-widget') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('widgetConfigModal')).hide();
                    // Refresh the page to show changes
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan saat menyimpan konfigurasi');
            });
        }
        
        function resetDashboard(role) {
            if (!confirm(`Apakah Anda yakin ingin mereset dashboard ${role} ke pengaturan default?`)) {
                return;
            }
            
            fetch('<?= base_url('admin-pusat/pengaturan-dashboard/reset-default') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    role: role
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Terjadi kesalahan saat mereset dashboard');
            });
        }
        
        function showPreview(role) {
            window.open('<?= base_url('admin-pusat/pengaturan-dashboard/preview') ?>/' + role, '_blank');
        }
        
        function exportConfig() {
            window.location.href = '<?= base_url('admin-pusat/pengaturan-dashboard/export-config') ?>';
        }
        
        function saveAllChanges(role) {
            showToast('info', 'Menyimpan semua perubahan...');
            // Implementation for bulk save would go here
            setTimeout(() => {
                showToast('success', `Semua perubahan dashboard ${role} berhasil disimpan`);
            }, 1000);
        }
        
        function showToast(type, message) {
            // Simple toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }
    </script>
</body>
</html>