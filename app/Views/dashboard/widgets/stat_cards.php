<?php
/**
 * Statistics Cards Widget
 * Menampilkan kartu statistik data waste management
 */
?>

<!-- Statistics Cards Widget -->
<div class="stats-grid">
    <?php if (isset($data['disetujui'])): ?>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['disetujui'] ?></h3>
            <p>Data Disetujui</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($data['dikirim'])): ?>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['dikirim'] ?></h3>
            <p>Menunggu Review</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($data['perlu_revisi'])): ?>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-edit"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['perlu_revisi'] ?></h3>
            <p>Perlu Revisi</p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (isset($data['draft'])): ?>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-save"></i>
        </div>
        <div class="stat-content">
            <h3><?= $data['draft'] ?></h3>
            <p>Draft</p>
        </div>
    </div>
    <?php endif; ?>
</div>