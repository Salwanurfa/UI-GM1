<?php
/**
 * Waste Summary Widget
 * Menampilkan ringkasan data sampah per jenis
 */
?>

<!-- Waste Summary Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-trash-alt"></i> Ringkasan Waste Management</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($data['waste_stats'])): ?>
        <div class="waste-grid">
            <?php foreach ($data['waste_stats'] as $jenis => $stats): ?>
            <div class="waste-item">
                <div class="waste-header">
                    <h5><?= $jenis ?></h5>
                    <span class="waste-total"><?= $stats['total'] ?> data</span>
                </div>
                
                <?php if ($data['show_details']): ?>
                <div class="waste-stats">
                    <div class="waste-stat">
                        <span class="stat-label">Disetujui</span>
                        <span class="stat-value text-success"><?= $stats['disetujui'] ?></span>
                    </div>
                    <div class="waste-stat">
                        <span class="stat-label">Revisi</span>
                        <span class="stat-value text-warning"><?= $stats['perlu_revisi'] ?></span>
                    </div>
                    <div class="waste-stat">
                        <span class="stat-label">Draft</span>
                        <span class="stat-value text-muted"><?= $stats['draft'] ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($data['show_value_calculation'] && isset($stats['total_nilai'])): ?>
                <div class="waste-value">
                    <small class="text-muted">Total Nilai: 
                        <strong class="text-success">Rp <?= number_format($stats['total_nilai'], 0, ',', '.') ?></strong>
                    </small>
                </div>
                <?php endif; ?>
                
                <a href="<?= base_url('/user/waste/' . urlencode($jenis)) ?>" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Data
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada data waste management</p>
        </div>
        <?php endif; ?>
    </div>
</div>