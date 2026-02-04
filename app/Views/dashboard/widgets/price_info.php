<?php
/**
 * Price Information Widget
 * Menampilkan informasi harga sampah terkini
 */
?>

<!-- Price Information Widget -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-money-bill-wave"></i> Informasi Harga Sampah</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($data['current_prices'])): ?>
        <div class="price-grid">
            <?php foreach ($data['current_prices'] as $price): ?>
            <div class="price-item">
                <div class="price-header">
                    <h6><?= $price['jenis_sampah'] ?></h6>
                    <span class="price-badge">
                        Rp <?= number_format($price['harga_per_kg'], 0, ',', '.') ?>/kg
                    </span>
                </div>
                
                <?php if (isset($price['perubahan_harga'])): ?>
                <div class="price-change">
                    <?php if ($price['perubahan_harga'] > 0): ?>
                        <small class="text-success">
                            <i class="fas fa-arrow-up"></i> 
                            +Rp <?= number_format($price['perubahan_harga'], 0, ',', '.') ?>
                        </small>
                    <?php elseif ($price['perubahan_harga'] < 0): ?>
                        <small class="text-danger">
                            <i class="fas fa-arrow-down"></i> 
                            Rp <?= number_format($price['perubahan_harga'], 0, ',', '.') ?>
                        </small>
                    <?php else: ?>
                        <small class="text-muted">
                            <i class="fas fa-minus"></i> Tidak berubah
                        </small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="price-meta">
                    <small class="text-muted">
                        Update: <?= date('d M Y', strtotime($price['tanggal_berlaku'])) ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="price-actions mt-3">
            <a href="<?= base_url('/user/prices') ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-list"></i> Lihat Semua Harga
            </a>
            <a href="<?= base_url('/user/price-history') ?>" class="btn btn-outline-info btn-sm">
                <i class="fas fa-chart-line"></i> Riwayat Harga
            </a>
        </div>
        
        <?php else: ?>
        <div class="text-center py-4">
            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
            <p class="text-muted">Informasi harga belum tersedia</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.price-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.price-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    border-left: 4px solid #3498db;
}

.price-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.price-header h6 {
    margin: 0;
    color: #2c3e50;
    font-weight: 600;
}

.price-badge {
    background: linear-gradient(135deg, #2ecc71, #27ae60);
    color: white;
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}

.price-change {
    margin-bottom: 5px;
}

.price-meta {
    border-top: 1px solid #e9ecef;
    padding-top: 8px;
}

.price-actions {
    border-top: 1px solid #e9ecef;
    padding-top: 15px;
}

@media (max-width: 768px) {
    .price-grid {
        grid-template-columns: 1fr;
    }
    
    .price-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>