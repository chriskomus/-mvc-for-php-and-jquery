<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $settings
     */

    // Guest access to catalog view
    define('GUEST_ACCESS' , boolean($settings['guest_catalog_access']));

    // Determine whether user is logged in or not. Determine their user type permissions.
    if ((get_session_value('user_id') && get_session_value('user_type')) ||
        (get_cookie('user_id') && get_cookie('user_type'))) {
        define('LOGGED_IN', true);
        define('USER_TYPE' , get_session_value('user_type'));
        
        // Constants are set for user permission types.
		define('USER', in_array('u', USER_TYPE, true));
		define('ADMIN', in_array('a', USER_TYPE, true));
		define('GUEST', in_array('g', USER_TYPE, true));
    } else {
        define('LOGGED_IN', false);
    }

?>