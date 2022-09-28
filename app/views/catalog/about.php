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
    $page = 'About Us';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <div class="card bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
        <div class="card-header"><?= $page ?></div>
        <div class="card-body">
            <h4 class="card-title"><?= $settings['about_title'] ?></h4>
            <p class="card-text"><?= $settings['about_body'] ?></p>
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