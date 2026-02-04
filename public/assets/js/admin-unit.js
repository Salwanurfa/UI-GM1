/**
 * Admin Unit Dashboard JavaScript
 */

class AdminUnitDashboard {
    constructor() {
        this.pengirimanId = window.PENGIRIMAN_ID;
        this.canEdit = window.CAN_EDIT;
        this.baseUrl = window.BASE_URL;
        this.autoSaveTimeout = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initFileUpload();
        this.initAutoSave();
        this.validateForms();
    }
    
    bindEvents() {
        // Save category data
        $(document).on('click', '.btn-simpan', (e) => {
            this.saveCategory($(e.target).data('kategori-id'));
        });
        
        // Reset form
        $(document).on('click', '.btn-reset', (e) => {
            this.resetCategory($(e.target).data('kategori-id'));
        });
        
        // Save all drafts
        $(document).on('click', '#btn-simpan-semua', () => {
            this.saveAllCategories();
        });
        
        // Submit data
        $(document).on('click', '#btn-kirim-data', () => {
            this.submitData();
        });
        
        // Form validation on input
        $(document).on('input change', '.category-form input, .category-form textarea', (e) => {
            this.validateField($(e.target));
            this.scheduleAutoSave($(e.target).closest('.category-form'));
        });
    }
    
    initFileUpload() {
        // Drag and drop file upload
        $('.file-upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).addClass('dragover');
        });
        
