/**
 * Tooltip Helper
 * UI GreenMetric POLBAN
 * 
 * Usage:
 * <button data-tooltip="Ini adalah tooltip">Hover me</button>
 * <input data-tooltip="Masukkan nama lengkap" data-tooltip-position="top">
 */

class TooltipHelper {
    constructor() {
        this.tooltip = null;
        this.init();
    }

    init() {
        // Create tooltip element
        if (!document.getElementById('custom-tooltip')) {
            this.tooltip = document.createElement('div');
            this.tooltip.id = 'custom-tooltip';
            this.tooltip.className = 'custom-tooltip';
            document.body.appendChild(this.tooltip);
        } else {
            this.tooltip = document.getElementById('custom-tooltip');
        }

        // Initialize tooltips on page load
        this.initializeTooltips();

        // Re-initialize on dynamic content
        this.observeDOMChanges();
    }

    initializeTooltips() {
        const elements = document.querySelectorAll('[data-tooltip]');
        elements.forEach(element => {
            if (!element.dataset.tooltipInitialized) {
                this.attachTooltip(element);
                element.dataset.tooltipInitialized = 'true';
            }
        });
    }

    attachTooltip(element) {
        element.addEventListener('mouseenter', (e) => this.show(e));
        element.addEventListener('mouseleave', () => this.hide());
        element.addEventListener('mousemove', (e) => this.updatePosition(e));
    }

    show(event) {
        const element = event.currentTarget;
        const text = element.dataset.tooltip;
        const position = element.dataset.tooltipPosition || 'top';

        if (!text) return;

        this.tooltip.textContent = text;
        this.tooltip.className = `custom-tooltip custom-tooltip-${position}`;
        this.tooltip.classList.add('active');

        this.updatePosition(event);
    }

    hide() {
        this.tooltip.classList.remove('active');
    }

    updatePosition(event) {
        const element = event.currentTarget;
        const position = element.dataset.tooltipPosition || 'top';
        const rect = element.getBoundingClientRect();
        const tooltipRect = this.tooltip.getBoundingClientRect();

        let left, top;

        switch (position) {
            case 'top':
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                top = rect.top - tooltipRect.height - 10;
                break;
            case 'bottom':
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                top = rect.bottom + 10;
                break;
            case 'left':
                left = rect.left - tooltipRect.width - 10;
                top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                break;
            case 'right':
                left = rect.right + 10;
                top = rect.top + (rect.height / 2) - (tooltipRect.height / 2);
                break;
            default:
                left = rect.left + (rect.width / 2) - (tooltipRect.width / 2);
                top = rect.top - tooltipRect.height - 10;
        }

        // Keep tooltip within viewport
        const padding = 10;
        if (left < padding) left = padding;
        if (left + tooltipRect.width > window.innerWidth - padding) {
            left = window.innerWidth - tooltipRect.width - padding;
        }
        if (top < padding) top = rect.bottom + 10; // Flip to bottom if no space on top

        this.tooltip.style.left = `${left}px`;
        this.tooltip.style.top = `${top}px`;
    }

    observeDOMChanges() {
        const observer = new MutationObserver(() => {
            this.initializeTooltips();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Manual tooltip trigger
    showAt(text, x, y) {
        this.tooltip.textContent = text;
        this.tooltip.className = 'custom-tooltip custom-tooltip-top active';
        this.tooltip.style.left = `${x}px`;
        this.tooltip.style.top = `${y}px`;
    }
}

// Initialize global tooltip instance
const tooltipHelper = new TooltipHelper();

// Make it available globally
window.tooltipHelper = tooltipHelper;

// Initialize Bootstrap tooltips if available
document.addEventListener('DOMContentLoaded', function () {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
