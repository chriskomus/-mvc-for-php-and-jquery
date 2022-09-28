<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $settings
     * @var $controller
     * @var $dark
     * @var $starttime
     * @const LOGGED_IN
     */
?>
<footer class="bg-<?= $dark ? 'dark text-white' : 'light' ?> text-center text-lg-start">
    <div class="container p-4">
        <div class="row">

            <?php if (!LOGGED_IN && GUEST_ACCESS): ?>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-uppercase">Contact Us</h5>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <i class="fa-solid fa-store"></i> <?= $settings['company_address1'] ?>
                        </li>
                        <?php if ($settings['company_address2']): ?>
                            <li>
                                <?= $settings['company_address2'] ?>
                            </li>
                        <?php endif; ?>
                        <li>
                            <?= $settings['company_city'] ?>, <?= $settings['company_prov'] ?>
                        </li>
                        <li>
                            <?= $settings['company_postalcode'] ?>
                        </li>
                        <?php if ($settings['company_phone']): ?>
                            <li>
                                <i class="fa-solid fa-phone"></i> <?= $settings['company_phone'] ?>
                            </li>
                        <?php endif; ?>
                        <?php if ($settings['company_email']): ?>
                            <li>
                                <i class="fa-solid fa-envelope"></i> <a
                                    href="mailto:<?= $settings['company_email'] ?>"><?= $settings['company_email'] ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <?php if (LOGGED_IN || GUEST_ACCESS): ?>
                    <h5 class="text-uppercase">Quick Links</h5>
                <?php endif; ?>
                <ul class="list-unstyled mb-0">
                    <?php if (LOGGED_IN): ?>
                        <?php foreach (FOOTER_USER as [$link, $title]): ?>
                            <?php require APP_DIRECTORY . '/views/includes/footer-item.php'; ?>
                        <?php endforeach; ?>
                    <?php elseif (GUEST_ACCESS): ?>
                        <?php foreach (FOOTER_GUEST as [$link, $title]): ?>
                            <?php require APP_DIRECTORY . '/views/includes/footer-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.2);">
        <p><?= $settings['copyright'] ?></p>
        <small>Page loaded in: <span id="page-load-time" data-start-time="<?= $starttime ?>"></span> seconds.</small>
    </div>
</footer>
