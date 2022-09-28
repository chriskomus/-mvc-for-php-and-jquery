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

    // Set card title and description.
    $page = 'Categories';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/search-modal.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <form id="main-form" action="categories/update" method="post">
        <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
            <div class="card-header"><?= $page ?></div>
            <div class="card-body">
                <a href="categories/category" class="btn btn-success"><i
                        class="fa-solid fa-plus"></i> Add New Category</a>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary"><i class="fa-solid fa-toolbox"></i> Tools</button>
                    <div class="btn-group" role="group">
                        <button id="tools" type="button" class="btn btn-primary dropdown-toggle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></button>
                        <div class="dropdown-menu" aria-labelledby="tools"
                             style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 40px);"
                             data-popper-placement="bottom-start">
                            <a class="dropdown-item" href="categories/generate_category_slugs">Regenerate Category Slugs</a>
                        </div>
                    </div>
                </div>
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