        $('.file-upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
        });
        
        $('.file-upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).removeClass('dragover');
            
            const files = e.originalEvent.dataTransfer.files;
            const input = $(this).find('input[type="file"]')[0];
            input.files = files;
            
            $(input).trigger('change');
        });
        
        // File input change
        $(document).on('change', 'input[type="file"]', function() {
            const files = this.files;
            const container = $(this).closest('.form-group');
            
            // Show file list
            let fileList = container.find('.file-list');
            if (fileList.length === 0) {
                fileList = $('<div class="file-list mt-2"></div>');
                container.append(fileList);
            }
            
            fileList.empty();
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = $(`
                    <div class="file-item d-flex align-items-center justify-content-between p-2 border rounded mb-1">
                        <div>
                            <i class="fas fa-file me-2"></i>
                            <span>${file.name}</span>
                            <small class="text-muted ms-2">(${this.formatFileSize(file.size)})</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${i}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                fileList.append(fileItem);
            }
        });
        
        // Remove file
        $(document).on('click', '.remove-file', function() {
            const index = $(this).data('index');
            const input = $(this).closest('.form-group').find('input[type="file"]')[0];
            
            // Create new FileList without the removed file
            const dt = new DataTransfer();
            const files = input.files;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            input.files = dt.files;
            $(input).trigger('change');
        });
    }
    
    initAutoSave() {
        // Auto-save every 30 seconds if there are changes
        setInterval(() => {
            if (this.hasUnsavedChanges()) {
                this.saveAllCategories(true); // Silent save
            }
        }, 30000);
    }
    
    validateForms() {
        $('.category-form').each((index, form) => {
            this.validateForm($(form));
        });
    }
    
    validateForm(form) {
        const kategoriId = form.data('kategori-id');
        let isValid = true;
        
        // Required fields validation
        form.find('input[required], textarea[required]').each((index, field) => {
            if (!this.validateField($(field))) {
                isValid = false;
            }
        });
        
        // Update category status
        this.updateCategoryStatus(kategoriId, isValid);
        
        return isValid;
    }
    
    validateField(field) {
        const value = field.val().trim();
        const isRequired = field.prop('required');
        let isValid = true;
        
        // Remove existing feedback
        field.removeClass('is-valid is-invalid');
        field.siblings('.invalid-feedback, .valid-feedback').remove();
        
        if (isRequired && !value) {
            isValid = false;
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">Field ini wajib diisi</div>');
        } else if (field.attr('type') === 'number' && value && isNaN(value)) {
            isValid = false;
            field.addClass('is-invalid');
            field.after('<div class="invalid-feedback">Masukkan angka yang valid</div>');
        } else if (value) {
            field.addClass('is-valid');
        }
        
        return isValid;
    }
    
    updateCategoryStatus(kategoriId, isComplete) {
        const card = $(`.category-card[data-kategori="${kategoriId}"]`);
        const badge = card.find('.badge');
        const progressIndicator = card.find('.category-progress');
        
        if (isComplete) {
            badge.removeClass('bg-secondary').addClass('bg-success')
                 .html('<i class="fas fa-check-circle me-1"></i>Lengkap');
            progressIndicator.removeClass('incomplete').addClass('complete').text('✓');
        } else {
            badge.removeClass('bg-success').addClass('bg-secondary')
                 .html('<i class="fas fa-circle me-1"></i>Belum Lengkap');
            progressIndicator.removeClass('complete').addClass('incomplete').text('○');
        }
    }
    
    scheduleAutoSave(form) {
        if (!this.canEdit) return;
        
        clearTimeout(this.autoSaveTimeout);
        this.autoSaveTimeout = setTimeout(() => {
            const kategoriId = form.data('kategori-id');
            this.saveCategory(kategoriId, true); // Silent save
        }, 5000);
    }
    
    saveCategory(kategoriId, silent = false) {
        if (!this.canEdit) {
            if (!silent) this.showToast('Data tidak dapat diedit', 'warning');
            return;
        }
        
        const form = $(`.category-form[data-kategori-id="${kategoriId}"]`);
        const button = $(`.btn-simpan[data-kategori-id="${kategoriId}"]`);
        
        // Validate form first
        if (!this.validateForm(form)) {
            if (!silent) this.showToast('Mohon lengkapi data yang wajib diisi', 'warning');
            return;
        }
        
        // Collect form data
        const formData = new FormData();
        formData.append('pengiriman_id', this.pengirimanId);
        formData.append('indikator_id', kategoriId);
        
        const dataInput = {
            deskripsi: form.find('[name="deskripsi"]').val(),
            nilai_numerik: form.find('[name="nilai_numerik"]').val(),
            target_rencana: form.find('[name="target_rencana"]').val(),
            catatan: form.find('[name="catatan"]').val(),
            dokumen: []
        };
        
        // Handle file uploads
        const fileInput = form.find('input[type="file"]')[0];
        if (fileInput && fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('files[]', fileInput.files[i]);
            }
        }
        
        formData.append('data_input', JSON.stringify(dataInput));
        
        // Show loading state
        if (!silent) {
            button.prop('disabled', true).addClass('loading');
        }
        
        $.ajax({
            url: this.baseUrl + '/admin-unit/simpan-kategori',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.success) {
                    if (!silent) this.showToast(response.message, 'success');
                    this.updateProgress(response.progress);
                    this.updateCategoryStatus(kategoriId, true);
                    
                    // Enable submit button if all complete
                    if (response.progress >= 100) {
                        $('#btn-kirim-data').prop('disabled', false);
                    }
                } else {
                    if (!silent) this.showToast(response.message, 'danger');
                }
            },
            error: () => {
                if (!silent) this.showToast('Terjadi kesalahan saat menyimpan data', 'danger');
            },
            complete: () => {
                if (!silent) {
                    button.prop('disabled', false).removeClass('loading');
                }
            }
        });
    }
    
    resetCategory(kategoriId) {
        const form = $(`.category-form[data-kategori-id="${kategoriId}"]`);
        
        if (confirm('Yakin ingin mereset data kategori ini?')) {
            form[0].reset();
            form.find('.file-list').empty();
            form.find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
            form.find('.invalid-feedback, .valid-feedback').remove();
            
            this.updateCategoryStatus(kategoriId, false);
            this.showToast('Data kategori direset', 'info');
        }
    }
    
    saveAllCategories(silent = false) {
        if (!this.canEdit) {
            if (!silent) this.showToast('Data tidak dapat diedit', 'warning');
            return;
        }
        
        const categories = $('.category-form');
        let savedCount = 0;
        
        categories.each((index, form) => {
            const kategoriId = $(form).data('kategori-id');
            
            // Save with a small delay to prevent server overload
            setTimeout(() => {
                this.saveCategory(kategoriId, silent);
                savedCount++;
                
                if (savedCount === categories.length && !silent) {
                    this.showToast('Semua kategori berhasil disimpan', 'success');
                }
            }, index * 200);
        });
    }
    
    submitData() {
        if (!this.canEdit) {
            this.showToast('Data tidak dapat dikirim', 'warning');
            return;
        }
        
        if (!confirm('Yakin ingin mengirim data ke Admin Pusat? Data tidak dapat diedit setelah dikirim.')) {
            return;
        }
        
        const button = $('#btn-kirim-data');
        button.prop('disabled', true).addClass('loading');
        
        $.ajax({
            url: this.baseUrl + '/admin-unit/kirim-data',
            method: 'POST',
            data: {
                pengiriman_id: this.pengirimanId
            },
            success: (response) => {
                if (response.success) {
                    this.showToast(response.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    this.showToast(response.message, 'danger');
                    button.prop('disabled', false).removeClass('loading');
                }
            },
            error: () => {
                this.showToast('Terjadi kesalahan saat mengirim data', 'danger');
                button.prop('disabled', false).removeClass('loading');
            }
        });
    }
    
    updateProgress(progress) {
        const progressFill = $('.progress-fill');
        const progressText = $('.progress-text');
        
        progressFill.css('width', progress + '%');
        progressText.text(Math.round(progress) + '%');
        
        // Update alert message
        const alert = $('.progress-container .alert');
        if (progress >= 100) {
            alert.removeClass('alert-info').addClass('alert-success')
                 .html('<i class="fas fa-check-circle me-2"></i><strong>Semua kategori sudah lengkap!</strong> Data siap untuk dikirim ke Admin Pusat.');
        } else {
            const completedCount = Math.floor((progress / 100) * 6);
            alert.removeClass('alert-success').addClass('alert-info')
                 .html(`<i class="fas fa-info-circle me-2"></i><strong>Lengkapi seluruh kategori sebelum mengirim data ke Admin Pusat.</strong> Progress saat ini: ${completedCount} dari 6 kategori.`);
        }
    }
    
    hasUnsavedChanges() {
        // Check if any form has been modified
        return $('.category-form').toArray().some(form => {
            return $(form).find('input, textarea').toArray().some(field => {
                return $(field).val() !== $(field).prop('defaultValue');
            });
        });
    }
    
    showToast(message, type = 'info') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = toastContainer.lastElementChild;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new AdminUnitDashboard();
});