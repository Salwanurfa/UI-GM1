<?php
$user = session()->get('user');
$role = $user['role'] ?? '';
$currentUrl = uri_string();

// Define menu items per role
$menus = [
    'admin_pusat' => [
        'title' => 'Admin Pusat',
        'subtitle' => 'UI GreenMetric POLBAN',
        'color' => '#1e3c72',
        'color2' => '#2a5298',
        'items' => [
            ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'url' => '/admin-pusat/dashboard'],
            ['section' => 'KATEGORI UIGM'],
            [
                'icon' => 'fa-building', 
                'label' => 'Setting & Infrastructure', 
                'submenu' => [
                    ['label' => 'Data Infrastructure', 'url' => '/admin-pusat/infrastructure'],
                    ['label' => 'Laporan', 'url' => '/admin-pusat/infrastructure/laporan'],
                ]
            ],
            [
                'icon' => 'fa-bolt', 
                'label' => 'Energy & Climate', 
                'submenu' => [
                    ['label' => 'Data Energi', 'url' => '/admin-pusat/energy'],
                    ['label' => 'Laporan', 'url' => '/admin-pusat/energy/laporan'],
                ]
            ],
            [
                'icon' => 'fa-trash-alt', 
                'label' => 'Waste Management', 
                'submenu' => [
                    ['label' => 'Manajemen Data Sampah', 'url' => '/admin-pusat/waste'],
                    ['label' => 'Manajemen Jenis Sampah', 'url' => '/admin-pusat/manajemen-harga'],
                    ['label' => 'Laporan Data Sampah', 'url' => '/admin-pusat/laporan-waste'],
                ]
            ],
            [
                'icon' => 'fa-tint', 
                'label' => 'Water Management', 
                'submenu' => [
                    ['label' => 'Data Air', 'url' => '/admin-pusat/water'],
                    ['label' => 'Laporan', 'url' => '/admin-pusat/water/laporan'],
                ]
            ],
            [
                'icon' => 'fa-car', 
                'label' => 'Transportation', 
                'submenu' => [
                    ['label' => 'Data Transportasi', 'url' => '/admin-pusat/transportation'],
                    ['label' => 'Laporan', 'url' => '/admin-pusat/transportation/laporan'],
                ]
            ],
            [
                'icon' => 'fa-graduation-cap', 
                'label' => 'Education & Research', 
                'submenu' => [
                    ['label' => 'Data Pendidikan', 'url' => '/admin-pusat/education'],
                    ['label' => 'Laporan', 'url' => '/admin-pusat/education/laporan'],
                ]
            ],
            ['section' => 'DATA MANAGEMENT'],
            ['icon' => 'fa-users', 'label' => 'Manajemen Akun', 'url' => '/admin-pusat/user-management'],
            ['section' => 'SYSTEM'],
            ['icon' => 'fa-user-circle', 'label' => 'Profil Akun', 'url' => '/admin-pusat/profil'],
        ]
    ],
    'user' => [
        'title' => 'User System',
        'subtitle' => 'Petugas Gedung',
        'color' => '#2c3e50',
        'color2' => '#34495e',
        'items' => [
            ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'url' => '/user/dashboard'],
            ['section' => 'WASTE MANAGEMENT'],
            ['icon' => 'fa-trash-alt', 'label' => 'Data Sampah', 'url' => '/user/waste'],
            ['section' => 'AKUN'],
            ['icon' => 'fa-user-circle', 'label' => 'Profil Akun', 'url' => '/user/profile'],
        ]
    ],
    'pengelola_tps' => [
        'title' => 'TPS System',
        'subtitle' => 'Pengelola TPS',
        'color' => '#2c3e50',
        'color2' => '#34495e',
        'items' => [
            ['icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'url' => '/pengelola-tps/dashboard'],
            ['section' => 'LAPORAN'],
            ['icon' => 'fa-inbox', 'label' => 'Laporan Masuk', 'url' => '/pengelola-tps/laporan-masuk'],
            ['section' => 'WASTE MANAGEMENT'],
            ['icon' => 'fa-trash-alt', 'label' => 'Data Sampah', 'url' => '/pengelola-tps/waste'],
            ['section' => 'AKUN'],
            ['icon' => 'fa-user-circle', 'label' => 'Profil Akun', 'url' => '/pengelola-tps/profile'],
        ]
    ]
];

