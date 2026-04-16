<?php
/**
 * User Activity Log View - Admin Pusat
 * Menampilkan log aktivitas limbah B3 dari user tertentu
 */

// Helper functions
if (!function_exists('getStatusBadge')) {
    function getStatusBadge($status) {
        $badges = [
            'draft' => ['bg-secondary', '<i class="fas fa-file-alt"></i> Draft'],
            'dikirim_ke_tps' => ['bg-warning', '<i class="fas fa-paper-plane"></i> Menunggu Review'],
            'disetujui_tps' => ['bg-info', '<i class="fas fa-check-circle"></i> Disetujui TPS'],
            'ditolak_tps' => ['bg-danger', '<i class="fas fa-times-circle"></i> Ditolak TPS'],
            'disetujui_admin' => ['bg-success', '<i class="fas fa-thumbs-up"></i> Disetujui Admin'],
        ];
        
        if (isset($badges[$status])) {
            return '<span class="badge ' . $badges[$status][0] . '">' . $badges[$status][1] . '</span>';
        }
        
        return '<span class="badge bg-secondary">Unknown</span>';
    }
}

// Safety checks
$target_user = $target_user ?? [];
$limbah_logs = $limbah_logs ?? [];
$stats = $stats ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Log Aktivitas User' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('/css/mobile-responsive.css') ?>" rel="stylesheet">
</head>
<body>
<?= $this->include('partials/sidebar') ?>

