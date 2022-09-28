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
     * @var $controller
     */

    // Page specific variable requirements
    $page = 'Users';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/search-modal.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <form id="main-form" action="users/update" method="post">
        <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
            <div class="card-header"><?= $page ?></div>
            <div class="card-body">
                <a href="users/user" class="btn btn-success"><i
                        class="fa-solid fa-plus"></i> Add New <?= ucwords(substr($controller, 0, -1)) ?></a>
            </div>
            <div class="card-footer">
                <?php require APP_DIRECTORY . '/views/includes/filter-sorts.php'; ?>
            </div>
        </div>

        <!-- PAGINATION BUTTONS CONTAINER -->
        <div class="" id="pagination-container"></div>

        <!-- MAIN TABLE -->
        <?php require APP_DIRECTORY . '/views/includes/tables.php'; ?>

        <!-- PAGINATION STATS CONTAINER -->
        <div class="alert alert-<?= $dark ? 'secondary' : 'light' ?>" id="pagination-stats"></div>

        <!-- MODALS -->
        <?php require APP_DIRECTORY . '/views/includes/product-detail-modal.php'; ?>
        <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

    </form>
    <!-- END PAGE CONTENT -->
</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
</body>
</html>