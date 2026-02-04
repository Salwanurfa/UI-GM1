/**
 * Laporan Waste - Detail Rekap Jenis Sampah
 * Menampilkan rincian per gedung dan pelapor
 */

// Global variables
let currentFilters = {};

/**
 * Show detail rekap jenis sampah in modal
 * @param {string} jenisSampah - Jenis sampah yang akan ditampilkan detailnya
 */
function showDetailRekapJenis(jenisSampah) {
    // Get current filters from form
    currentFilters = {
        start_date: document.getElementById('start_date')?.value || '',
        end_date: document.getElementById('end_date')?.value || '',
        unit_id: document.getElementById('unit_id')?.value || ''
    };

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailRekapModal'));
    modal.show();

    // Set modal title
    document.getElementById('detailRekapModalLabel').innerHTML =
        `<i class="fas fa-info-circle"></i> Detail Rekap: ${escapeHtml(jenisSampah)}`;

    // Show loading
    document.getElementById('detailRekapBody').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data...</p>
        </div>
    `;

    // Fetch data via AJAX
    const params = new URLSearchParams({
        jenis_sampah: jenisSampah,
        ...currentFilters
    });

    fetch(`/admin-pusat/laporan-waste/detail-rekap-jenis?${params.toString()}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderDetailRekapJenis(data);
            } else {
                showError(data.message || 'Gagal memuat data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Terjadi kesalahan saat memuat data');
        });
}

/**
 * Render detail rekap jenis sampah
 * @param {Object} data - Data from API
 */
function renderDetailRekapJenis(data) {
    const summary = data.summary || {};
    const details = data.details || [];

    let html = '';

    // Summary Section
    html += `
        <div class="alert alert-info">
            <div class="row text-center">
                <div class="col-md-3">
                    <h5 class="mb-0">${formatNumber(summary.total_transaksi || 0)}</h5>
                    <small>Total Transaksi</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0">${formatNumber(summary.total_gedung || 0)}</h5>
                    <small>Total Gedung</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0">${formatNumber(summary.total_pelapor || 0)}</h5>
                    <small>Total Pelapor</small>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-0">${formatCurrency(summary.total_nilai_disetujui || 0)}</h5>
                    <small>Total Nilai Disetujui</small>
                </div>
            </div>
        </div>
    `;

    // Details Table
    if (details.length > 0) {
        html += `
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Gedung</th>
                            <th>Unit</th>
                            <th>Pelapor</th>
                            <th>Nama Sampah</th>
                            <th>Jumlah Laporan</th>
                            <th>Disetujui</th>
                            <th>Ditolak</th>
                            <th>Berat (kg)</th>
                            <th>Nilai (Rp)</th>
                            <th>Periode</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        details.forEach((item, index) => {
            const periodeStart = item.laporan_pertama ? formatDate(item.laporan_pertama) : '-';
            const periodeEnd = item.laporan_terakhir ? formatDate(item.laporan_terakhir) : '-';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${escapeHtml(item.gedung || '-')}</strong></td>
                    <td>${escapeHtml(item.nama_unit || '-')}</td>
                    <td>
                        ${escapeHtml(item.nama_pelapor || '-')}
                        <br><small class="text-muted">${escapeHtml(item.username || '')}</small>
                    </td>
                    <td><span class="badge bg-primary">${escapeHtml(item.nama_sampah || '-')}</span></td>
                    <td><span class="badge bg-info">${formatNumber(item.jumlah_laporan || 0)}</span></td>
                    <td><span class="badge bg-success">${formatNumber(item.total_disetujui || 0)}</span></td>
                    <td><span class="badge bg-danger">${formatNumber(item.total_ditolak || 0)}</span></td>
                    <td>${formatNumber(item.total_berat_disetujui || 0)}</td>
                    <td>${formatCurrency(item.total_nilai_disetujui || 0)}</td>
                    <td>
                        <small>${periodeStart}<br>s/d<br>${periodeEnd}</small>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;
    } else {
        html += `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada data detail untuk jenis sampah ini</p>
            </div>
        `;
    }

    document.getElementById('detailRekapBody').innerHTML = html;
}

/**
 * Show error message in modal
 * @param {string} message - Error message
 */
function showError(message) {
    document.getElementById('detailRekapBody').innerHTML = `
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${escapeHtml(message)}
        </div>
    `;
}

/**
 * Format number with thousand separator
 * @param {number} num - Number to format
 * @returns {string} Formatted number
 */
function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

/**
 * Format currency (Rupiah)
 * @param {number} num - Number to format
 * @returns {string} Formatted currency
 */
function formatCurrency(num) {
    return 'Rp ' + parseFloat(num || 0).toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}

/**
 * Format date to Indonesian format
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Export functions for global use
window.showDetailRekapJenis = showDetailRekapJenis;
