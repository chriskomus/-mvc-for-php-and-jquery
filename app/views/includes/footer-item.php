<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */



    /**
     * @var $title
     * @var $link
     * @var $dark
     * @var $settings
     */
?>
<?php if (($title !== 'Register') || ($title === 'Register' && boolean($settings['guests_can_register']))): ?>
<li>
    <a href="<?= ROOT_URL . '/' . $link ?>" class="text-<?= $dark ? 'light' : 'dark' ?>"><?= $title ?></a>
</li>
<?php endif; ?>