<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */
	
	/**
	 * The Base Controller class that loads the associated model and view.
	 * All controller classes will extend this class.
	 * All controller classes will inherit site wide settings, user data, and category data (used by menu items).
	 * The MVC url is structured like this: /Controller/Method/Parameter/[parameter/parameter/...]
	 */
	class Controller
	{
		protected mixed $categoryModel;
		protected mixed $settingModel;
		protected mixed $categoryLinks;
		protected mixed $data;
		protected string $controller;
		protected bool $authenticated;
		protected bool $logged_in;
		
		public function __construct()
		{
			// Get site-wide settings
			$this->settingModel = $this->model('Setting');
			$settings = $this->settingModel->getAllAsKeyValuePairs();
			
			// Get category links for menu
			$this->categoryModel = $this->model('Category');
			$this->categoryLinks = $this->categoryModel->getAll(show_empty_categories: false, as_list_array: true);
			
			// Get the name of the controller from the class name
			$this->controller = strtolower(get_class($this));
			
			// By default, a user is not authenticated or logged in
			$this->authenticated = false;
			$this->logged_in = false;
			
			// Start a session and set user id and type
			if (!isset($_SESSION)) {
				session_start();
			}
			
			$user_id = '';
			if (isset($_SESSION['user_id'])) {
				$this->logged_in = true;
				$user_id = get_session_value('user_id');
			}
			
			$user_type = [];
			if (isset($_SESSION['user_type'])) {
				$user_type = get_session_value('user_type');
			}
			
			// Populate site-wide data
			$this->data = [
				'user_id' => $user_id,
				'user_type' => $user_type,
				'settings' => $settings,
				'category_links' => $this->categoryLinks
			];
		}
		
		/**
		 * Load the model file. The model file represents the business logic of the site.
		 * @param $model
		 * @return mixed
		 */
		public function model($model)
		{
			require_once APP_DIRECTORY . '/models/' . $model . '.php';
			return new $model();
		}
		
		/**
		 * Check if the associated view file exists and load it if it exists.
		 * The view represents the visual presentation of the site.
		 * @param $view
		 * @param array $data
		 */
		public function view($view, array $data = []): void
		{
			if (file_exists(APP_DIRECTORY . '/views/' . $view . '.php')) {
				// Default settings for view
				$controller = $this->controller;
				$model = depluralize($this->controller);
				$settings = $data['settings'];
				
				require_once APP_DIRECTORY . '/views/' . $view . '.php';
			} else {
				die('View not found.');
			}
		}
		
		/**
		 * Determine if a user has the correct user type to access a controller.
		 * If a user is not authenticated for this controller, redirect to another page (404 by default).
		 * This is a 403 code, but as per w3 protocol, using 404 is acceptable to prevent information disclosure.
		 * @param $user_type_required - The current user type must match this requirement to access.
		 * @param string $redirect
		 * @param bool $api
		 * @return bool|void
		 * @throws JsonException
		 */
		protected function authorizationRequired($user_type_required = null, string $redirect = 'catalog/404', bool $api = false)
		{
			if ($user_type_required) {
				$user_type = $user_type_required;
			} else {
				$user_type = CONTROLLER_PERMISSIONS[get_class($this)];
			}
			
			if ($user_type === 'c' && boolean($this->data['settings']['guest_catalog_access'])) {
				$this->authenticated = true;
			} else {
				$this->authenticated = in_array($user_type, $this->data['user_type'], true);
			}
			
			// If a user is not authenticated for this controller, redirect to another page and die.
			if (!$this->authenticated && $api) {
				$this->prepareApi();
			} elseif (!$this->authenticated && !$api) {
				$this->view($redirect, $this->data);
				die();
			}
			
			return true;
		}
		
		/**
		 * Sort, filter, and display the results of a database query for the associated view.
		 * @param $sort_by_column
		 * @param bool $show_archived
		 * @param bool $descending
		 * @return void
		 */
		protected function displayResults($sort_by_column, bool $show_archived = true, bool $descending = false): void
		{
			// Get controller name from the name of the class
//            $controller = strtolower(get_class($this));
			
			// Show archived
			if ($show_archived) {
				$show_or_hide = 'show';
			} else {
				$show_or_hide = 'hide';
			}
			
			// Sort ascending or descending
			if ($descending) {
				$asc_or_desc = 'desc';
			} else {
				$asc_or_desc = 'asc';
			}
			
			// All columns from the database view
			$all_columns = $this->settingModel->getAllColumns($this->controller . '_view');
			
			// Columns currently displayed on a page
			$columns = $this->settingModel->validateColumns($this->data['settings']['columns_' . $this->controller], $this->controller . '_view');
			
			$archived = valid_cookie($this->controller . '_show_archived', ['show', 'hide'], $show_or_hide);
			$sort_by = valid_cookie($this->controller . '_sort_by_column', $all_columns, $sort_by_column);
			$sort_by_order = valid_cookie($this->controller . '_asc_desc', ['asc', 'desc'], $asc_or_desc);
			$pagination = valid_cookie($this->controller . '_pagination_count', validate_ints(PAGINATION_OPTIONS), (int)DEFAULT_PAGINATION);
			
			$this->data += [
				'all_columns' => $all_columns,
				'columns' => $columns,
				'show_archived' => $archived,
				'sort_by' => $sort_by,
				'sort_by_order' => $sort_by_order,
				'pagination' => $pagination
			];
		}
		
		/**
		 * Validate data for api
		 * @param $data
		 * @return void
		 * @throws JsonException
		 */
		protected function prepareApi($data = null): void
		{
			$error = '';
			$error_header = '';
			$response_data = json_encode([], JSON_THROW_ON_ERROR);
			$request_method = $_SERVER["REQUEST_METHOD"];
			
			if ($this->authenticated) {
				if (strtoupper($request_method) === 'GET') {
					if (count($data) > 0) {
						try {
							$response_data = json_encode($data, JSON_THROW_ON_ERROR);
						} catch (Error $e) {
							$error = $e->getMessage() . 'An error occurred while retrieving the data.';
							$error_header = 'HTTP/1.1 500 Internal Server Error';
						}
					} elseif (count($data) === 0) {
						$error = 'No records found.';
						$error_header = 'HTTP/1.1 200 OK';
					} else {
						$error = 'Not found.';
						$error_header = 'HTTP/1.1 404 Not Found';
					}
					
				} else {
					$error = 'Method not supported';
					$error_header = 'HTTP/1.1 422 Unprocessable Entity';
				}
			} else {
				$error = 'Not authorized.';
				$error_header = 'HTTP/1.1 401 Unauthorized';
			}
			
			// send output
			if (!$error) {
				$this->sendOutput(
					$response_data,
					array('Content-Type: application/json', 'HTTP/1.1 200 OK')
				);
			} else {
				$this->sendOutput(json_encode(array('error' => $error), JSON_THROW_ON_ERROR),
					array('Content-Type: application/json', $error_header)
				);
			}
		}
		
		/**
		 * Send output
		 * @param $data
		 * @param array $httpHeaders
		 * @return void
		 */
		protected function sendOutput($data, array $httpHeaders = array()): void
		{
			header_remove('Set-Cookie');
			
			if (is_array($httpHeaders) && count($httpHeaders)) {
				foreach ($httpHeaders as $httpHeader) {
					header($httpHeader);
				}
			}
			
			echo $data;
			exit;
		}
		
		/**
		 * Reset columns back to settings default.
		 * @return void
		 */
		protected function resetColumns(): void
		{
			// Get controller name from the name of the class
//            $controller = strtolower(get_class($this));
			
			$default_id = $this->settingModel->getIdBySetting('columns_' . $this->controller . '_default');
			$current_id = $this->settingModel->getIdBySetting('columns_' . $this->controller);
			
			$default_columns = $this->settingModel->get($default_id);
			
			$this->settingModel->update($current_id, $default_columns['value']);
			
			header("Location: " . ROOT_URL . "/" . $this->controller);
		}
		
		/**
		 * Add or remove a column from a list view
		 * @param $column_name
		 * @return void
		 */
		protected function addRemoveColumns($column_name): void
		{
			// Get controller name from the name of the class
//            $controller = strtolower(get_class($this));
			
			$id = $this->settingModel->getIdBySetting('columns_' . $this->controller);
			
			$current_columns = explode(',', $this->data['settings']['columns_' . $this->controller]);
			
			if (in_array($column_name, $current_columns, true)) {
				$position = array_search($column_name, $current_columns, true);
				unset($current_columns[$position]);
			} else {
				$sanitized_column_name = $this->settingModel->validateColumns($column_name, $this->controller . '_view');
				if (!is_null($sanitized_column_name)) {
					$current_columns[] = implode(',', $sanitized_column_name);
				}
			}
			
			$sanitized_columns = rtrim(implode(',', $current_columns), ',');
			
			$this->settingModel->update($id, $sanitized_columns);
			
			header("Location: " . ROOT_URL . "/" . $this->controller);
		}
	}