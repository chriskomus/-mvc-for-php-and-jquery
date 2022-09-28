<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $dark
     * @var $data
     * @var $settings
     */

    // Page specific variable requirements
    $row = $data['product'];
    $page = $row['title'];
    $image_src = ROOT_URL . '/public/images/products/' . $row['image'];

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
        <div class="card-header"><?= $page ?></div>
        <div class="card-body">
            <div class="row">
                <?php if(@getimagesize($image_src)): ?>
                    <div class="col-sm-4">
                        <img src="<?= $image_src ?>" class="rounded img-fluid" alt="<?= $page ?>">
                    </div>
                <?php endif; ?>
                <div class="col-sm-8">
                    <h5><?= $row['title'] ?></h5>
                    <h6><a href="catalog/<?= $row['category_slug'] ?>"><?= $row['category'] ?></a></h6>
                    <h3><span class="badge rounded-pill bg-warning modal-price"><?= $row['sale_price'] && $row['sale_price'] > 0 ? $settings['currency'] . number_format($row['sale_price'], $settings['price_decimals']) : 'Call for pricing' ?></span></h3>
                    <h6>SKU: <?= $row['sku'] ?></h6>
                </div>
            </div>

            <div class="modal-desc"><?= html_entity_decode($row['detailed_description']) ?></div>
        </div>
    </div>

    <!-- END PAGE CONTENT -->

    <!-- MODALS -->
    <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
</body>
</html>