<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

use Gumlet\ImageResize;
    use Gumlet\ImageResizeException;

    /**
     * This class represents a Product.
     */
    class Product
    {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
        }

        /**
         * Create a single record.
         * @throws Exception
         */
        public function create($product_data)
        {
            $validated_product_data = $this->validate(product_data: $product_data, generate_slug: true);

            // Starting quantity
            $quantity = 0;

            // Insert into database
            $query = "INSERT INTO products (title, category_id, sku, description, detailed_description, sale_price,
                            purchase_price, quantity, bin, reorder, type, notes, enabled, slug)
                      VALUES (:title, :category_id, :sku, :description, :detailed_description,
                              :sale_price, :purchase_price, :quantity, :bin, :reorder, :type, :notes, :enabled, :slug)";
            $this->db->query($query);
            $this->bindAll($validated_product_data);
            $this->db->bindValue(':quantity', $quantity, PDO::PARAM_INT);

            // execute INSERT statement and return the new product ic.
            try {
                $this->db->execute();
                return $this->db->lastInsert();

            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Update a single record.
         * @throws Exception
         */
        public function update($id, $product_data)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $update_slug = false;
            if (!$product_data['slug']) {
                $update_slug = true;
            }

            $validated_product_data = $this->validate(product_data: $product_data, generate_slug: $update_slug, product_id: $product_id);

            // Update row in database
            $query = "UPDATE products SET title = :title,
                                    category_id = :category_id,
                                    sku = :sku,
                                    description = :description,
                                    detailed_description = :detailed_description,
                                    sale_price = :sale_price,
                                    purchase_price = :purchase_price,
                                    bin = :bin,
                                    quantity = :quantity,
                                    reorder = :reorder,
                                    type = :type,
                                    notes = :notes,
                                    slug = :slug,
                                    enabled = :enabled
                                    WHERE product_id = :product_id LIMIT 1";

            $this->db->query($query);
            $this->bindAll($validated_product_data);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            // execute UPDATE statement
            try {
                $this->db->execute();

            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Update the slug of a single record.
         * If the slug already exists, append the product_id to the end to make it unique.
         * @param $id - the product id
         * @param $slug_unfiltered - the text that will be converted into a slug
         * @return bool
         * @throws Exception
         */
        public function updateSlug($id, $slug_unfiltered): bool
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $slug = generate_slug($slug_unfiltered);

            if ($this->isSlugExist($slug)) {
                $slug .= $product_id;
            }

            $this->db->query("UPDATE products SET slug = :slug WHERE product_id = :product_id");
            $this->db->bindValue(':slug', $slug);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            if ($this->db->execute()) {
                $isSet = true;
            } else {
                $isSet = false;
            }

            return $isSet;
        }

        /**
         * Update a product with a new quantity.
         * @param $product_id
         * @param $quantity
         * @return void
         * @throws Exception
         */
        public function updateQuantity($product_id, $quantity): void
        {
            $this->db->query("UPDATE products SET quantity = :quantity WHERE product_id = :product_id");
            $this->db->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot validate product quantity: ' . $e->getMessage());
            }
        }

        /**
         * Check if the submitted file is an image. If so, check its filesize.
         * Rename it to a 4 digit random number - the current unix timestamp to prevent saving over another image.
         * Resize it, generate a thumbnail and associate it with a product.
         * @param $id
         * @param $image_filename
         * @param $temporary_image_path
         * @return void
         * @throws Exception
         */
        public function updateImage($id, $image_filename, $temporary_image_path)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $new_image_path = file_upload_path(filename: $image_filename, subfolder: 'products');

            // If an image association exists, delete the image first
            $product = $this->get($product_id);
            if ($product['image']) {
                $this->deleteImage($product_id);
            }

            if (file_is_an_image($temporary_image_path, $new_image_path)) {
                $fileSize = filesize($temporary_image_path);

                // Validate filesize.
                if ($fileSize > 8388608) {
                    throw new Error('The image must be under 8MB (8,388,608 bytes).');
                }

                if ($fileSize === 0) {
                    throw new Error('The image filesize is zero.');
                }

                try {
                    $old_filename = explode(".", $image_filename);
                    $filename_extension = end($old_filename);
                    $new_filename = random_int(10 ** 3, (10 ** 4) - 1) . '-' . round(microtime(true)) . '.' . $filename_extension;
                    $new_image_path = file_upload_path(filename: $new_filename, subfolder: 'products');

                    move_uploaded_file($temporary_image_path, $new_image_path);

                    image_resize(225, '_thumb', $new_filename, $new_image_path);

                    $this->db->query("UPDATE products SET image = :image WHERE product_id = :product_id");
                    $this->db->bindValue(':image', $new_filename);
                    $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

                    $this->db->execute();

                } catch (Exception $e) {
                    throw new Error('An error occurred: ' . $e->getMessage());
                }
            } else {
                throw new Error('The uploaded file is not a valid image file. Choose an image file (jpeg, jpg, gif, png) and try again.');
            }
        }

        /**
         * Delete a single record. First validate its quantity. Then check if it has history
         * (purchase orders, sales orders, adjustments, or builds), then delete it. Then delete it's associated
         * image if it has one.
         * @param $id
         * @throws Exception
         */
        public function delete($id)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $product = $this->get($product_id);

            if ($this->isHistoryExist($product_id)) {
                throw new Error('This product cannot be deleted because it has a history. Archive it instead.');
            }

            $image = $product['image'];

            if ($image) {
                $this->deleteImage($product_id);
            }

            $query = "DELETE FROM products WHERE product_id = :product_id LIMIT 1";

            $this->db->query("SET foreign_key_checks = 1");
            $this->db->query($query);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot delete product: ' . $e->getMessage());
            }
        }

        /**
         * Delete an image file and its association to a product.
         * @param $id
         * @return void
         * @throws Exception
         */
        public function deleteImage($id)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $product = $this->get($product_id);
            $filename = $product['image'];

            // Remove from the database
            try {
                $this->db->query("UPDATE products SET image = :image WHERE product_id = :product_id");
                $this->db->bindValue(':image', null);
                $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

                $this->db->execute();

            } catch (Exception $e) {
                throw new Error('An error occurred: ' . $e->getMessage());
            }

            // Delete the file
            try {
                $image_path = file_upload_path(filename: $filename, subfolder: 'products');

                $extension_position = strrpos($image_path, '.');
                $thumbnail_filename = substr($image_path, 0, $extension_position) . '_thumb' . substr($image_path, $extension_position);

                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                if (file_exists($thumbnail_filename)) {
                    unlink($thumbnail_filename);
                }
            } catch (Exception $e) {
                throw new Error('The file was not found, or could not be deleted:' . $e->getMessage());
            }
        }

        /**
         * Clear all slugs.
         * @return void
         * @throws Exception
         */
        public function clearAllSlugs(): void
        {
            $this->db->query("UPDATE products SET slug = NULL");

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot clear product slugs: ' . $e->getMessage());
            }
        }

        /**
         * Archive/un-archive a product.
         * @throws Exception
         */
        public function archive($id)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $product = $this->get($product_id);

            $enabled = true;
            if ($product['enabled']) {
                $enabled = false;
            }

            // Update row in database
            $query = "UPDATE products
                        SET enabled = :enabled
                        WHERE product_id = :product_id LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':enabled', $enabled, PDO::PARAM_BOOL);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            // execute UPDATE statement
            try {
                $this->db->execute();

            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Get a single record.
         * Validate its quantity when loading.
         * Catalog view returns only a limited amount of information meant for the catalog and public api.
         * @param string $id
         * @param bool $catalog_view
         * @return mixed
         * @throws Exception
         */
        public function get(string $id, bool $catalog_view = false)
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            if ($catalog_view) {
                $query = "SELECT p.product_id, p.title, p.sku, p.detailed_description, p.image,
                        p.sale_price, p.slug, p.enabled, c.title as category, c.slug as category_slug
                        FROM products p
                        JOIN categories c ON c.category_id = p.category_id
                        WHERE product_id = :product_id LIMIT 1";
            } else {
                $query = "SELECT p.*, c.title as category, c.slug as category_slug
                        FROM products p
                        JOIN categories c ON c.category_id = p.category_id
                        WHERE product_id = :product_id LIMIT 1";
            }

            $this->db->query($query);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            return $this->db->single();
        }

        /**
         * Get all records.
         * The category slug is used to filter the product results.
         * @param $category_slug - optional category slug for filtering the product results.
         * @return mixed
         * @throws Exception
         */
        public function getAll($category_slug = null): mixed
        {
            if ($category_slug) {
                $category_slug = filter_var($category_slug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $query = "SELECT p.*, c.title as category, c.slug as category_slug
                          FROM products p
                          JOIN categories c ON c.category_id = p.category_id
                          WHERE c.slug LIKE :category_slug";

                $this->db->query($query);
                $this->db->bindValue(':category_slug', $category_slug);
            } else {
                $query = "SELECT p.*, c.title as category, c.slug as category_slug
                          FROM products p
                          JOIN categories c ON c.category_id = p.category_id";

                $this->db->query($query);
            }

            return $this->db->results();
        }

        /**
         * Get all records with a limited number of fields.
         * The category slug is used to filter the product results.
         * @param $category_slug - optional category slug for filtering the product results.
         * @return mixed
         * @throws Exception
         */
        public function getAllLimited($category_slug = null): mixed
        {
            if ($category_slug) {
                $category_slug = filter_var($category_slug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                $query = "SELECT p.product_id, p.title, p.sku, p.detailed_description, 
                        p.image, p.sale_price, p.slug, p.enabled, c.title as category, c.slug as category_slug
                        FROM products p
                        JOIN categories c ON c.category_id = p.category_id
                        WHERE p.enabled = TRUE AND c.slug LIKE :category_slug";

                $this->db->query($query);
                $this->db->bindValue(':category_slug', $category_slug);
            } else {
                $query = "SELECT p.product_id, p.title, p.sku, p.detailed_description, 
                        p.image, p.sale_price, p.slug, p.enabled, c.title as category, c.slug as category_slug
                        FROM products p
                        JOIN categories c ON c.category_id = p.category_id
                        WHERE p.enabled = TRUE";

                $this->db->query($query);
            }

            return $this->db->results();
        }

        /**
         * Get a single record by slug.
         * @param string $product_slug
         * @return mixed
         * @throws Exception
         */
        public function getBySlug(string $product_slug)
        {
            $slug = filter_var($product_slug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "SELECT p.*, c.title as category, c.slug as category_slug
                      FROM products p
                      JOIN categories c ON c.category_id = p.category_id
                      WHERE p.slug = :slug LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':slug', $slug);

            return $this->db->single();
        }

        /**
         * Return a list of all product types.
         * @return array
         * @throws Exception
         */
        public function getTypes(): array
        {
            $query = "SELECT DISTINCT type FROM products";

            $this->db->query($query);

            return $this->db->resultsAsList('type');
        }

        /**
         * Provide data validation for a single record.
         * Throw errors if invalid. Optionally, generate a slug.
         * @param $product_data
         * @param bool $generate_slug
         * @param null $product_id
         * @return mixed
         * @throws Exception
         */
        public function validate($product_data, bool $generate_slug = false, $product_id = null): mixed
        {
            if ($product_id) {
                $product = $this->get($product_id);
            }

            foreach ($product_data as $key => $value) {
                $product_data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }

            // Generate slug - If it already exists, append the product id or a random number to make it unique.
            // User can set a better slug later, or all products can be re-slugged using title and sku.
            if ($generate_slug || !isset($product_data['slug'])) {
                $slug = generate_slug($product_data['title']);
                if ($this->isSlugExist($slug)) {
                    if ($product_id) {
                        $slug .= $product_id;
                    } else {
                        $slug .= random_int(10 ** 2, (10 ** 3) - 1);
                    }
                }
                $product_data['slug'] = $slug;
            }

            // Unique Slugs
            if ($product_id && $product_data['slug'] !== $product['slug']) {
                $slug = generate_slug($product_data['slug']);
                if ($this->isSlugExist($slug)) {
                    throw new Error('Slug must be unique.');
                }

                $product_data['slug'] = $slug;
            }

            // Validation - required fields
            $required = ['category_id', 'title', 'sku', 'type', 'slug'];
            $error = [];
            foreach ($required as $item) {
                if (!$product_data[$item]) {
                    $error[] = ucwords($item);
                }
            }

            if (!empty($error)) {
                throw new Error('Missing required fields: ' . implode(", ", $error));
            }

            // Unique SKUs
            if (!$product_id || ($product_id && $product['sku'] !== $product_data['sku'])) {
                if ($this->isSkuExist($product_data['sku'])) {
                    throw new Error('SKU must be unique.');
                }
            }

            // Validate as integers
            $validate = ['category_id', 'quantity', 'reorder'];
            foreach ($validate as $column) {
                if (isset($product_data[$column])) {
                    $product_data[$column] = filter_var($product_data[$column], FILTER_SANITIZE_NUMBER_INT);
                }
            }

            // Validate as floats
            $validate = ['sale_price', 'purchase_price'];
            foreach ($validate as $column) {
                if (isset($product_data[$column])) {
                    $product_data[$column] = filter_var($product_data[$column], FILTER_SANITIZE_NUMBER_FLOAT,
                        FILTER_FLAG_ALLOW_FRACTION);
                }
            }

            // Truncate
            $truncate_values = [
                'title' => 255,
                'sku' => 255,
                'bin' => 255,
                'slug' => 255,
                'type' => 45,
            ];

            foreach ($truncate_values as $key => $value) {
                if (isset($product_data[$key])) {
                    $product_data[$key] = substr($product_data[$key], 0, $value);
                }
            }

            return $product_data;
        }

        /**
         * Bind all fields in a record.
         * @param $product_data
         * @return void
         * @throws Exception
         */
        public function bindAll($product_data): void
        {
            $this->db->bindValue(':title', $product_data['title']);
            $this->db->bindValue(':category_id', $product_data['category_id']);
            $this->db->bindValue(':sku', $product_data['sku']);
            $this->db->bindValue(':description', $product_data['description']);
            $this->db->bindValue(':detailed_description', $product_data['detailed_description']);
            $this->db->bindValue(':sale_price', $product_data['sale_price']);
            $this->db->bindValue(':purchase_price', $product_data['purchase_price']);
            $this->db->bindValue(':bin', $product_data['bin']);
            $this->db->bindValue(':quantity', $product_data['quantity']);
            $this->db->bindValue(':reorder', $product_data['reorder']);
            $this->db->bindValue(':type', $product_data['type']);
            $this->db->bindValue(':notes', $product_data['notes']);
            $this->db->bindValue(':slug', $product_data['slug']);
            $this->db->bindValue(':enabled', isset($product_data['enabled']), PDO::PARAM_BOOL);
        }

        /**
         * Returns true if a record exists with a provided sku.
         * @param $sku
         * @param $id
         * @return bool
         * @throws Exception
         */
        public function isSkuExist($sku): bool
        {
            $query = "SELECT sku FROM products WHERE sku = :sku LIMIT 1";
            $this->db->query($query);
            $this->db->bindValue(':sku', $sku);

            $this->db->single();

            return $this->db->rowCount() > 0;
        }

        /**
         * Returns true if a record exists with a provided slug.
         * @param $slug
         * @param $id
         * @return bool
         * @throws Exception
         */
        public function isSlugExist($slug): bool
        {
            $query = "SELECT slug FROM products WHERE slug = :slug LIMIT 1";
            $this->db->query($query);
            $this->db->bindValue(':slug', $slug);

            $this->db->single();

            return $this->db->rowCount() > 0;
        }

        /**
         * Returns true if the record of a provided id has any recorded history/activity.
         * Any product with a history cannot be deleted, only archived, as it will be attached to orders, etc.
         * Check the purchasing order, sales order, adjustment, and build tables for a product id and
         * return 1 for each table, if it has history. Add up the values at the end, if its over 0 (meaning at
         * least one table returned a value), the product has history.
         * @param $id
         * @return bool
         * @throws Exception
         */
        public function isHistoryExist($id): bool
        {
            $product_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $history = 0;

            // Purchase history
            $query = "SELECT product_id FROM purchase_orders_details WHERE product_id = :product_id LIMIT 1";
            $history += $this->isHistoryExistQueryCount($query, $product_id);

            // Sales history
            $query = "SELECT product_id FROM sales_orders_details WHERE product_id = :product_id LIMIT 1";
            $history += $this->isHistoryExistQueryCount($query, $product_id);

            // Adjustment history
            $query = "SELECT product_id FROM adjustments WHERE product_id = :product_id LIMIT 1";
            $history += $this->isHistoryExistQueryCount($query, $product_id);

            // build history
            $query = "SELECT product_id FROM builds WHERE product_id = :product_id LIMIT 1";
            $history += $this->isHistoryExistQueryCount($query, $product_id);

            return $history > 0;
        }

        /**
         * Functionality for the isHistoryExist function
         * @param $query
         * @param $product_id
         * @return mixed
         * @throws Exception
         */
        public function isHistoryExistQueryCount($query, $product_id): mixed
        {
            $this->db->query($query);
            $this->db->bindValue(':product_id', $product_id, PDO::PARAM_INT);

            $this->db->single();

            return $this->db->rowCount();
        }
    }