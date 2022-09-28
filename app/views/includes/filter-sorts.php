<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $data
     * @var $category
     * @var $controller
     * @var $model
     */
    // This determines which columns are searchable in the client side page search filter.
    // An array is passed as a data attribute string to javascript, to be converted back to an array.
    if ($controller === 'catalog') {
        $page_search_columns = 'title,category';
    } else {
        $exclusions = ['sale_price', 'purchase_price', 'quantity', 'reorder'];
        $page_search_columns = implode(",", array_diff($data['columns'], $exclusions));
    }
?>
<div class="row">

    <ul class="nav nav-pills justify-content-end" style="font-size: 0.9em;">
        <li class="me-4 mt-2 mb-2" id="header-results"></li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true"
               aria-expanded="false">Results Per Page</a>
            <div class="dropdown-menu" id="pagination-dropdown-menu" data-default="<?= (int)DEFAULT_PAGINATION ?>">
                <?php foreach (validate_ints(PAGINATION_OPTIONS) as $count): ?>
                    <a class="dropdown-item pagination-count<?= (int)$data['pagination'] === $count ? ' active' : '' ?>"
                       href="#" data-count="<?= $count ?>"><?= $count ?></a>
                <?php endforeach ?>
            </div>
        </li>
        <li>
            <span class="col-sm-4">
                <input class="form-control input-sm" type="text" name="page-search-input" id="page-search-input"
                       onClick="this.select()" placeholder="Search..." data-columns="<?= $page_search_columns ?>">
        </span>
        </li>
        <?php if ($controller === 'products' || $controller === 'orders' || $controller === 'purchasing'): ?>
            <li>
                <div class="custom-tooltip no-underline">
                    <button type="button"
                            class="btn btn-secondary text-<?= $data['show_archived'] === 'show' ? 'success' : 'muted' ?>"
                            id="show-archived" data-show-archived="<?= $data['show_archived'] ?>">
                        <i class="fa-solid fa-box-archive"></i>
                    </button>
                    <span class="custom-tooltip-text">Toggle Archived <?= ucwords($controller) ?></span></div>

            </li>
        <?php endif; ?>
        <?php if ($controller === 'products' || $controller === 'catalog'): ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
               aria-expanded="false">Filter Category</a>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item<?= isset($data['category_details']) ? '' : ' active' ?><?= isset($data['category_details']) ? '' : ' filter-by' ?>"
                       href="<?= $controller ?>/<?= isset($data['category_details']) ? '' : '#' ?>"
                       data-filter-by="Show All">Show All</a>
                </li>
                <?php foreach ($data['categories'] as $category): ?>
                    <li>
                        <a class="dropdown-item<?= isset($data['category_details']) ? '' : ' filter-by' ?><?= isset($data['category_details']) && ($category['title'] === $data['category_details']['title']) ? ' active' : '' ?>"
                           href="<?= $controller ?>/<?= isset($data['category_details']) ? $category['slug'] : '#' ?>"
                           data-filter-by="<?= $category['title'] ?>"><?= $category['title'] ?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        </li>
        <?php endif; ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="main-dropdown-toggle" data-bs-toggle="dropdown"
               data-controller="<?= strtolower($controller) ?>" data-model="<?= strtolower($model) ?>" href="#" role="button"
               aria-expanded="false">Sort</a>
            <ul class="dropdown-menu">
                <?php foreach ($data['columns'] as $sort): ?>
                    <li><a class="dropdown-item sort-by<?= $data['sort_by'] === $sort ? ' active' : '' ?>"
                           href="#"
                           data-sort-by="<?= $sort ?>"
                           data-controller="<?= strtolower($controller) ?>"><?= ucwords(str_replace("_", " ", $sort)) ?></a>
                    </li>
                <?php endforeach ?>
            </ul>
        </li>
        <li>
            <button type="button" class="btn btn-secondary" id="sort-asc-desc"
                    data-asc-desc="<?= $data['sort_by_order'] ?>">
                <i class="fa-solid fa-arrow-down-<?= $data['sort_by_order'] === 'desc' ? 'z-a' : 'a-z' ?>"></i>
            </button>
        </li>
    </ul>
</div>




