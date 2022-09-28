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
    $page = 'Log In';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->
    <form id="login-form" action="users/login" method="post" novalidate class="needs-validation">
        <form>
            <div class="row d-flex">
                <div class="col-md-4 mx-auto">
                    <div class="card justify-content-center bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
                        <div class="card-header"><?= $page ?></div>
                        <div class="card-body">

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control validate-me" id="email" name="email"
                                       data-validate-me="email" required>
                                <div class="invalid-feedback">Enter a valid email address.</div>
                                <div class="valid-feedback">Looks good!</div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control validate-me" id="password" name="password"
                                       data-validate-me="password" required>
                                <div class="invalid-feedback">Enter a password. It must be alphanumeric and between 8 and 32 characters.</div>
                                <div class="valid-feedback">Your password is secure</div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" name="login" id="login-main" class="btn btn-primary">Log In
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- END PAGE CONTENT -->

        <!-- MODALS -->
        <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
</body>
</html>