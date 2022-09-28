<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $settings
     * @var $data
     * @var $dark
     * @var $page
     * @var $model
     * @var $controller
     * @var $parameter
     */
?>

<table class="table <?= $dark ? 'table-dark' : '' ?> table-striped" data-toggle="table">
    <caption class="d-none"><?= $page ?></caption>
    <thead class="align-middle">
    <tr>
        <?php foreach ($data['columns'] as $column): ?>
            <?php $column_name = ucwords(str_replace("_", " ", $column)) ?>
            <th<?= $column === 'sku' ? ' style="width:8.33%"' : '' ?>>
                <a href="#" class="text-<?= $dark ? 'white' : 'muted' ?> text-decoration-none sort-by"
                   data-sort-by="<?= strtolower($column_name) ?>"
                   data-controller="<?= strtolower($controller) ?>"><?= $column_name ?></a>
            </th>
        <?php endforeach ?>
        <th class="d-none d-lg-table-cell" style="width:10%"><a
                class="nav-link dropdown-toggle text-<?= $dark ? 'white' : 'muted' ?> text-decoration-none"
                data-bs-toggle="dropdown" href="#" role="button"
                aria-haspopup="true" aria-expanded="false"><i
                    class="fa-solid fa-plus-minus"></i></a>
            <div class="dropdown-menu" style="">
                <?php foreach ($data['all_columns'] as $column): ?>
                    <?php $column_name = ucwords(str_replace("_", " ", $column)) ?>
                    <a class="dropdown-item<?= in_array($column, $data['columns'], true) ? ' text-success' : '' ?>"
                       href="<?= $controller ?>/add_remove_columns/<?= $column ?>"><?= $column_name ?></a>
                <?php endforeach ?>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= $controller ?>/reset_columns/">Reset Columns</a>
            </div>
        </th>
    </tr>
    </thead>

    <!-- START ITEM CONTENT  -->
    <tbody id="sortable-wrapper" data-api="<?= strtolower($controller) ?>"
           data-parameter="<?= $parameter ?? '' ?>">
    </tbody>
    <!-- END ITEM CONTENT  -->

</table>