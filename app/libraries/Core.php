<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * Check the url and explode into an array $url.
     * Look for a Controller that matches the first segment of the url.
     * If it exists, set a new Controller, and then unset the first position of the $url array.
     * Run the new Controller file and then instantiate it's class.
     * Next, check if there is a method for that controller that matches $url[1].
     * Finally, get parameters associated with the remaining segments of the $url array.
     * A default controller and method is set.
     * The MVC url is structured like this: /Controller/Method/Parameter/[parameter/parameter/...]
     * The controller represents a major component of the site, such as Products, and mediates between the model and view.
     * The model represents the business logic of the site. The view represents the presentation.
     */
    class Core
    {
        // If a controller or method is missing from the URL, it will load the defaults.
        // Default Controller is 'Catalog', default method is 'index'.
        protected $currentController = 'Catalog';
        protected $currentMethod = 'index';
        protected $params = [];

        public function __construct()
        {
            $url = $this->getUrl();
            if (isset($url[0])) {
                if (file_exists(APP_DIRECTORY . '/controllers/' . ucwords($url[0]) . '.php')) {
                    $this->currentController = ucwords($url[0]);
                    unset($url[0]);
                }
            }

            // Get Controller
            require_once APP_DIRECTORY . '/controllers/' . $this->currentController . '.php';
            $this->currentController = new $this->currentController;

            // Check if there is a method associated with the 2nd segment of the url
            if (isset($url[1])) {
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];
                    unset($url[1]);
                }
            }

            // Get parameters associated with the remaining segments of the $url array.
            $this->params = $url ? array_values($url) : [];
            try {
                call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
            }
            catch (Error $e) {
                die('Critical Error. The controller and/or parameters failed.');
            }

        }

        /**
         * Get a URL, stripping a final '/' and sanitize the URL.
         * Then explode the url into an array.
         */
        public function getUrl()
        {
            if (isset($_GET['url'])) {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                return explode('/', $url);
            }
        }
    }