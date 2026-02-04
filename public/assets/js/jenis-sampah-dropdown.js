/**
 * Dropdown Bertingkat Jenis Sampah
 * Handles cascading dropdowns for organic waste types
 */

// Static data untuk testing (akan diganti dengan API calls nanti)
const JENIS_SAMPAH_DATA = {
    areas: [
        { id: 2, nama: 'Sampah dari Kantin' },
        { id: 3, nama: 'Sampah dari Lingkungan Kampus' }
    ],
    details: {
        2: [ // Kantin
            { id: 4, nama: 'Sisa Makanan atau Sayuran' },
            { id: 5, nama: 'Sisa Buah-buahan' },
            { id: 6, nama: 'Produk Sisa Dapur (Ampas Kopi/Teh, Kulit Telur)' }
        ],
        3: [ // Lingkungan Kampus
            { id: 7, nama: 'Daun-daun Kering yang Gugur' },
            { id: 8, nama: 'Rumput yang Dipotong' },
            { id: 9, nama: 'Ranting-ranting Pohon Kecil' }
        ]
    }
};

// Initialize dropdown functionality
function initJenisSampahDropdown() {
    console.log('🔧 Initializing Jenis Sampah Dropdown...');
    
    // Handle jenis sampah change
    $(document).on('change', 'select[name="jenis_sampah"]', function() {
        const $this = $(this);
        const selectedValue = $this.val();
        const kategoriId = $this.closest('.category-form').data('kategori-id');
        
        console.log('📝 Jenis sampah changed:', selectedValue, 'for kategori:', kategoriId);
        
        // Reset dependent dropdowns
        resetAreaDropdown(kategoriId);
        resetDetailDropdown(kategoriId);
        
        if (selectedValue === 'Organik') {
            console.log('🌱 Showing organic waste dropdowns...');
            showAreaDropdown(kategoriId);
            loadAreaOptions(kategoriId);
        } else {
            console.log('🚫 Hiding organic waste dropdowns...');
            hideAreaDropdown(kategoriId);
            hideDetailDropdown(kategoriId);
        }
    });
    
    // Handle area sampah change
    $(document).on('change', 'select[name="area_sampah"]', function() {
        const $this = $(this);
        const selectedAreaId = $this.val();
        const kategoriId = $this.closest('.category-form').data('kategori-id');
        
        console.log('📍 Area sampah changed:', selectedAreaId, 'for kategori:', kategoriId);
        
        // Reset detail dropdown
        resetDetailDropdown(kategoriId);
        
        if (selectedAreaId) {
            console.log('📋 Showing detail dropdown...');
            showDetailDropdown(kategoriId);
            loadDetailOptions(kategoriId, selectedAreaId);
        } else {
            console.log('🚫 Hiding detail dropdown...');
            hideDetailDropdown(kategoriId);
        }
    });
    
    console.log('✅ Jenis Sampah Dropdown initialized successfully');
}

// Load area options
function loadAreaOptions(kategoriId) {
    console.log('📥 Loading area options for kategori:', kategoriId);
    
    const $areaSelect = $(`#area_sampah_${kategoriId}`);
    
    if ($areaSelect.length === 0) {
        console.error('❌ Area select element not found for kategori:', kategoriId);
        return;
    }
    
    // Show loading
    $areaSelect.html('<option value="">Loading...</option>');
    
    // Use static data for now
    setTimeout(() => {
        let options = '<option value="">Pilih Area Sampah</option>';
        JENIS_SAMPAH_DATA.areas.forEach(area => {
            options += `<option value="${area.id}">${area.nama}</option>`;
        });
        $areaSelect.html(options);
        console.log('✅ Area options loaded successfully');
    }, 300);
}

// Load detail options
function loadDetailOptions(kategoriId, areaId) {
    console.log('📥 Loading detail options for kategori:', kategoriId, 'area:', areaId);
    
    const $detailSelect = $(`#detail_sampah_${kategoriId}`);
    
    if ($detailSelect.length === 0) {
        console.error('❌ Detail select element not found for kategori:', kategoriId);
        return;
    }
    
    // Show loading
    $detailSelect.html('<option value="">Loading...</option>');
    
    // Use static data for now
    setTimeout(() => {
        const details = JENIS_SAMPAH_DATA.details[areaId] || [];
        let options = '<option value="">Pilih Detail Sampah</option>';
        details.forEach(detail => {
            options += `<option value="${detail.id}">${detail.nama}</option>`;
        });
        $detailSelect.html(options);
        console.log('✅ Detail options loaded successfully');
    }, 300);
}

// Show/Hide functions
function showAreaDropdown(kategoriId) {
    console.log('👁️ Showing area dropdown for kategori:', kategoriId);
    const $element = $(`#area_sampah_group_${kategoriId}`);
    if ($element.length > 0) {
        $element.slideDown(300);
        console.log('✅ Area dropdown shown');
    } else {
        console.error('❌ Area dropdown element not found');
    }
}

function hideAreaDropdown(kategoriId) {
    console.log('🙈 Hiding area dropdown for kategori:', kategoriId);
    $(`#area_sampah_group_${kategoriId}`).slideUp(300);
}

function showDetailDropdown(kategoriId) {
    console.log('👁️ Showing detail dropdown for kategori:', kategoriId);
    const $element = $(`#detail_sampah_group_${kategoriId}`);
    if ($element.length > 0) {
        $element.slideDown(300);
        console.log('✅ Detail dropdown shown');
    } else {
        console.error('❌ Detail dropdown element not found');
    }
}

function hideDetailDropdown(kategoriId) {
    console.log('🙈 Hiding detail dropdown for kategori:', kategoriId);
    $(`#detail_sampah_group_${kategoriId}`).slideUp(300);
}

// Reset functions
function resetAreaDropdown(kategoriId) {
    console.log('🔄 Resetting area dropdown for kategori:', kategoriId);
    $(`#area_sampah_${kategoriId}`).html('<option value="">Pilih Area Sampah</option>');
}

function resetDetailDropdown(kategoriId) {
    console.log('🔄 Resetting detail dropdown for kategori:', kategoriId);
    $(`#detail_sampah_${kategoriId}`).html('<option value="">Pilih Detail Sampah</option>');
}

// Test function to verify elements exist
function testDropdownElements() {
    console.log('🧪 Testing dropdown elements...');
    
    $('.category-form').each(function() {
        const kategoriId = $(this).data('kategori-id');
        const jenisSampahSelect = $(this).find('select[name="jenis_sampah"]');
        const areaGroup = $(`#area_sampah_group_${kategoriId}`);
        const detailGroup = $(`#detail_sampah_group_${kategoriId}`);
        
        console.log(`Kategori ${kategoriId}:`, {
            jenisSampahSelect: jenisSampahSelect.length > 0,
            areaGroup: areaGroup.length > 0,
            detailGroup: detailGroup.length > 0
        });
    });
}

// Initialize when document is ready
$(document).ready(function() {
    console.log('🚀 Document ready, initializing jenis sampah dropdown...');
    
    // Test elements first
    setTimeout(() => {
        testDropdownElements();
        initJenisSampahDropdown();
    }, 1000);
});