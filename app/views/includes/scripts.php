<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $settings
     * @var $controller
     * @var $starttime
     */
?>
<?php
    // Pass PHP variables to be used by Javascript.
?>
    <input type="hidden" id="site-info"
           data-root-url="<?= ROOT_URL ?>"
           data-dark-css="<?= ROOT_URL ?>/public/css/<?= THEMES[DARK_CSS] ?>.css"
           data-light-css="<?= ROOT_URL ?>/public/css/<?= THEMES[LIGHT_CSS] ?>.css"
           data-dark-mode="<?= $settings['dark_mode'] ?>"
           data-currency="<?= $settings['currency'] ?>"
           data-price-decimals="<?= $settings['price_decimals'] ?>">

<?php
    // Bootstrap and jQuery
?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php
    // This contains controller specific functionality. Each controller needs its own js file for generating
    // controller specific data.
?>
    <script src="<?= ROOT_URL ?>/public/javascript/controllers/<?= $controller ?>.js"></script>

<?php
    // Make API calls. This must happen after the controller js is loaded in case it has additional API call requests
?>
    <script src="<?= ROOT_URL ?>/public/javascript/api.js"></script>

<?php
    // These contain site wide functionality
?>
    <script src="<?= ROOT_URL ?>/public/javascript/func.js"></script>
    <script src="<?= ROOT_URL ?>/public/javascript/filter-sorts.js"></script>
    <script src="<?= ROOT_URL ?>/public/javascript/pagination.js"></script>
    <script src="<?= ROOT_URL ?>/public/javascript/modals.js"></script>
    <script src="<?= ROOT_URL ?>/public/javascript/load-last.js"></script>

    <script src="<?= ROOT_URL ?>/public/javascript/main.js"></script>