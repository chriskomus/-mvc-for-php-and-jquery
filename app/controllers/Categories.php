<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents the Category controller.
     */
    class Categories extends Controller
    {
        private array $all_columns;

        /**
         * @throws JsonException
         */
        public function __construct()
        {
            parent::__construct();

            $this->authorizationRequired();
        }

        /**
         * Default view for the controller. Get or set cookies for sorting.
         * @return void
         */
        public function index(): void
        {
            // Provide sorting and filtering functionality for the controller.
            $this->displayResults($this->data['settings']['default_sort']);

            $this->view('categories/index', $this->data);
        }

        /**
         * Individual category view for the controller.
         * @param $category_id - product slug
         * @return void
         */
        public function category($category_id = null): void
        {
            if (!is_null($category_id)) {
                $category = $this->categoryModel->get($category_id);
            } else {
                $category = [];
            }

            $this->data += [
                'category' => $category
            ];

            $this->view('categories/category', $this->data);
        }

        /**
         * Create a category.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function create()
        {
            if ($_POST) {
                // Sanitize data
                $category_data = sanitize_post();

                try {
                    $category_id = $this->categoryModel->create(category_data: $category_data);
                    $category = $this->categoryModel->get($category_id);

                    $error_type = 'success';
                    $error_message = $category['title'] . ' has been created. ';

                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'There was an error creating the category.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                $this->index();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Update a category.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function update()
        {
            if (!empty($_POST['id']) && isset($_POST['submit'])) {
                // Sanitize data
                $category_data = sanitize_post();

                $category_id = filter_var($category_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    $this->categoryModel->update(id: $category_id, category_data: $category_data);
                    $category = $this->categoryModel->get($category_id);

                    $error_type = 'success';
                    $error_message = $category['title'] . ' has been updated. ';

                } catch (Error $e) {
                    // There was an error updating the category
                    $error_type = 'warning';
                    $error_message = 'There was an error updating the category.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                $this->index();


            } else if (!empty($_POST['id']) && isset($_POST['delete'])) {
                $this->delete();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Delete a category.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function delete()
        {
            if ($_POST && $_POST['id']) {
                // Sanitize data
                $category_data = sanitize_post();

                $category_id = filter_var($category_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    $category = $this->categoryModel->get($category_id);
                    $category_title = $category['title'];

                    $this->categoryModel->delete(id: $category_id);

                    $error_type = 'success';
                    $error_message = $category_title . ' has been deleted.';

                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'There was an error deleting the category.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                $this->index();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Regenerate slugs for all categories. This will erase existing slugs and replace with new slugs.
         * @return void
         */
        public function generate_category_slugs(): void
        {
            $categories = $this->categoryModel->getAll(show_empty_categories: true);

            try {
                foreach ($categories as $category) {
                    $this->categoryModel->updateSlug($category['category_id'], $category['title']);
                }

                $error_type = 'success';
                $error_message = 'Category slugs (SEO safe links) have been regenerated.';

            } catch (Error $e) {
                // There was an error updating the address
                $error_type = 'warning';
                $error_message = 'There was an error updating the category slugs.';
                if (DETAILED_ERROR_MSG) {
                    $error_message .= '</br>' . $e->getMessage();
                }
            }

            // Send data to the view.
            $this->data += [
                'error_type' => $error_type,
                'error_message' => $error_message
            ];

            $this->index();
        }

        /**
         * Add or remove a column from the category view. The column name will be checked against the categories_view view.
         * @param $column_name
         * @return void
         */
        public function add_remove_columns($column_name = null)
        {
            $this->addRemoveColumns($column_name);
        }

        /**
         * Reset headings to default.
         * @return void
         */
        public function reset_columns()
        {
            $this->resetColumns();
        }
    }