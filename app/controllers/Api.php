<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

    /*
    Binpoint - Inventory Management System
    (C) Chris Komus 2022
    */

    use JetBrains\PhpStorm\NoReturn;


    /**
     * This class represents the REST API.
     * The Api controller is a special controller different from the rest of the controllers, but still
     * inheriting from the base controller, thus able to use the same authentication and login variables.
     * It has no associated views and will return JSON data instead.
     */
    class Api extends Controller
    {
        public function __construct()
        {
            parent::__construct();

            header("Access-Control-Allow-Origin: *");
            header("Content-Type: application/json; charset=UTF-8");
        }

        /**
         * Default view for the controller.
         * @return void
         * @throws JsonException
         */
        public function index(): void
        {
            $this->prepareApi();
        }

        /**
         * Catalog
         * @param null $category - category slug
         * @throws JsonException
         */
        public function catalog($category = null): void
        {
            $user_type = get_usertype_requirement(__FUNCTION__);
            $this->authorizationRequired(user_type_required: $user_type, api: true);

            // Check if the provided category slug exists, otherwise set it to null.
            if (!$this->categoryModel->isSlugExist($category)) {
                $category = null;
            }

            try {
                $productModel = $this->model('Product');
                $data = $productModel->getAllLimited(category_slug: $category);
            } catch (Error $e) {
                $data = [];
            }

            $this->prepareApi($data);
        }

        /**
         * Products
         * @param null $category - category slug
         * @throws JsonException
         */
        public function products($category = null): void
        {
            $user_type = get_usertype_requirement(__FUNCTION__);
            $this->authorizationRequired(user_type_required: $user_type, api: true);

            try {
                $productModel = $this->model('Product');
                $data = $productModel->getAll(category_slug: $category);
            } catch (Error $e) {
                $data = [];
            }

            $this->prepareApi($data);
        }

        /**
         * Individual product view for the controller.
         * @param $product_id - product slug
         * @throws JsonException
         */
        public function product($product_id = null): void
        {
            $user_type = get_usertype_requirement(pluralize(__FUNCTION__));
            $this->authorizationRequired(user_type_required: $user_type, api: true);

            $data = [];
            if (!is_null($product_id)) {
                try {
                    $productModel = $this->model('Product');
                    $data = $productModel->get(id: $product_id);
                } catch (Error $e) {
                    $data = [];
                }
            }

            $this->prepareApi($data);
        }

        /**
         * Users
         * @throws JsonException
         */
        public function users(): void
        {
            $this->authorizationRequired(user_type_required: 'a', api: true);

            try {
                $userModel = $this->model('User');
                $data = $userModel->getAll();
            } catch (Error $e) {
                $data = [];
            }

            $this->prepareApi($data);
        }

        /**
         * Categories
         * @throws JsonException
         */
        public function categories(): void
        {
            $user_type = get_usertype_requirement(__FUNCTION__);
            $this->authorizationRequired(user_type_required: $user_type, api: true);

            try {
                $categoryModel = $this->model('Category');
                $data = $categoryModel->getAll(show_empty_categories: true);
            } catch (Error $e) {
                $data = [];
            }

            $this->prepareApi($data);
        }

        /**
         * Get a list of columns from the associated view
         * @throws JsonException
         */
        public function columns($controller): void
        {
            $user_type = get_usertype_requirement($controller);
            $this->authorizationRequired(user_type_required: $user_type, api: true);

            try {
                $data = $this->settingModel->validateColumns($this->data['settings']['columns_' . $controller], $controller . '_view');
            } catch (Error $e) {
                $data = [];
            }

            $this->prepareApi($data);
        }


    }