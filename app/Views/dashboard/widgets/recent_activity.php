<?php
/**
 * Recent Activity Widget
 * Menampilkan aktivitas terbaru
 */
?>

<!-- Recent Activity Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-history"></i> Aktivitas Terbaru</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($data)): ?>
        <div class="activity-list">
            <?php foreach ($data as $activity): ?>
            <div class="activity-item">
                <div class="activity-icon">
                    <i class="fas fa-<?= $activity['icon'] ?>"></i>
                </div>
                <div class="activity-content">
                    <p><?= $activity['message'] ?></p>
                    <small class="text-muted"><?= $activity['time'] ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada aktivitas terbaru</p>
        </div>
        <?php endif; ?>
    </div>
</div>