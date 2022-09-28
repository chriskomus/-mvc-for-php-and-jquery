<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents the Products controller.
     */
    class Products extends Controller
    {
        private mixed $productModel;

        /**
         * @throws JsonException
         */
        public function __construct()
        {
            parent::__construct();

            $this->authorizationRequired();

            $categories = $this->categoryModel->getAll(show_empty_categories: true);

            $this->productModel = $this->model('Product');

            $this->data += [
                'categories' => $categories
            ];
        }

        /**
         * Default view for the controller. Get or set cookies for sorting.
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

            $this->data += [
                'category_details' => $category_details,
            ];

            $this->view('products/index', $this->data);
        }

        /**
         * Individual product view for the controller.
         * @param $product_id - product slug
         * @return void
         */
        public function product($product_id = null): void
        {
            if (!is_null($product_id)) {
                $product = $this->productModel->get(id: $product_id);
            } else {
                $product = [];
            }

            $product_types = $this->productModel->getTypes();

            $this->data += [
                'product' => $product,
                'product_types' => $product_types,
            ];

            $this->view('products/product', $this->data);
        }

        /**
         * Create a product.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function create()
        {
            if ($_POST) {
                // Sanitize data
                $product_data = sanitize_post();

                try {
                    $product_id = $this->productModel->create(product_data: $product_data);
                    $product = $this->productModel->get($product_id);

                    $error_type = 'success';
                    $error_message = $product['title'] . ' has been created. ';

                } catch (Error $e) {
                    // There was an error updating the product
                    $error_type = 'warning';
                    $error_message = 'There was an error updating the product.';
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
         * Update a product.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function update()
        {
            if (!empty($_POST['id']) && isset($_POST['submit'])) {
                // Sanitize data
                $product_data = sanitize_post();

                $product_id = filter_var($product_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    $this->productModel->update(id: $product_id, product_data: $product_data);
                    $product = $this->productModel->get($product_id);

                    $error_type = 'success';
                    $error_message = $product['title'] . ' has been updated. ';

                } catch (Error $e) {
                    // There was an error updating the product
                    $error_type = 'warning';
                    $error_message = 'There was an error updating the product.';
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
            } else if (!empty($_POST['id']) && isset($_POST['archive'])) {
                $this->archive();
            } else if (!empty($_POST['id']) && isset($_POST['upload-image'])) {
                $this->upload_image();
            } else if (!empty($_POST['id']) && isset($_POST['delete-image'])) {
                $this->delete_image();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Delete a product.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function delete()
        {
            if ($_POST && $_POST['id']) {
                // Sanitize data
                $product_data = sanitize_post();

                $product_id = filter_var($product_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    $product = $this->productModel->get($product_id);
                    $product_title = $product['title'];

                    $this->productModel->delete(id: $product_id);

                    $error_type = 'success';
                    $error_message = $product_title . ' has been deleted.';

                } catch (Error $e) {
                    // There was an error archiving the product
                    $error_type = 'warning';
                    $error_message = 'There was an error deleting the product.';
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
         * Delete a product image.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function delete_image()
        {
            if ($_POST && $_POST['id']) {
                // Sanitize data
                $product_data = sanitize_post();

                $product_id = filter_var($product_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    // Verify that uploaded file is an image. updateImage will throw an Error if not.
                    $this->productModel->deleteImage(id: $product_id);

                    $product = $this->productModel->get($product_id);

                    $error_type = 'success';
                    $error_message = 'The image belonging to ' . $product['title'] . ' has been deleted.';

                } catch (Error $e) {
                    // There was an error archiving the product
                    $error_type = 'warning';
                    $error_message = 'There was an error deleting an image.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                $this->product($product_id);
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Archive a product.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function archive()
        {
            if ($_POST && $_POST['id']) {
                // Sanitize data
                $product_data = sanitize_post();

                $product_id = filter_var($product_data['id'], FILTER_SANITIZE_NUMBER_INT);

                try {
                    $this->productModel->archive(id: $product_id);
                    $product = $this->productModel->get($product_id);
                    $isArchived = $product['enabled'];

                    $error_type = 'success';
                    $error_message = $product['title'] . ' has been ' . ($isArchived ? 'un' : '') . 'archived.';

                } catch (Error $e) {
                    // There was an error archiving the product
                    $error_type = 'warning';
                    $error_message = 'There was an error archiving the product.';
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
         * Upload a product image, resize and generate thumbnails.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function upload_image()
        {
            if ($_POST && $_POST['id']) {
                // Sanitize data
                $product_data = sanitize_post();
                $product_id = filter_var($product_data['id'], FILTER_SANITIZE_NUMBER_INT);

                if (isset($_FILES['image-file']) && $_FILES['image-file']['error'] === 0) {
                    try {
                        // Verify that uploaded file is an image. updateImage will throw an Error if not.
                        $this->productModel->updateImage(id: $product_id,
                            image_filename: $_FILES['image-file']['name'],
                            temporary_image_path: $_FILES['image-file']['tmp_name']);

                        $product = $this->productModel->get($product_id);

                        $error_type = 'success';
                        $error_message = 'An image has been successfully added to ' . $product['title'];

                    } catch (Error $e) {
                        // There was an error archiving the product
                        $error_type = 'warning';
                        $error_message = 'There was an error adding an image.';
                        if (DETAILED_ERROR_MSG) {
                            $error_message .= '</br>' . $e->getMessage();
                        }
                    }
                } else if (isset($_FILES['image-file']) && $_FILES['image-file']['error'] === 4) {
                    $error_type = 'warning';
                    $error_message = 'No file uploaded. Choose an image file (jpeg, jpg, gif, png) and try again.';
                } else {
                    $error_type = 'warning';
                    $error_message = 'There was an unexpected error uploading the file.';
                }

                // Send data to the view.
                $this->data += [
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                $this->product($product_id);
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Regenerate slugs for all products. This will erase existing slugs and replace with new slugs.
         * @return void
         */
        public function generate_product_slugs(): void
        {
            try {
                $products = $this->productModel->getAll();
                foreach ($products as $product) {
                    $this->productModel->updateSlug($product['product_id'], $product['title']);
                }

                $error_type = 'success';
                $error_message = 'Product slugs (SEO safe links) have been regenerated.';
            } catch (Error $e) {
                $error_type = 'warning';
                $error_message = 'There was an error regenerating slugs.';
                if (DETAILED_ERROR_MSG) {
                    $error_message .= '</br>' . $e->getMessage();
                }
            }

            $this->data += [
                'error_type' => $error_type,
                'error_message' => $error_message,
            ];

            $this->index();
        }

        /**
         * Add or remove a column from the product view. The column name will be checked against the productdeti
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