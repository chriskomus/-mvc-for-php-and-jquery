<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $dark
     * @var $data
     * @var $page
     * @var $card_title
     * @var $card_description
     * @var $settings
     */

    // Page specific variable requirements
    if (isset($data['category_details'])) {
        $page = $data['category_details']['title'];
        $card_title = $data['category_details']['title'];
        $card_description = html_entity_decode($data['category_details']['description']);
    } else {
        $page = $settings['homepage_title'];
        $card_title = $settings['homepage_header'];
        $card_description = $settings['homepage_body'];
    }

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/search-modal.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <div class="card bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
        <div class="card-header"><?= $page ?></div>
        <div class="card-body">
            <?php if (isset($data['category_details'])): ?>
                <?= $card_description ?>
            <?php else: ?>
                <h4 class="card-title"><i class="fa-solid fa-box-open"></i> <?= $card_title ?></h4>
                <p class="card-text"><?= $card_description ?></p>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <?php require APP_DIRECTORY . '/views/includes/filter-sorts.php'; ?>
        </div>
    </div>

    <!-- PAGINATION BUTTONS CONTAINER -->
    <div class="" id="pagination-container"></div>

    <!-- START ITEM CONTENT - datal-model and data-parameter are used to set the REST API parameters -->
    <div class="row" id="sortable-wrapper" data-api="catalog"
         data-parameter="<?= $data['category_details']['slug'] ?? '' ?>">
    </div>
    <!-- END ITEM CONTENT  -->

    <!-- PAGINATION STATS CONTAINER -->
    <div class="alert alert-<?= $dark ? 'secondary' : 'light' ?>" id="pagination-stats"></div>

    <!-- MODALS -->
    <?php require APP_DIRECTORY . '/views/includes/product-detail-modal.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

    <!-- END PAGE CONTENT -->
</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
</body>
</html>