<div class="main-content">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-history"></i> Log Aktivitas User</h1>
                <p>Riwayat aktivitas limbah B3 dan sampah dari <?= esc($target_user['nama_lengkap'] ?? 'User') ?></p>
            </div>
            <div class="header-actions">
                <a href="<?= base_url('/admin-pusat/user-management/export-user-log/' . ($target_user['id'] ?? 0)) ?>" 
                   class="btn btn-success me-2" title="Backup Semua Data User Ini">
                    <i class="fas fa-download"></i> Backup Data
                </a>
                <a href="<?= base_url('/admin-pusat/user-management') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- User Info Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-user"></i> Informasi User</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Nama Lengkap:</strong><br>
                    <?= esc($target_user['nama_lengkap'] ?? '-') ?>
                </div>
                <div class="col-md-3">
                    <strong>Username:</strong><br>
                    <?= esc($target_user['username'] ?? '-') ?>
                </div>
                <div class="col-md-3">
                    <strong>Email:</strong><br>
                    <?= esc($target_user['email'] ?? '-') ?>
                </div>
                <div class="col-md-3">
                    <strong>Unit:</strong><br>
                    <?= esc($target_user['nama_unit'] ?? '-') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if (!empty($stats)): ?>
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="stat-card primary">
                <div class="stat-content">
                    <h3><?= $stats['total_entries'] ?? 0 ?></h3>
                    <p>Total Entri</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card info">
                <div class="stat-content">
                    <h3><?= $stats['limbah_count'] ?? 0 ?></h3>
                    <p>Limbah B3</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card success">
                <div class="stat-content">
                    <h3><?= $stats['waste_count'] ?? 0 ?></h3>
                    <p>Sampah</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card secondary">
                <div class="stat-content">
                    <h3><?= $stats['draft_count'] ?? 0 ?></h3>
                    <p>Draft</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card warning">
                <div class="stat-content">
                    <h3><?= $stats['submitted_count'] ?? 0 ?></h3>
                    <p>Dikirim</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card danger">
                <div class="stat-content">
                    <h3><?= number_format($stats['total_timbulan'] ?? 0, 1) ?></h3>
                    <p>Total Timbulan</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Activity Log Table -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list"></i> Riwayat Aktivitas Limbah B3 & Sampah</h5>
                <button type="button" id="bulkDeleteBtn" class="btn btn-danger" style="display: none;" onclick="bulkDeleteSelected()">
                    <i class="fas fa-trash-alt"></i> Hapus Data Terpilih (<span id="selectedCount">0</span>)
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($all_logs)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="80" class="checkbox-header">
                                    <div class="checkbox-container">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        <div class="select-all-text">Pilih Semua</div>
                                    </div>
                                </th>
                                <th width="60" class="text-center">No</th>
                                <th width="100" class="text-center">Jenis</th>
                                <th width="140" class="text-center">Tanggal</th>
                                <th class="text-center">Nama</th>
                                <th width="100" class="text-center">Kode</th>
                                <th class="text-center">Lokasi</th>
                                <th width="130" class="text-center">Timbulan</th>
                                <th width="130" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($all_logs as $log): ?>
                                <tr class="log-row" data-id="<?= $log['id'] ?>" data-kategori="<?= esc($log['kategori']) ?>">
                                    <td class="checkbox-cell">
                                        <input class="form-check-input log-checkbox" type="checkbox" 
                                               value="<?= $log['id'] ?>" 
                                               data-kategori="<?= esc($log['kategori']) ?>"
                                               onchange="updateBulkDeleteButton()">
                                    </td>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $log['kategori'] === 'Limbah B3' ? 'bg-danger' : 'bg-success' ?>">
                                            <?= esc($log['kategori']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center text-nowrap"><?= date('d/m/Y H:i', strtotime($log['tanggal'])) ?></td>
                                    <td class="nama-column">
                                        <div class="nama-content">
                                            <strong><?= esc($log['nama'] ?? '-') ?></strong>
                                            <?php if (!empty($log['karakteristik'])): ?>
                                                <br><small class="text-muted karakteristik-text"><?= esc($log['karakteristik']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="text-center"><code class="kode-text"><?= esc($log['kode'] ?? '-') ?></code></td>
                                    <td class="lokasi-column"><?= esc($log['lokasi'] ?? '-') ?></td>
                                    <td class="text-end timbulan-column"><?= number_format($log['timbulan'] ?? 0, 2, ',', '.') ?> <span class="satuan-text"><?= esc($log['satuan'] ?? '') ?></span></td>
                                    <td class="text-center"><?= getStatusBadge($log['status'] ?? 'draft') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada aktivitas limbah B3 atau sampah dari user ini</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Toggle select all checkboxes
    function toggleSelectAll() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const logCheckboxes = document.querySelectorAll('.log-checkbox');
        
        logCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        
        updateBulkDeleteButton();
    }

    // Update bulk delete button visibility and count
    function updateBulkDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCount = document.getElementById('selectedCount');
        const selectAllCheckbox = document.getElementById('selectAll');
        const allCheckboxes = document.querySelectorAll('.log-checkbox');
        
        // Update selected count
        selectedCount.textContent = checkedBoxes.length;
        
        // Show/hide bulk delete button
        if (checkedBoxes.length > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
        
        // Update select all checkbox state
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === allCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
        
        // Update row highlighting
        updateRowHighlight();
    }

    // Bulk delete selected items
    async function bulkDeleteSelected() {
        const checkedBoxes = document.querySelectorAll('.log-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            Swal.fire({
                title: 'Peringatan!',
                text: 'Tidak ada data yang dipilih',
                icon: 'warning',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        // Collect selected items
        const selectedItems = [];
        checkedBoxes.forEach(checkbox => {
            selectedItems.push({
                id: checkbox.value,
                kategori: checkbox.dataset.kategori
            });
        });

        // Count by category for display
        const limbahCount = selectedItems.filter(item => item.kategori === 'Limbah B3').length;
        const sampahCount = selectedItems.filter(item => item.kategori === 'Sampah').length;

        // Show confirmation dialog
        const result = await Swal.fire({
            title: 'Konfirmasi Hapus Masal',
            html: `
                <div class="text-start">
                    <p><strong>Apakah Anda yakin ingin menghapus ${selectedItems.length} data yang dipilih?</strong></p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Data yang akan dihapus:</strong><br>
                        • Limbah B3: ${limbahCount} data<br>
                        • Sampah: ${sampahCount} data<br><br>
                        <strong>Peringatan:</strong> Data yang dihapus tidak dapat dikembalikan kecuali Anda sudah melakukan backup.
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus Semua!',
            cancelButtonText: '<i class="fas fa-times"></i> Batal',
            reverseButtons: true,
            customClass: {
                popup: 'swal-wide'
            }
        });

        if (!result.isConfirmed) {
            return;
        }

        // Show loading
        Swal.fire({
            title: 'Menghapus Data...',
            text: `Menghapus ${selectedItems.length} data yang dipilih`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            // Send bulk delete request
            const formData = new FormData();
            formData.append('selected_items', JSON.stringify(selectedItems));
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            const response = await fetch('<?= base_url('/admin-pusat/user-management/bulk-delete-log') ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                await Swal.fire({
                    title: 'Berhasil!',
                    html: `
                        <div class="text-start">
                            <p>${data.message}</p>
                            ${data.deleted_count !== data.total_selected ? 
                                `<div class="alert alert-info">
                                    <strong>Detail:</strong><br>
                                    • Berhasil dihapus: ${data.deleted_count}<br>
                                    • Total dipilih: ${data.total_selected}
                                </div>` : ''
                            }
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonColor: '#28a745'
                });

                // Reload page to refresh data
                location.reload();
            } else {
                // Show error message
                Swal.fire({
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan saat menghapus data',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            console.error('Bulk delete error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'Terjadi kesalahan sistem: ' + error.message,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
        }
    }

    // Add row highlighting for selected checkboxes
    function updateRowHighlight() {
        const allRows = document.querySelectorAll('.log-row');
        allRows.forEach(row => {
            const checkbox = row.querySelector('.log-checkbox');
            if (checkbox && checkbox.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Delete log entry with confirmation
    async function deleteLogEntry(logId, kategori) {
        const result = await Swal.fire({
            title: 'Hapus Log Aktivitas?',
            text: `Apakah Anda yakin ingin menghapus log ${kategori} ini? Data akan hilang dari database dan tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            try {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Sedang menghapus log aktivitas',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const formData = new FormData();
                formData.append('log_id', logId);
                formData.append('kategori', kategori);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                const response = await fetch('<?= base_url('/admin-pusat/user-management/delete-log-entry') ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Log berhasil dihapus',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload page to refresh data
                    location.reload();
                } else {
                    await Swal.fire({
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus log',
                        icon: 'error'
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan sistem: ' + error.message,
                    icon: 'error'
                });
            }
        }
    }

    // Show success message for backup
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handler for backup button
        const backupBtn = document.querySelector('a[href*="export-user-log"]');
        if (backupBtn) {
            backupBtn.addEventListener('click', function(e) {
                // Show loading toast
                Swal.fire({
                    title: 'Memproses Backup...',
                    text: 'Sedang mengunduh data aktivitas user',
                    icon: 'info',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }
    });
</script>

<style>
    /* Main Content */
    .main-content {
        margin-left: 280px;
        padding: 30px;
        min-height: 100vh;
        max-width: calc(100vw - 280px);
        overflow-x: hidden;
    }

    /* Page Header */
    .page-header {
        margin-bottom: 30px;
        padding: 20px 0;
        border-bottom: 2px solid #e9ecef;
    }

    .page-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
        font-size: 28px;
        font-weight: 700;
    }

    .page-header p {
        color: #6c757d;
        font-size: 16px;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* Stats Cards */
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        margin-bottom: 20px;
        border-left: 4px solid transparent;
    }

    .stat-card.primary { border-left-color: #007bff; }
    .stat-card.success { border-left-color: #28a745; }
    .stat-card.warning { border-left-color: #ffc107; }
    .stat-card.info { border-left-color: #17a2b8; }
    .stat-card.danger { border-left-color: #dc3545; }
    .stat-card.secondary { border-left-color: #6c757d; }

    .stat-content h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 5px 0;
        color: #2c3e50;
    }

    .stat-content p {
        margin: 0;
        color: #6c757d;
        font-weight: 500;
        font-size: 12px;
    }

    /* Cards */
    .card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
        border: none;
    }

    .card-header {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 15px 20px;
        border: none;
        border-radius: 10px 10px 0 0;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 600;
    }

    .card-body {
        padding: 20px;
    }

    /* Tables */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table th {
        background: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #2c3e50;
        padding: 12px;
        font-size: 13px;
    }

    .table td {
        border: none;
        padding: 12px;
        vertical-align: middle;
        font-size: 13px;
    }

    .table tbody tr {
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 16px;
    }

    /* Badges */
    .badge {
        padding: 6px 10px;
        border-radius: 5px;
        font-weight: 600;
        font-size: 11px;
    }

    /* Buttons */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    /* SweetAlert2 Custom Styles */
    .swal-wide {
        width: 600px !important;
    }

    .swal2-html-container {
        text-align: left !important;
    }

    /* Checkbox styling */
    .checkbox-header {
        text-align: center;
        vertical-align: middle;
        padding: 15px 12px;
        width: 80px;
        min-width: 80px;
        max-width: 80px;
    }

    .checkbox-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 60px;
    }

    .select-all-text {
        font-size: 12px;
        color: #495057;
        font-weight: 500;
        line-height: 1.2;
        text-align: center;
        white-space: nowrap;
        margin: 0;
    }

    .checkbox-cell {
        text-align: center;
        vertical-align: middle;
        padding: 15px 12px;
        width: 80px;
        min-width: 80px;
        max-width: 80px;
    }

    .form-check-input {
        margin: 0;
        width: 18px;
        height: 18px;
        border: 2px solid #dee2e6;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }

    .form-check-input:hover {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Table layout improvements */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 0 20px;
        margin: 0 -20px;
    }

    .table {
        margin-bottom: 0;
        font-size: 0.9rem;
        table-layout: auto;
        width: 100%;
    }

    .table th {
        background: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #2c3e50;
        padding: 18px 15px;
        font-size: 0.85rem;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        border: none;
        padding: 18px 15px;
        vertical-align: middle;
        font-size: 0.85rem;
        word-wrap: break-word;
        border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Row highlighting for selected items */
    .log-row.selected {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
    }

    .log-row.selected:hover {
        background-color: #bbdefb !important;
    }

    /* Column-specific styling */
    .nama-column {
        min-width: 200px;
        max-width: 300px;
    }

    .nama-content {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .karakteristik-text {
        font-size: 0.75rem;
        color: #6c757d;
        line-height: 1.3;
    }

    .lokasi-column {
        min-width: 150px;
        max-width: 250px;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .timbulan-column {
        white-space: nowrap;
        font-weight: 500;
    }

    .satuan-text {
        color: #6c757d;
        font-size: 0.8rem;
    }

    .kode-text {
        font-size: 0.8rem;
        background-color: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #e9ecef;
        font-family: 'Courier New', monospace;
    }

    /* Bulk delete button improvements */
    #bulkDeleteBtn {
        animation: fadeIn 0.3s ease-in-out;
        box-shadow: 0 3px 6px rgba(220, 53, 69, 0.3);
        white-space: nowrap;
        font-size: 0.9rem;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        border-radius: 8px;
    }

    #bulkDeleteBtn i {
        font-size: 1.1rem;
    }

    #selectedCount {
        background: rgba(255, 255, 255, 0.25);
        padding: 4px 10px;
        border-radius: 15px;
        font-weight: 700;
        min-width: 24px;
        text-align: center;
        font-size: 0.85rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #bulkDeleteBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
    }

    /* Card header with bulk delete button */
    .card-header .d-flex {
        align-items: center;
        gap: 25px;
    }

    .card-header h5 {
        margin: 0;
        flex: 1;
    }

    /* Indeterminate checkbox styling */
    input[type="checkbox"]:indeterminate {
        background-color: #007bff;
        border-color: #007bff;
        position: relative;
    }

    input[type="checkbox"]:indeterminate::after {
        content: '−';
        color: white;
        font-weight: bold;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        font-size: 14px;
        line-height: 1;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 0;
            padding: 20px;
            max-width: 100vw;
        }

        .page-header h1 {
            font-size: 24px;
        }

        .page-header p {
            font-size: 14px;
        }

        .card-header .d-flex {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .card-header h5 {
            text-align: center;
            margin-bottom: 10px;
        }

        #bulkDeleteBtn {
            width: 100%;
            justify-self: center;
        }

        .stat-card {
            margin-bottom: 15px;
        }

        .table-responsive {
            font-size: 12px;
            max-width: 100%;
            overflow-x: auto;
        }

        .table th, .table td {
            padding: 8px 4px;
            font-size: 0.75rem;
        }

        .table th:first-child,
        .table td:first-child {
            width: 60px;
            min-width: 60px;
            max-width: 60px;
            padding: 8px 4px;
        }

        .checkbox-container {
            padding: 6px 4px;
            min-height: 45px;
            gap: 4px;
        }

        .select-all-text {
            font-size: 0.65rem;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
        }

        .btn-group {
            flex-direction: row;
            flex-wrap: nowrap;
        }

        .btn-group .btn {
            padding: 0.25rem 0.5rem;
            font-size: 11px;
        }

        .badge {
            font-size: 0.6rem;
            padding: 4px 6px;
        }

        /* Hide some columns on very small screens */
        @media (max-width: 576px) {
            .table th:nth-child(6), /* Kode */
            .table td:nth-child(6),
            .table th:nth-child(7), /* Lokasi */
            .table td:nth-child(7) {
                display: none;
            }

            .nama-column {
                max-width: 150px;
            }
        }
    }
</style>

</body>
</html>