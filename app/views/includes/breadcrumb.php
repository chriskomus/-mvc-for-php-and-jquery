<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $data
     * @var $page
     * @var $row
     * @var $model
     * @var $controller
     * @var $parameter
     * @var $parameter_title
     */
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $controller ?>/"><?= ucwords($controller) ?></a></li>
        <?php if (isset($row['category'])): ?>
            <li class="breadcrumb-item"><a href="<?= $controller ?>/<?= $row['category_slug'] ?>"><?= ucwords($row['category']) ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page"><?= $parameter_title ?? $page ?></li>
    </ol>
</nav>