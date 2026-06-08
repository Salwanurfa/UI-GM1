<?php
/**
 * Custom Pagination Template for UI GreenMetric
 * Consistent with Data Sampah pagination style
 */

$pager->setSurroundCount(2);
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination pagination-sm justify-content-center">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link" aria-label="<?= lang('Pager.first') ?>">
                    <span aria-hidden="true"><i class="fas fa-angle-double-left"></i></span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link" aria-label="<?= lang('Pager.previous') ?>">
                    <span aria-hidden="true"><i class="fas fa-angle-left"></i> Previous</span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link" aria-label="<?= lang('Pager.next') ?>">
                    <span aria-hidden="true">Next <i class="fas fa-angle-right"></i></span>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link" aria-label="<?= lang('Pager.last') ?>">
                    <span aria-hidden="true"><i class="fas fa-angle-double-right"></i></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<style>
/* Custom Pagination Styling for UI GreenMetric */
.pagination {
    margin: 20px 0;
}

.pagination .page-link {
    color: #28a745;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}

.pagination .page-item.active .page-link {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
    font-weight: bold;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Responsive pagination */
@media (max-width: 576px) {
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .pagination .page-link span {
        display: none;
    }
    
    .pagination .page-link i {
        display: inline-block !important;
    }
}
</style>
