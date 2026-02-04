<?php
/**
 * TPS Operations Widget
 * Widget khusus untuk operasional TPS
 */
?>

<!-- TPS Operations Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-cogs"></i> Operasional TPS</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <?php if (isset($data['capacity'])): ?>
            <div class="col-md-6 mb-3">
                <div class="tps-metric">
                    <div class="metric-header">
                        <i class="fas fa-weight-hanging text-primary"></i>
                        <h6>Kapasitas TPS</h6>
                    </div>
                    <div class="metric-content">
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?= ($data['capacity']['current'] / $data['capacity']['max']) * 100 ?>%">
                            </div>
                        </div>
                        <small class="text-muted">
                            <?= $data['capacity']['current'] ?> / <?= $data['capacity']['max'] ?> <?= $data['capacity']['unit'] ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($data['schedule'])): ?>
            <div class="col-md-6 mb-3">
                <div class="tps-metric">
                    <div class="metric-header">
                        <i class="fas fa-calendar-alt text-success"></i>
                        <h6>Jadwal Pengangkutan</h6>
                    </div>
                    <div class="metric-content">
                        <p class="mb-1">
                            <strong><?= date('d M Y H:i', strtotime($data['schedule']['next_pickup'])) ?></strong>
                        </p>
                        <small class="text-muted">Frekuensi: <?= $data['schedule']['frequency'] ?></small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="tps-actions mt-3">
            <a href="<?= base_url('/pengelola-tps/capacity') ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chart-bar"></i> Lihat Kapasitas
            </a>
            <a href="<?= base_url('/pengelola-tps/schedule') ?>" class="btn btn-outline-success btn-sm">
                <i class="fas fa-calendar"></i> Kelola Jadwal
            </a>
        </div>
    </div>
</div>

<style>
.tps-metric {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    height: 100%;
}

.metric-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.metric-header i {
    font-size: 20px;
    margin-right: 10px;
}

.metric-header h6 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.metric-content {
    margin-left: 30px;
}

.progress {
    height: 8px;
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 4px;
}

.tps-actions {
    border-top: 1px solid #e9ecef;
    padding-top: 15px;
}
</style>