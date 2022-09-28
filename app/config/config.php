<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


// ------------------------------------------ SITE SETTINGS ---------------------------------------------------------

// Database settings
	const DB_NAME = 'mvc-for-php-and-jquery';
	const DB_HOST = 'localhost';
	const DB_USER = '';
	const DB_PASS = '';
	const DB_CHARSET = 'utf8';

// Pagination Counts
	const PAGINATION_OPTIONS = [5, 10, 25, 50, 100];
	const DEFAULT_PAGINATION = 25; // provide a single int for default items per page

// Default New User Type (See USER_TYPES below to set user types)
// Recommended to leave as 'g' and allow admin users to elevate permissions after account creation.
	const DEFAULT_USER_TYPE = 'g';

// Detailed error mode (will show exception error messages and detailed error message on database interactions)
	const DETAILED_ERROR_MSG = true;

// Cookie expirations
	const DEFAULT_COOKIE_EXPIRATION = 14;
	const STAY_LOGGED_IN = 5;

// Bootstrap 5 Themes
// Place bootstrap css files in /public/css folder and add to THEMES array.
// DARK_CSS and LIGHT_CSS are set to the corresponding index of THEMES.
	const THEMES = array('bootswatch-a', 'bootswatch-b', 'bootswatch-c', 'bootswatch-d', 'bootswatch-e', 'bootswatch-f');
	const DARK_CSS = 3;
	const LIGHT_CSS = 1;

// ------------------------------------------ MENU STRUCTURE ---------------------------------------------------------

// Nav Menu Items - [link, title, font awesome icon suffix, [sub menu]]
	const MENU_USER = [
		['#', 'Inventory', '', [
			['products', 'Products', 'box-open'],
			['categories', 'Categories', 'boxes-stacked'],
		]],
		['#', 'Admin', '', [
			['users/users', 'Users', 'users'],
			['-', '', ''],
			['settings/dark_mode', 'Dark Mode', 'toggle-on'],
			['-', '', ''],
			['users/logout', 'Log Out', 'arrow-right-from-bracket']
		]]
	];
	
	const MENU_GUEST = [
		['#', 'Categories', 'layer-group', []],
		['catalog/about', 'About Us', 'shop', []]
	];
	
	const MENU_RIGHT_GUEST = [
		['users/register', 'Register', 'user', []],
		['users/login', 'Log In', 'right-to-bracket', []]
	];
	
	const MENU_RIGHT_USER = [
		['users/register', 'Account', 'user', []],
		['users/logout', 'Log Out', 'right-from-bracket', []]
	];

// Footer Menu Items - (link, title)
	const FOOTER_USER = [
		['users/register', 'Account'],
		['catalog/about', 'About Us'],
		['users/logout', 'Log Out']
	];
	
	const FOOTER_GUEST = [
		['catalog', 'See our Products!'],
		['users/register', 'Register'],
		['catalog/about', 'About Us'],
		['users/login', 'Log In']
	];

// ------------------------------------------ USER TYPE PERMISSIONS --------------------------------------------
// Control access to controllers by assigning them a user type.
// Only users with that type will be able to access the controller and see the menu item.
// Menu items that are not controllers will be visible regardless.
// Note that the Users will always be accessible only by Admin. It cannot be overridden here.
// Note that the Catalog will be accessible by everyone, regardless of user type, if setting 'guest_catalog_access' is True.
	
	const USER_TYPES = [
		'a' => 'Admin',
		'u' => 'User',
		'g' => 'Guest',
	];
	
	const CONTROLLER_PERMISSIONS = [
		'Catalog' => 'g',
		'Categories' => 'u',
		'Products' => 'u',
		'Users' => 'a',
		'Settings' => 'a',
	];

// ------------------------------------------ DEFAULTS ---------------------------------------------------------

// Application directory
	define('APP_DIRECTORY', dirname(__FILE__, 2));
	define('ROOT_DIRECTORY', dirname(APP_DIRECTORY, 1));

// URL root
	if (str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '127.0.0.1')) {
		define('ROOT_URL', 'http://' . $_SERVER['HTTP_HOST'] . str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']));
	} else {
		define('ROOT_URL', 'https://' . $_SERVER['HTTP_HOST'] . str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']));
	}


