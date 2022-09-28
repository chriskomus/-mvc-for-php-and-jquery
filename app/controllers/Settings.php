<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents the Settings controller.
     */
    class Settings extends Controller
    {
        /**
         * @throws JsonException
         */
        public function __construct()
        {
            // Required for providing settings, users info, and category lists (for menu items)
            parent::__construct();

            $this->authorizationRequired();
        }

        /**
         * Default view for the controller.
         * @return void
         */
        public function index(): void
        {
            $this->view('settings/index', $this->data);
        }

        /**
         * Change dark mode for user and get or set cookie.
         * @return void
         */
        public function dark_mode(): void
        {
            if (get_cookie('dark_mode') !== false) {
                $setting = get_cookie('dark_mode');
            } else {
                $dark_mode = $this->settingModel->getBySetting('dark_mode');
                $setting = $dark_mode['value'];
            }

            if ($setting === 'dark') {
                $value = 'light';
            } else {
                $value = 'dark';
            }

            set_cookie('dark_mode', $value, DEFAULT_COOKIE_EXPIRATION);

            $this->index();
        }

        /**
         * Clear all cookies and session data. This will also log the admin user out.
         * @return void
         */
        public function clear_cookies_and_session()
        {
            delete_all_cookies();
            clear_sessions();

            header("Location: " . ROOT_URL . "/catalog");
        }
    }