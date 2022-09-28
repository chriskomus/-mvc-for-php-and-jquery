<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $settings
     * @var $dark
     * @var $data
     * @var LOGGED_IN
     * @var $category_links
     */
?>

<header>
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-primary">
        <div class="container d-flex flex-grow-1">
            <a class="navbar-brand" href="<?= ROOT_URL ?>/<?= LOGGED_IN ? 'index' : 'catalog' ?>"><span
                    class="d-none d-xxl-inline"><i
                        class="fa-solid fa-boxes-stacked"></i> </span><?= LOGGED_IN ? $settings['page_title'] : $settings['company_name'] ?>
            </a>
            <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#header-menu" aria-controls="header-menu" aria-expanded="false"
                    aria-label="Toggle Menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse collapse" id="header-menu">
                <ul class="navbar-nav me-auto">
                    <?php if (LOGGED_IN): ?>
                        <?php foreach (MENU_USER as [$link, $title, $icon, $sub_menu]): ?>
                            <?php if (($title === 'Inventory' && USER) ||
                                ($title === 'Admin' && ADMIN) ||
                                ($title === 'Categories')): ?>
                                <?php $title === 'Categories' ? $sub_menu = $category_links : $sub_menu ?>
                                <?php require APP_DIRECTORY . '/views/includes/nav-item.php'; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php elseif (GUEST_ACCESS): ?>
                        <?php foreach (MENU_GUEST as [$link, $title, $icon, $sub_menu]): ?>
                            <?php $title === 'Categories' ? $sub_menu = $category_links : $sub_menu ?>
                            <?php require APP_DIRECTORY . '/views/includes/nav-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ml-auto flex-nowrap">
                    <?php if (LOGGED_IN): ?>
                        <?php foreach (MENU_RIGHT_USER as [$link, $title, $icon, $sub_menu]): ?>
                            <?php require APP_DIRECTORY . '/views/includes/nav-item.php'; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach (MENU_RIGHT_GUEST as [$link, $title, $icon, $sub_menu]): ?>
                            <?php require APP_DIRECTORY . '/views/includes/nav-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div class="d-flex mb-0">
                    <div class="input-group">
                        <input type="text" class="form-control" id="site-search-input"
                               name="site-search-input">
                        <button class="btn btn-secondary" type="button" id="search-button" name="search-button"
                                data-bs-toggle="modal" data-bs-target="#search-popup">Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>

