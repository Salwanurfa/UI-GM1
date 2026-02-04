/**
 * Loading State Manager
 * UI GreenMetric POLBAN
 */

class LoadingState {
    constructor() {
        this.overlay = null;
        this.init();
    }

    init() {
        // Create loading overlay
        if (!document.getElementById('loading-overlay')) {
            this.overlay = document.createElement('div');
            this.overlay.id = 'loading-overlay';
            this.overlay.className = 'loading-overlay';
            this.overlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="loading-text">Memproses...</div>
                </div>
            `;
            document.body.appendChild(this.overlay);
        } else {
            this.overlay = document.getElementById('loading-overlay');
        }
    }

    show(text = 'Memproses...') {
        if (this.overlay) {
            const loadingText = this.overlay.querySelector('.loading-text');
            if (loadingText) {
                loadingText.textContent = text;
            }
            this.overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    hide() {
        if (this.overlay) {
            this.overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Button loading state
    buttonLoading(button, loading = true) {
        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Memproses...
            `;
        } else {
            button.disabled = false;
            if (button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        }
    }

    // Table loading skeleton
    showTableSkeleton(tableBody, rows = 5, cols = 6) {
        let skeletonHTML = '';
        for (let i = 0; i < rows; i++) {
            skeletonHTML += '<tr class="skeleton-row">';
            for (let j = 0; j < cols; j++) {
                skeletonHTML += '<td><div class="skeleton-line"></div></td>';
            }
            skeletonHTML += '</tr>';
        }
        tableBody.innerHTML = skeletonHTML;
    }

    hideTableSkeleton(tableBody) {
        const skeletonRows = tableBody.querySelectorAll('.skeleton-row');
        skeletonRows.forEach(row => row.remove());
    }
}

// Initialize global loading instance
const loading = new LoadingState();

// Make it available globally
window.loading = loading;

// Helper function for fetch with loading
window.fetchWithLoading = async function (url, options = {}, loadingText = 'Memproses...') {
    loading.show(loadingText);
    try {
        const response = await fetch(url, options);
        return response;
    } finally {
        loading.hide();
    }
};
