<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pengisian Data UIGM' ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?= $this->include('partials/sidebar') ?>
    
    <div class="main-content">
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-clipboard-list"></i> Pengisian Data UIGM</h1>
                <p>Kategori: <?= $kategoriName ?></p>
            </div>
            
            <div class="header-actions">
                <button type="button" class="btn btn-success" onclick="submitAllKategori()">
                    <i class="fas fa-paper-plane"></i> Kirim Semua ke Admin
                </button>
            </div>
        </div>

        <!-- Category Navigation -->
        <div class="category-nav">
            <?php foreach ($kategoriList as $code => $name): ?>
            <a href="<?= base_url('/user/pengisian/' . $code) ?>" 
               class="category-tab <?= ($kategori === $code) ? 'active' : '' ?>">
                <?= $code ?>
                <span><?= $name ?></span>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Indikator List -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i>
                <h3>Daftar Indikator - <?= $kategoriName ?></h3>
            </div>
            <div class="card-body">
                <?php if (empty($penilaianData)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard"></i>
                    <p>Belum ada indikator untuk kategori ini</p>
                </div>
                <?php else: ?>
                <div class="indikator-list">
                    <?php foreach ($penilaianData as $index => $item): ?>
                    <div class="indikator-item">
                        <div class="indikator-header">
                            <div class="indikator-info">
                                <h4><?= htmlspecialchars($item['indikator']) ?></h4>
                                <span class="status-badge status-<?= $item['status'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                </span>
                            </div>
                            
                            <?php if ($item['status'] === 'perlu_revisi' && !empty($item['catatan_admin'])): ?>
                            <div class="revision-note">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Catatan Revisi:</strong>
                                <p><?= htmlspecialchars($item['catatan_admin']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST" action="<?= base_url('/user/pengisian/save') ?>" class="indikator-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            
                            <div class="form-group">
                                <label for="nilai_input_<?= $item['id'] ?>">
                                    Nilai Input: <span class="required">*</span>
                                    <small class="text-muted">(0-100)</small>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       min="0"
                                       max="100"
                                       name="nilai_input" 
                                       id="nilai_input_<?= $item['id'] ?>"
                                       class="form-control"
                                       value="<?= $item['nilai_input'] ?? 0 ?>"
                                       placeholder="Masukkan nilai 0-100"
                                       required
                                       <?= in_array($item['status'], ['dikirim', 'review', 'disetujui']) ? 'readonly' : '' ?>>
                                <div class="invalid-feedback">
                                    Nilai input harus diisi dengan angka antara 0-100
                                </div>
                            </div>
                            
                            <?php if (!in_array($item['status'], ['dikirim', 'review', 'disetujui'])): ?>
                            <div class="form-actions">
                                <button type="submit" name="action" value="draft" class="btn btn-secondary">
                                    <i class="fas fa-save"></i> Simpan Draft
                                </button>
                                <button type="submit" name="action" value="kirim" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Kirim ke Admin
                                </button>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Submit All Modal -->
        <div class="modal fade" id="submitAllModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Pengiriman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin mengirim semua data kategori <strong><?= $kategoriName ?></strong> ke Admin Pusat?</p>
                        <p class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Data yang sudah dikirim tidak dapat diubah kecuali dikembalikan untuk revisi.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <form method="POST" action="<?= base_url('/user/pengisian/submit/' . $kategori) ?>" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Ya, Kirim Semua
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function submitAllKategori() {
        const modal = new bootstrap.Modal(document.getElementById('submitAllModal'));
        modal.show();
    }

    // Form validation and auto-save functionality
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.indikator-form');
        
        forms.forEach(form => {
            const input = form.querySelector('input[name="nilai_input"]');
            if (input && !input.readOnly) {
                let timeout;
                
                // Real-time validation
                input.addEventListener('input', function() {
                    validateInput(this);
                    
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        // Auto-save as draft after 2 seconds of no typing
                        if (this.value && this.checkValidity()) {
                            autoSaveDraft(form);
                        }
                    }, 2000);
                });

                // Validate on blur
                input.addEventListener('blur', function() {
                    validateInput(this);
                });
            }

            // Form submission validation
            form.addEventListener('submit', function(e) {
                const input = this.querySelector('input[name="nilai_input"]');
                if (!validateInput(input)) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        function validateInput(input) {
            const value = parseFloat(input.value);
            const isValid = !isNaN(value) && value >= 0 && value <= 100;
            
            if (input.value === '' || input.value === null) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                return false;
            } else if (!isValid) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                return false;
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }
        }

        function autoSaveDraft(form) {
            const formData = new FormData(form);
            formData.set('action', 'draft');
            
            fetch(form.action, {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    // Show subtle success indicator
                    const input = form.querySelector('input[name="nilai_input"]');
                    input.style.borderColor = '#28a745';
                    setTimeout(() => {
                        input.style.borderColor = '';
                    }, 1000);
                }
            }).catch(error => {
                console.log('Auto-save failed:', error);
            });
        }
    });
    </script>
</body>
</html>

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

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px 0;
    border-bottom: 2px solid #e9ecef;
}

.header-content h1 {
    color: #2c3e50;
    margin-bottom: 5px;
    font-size: 28px;
    font-weight: 700;
}

.header-content p {
    color: #6c757d;
    margin: 0;
    font-size: 16px;
}

.category-nav {
    display: flex;
    gap: 10px;
    margin-bottom: 30px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.category-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 15px 20px;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    text-decoration: none;
    color: #6c757d;
    font-weight: 600;
    min-width: 120px;
    text-align: center;
    transition: all 0.3s ease;
}

.category-tab:hover {
    border-color: #007bff;
    color: #007bff;
    transform: translateY(-2px);
}

.category-tab.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
}

.category-tab span {
    font-size: 12px;
    margin-top: 5px;
    opacity: 0.8;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.card-body {
    padding: 25px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
    opacity: 0.5;
}

.indikator-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.indikator-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    border-left: 4px solid #007bff;
}

.indikator-header {
    margin-bottom: 20px;
}

.indikator-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.indikator-info h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-draft {
    background: #e9ecef;
    color: #6c757d;
}

.status-dikirim {
    background: #cce5ff;
    color: #0066cc;
}

.status-review {
    background: #fff3cd;
    color: #856404;
}

.status-disetujui {
    background: #d4edda;
    color: #155724;
}

.status-perlu_revisi {
    background: #f8d7da;
    color: #721c24;
}

.revision-note {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 15px;
    margin-top: 10px;
}

.revision-note i {
    color: #856404;
    margin-right: 8px;
}

.revision-note strong {
    color: #856404;
}

.revision-note p {
    margin: 5px 0 0 0;
    color: #856404;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
}

.form-group label .required {
    color: #dc3545;
    font-weight: 700;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.form-control:read-only {
    background: #f8f9fa;
    color: #6c757d;
}

.form-control.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 2.94 2.94L8.5 6.4l.94.94L6.5 10.27z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control.is-invalid ~ .invalid-feedback {
    display: block;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .category-nav {
        flex-wrap: wrap;
    }
    
    .indikator-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>