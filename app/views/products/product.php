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
     * @var $model
     */

    // Page specific variable requirements
    $row = $data['product'];

    if (isset($row['product_id'])) {
        $action = 'edit';
        $product_id = $row['product_id'];
        $image_src = ROOT_URL . '/public/images/products/' . $row['image'];
    } else {
        $action = 'create';
        $product_id = false;
        $image_src = null;
    }

    $page = $product_id ? $row['title'] : 'Product';

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/search-modal.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>

    <!-- START PAGE CONTENT -->
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>
    <form id="main-form" action="products/<?= $product_id ? 'update' : 'create' ?>" method="post" novalidate
          class="needs-validation">
        <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
            <div class="card-header">
                <?= ucwords($action) ?> <?= $page ?>
            </div>
            <div class="card-body">
                <?php if ($product_id): ?><input type="hidden" name="id" id="id"
                                                 value="<?= $row['product_id'] ?>"><?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="col-form-label" for="title">Title:</label>
                                    <input type="text" class="form-control validate-me"
                                           value="<?= $product_id ? $row['title'] : '' ?>" id="title" name="title"
                                           required>
                                    <div class="invalid-feedback">A product title is required.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="sku">SKU:</label>
                                    <input type="text" class="form-control validate-me"
                                           value="<?= $product_id ? $row['sku'] : '' ?>" id="sku" name="sku"
                                           required>
                                    <div class="invalid-feedback">A unique sku is required.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="category_id">Category:</label>
                                    <select class="form-select validate-me" id="category_id" name="category_id"
                                            required>
                                        <option value="">Choose Category
                                        </option><?php foreach ($data['categories'] as $category): ?>
                                            <option label="<?= $category['title'] ?>"
                                                    value="<?= $category['category_id'] ?>"<?php if ($product_id && $category['category_id'] === $row['category_id']): ?> selected<?php endif; ?>><?= $category['title'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <div class="invalid-feedback">A category is required.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="type">Type:</label>
                                    <select class="form-select validate-me" id="type" name="type">
                                        <?php foreach ($data['product_types'] as $product_type): ?>
                                            <option value="<?= $product_type ?>"
                                                    <?php if ($product_id && $product_type === $row['type']): ?>selected<?php endif; ?>><?= $product_type ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <div class="invalid-feedback">A product type is required.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="bin">BIN:</label>
                                    <input type="text" class="form-control"
                                           value="<?= $product_id ? $row['bin'] : '' ?>" id="bin" name="bin">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="col-form-label" for="sale_price">Sale Price:</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control validate-me"
                                               value="<?= $product_id ? number_format($row['sale_price'], $settings['price_decimals']) : '' ?>"
                                               id="sale_price" name="sale_price" data-validate-me="float">
                                        <div class="invalid-feedback">Enter a valid numeric price value or leave the field blank.</div>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="purchase_price">Purchase Price:</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control validate-me"
                                               value="<?= $product_id ? number_format($row['purchase_price'], $settings['price_decimals']) : '' ?>"
                                               id="purchase_price" name="purchase_price" data-validate-me="float">
                                        <div class="invalid-feedback">Enter a valid numeric price value or leave the field blank.</div>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="quantity">Quantity:</label>
                                    <input type="text" class="form-control validate-me"
                                           value="<?= $product_id ? $row['quantity'] : '' ?>" id="quantity"
                                           name="quantity" data-validate-me="integer">
                                    <div class="invalid-feedback">Enter a valid whole number or leave the field blank.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label" for="reorder">Reorder Point:</label>
                                    <input type="text" class="form-control validate-me"
                                           value="<?= $product_id ? $row['reorder'] : '' ?>" id="reorder"
                                           name="reorder" data-validate-me="integer">
                                    <div class="invalid-feedback">Enter a valid whole number or leave the field blank.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <?php if ($product_id): ?>
                                <?php if ($row['image']): ?>
                                    <?php if (@getimagesize($image_src)): ?>
                                        <img src="<?= $image_src ?>"
                                             class="card-img-top" alt="<?= $row['title'] ?>">
                                    <?php else: ?>
                                        <div class="alert alert-dismissible alert-warning">
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            <p class="mb-0">Image file is missing.</p>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="card-body d-flex d-grid gap-2 d-md-flex justify-content-center">
                                    <?php if ($row['image']): ?>
                                        <button type="button" class="btn btn-danger image-delete-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-template" data-id="<?= $product_id ?>">
                                            Delete Image
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-secondary image-upload-button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-form-template" data-id="<?= $product_id ?>">
                                        <?= $row['image'] ? 'Replace' : 'Upload' ?> Image
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="card-body">
                                    To upload a picture, first create the product.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-1">
                    <label class="col-form-label" for="description">Description:</label>
                    <input type="text" class="form-control" value="<?= $product_id ? $row['description'] : '' ?>"
                           id="description" name="description">
                </div>
                <div class="form-group mb-1">
                    <label class="col-form-label" for="richtexteditor">Detailed Description:</label>
                    <textarea class="form-control" id="richtexteditor" name="detailed_description"
                              rows="10"><?= $product_id ? $row['detailed_description'] : '' ?></textarea>
                </div>
                <?php if ($product_id): ?>
                    <div class="form-group mb-1">
                        <label class="col-form-label" for="slug">Slug (Leave blank to auto-generate a new slug):</label>
                        <input type="text" class="form-control" value="<?= $row['slug'] ?>"
                               id="slug" name="slug">
                    </div>
                <?php endif; ?>
                <div class="form-group mb-3">
                    <label class="col-form-label" for="type">Notes:</label>
                    <textarea class="form-control" name="notes"
                              rows="5"><?= $product_id ? $row['notes'] : '' ?></textarea>
                </div>
                <fieldset>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enabled"
                            <?= (($product_id && $row['enabled']) || !$product_id) ? 'checked=""' : '' ?>
                               name="enabled">
                        <div class="custom-tooltip">
                            <label class="form-check-label" for="enabled">Enabled</label>
                            <span class="custom-tooltip-text">
                                    Disabling a product will archive it but not delete it. Archived products will not be displayed in the guest view of the catalog.
                                </span>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <?php if ($product_id && $row['quantity'] === 0): ?>
                    <button type="button" class="btn btn-danger delete-button" data-bs-toggle="modal"
                            data-bs-target="#modal-template" data-id="<?= $product_id ?>">Delete
                        </button><?php endif; ?>
                    <button type="submit" class="btn btn-primary" name="submit"
                            id="submit"><?= $product_id ? 'Save' : 'Create' ?> <?= ucwords($model) ?>
                    </button>
                </div>
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