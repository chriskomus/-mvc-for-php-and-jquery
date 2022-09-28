<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $sub_menu
     * @var $title
     * @var $link
     * @var $icon
     * @var $dark
     * @var $settings
     */

    count($sub_menu) > 0 ? $submenu = true : $submenu = false;
?>
<?php if (($title !== 'Register' || ($title === 'Register' && boolean($settings['guests_can_register'])))): ?>
    <li class="nav-item<?= $submenu ? ' dropdown' : '' ?>">
        <a class="nav-link<?= $submenu ? ' dropdown-toggle' : '' ?>"
           href="<?= $submenu ? '#' : ROOT_URL . '/' . $link ?>"
            <?= $title === 'Log In' ? ' id="login-modal" data-bs-toggle="modal" data-bs-target="#modal-form-template"' : '' ?>
            <?= $submenu ? ' data-bs-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false"' : '' ?>><span
                class="d-none d-xxl-inline"><i
                    class="fa-solid fa-<?= $icon ?>"></i> </span><?= $title ?></a>
        <?= $submenu ? '<div class="dropdown-menu">' : '' ?>
        <?php foreach ($sub_menu as [$sub_link, $sub_title, $sub_icon]): ?>
            <?php
            $authenticated_menu_item = true;

            $title_as_controller = str_replace(' ', '', $sub_title);
            if (array_key_exists($sub_title, CONTROLLER_PERMISSIONS)) {
                $user_type = CONTROLLER_PERMISSIONS[$title_as_controller];
                $authenticated_menu_item = in_array($user_type, USER_TYPE, true);
            }

            ?>
            <?php if ($sub_link === 'settings/dark_mode'): ?>
                <?php $dark ? $sub_title = 'Light Mode' : $sub_title = 'Dark Mode' ?>
                <?php $dark ? $sub_icon = 'toggle-off' : $sub_icon = 'toggle-on' ?>
            <?php endif; ?>
            <?php if ($sub_link === '-'): ?>
                <div class="dropdown-divider"></div>
            <?php elseif ($authenticated_menu_item): ?>
                <a class="dropdown-item<?= $sub_link === 'settings/dark_mode' ? ' darkmode-toggle' : '' ?>"
                   href="<?= $sub_link === '' ? '#' : ROOT_URL . '/' . $sub_link ?>"><?php if (isset($sub_icon)): ?><i
                        class="fa-solid fa-<?= $sub_icon ?>"></i> <?php endif; ?><?= $sub_title ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
        <?= $submenu ? '</div>' : '' ?>
    </li>
<?php endif; ?>