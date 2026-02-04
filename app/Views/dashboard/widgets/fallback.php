<?php
/**
 * Fallback Widget
 * Widget default ketika widget yang diminta tidak ditemukan
 */
?>

<!-- Fallback Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-exclamation-triangle"></i> Widget Tidak Ditemukan</h3>
    </div>
    <div class="card-body text-center py-4">
        <i class="fas fa-puzzle-piece fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Widget "<?= $widget['widget_key'] ?>" Tidak Tersedia</h5>
        <p class="text-muted">Widget ini belum diimplementasikan atau terjadi kesalahan konfigurasi.</p>
        
        <?php if (ENVIRONMENT === 'development'): ?>
        <div class="alert alert-info mt-3">
            <small>
                <strong>Debug Info:</strong><br>
                Widget Key: <?= $widget['widget_key'] ?><br>
                Widget Name: <?= $widget['widget_name'] ?><br>
                Expected View: app/Views/dashboard/widgets/<?= $widget['widget_key'] ?>.php
            </small>
        </div>
        <?php endif; ?>
    </div>
</div>