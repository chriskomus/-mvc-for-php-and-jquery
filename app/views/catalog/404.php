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
    $page = '404';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<main class="container mt-4 main">
    <!-- START PAGE CONTENT -->
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <div class="card bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
        <div class="card-header"><i class="fa-solid fa-file-circle-question"></i> 404 - Not Found</div>
        <div class="card-body">
            <h4 class="card-title"></h4>
            <p class="card-text">The page you are looking for is not here.</p>
            <div class="row">
                <span class="text-center" style="font-size: 20em"><i class="fa-solid fa-face-sad-tear"></i></span>
            </div>

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