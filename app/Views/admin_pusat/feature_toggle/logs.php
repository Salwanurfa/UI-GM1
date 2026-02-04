<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Feature Toggle Logs' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="dashboard-header">
            <h1><i class="fas fa-history"></i> Feature Toggle Logs</h1>
            <p>Audit trail perubahan feature toggles</p>
            
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/feature-toggle') ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Feature Toggle
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="feature_key" class="form-label">Feature</label>
                        <select class="form-select" id="feature_key" name="feature_key">
                            <option value="">All Features</option>
                            <?php
                            // Get unique feature keys from logs
                            $db = \Config\Database::connect();
                            $query = $db->query("SELECT DISTINCT feature_key FROM feature_toggle_logs ORDER BY feature_key");
                            $features = $query->getResultArray();
                            
                            foreach ($features as $feature):
                            ?>
                            <option value="<?= $feature['feature_key'] ?>" <?= ($featureKey === $feature['feature_key']) ? 'selected' : '' ?>>
                                <?= $feature['feature_key'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="action" class="form-label">Action</label>
                        <select class="form-select" id="action" name="action">
                            <option value="">All Actions</option>
                            <option value="enabled">Enabled</option>
                            <option value="disabled">Disabled</option>
                            <option value="updated">Updated</option>
                            <option value="created">Created</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> Change Logs</h5>
                <div class="card-actions">
                    <span class="badge bg-info"><?= count($logs) ?> records</span>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($logs)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Feature</th>
                                <th>Action</th>
                                <th>Admin</th>
                                <th>Changes</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong><?= $log['feature_name'] ?? $log['feature_key'] ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $log['feature_key'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getActionBadgeClass($log['action']) ?>">
                                        <?= ucfirst($log['action']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $log['admin_name'] ?></strong>
                                    <br>
                                    <small class="text-muted">ID: <?= $log['admin_id'] ?></small>
                                </td>
                                <td>
                                    <?php if ($log['old_value'] && $log['new_value']): ?>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="showChanges('<?= htmlspecialchars($log['old_value']) ?>', '<?= htmlspecialchars($log['new_value']) ?>')">
                                        <i class="fas fa-eye"></i> View Changes
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $log['reason'] ?: '<span class="text-muted">-</span>' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No logs found</h5>
                    <p class="text-muted">No feature toggle changes have been recorded yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Changes Modal -->
    <div class="modal fade" id="changesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feature Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Old Value:</h6>
                            <pre id="oldValue" class="bg-light p-3 rounded"></pre>
                        </div>
                        <div class="col-md-6">
                            <h6>New Value:</h6>
                            <pre id="newValue" class="bg-light p-3 rounded"></pre>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showChanges(oldValue, newValue) {
            try {
                const oldObj = JSON.parse(oldValue);
                const newObj = JSON.parse(newValue);
                
                document.getElementById('oldValue').textContent = JSON.stringify(oldObj, null, 2);
                document.getElementById('newValue').textContent = JSON.stringify(newObj, null, 2);
            } catch (e) {
                document.getElementById('oldValue').textContent = oldValue;
                document.getElementById('newValue').textContent = newValue;
            }
            
            new bootstrap.Modal(document.getElementById('changesModal')).show();
        }
    </script>
</body>
</html>

<?php
function getActionBadgeClass($action) {
    switch ($action) {
        case 'enabled': return 'success';
        case 'disabled': return 'danger';
        case 'updated': return 'warning';
        case 'created': return 'info';
        default: return 'secondary';
    }
}
?>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f9fa;
}

.main-content {
    margin-left: 280px;
    padding: 30px;
    min-height: 100vh;
}

.dashboard-header {
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.dashboard-header h1 {
    color: #2c3e50;
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 5px;
}

.dashboard-header p {
    color: #7f8c8d;
    font-size: 16px;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    color: white;
    border-radius: 15px 15px 0 0 !important;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #2c3e50;
    background-color: #f8f9fa;
}

.table td {
    vertical-align: middle;
}

pre {
    font-size: 12px;
    max-height: 300px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .dashboard-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .header-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>