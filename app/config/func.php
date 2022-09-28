<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

use Gumlet\ImageResize;
    use Gumlet\ImageResizeException;

    /**
     * Send $output to the browser console using JavaScript. Used for testing purposes.
     * Objects are converted to an array, arrays are imploded to a string.
     * @param $data
     */
    function console_log($output)
    {
        $console = $output;
        if (is_object($console)) {
            $console = get_object_vars($console);
        }

        if (is_array($console)) {
            $console = implode(',', $console);
        }

        echo "<script>console.log('" . $console . "');</script>";
    }

    /**
     * Return the current page.
     * @return string
     */
    function get_current_page(): string
    {
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443) ? "https://" : "http://";
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * This function provides a way of only setting cookie values that are valid.
     * Check the provided value of a cookie if it is exists in the array. Set a valid cookie value for the provided key.
     * If no cookie has been set and a default value has been provided, set a cookie with the default value.
     * If neither condition is satisfied, it will attempt to get a cookie. It will return false if that cookie doesn't exist.
     * @param $key
     * @param $validation_array
     * @param $default_value
     * @param null $provided_value
     * @return bool|mixed
     */
    function valid_cookie($key, $validation_array, $default_value = null, $provided_value = null): mixed
    {
        if (isset($provided_value) && in_array($provided_value, $validation_array, true)) {
            set_cookie($key, $provided_value, DEFAULT_COOKIE_EXPIRATION);
        } else if (isset($default_value) && in_array($default_value, $validation_array, true) && get_cookie($key) === false) {
            set_cookie($key, $default_value, DEFAULT_COOKIE_EXPIRATION);
        }

        return get_cookie($key);
    }

    /**
     * Check if a key exists in the array of cookies, and provide the value of the key.
     * @param $key - the cookie key
     * @return bool|mixed The value of a given key, or false if the key doesn't exist.
     */
    function get_cookie($key): mixed
    {
        return array_key_exists($key, $_COOKIE) ? $_COOKIE[$key] : false;
    }

    /**
     * Add a new cookie. If a cookie of an existing key is provided, it will be overwritten.
     * @param $key - the cookie key
     * @param $value - the cookie value to set
     * @param int $expire_in_days - number of days until the cookie expires
     */
    function set_cookie($key, $value, int $expire_in_days = 0)
    {
        $value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (is_numeric($expire_in_days)) {
            $expires = time() + ((int)$expire_in_days * 86400); // 86400 = 1 day
        } else {
            $expires = 0;
        }

        setcookie($key, $value, $expires, '/');
        $_COOKIE[$key] = $value;
    }

    /**
     * Delete a cookie.
     * @param $key - the cookie key
     */
    function delete_cookie($key)
    {
        setcookie($key, '', time() - 1000);
        setcookie($key, '', time() - 1000, '/');
    }

    /**
     * Delete all cookies.
     */
    function delete_all_cookies()
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - 1000);
                setcookie($name, '', time() - 1000, '/');
            }
        }
    }

    /**
     * Return a session value from a provided key. Start a session if it isn't already started.
     * @param $key - the session key
     * @return mixed|null The session value, or null if it doesn't exist.
     */
    function get_session_value($key): mixed
	{
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        return $_SESSION[$key] ?? null;
    }

    /**
     * Start a session if one hasn't been started yet. Then set a session value.
     * @param $key
     * @param $value
     */
    function set_session_value($key, $value)
    {
        session_start();
        $_SESSION[$key] = $value;
    }

    /**
     * Remove all session data.
     */
    function clear_sessions()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }

    /**
     * Check if the parameter 'desc' has been passed, otherwise set to 'asc'.
     * @param $asc_or_desc
     * @return string
     */
    function ascending_or_descending_sort($asc_or_desc): string
    {
        if (!is_null($asc_or_desc) && strtolower($asc_or_desc) === 'desc') {
            return "DESC";
        }
        return "ASC";
    }

    /**
     * Returns a sanitized slug url safe link. First it replaces any characters found in an array of key value pairs of characters.
     * Then it is set to lowercase. Convert non-alphanumeric characters to a dash. Limit length (to 255 by default).
     * @param $link - un-sanitized link
     * @param int $length - max length of slug
     * @return string|array|null Sanitized link with dashes instead of spaces, and lowercase
     */
    function generate_slug($link, int $length = 255): string|array|null
    {
        $character_replacements = [
            '<' => '', '>' => '', '-' => ' ', '&' => '', '"' => '', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'Ae', 'Ä' => 'A', 'Å' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Ă' => 'A', 'Æ' => 'Ae', 'Ç' => 'C', "'" => '', 'Ć' => 'C', 'Č' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C', 'Ď' => 'D', 'Đ' => 'D', 'Ð' => 'D', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ě' => 'E', 'Ĕ' => 'E', 'Ė' => 'E', 'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G', 'Ĥ' => 'H', 'Ħ' => 'H', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ī' => 'I', 'Ĩ' => 'I', 'Ĭ' => 'I', 'Į' => 'I', 'İ' => 'I', 'Ĳ' => 'IJ', 'Ĵ' => 'J', 'Ķ' => 'K', 'Ł' => 'L', 'Ľ' => 'L', 'Ĺ' => 'L', 'Ļ' => 'L', 'Ŀ' => 'L', 'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'Oe', 'Ö' => 'Oe', 'Ø' => 'O', 'Ō' => 'O', 'Ő' => 'O', 'Ŏ' => 'O', 'Œ' => 'OE', 'Ŕ' => 'R', 'Ř' => 'R', 'Ŗ' => 'R', 'Ś' => 'S', 'Š' => 'S', 'Ş' => 'S', 'Ŝ' => 'S', 'Ș' => 'S', 'Ť' => 'T', 'Ţ' => 'T', 'Ŧ' => 'T', 'Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'Ue', 'Ū' => 'U', 'Ü' => 'Ue', 'Ů' => 'U', 'Ű' => 'U', 'Ŭ' => 'U', 'Ũ' => 'U', 'Ų' => 'U', 'Ŵ' => 'W', 'Ý' => 'Y', 'Ŷ' => 'Y', 'Ÿ' => 'Y', 'Ź' => 'Z', 'Ž' => 'Z', 'Ż' => 'Z', 'Þ' => 'T', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'ae', 'ä' => 'ae', 'å' => 'a', 'ā' => 'a', 'ą' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'ć' => 'c', 'č' => 'c', 'ĉ' => 'c', 'ċ' => 'c', 'ď' => 'd', 'đ' => 'd', 'ð' => 'd', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ē' => 'e', 'ę' => 'e', 'ě' => 'e', 'ĕ' => 'e', 'ė' => 'e', 'ƒ' => 'f', 'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g', 'ĥ' => 'h', 'ħ' => 'h', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ī' => 'i', 'ĩ' => 'i', 'ĭ' => 'i', 'į' => 'i', 'ı' => 'i', 'ĳ' => 'ij', 'ĵ' => 'j', 'ķ' => 'k', 'ĸ' => 'k', 'ł' => 'l', 'ľ' => 'l', 'ĺ' => 'l', 'ļ' => 'l', 'ŀ' => 'l', 'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŉ' => 'n', 'ŋ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'oe', 'ö' => 'oe', 'ø' => 'o', 'ō' => 'o', 'ő' => 'o', 'ŏ' => 'o', 'œ' => 'oe', 'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r', 'š' => 's', 'ś' => 's', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'ue', 'ū' => 'u', 'ü' => 'ue', 'ů' => 'u', 'ű' => 'u', 'ŭ' => 'u', 'ũ' => 'u', 'ų' => 'u', 'ŵ' => 'w', 'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y', 'ž' => 'z', 'ż' => 'z', 'ź' => 'z', 'þ' => 't', 'α' => 'a', 'ß' => 'ss', 'ẞ' => 'b', 'ſ' => 'ss', 'ый' => 'iy', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'YO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'YU', 'Я' => 'YA', 'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '.' => '-', '€' => '-eur-', '$' => '-usd-'
        ];
        // Replace any characters found in $character_replacements and set to lowercase.
        $link = strtolower(strtr($link, $character_replacements));

        // Convert non-alphanumeric characters to a dash.
        $link = preg_replace('~[^-\w.]+~', '-', preg_replace('~[^\pL\d.]+~u', '-', $link));

        // Limit length
        $link = rtrim(substr($link, 0, $length), '-');

        // Return after trimming off extra and duplicate dashes.
        return preg_replace('~-+~', '-', trim($link, '-'));
    }

    /**
     * Convert an array to integers
     * @param $array
     * @return array
     */
    function validate_ints($array): array
    {
        $integers = [];

        foreach ($array as $item) {
            $integers[] = (int)$item;
        }

        return $integers;
    }

    /**
     * Loop through all Posted data and return a key value array with sanitized values.
     */
    function sanitize_post(): bool|array|null
    {
        if ($_POST) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            return $_POST;
        } else {
            return null;
        }
    }

    /**
     * Check a string against a regex match, based on a provided key. Will return null if mismatched or an int if matched.
     * @param $key - The type of validation required (ie: email, phone)
     * @param $value - The string that will be evaluated
     */
    function regex_match($key, $value): bool|int
    {
        $regex_patterns = array(
            'password' => '/^[\d\w@-]{8,32}$/i',
            'email' => '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/',
            'phone' => '/(\+?( |-|\.)?\d{1,2}( |-|\.)?)?(\(?\d{3}\)?|\d{3})( |-|\.)?(\d{3}( |-|\.)?\d{4})/',
            'postalCode' => '/^[ABCEGHJ-NPRSTVXY]\d[ABCEGHJ-NPRSTV-Z][ -]?\d[ABCEGHJ-NPRSTV-Z]\d$/i',
            'zipCode' => '/^[0-9]{5}(?:-[0-9]{4})?$/'
        );

        return preg_match($regex_patterns[$key], $value);
    }

    /**
     * Returns the file upload path for the image.
     * @param string $filename
     * @param string $subfolder
     * @return string
     */
    function file_upload_path(string $filename = '', string $subfolder = ''): string
    {
        $path_segments = [ROOT_DIRECTORY . '/public/images', $subfolder, basename($filename)];
        return implode(DIRECTORY_SEPARATOR, $path_segments);
    }

    /**
     * Determine whether an uploaded file an allowed mime type or file extension.
     * Allowed mime type: ['image/gif', 'image/jpeg', 'image/png']
     * Allowed file extensions: ['gif', 'jpg', 'jpeg', 'png']
     * @param $temporary_path
     * @param $new_path
     * @return bool
     */
    function file_is_an_image($temporary_path, $new_path): bool
    {
        $image_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
        $image_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type = mime_content_type($temporary_path);

        $file_extension_is_valid = in_array($actual_file_extension, $image_file_extensions, true);
        $mime_type_is_valid = in_array($actual_mime_type, $image_mime_types, true);

        return $file_extension_is_valid && $mime_type_is_valid;
    }

    /**
     * Resize an image
     * @param $width
     * @param $filename_suffix
     * @param $image_filename
     * @param $new_image_path
     * @return void
     * @throws ImageResizeException
     */
    function image_resize($width, $filename_suffix, $image_filename, $new_image_path)
    {
        $image = new ImageResize($new_image_path);
        $image->resizeToWidth($width);

        $extension_position = strrpos($image_filename, '.');
        $new_filename = substr($image_filename, 0, $extension_position) . $filename_suffix . substr($image_filename, $extension_position);

        $image->save(file_upload_path(filename: $new_filename, subfolder: 'products'));
    }

    /**
     * Convert a variable to boolean
     * @param $value
     * @return mixed
     */
    function boolean($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * De-pluralize a word.
     * Not foolproof, and controllers/models rely on this function for the site to run correctly.
     * Ensure that all controllers/models follow a strict consistent naming convention to avoid problems.
     * ie: Controllers are plural (products, categories), models are singular (product, category).
     * @param string $plural
     * @param null $singular
     * @return string
     */
    function depluralize(string $plural, $singular = null)
    {

        if (!$singular) {
            if (str_ends_with($plural, 'ies')) {
                $singular = substr($plural, 0, -3) . 'y';
            } elseif (str_ends_with($plural, 'es')) {
                $singular = substr($plural, 0, -2);
            } else {
                $singular = substr($plural, 0, -1);
            }
        }

        return $singular;
    }

    /**
     * Pluralizes a word.
     * Not foolproof, and controllers/models rely on this function for the site to run correctly.
     * Ensure that all controllers/models follow a strict consistent naming convention to avoid problems.
     * ie: Controllers are plural (products, categories), models are singular (product, category).
     * @param string $plural
     * @param null $singular
     * @return string
     */
    function pluralize(string $singular, $plural = null)
    {

        if (!$plural) {
            if (str_ends_with($singular, 'y')) {
                $plural = substr($singular, 0, -1) . 'ies';
            } elseif (str_ends_with($singular, 's')) {
                $plural = $singular . 'es';
            } else {
                $plural = $singular . 's';
            }
        }

        return $plural;
    }

    /**
     * Return the user type that is required to access a specified controller
     * @param $controller
     * @return string
     */
    function get_usertype_requirement($controller): string
    {
        $controller_permissions = array_change_key_case(CONTROLLER_PERMISSIONS, CASE_LOWER);
        return $controller_permissions[$controller];
    }


