/**
 * Confirmation Dialog System
 * UI GreenMetric POLBAN
 */

class ConfirmationDialog {
    constructor() {
        this.modal = null;
        this.init();
    }

    init() {
        // Create confirmation modal if not exists
        if (!document.getElementById('confirmation-modal')) {
            this.modal = document.createElement('div');
            this.modal.id = 'confirmation-modal';
            this.modal.className = 'confirmation-modal';
            this.modal.innerHTML = `
                <div class="confirmation-overlay"></div>
                <div class="confirmation-content">
                    <div class="confirmation-icon"></div>
                    <h3 class="confirmation-title"></h3>
                    <p class="confirmation-message"></p>
                    <div class="confirmation-buttons">
                        <button class="btn btn-secondary confirmation-cancel">Batal</button>
                        <button class="btn btn-primary confirmation-confirm">Konfirmasi</button>
                    </div>
                </div>
            `;
            document.body.appendChild(this.modal);
        } else {
            this.modal = document.getElementById('confirmation-modal');
        }
    }

    show(options = {}) {
        return new Promise((resolve) => {
            const {
                title = 'Konfirmasi',
                message = 'Apakah Anda yakin?',
                type = 'warning',
                confirmText = 'Ya, Lanjutkan',
                cancelText = 'Batal',
                confirmClass = 'btn-primary'
            } = options;

            // Set content
            const iconElement = this.modal.querySelector('.confirmation-icon');
            const titleElement = this.modal.querySelector('.confirmation-title');
            const messageElement = this.modal.querySelector('.confirmation-message');
            const confirmBtn = this.modal.querySelector('.confirmation-confirm');
            const cancelBtn = this.modal.querySelector('.confirmation-cancel');

            // Set icon based on type
            const icons = {
                warning: '<i class="fas fa-exclamation-triangle"></i>',
                danger: '<i class="fas fa-exclamation-circle"></i>',
                info: '<i class="fas fa-info-circle"></i>',
                success: '<i class="fas fa-check-circle"></i>'
            };
            iconElement.innerHTML = icons[type] || icons.warning;
            iconElement.className = `confirmation-icon confirmation-icon-${type}`;

            titleElement.textContent = title;
            messageElement.textContent = message;
            confirmBtn.textContent = confirmText;
            cancelBtn.textContent = cancelText;

            // Update button class
            confirmBtn.className = `btn ${confirmClass} confirmation-confirm`;

            // Show modal
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Handle confirm
            const handleConfirm = () => {
                this.hide();
                resolve(true);
                cleanup();
            };

            // Handle cancel
            const handleCancel = () => {
                this.hide();
                resolve(false);
                cleanup();
            };

            // Handle overlay click
            const handleOverlay = (e) => {
                if (e.target.classList.contains('confirmation-overlay')) {
                    handleCancel();
                }
            };

            // Cleanup listeners
            const cleanup = () => {
                confirmBtn.removeEventListener('click', handleConfirm);
                cancelBtn.removeEventListener('click', handleCancel);
                this.modal.removeEventListener('click', handleOverlay);
            };

            // Add event listeners
            confirmBtn.addEventListener('click', handleConfirm);
            cancelBtn.addEventListener('click', handleCancel);
            this.modal.addEventListener('click', handleOverlay);
        });
    }

    hide() {
        if (this.modal) {
            this.modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Preset confirmations
    async delete(itemName = 'data ini') {
        return await this.show({
            title: 'Hapus Data',
            message: `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`,
            type: 'danger',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            confirmClass: 'btn-danger'
        });
    }

    async submit(message = 'Apakah Anda yakin ingin mengirim data ini?') {
        return await this.show({
            title: 'Kirim Data',
            message: message,
            type: 'info',
            confirmText: 'Ya, Kirim',
            cancelText: 'Batal',
            confirmClass: 'btn-primary'
        });
    }

    async approve(itemName = 'data ini') {
        return await this.show({
            title: 'Setujui Data',
            message: `Apakah Anda yakin ingin menyetujui ${itemName}?`,
            type: 'success',
            confirmText: 'Ya, Setujui',
            cancelText: 'Batal',
            confirmClass: 'btn-success'
        });
    }

    async reject(itemName = 'data ini') {
        return await this.show({
            title: 'Tolak Data',
            message: `Apakah Anda yakin ingin menolak ${itemName}?`,
            type: 'warning',
            confirmText: 'Ya, Tolak',
            cancelText: 'Batal',
            confirmClass: 'btn-warning'
        });
    }
}

// Initialize global confirmation instance
const confirm = new ConfirmationDialog();

// Make it available globally
window.confirm = confirm;
