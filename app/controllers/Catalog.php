<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents the Catalog controller, which is the default controller.
     */
    class Catalog extends Controller
    {
        private mixed $productModel;

        /**
         * @throws JsonException
         */
        public function __construct()
        {
            parent::__construct();

            if ($this->logged_in) {
                $this->authorizationRequired(redirect: 'catalog/about');
            } else {
                $this->authorizationRequired(redirect: 'users/login');
            }


            $this->productModel = $this->model('Product');
        }

        /**
         * Default view for the controller.
         * @param $category - category slug
         * @return void
         */
        public function index($category = null): void
        {
            $this->displayResults($this->data['settings']['default_sort']);

            // Check if the provided category slug exists, otherwise set it to null.
            $category_details = null;
            if ($this->categoryModel->isSlugExist($category)) {
                $category_details = $this->categoryModel->getBySlug($category);
            }

            $categories = $this->categoryModel->getAll(show_empty_categories: false);

            $this->data += [
                'category_details' => $category_details,
                'categories' => $categories
            ];

            $this->view('catalog/index', $this->data);
        }

        /**
         * Individual product. If a matching slug is not found, or the product is disabled, display the 404 page.
         * @param null $slug
         * @return void
         */
        public function product($slug = null): void
        {
            if (!is_null($slug)) {
                $product = $this->productModel->getBySlug(product_slug: $slug);
                if ($product && $product['enabled']) {
                    $this->data += [
                        'product' => $product
                    ];

                    $this->view('catalog/product', $this->data);
                } else {
                    $this->view('catalog/404', $this->data);
                }
            } else {
                $this->index();
            }
        }

        /**
         * The company about page.
         * @return void
         */
        public function about(): void
        {
            $this->view('catalog/about', $this->data);
        }
    }