$currentMenu = $menus[$role] ?? $menus['user'];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-recycle"></i>
        </div>
        <div class="sidebar-title"><?= $currentMenu['title'] ?></div>
        <div class="sidebar-subtitle"><?= $currentMenu['subtitle'] ?></div>
    </div>

    <div class="user-info">
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="user-details">
            <h4><?= esc($user['nama_lengkap']) ?></h4>
            <p><?= ucfirst(str_replace('_', ' ', $role)) ?></p>
            <?php if (isset($user['nama_unit'])): ?>
                <small><?= esc($user['nama_unit']) ?></small>
            <?php endif; ?>
        </div>
    </div>

    <nav class="sidebar-menu">
        <?php foreach ($currentMenu['items'] as $item): ?>
            <?php if (isset($item['section'])): ?>
                <div class="menu-section">
                    <div class="menu-section-title">
                        <i class="fas fa-database"></i>
                        <?= $item['section'] ?>
                    </div>
                </div>
            <?php elseif (isset($item['submenu'])): ?>
                <div class="menu-item-with-submenu">
                    <a href="javascript:void(0)" class="menu-item menu-toggle">
                        <i class="fas <?= $item['icon'] ?>"></i>
                        <span><?= $item['label'] ?></span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    <div class="submenu">
                        <?php foreach ($item['submenu'] as $subitem): ?>
                            <a href="<?= base_url($subitem['url']) ?>" 
                               class="submenu-item <?= (strpos($currentUrl, $subitem['url']) !== false) ? 'active' : '' ?>">
                                <i class="fas fa-circle submenu-dot"></i>
                                <span><?= $subitem['label'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= base_url($item['url']) ?>" 
                   class="menu-item <?= (strpos($currentUrl, $item['url']) !== false) ? 'active' : '' ?>">
                    <i class="fas <?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <a href="<?= base_url('/auth/logout') ?>" class="menu-item logout-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</div>

<style>
.sidebar {
    width: 280px;
    height: 100vh;
    background: linear-gradient(180deg, <?= $currentMenu['color'] ?> 0%, <?= $currentMenu['color2'] ?> 100%);
    color: white;
    position: fixed;
    left: 0;
    top: 0;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    padding: 30px 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.1);
}

.sidebar-logo {
    font-size: 40px;
    color: #27ae60;
    margin-bottom: 10px;
}

.sidebar-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 5px;
}

.sidebar-subtitle {
    font-size: 14px;
    opacity: 0.8;
}

.user-info {
    padding: 25px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.user-details h4 {
    margin: 0 0 5px 0;
    font-size: 16px;
    font-weight: 600;
}

.user-details p {
    margin: 0 0 3px 0;
    font-size: 12px;
    color: #27ae60;
    font-weight: 500;
}

.user-details small {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.7);
}

.sidebar-menu {
    padding: 20px 0;
}

.menu-section {
    margin-bottom: 10px;
}

.menu-section-title {
    padding: 15px 20px 10px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    opacity: 0.7;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 8px;
}

.menu-item {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
    position: relative;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-left-color: #27ae60;
}

.menu-item.active {
    background: rgba(39, 174, 96, 0.2);
    color: #27ae60;
    border-left: 3px solid #27ae60;
    font-weight: 600;
}

.menu-item i {
    width: 20px;
    margin-right: 15px;
    font-size: 16px;
}

.menu-item span {
    font-size: 14px;
    font-weight: 500;
    flex: 1;
}

/* Submenu Styles */
.menu-item-with-submenu {
    position: relative;
}

.menu-toggle {
    cursor: pointer;
}

.submenu-arrow {
    margin-left: auto;
    font-size: 12px;
    transition: transform 0.3s ease;
}

.menu-item-with-submenu.open .submenu-arrow {
    transform: rotate(180deg);
}

.submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: rgba(0, 0, 0, 0.2);
}

.menu-item-with-submenu.open .submenu {
    max-height: 500px;
}

.submenu-item {
    display: flex;
    align-items: center;
    padding: 12px 20px 12px 55px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 13px;
}

.submenu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding-left: 60px;
}

.submenu-item.active {
    background: rgba(39, 174, 96, 0.15);
    color: #27ae60;
    font-weight: 600;
    border-left: 3px solid #27ae60;
}

.submenu-dot {
    font-size: 6px;
    margin-right: 10px;
}

.logout-item {
    margin-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
}

.logout-item:hover {
    background: rgba(231, 76, 60, 0.2);
    border-left-color: #e74c3c;
    color: #e74c3c;
}

/* Scrollbar styling */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar.active {
        transform: translateX(0);
    }
}
</style>

<script>
// Toggle submenu
document.addEventListener('DOMContentLoaded', function() {
    const menuToggles = document.querySelectorAll('.menu-toggle');
    
    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.menu-item-with-submenu');
            
            // Close other submenus
            document.querySelectorAll('.menu-item-with-submenu').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('open');
                }
            });
            
            // Toggle current submenu
            parent.classList.toggle('open');
        });
    });
    
    // Auto-open submenu if current page is in submenu
    const activeSubmenuItem = document.querySelector('.submenu-item.active');
    if (activeSubmenuItem) {
        const parent = activeSubmenuItem.closest('.menu-item-with-submenu');
        if (parent) {
            parent.classList.add('open');
        }
    }
});
</script>
