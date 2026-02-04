/**
 * Mobile Menu Handler
 * UI GreenMetric POLBAN
 */

(function () {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function () {
        initMobileMenu();
    });

    function initMobileMenu() {
        // Create mobile menu toggle button if not exists
        if (!document.querySelector('.mobile-menu-toggle')) {
            createMobileMenuButton();
        }

        // Create sidebar overlay if not exists
        if (!document.querySelector('.sidebar-overlay')) {
            createSidebarOverlay();
        }

        // Setup event listeners
        setupEventListeners();

        // Handle window resize
        handleResize();
        window.addEventListener('resize', handleResize);
    }

    function createMobileMenuButton() {
        const button = document.createElement('button');
        button.className = 'mobile-menu-toggle';
        button.innerHTML = '<i class="fas fa-bars"></i>';
        button.setAttribute('aria-label', 'Toggle Menu');
        document.body.appendChild(button);
    }

    function createSidebarOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    function setupEventListeners() {
        const menuToggle = document.querySelector('.mobile-menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');

        if (!menuToggle || !sidebar || !overlay) {
            console.error('Mobile menu elements not found:', {
                menuToggle: !!menuToggle,
                sidebar: !!sidebar,
                overlay: !!overlay
            });
            return;
        }

        console.log('Mobile menu initialized successfully');

        // Toggle menu on button click
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            console.log('Menu toggle clicked');
            toggleMenu();
        });

        // Close menu on overlay click
        overlay.addEventListener('click', function () {
            closeMenu();
        });

        // Close menu when clicking sidebar links
        const sidebarLinks = sidebar.querySelectorAll('a');
        sidebarLinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                // Don't close if it's a submenu toggle
                if (link.classList.contains('menu-toggle')) {
                    e.stopPropagation();
                    return;
                }

                // Only close on mobile
                if (window.innerWidth <= 1024) {
                    closeMenu();
                }
            });
        });

        // Close menu on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // Prevent body scroll when menu is open
        sidebar.addEventListener('touchmove', function (e) {
            if (sidebar.classList.contains('active')) {
                e.stopPropagation();
            }
        });
    }

    function toggleMenu() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const menuToggle = document.querySelector('.mobile-menu-toggle');

        if (!sidebar || !overlay || !menuToggle) return;

        const isActive = sidebar.classList.contains('active');

        if (isActive) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    function openMenu() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const menuToggle = document.querySelector('.mobile-menu-toggle');

        if (!sidebar || !overlay || !menuToggle) return;

        console.log('Opening menu...');
        sidebar.classList.add('active');
        overlay.classList.add('active');
        menuToggle.innerHTML = '<i class="fas fa-times"></i>';

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        console.log('Menu opened, sidebar classes:', sidebar.className);
    }

    function closeMenu() {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const menuToggle = document.querySelector('.mobile-menu-toggle');

        if (!sidebar || !overlay || !menuToggle) return;

        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        menuToggle.innerHTML = '<i class="fas fa-bars"></i>';

        // Restore body scroll
        document.body.style.overflow = '';
    }

    function handleResize() {
        // Close menu if window is resized to desktop
        if (window.innerWidth > 1024) {
            closeMenu();
        }
    }

    // Expose functions globally if needed
    window.mobileMenu = {
        open: openMenu,
        close: closeMenu,
        toggle: toggleMenu
    };
})();
