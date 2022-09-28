<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $controller
     * @var $settings
     * @var $page
     * @var $data
     * @const LOGGED_IN
     */

    $starttime = microtime(true);

    // Authentication
    require APP_DIRECTORY . '/views/includes/auth.php';

    // Determine whether to display the site in dark or light mode.
    if (get_cookie('dark_mode') !== false) {
        $dark_mode = get_cookie('dark_mode');
    } else {
        $dark_mode = $settings['dark_mode'];
    }
    $dark_mode === 'dark' ? $dark = true : $dark = false;

    // Append the default controller to the category links in a new array.
    $category_links = $data['category_links'];
    foreach ($category_links as $i => $link) {
        $category_links[$i][0] = $controller . '/' . $link[0];
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= LOGGED_IN ? $settings['page_title'] : $settings['company_name'] ?> - <?= $page ?></title>
    <base href="<?= ROOT_URL ?>/public">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Quicksand:wght@300;400;500&display=swap"
          rel="stylesheet">
    <link rel="stylesheet"
          href="<?= ROOT_URL ?>/public/css/<?php if ($dark): ?><?= THEMES[DARK_CSS] ?><?php else: ?><?= THEMES[LIGHT_CSS] ?><?php endif; ?>.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>/public/css/main.css" type="text/css">

    <script src="https://cdn.tiny.cloud/1/an8sz4qg359nlbro0uc30thp18qnywdd2wzl70n349z9relc/tinymce/5/tinymce.min.js"
            referrerpolicy="origin"></script>
    <script src="https://kit.fontawesome.com/aaa301db0b.js" crossorigin="anonymous"></script>
    <script>
        tinymce.init({
            selector: '#richtexteditor',
            menubar: false,
            plugins: "link image code",
            toolbar: 'undo redo | styleselect | forecolor | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | link image | removeformat code'
        });
    </script>
</head>