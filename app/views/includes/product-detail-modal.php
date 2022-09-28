<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

    /**
     * @var $controller
     */
?>
<div class="modal fade" id="product-detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="modal-name">Title</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="top-container">
                    <div class="col-sm-12" id="top-contents-container">
                        <h4 class="modal-title" id="modal-title">Title</h4>
                        <h5 class="modal-category" id="modal-category">Category</h5>
                        <h4><span class="badge rounded-pill bg-warning modal-price">Price</span></h4>
                        <h6 class="modal-sku" id="modal-sku">SKU</h6>
                    </div>
                </div>

                <div class="modal-desc">Description</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php if ($controller === 'catalog'): ?>
                    <a href="" class="btn btn-primary" id="view-product">View Full Details</a>
                <?php else: ?>
                    <a href="" target="_blank" class="btn btn-primary" id="view-product"><i class="fa-solid fa-up-right-from-square"></i> View On Site</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>