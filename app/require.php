<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    // Require necessary libraries from the app/libraries
    // Core - takes the url and converts it into controller/method/parameter(s)
    // Controller - Base class of all controllers that loads the model and view
    // Database - Connects to the database
    require_once 'libraries/Core.php';
    require_once 'libraries/Controller.php';
    require_once 'libraries/Database.php';

    // config - contains database connection information and application and root directory
    require_once 'config/config.php';

    // func - assorted functions used by the application
    require_once 'config/func.php';

    // composer
    require_once(ROOT_DIRECTORY . '/vendor/autoload.php');

    // Instantiate the Core class - it all starts here!
    $init = new Core();