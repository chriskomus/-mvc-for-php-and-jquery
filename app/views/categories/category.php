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
    $row = $data['category'];

    if (isset($row['category_id'])) {
        $action = 'edit';
        $category_id = $row['category_id'];
    } else {
        $action = 'create';
        $category_id = false;
    }

    $page = $category_id ? $row['title'] : 'Category';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/search-modal.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>

    <!-- START PAGE CONTENT -->
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>
    <form id="main-form" action="categories/<?= $category_id ? 'update' : 'create' ?>" method="post" novalidate
          class="needs-validation">
        <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
            <div
                class="card-header"><?php if (!isset($error)): ?> <?= ucwords($action) ?> <?= $category_id ? $row['title'] : $page ?><?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!isset($error)): ?>

                    <?php if ($category_id): ?><input type="hidden" name="id" id="id"
                                                     value="<?= $row['category_id'] ?>"><?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Category Information</h5>
                                    <div class="form-group">
                                        <label class="col-form-label" for="title">Title:</label>
                                        <input type="text" class="form-control validate-me"
                                               value="<?= $category_id ? $row['title'] : '' ?>" id="title" name="title"
                                               required>
                                        <div class="invalid-feedback">A category title is required.</div>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-1">
                        <label class="col-form-label" for="richtexteditor">Description:</label>
                        <textarea class="form-control" id="richtexteditor" name="description"
                                  rows="10"><?= $category_id ? $row['description'] : '' ?></textarea>
                    </div>
                    <?php if ($category_id): ?>
                        <div class="form-group mb-1">
                            <label class="col-form-label" for="slug">Slug:</label>
                            <input type="text" class="form-control" value="<?= $row['slug'] ?>"
                                   id="slug" name="slug" required>
                            <div class="invalid-feedback">A slug is required. This will be the SEO link to the
                                category.
                            </div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <?php if ($category_id && $row['product_count'] === 0): ?>
                        <button type="button" class="btn btn-danger delete-button" data-bs-toggle="modal"
                                data-bs-target="#modal-template" data-id="<?= $category_id ?>">Delete
                            </button>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary" name="submit"
                                id="submit"><?= $category_id ? 'Save' : 'Create' ?> <?= ucwords($model) ?>
                        </button>
                    </div>
                <?php else: ?>
                    <h2>An error occurred while processing your request.</h2>
                    <p>Ensure that all required fields have been filled out.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- MODALS -->
        <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

    </form>

    <!-- END PAGE CONTENT -->
</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
<script src="<?= ROOT_URL ?>/public/javascript/controllers/catalog.js"></script>
</body>
</html>