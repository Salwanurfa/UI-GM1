<?php
/**
 * Quick Actions Widget
 * Menampilkan aksi cepat untuk user
 */
?>

<!-- Quick Actions Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($data)): ?>
        <div class="quick-actions">
            <?php foreach ($data as $action): ?>
            <a href="<?= $action['url'] ?>" class="btn btn-<?= $action['color'] ?>">
                <i class="<?= $action['icon'] ?>"></i> <?= $action['title'] ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-bolt fa-3x text-muted mb-3"></i>
            <p class="text-muted">Tidak ada aksi cepat tersedia</p>
        </div>
        <?php endif; ?>
    </div>
</div>