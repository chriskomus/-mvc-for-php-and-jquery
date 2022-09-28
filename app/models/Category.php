<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents a Category of products.
     */
    class Category
    {
        private Database $db;

        public function __construct()
        {
            $this->db = new Database;
        }

        /**
         * Create a single record.
         * @throws Exception
         */
        public function create($category_data)
        {
            $validated_category_data = $this->validate(category_data: $category_data, generate_slug: true);

            // Insert into database
            $query = "INSERT INTO categories (title, description, slug)
                      VALUES (:title, :description, :slug)";

            $this->db->query($query);
            $this->bindAll($validated_category_data);

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
        public function update($id, $category_data)
        {
            $category_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $validated_category_data = $this->validate(category_data: $category_data, category_id: $category_id);

            // Update row in database
            $query = "UPDATE categories SET title = :title,
                                    description = :description,
                                    slug = :slug
                                    WHERE category_id = :category_id LIMIT 1";

            $this->db->query($query);
            $this->bindAll($validated_category_data);
            $this->db->bindValue(':category_id', $category_id, PDO::PARAM_INT);

            // execute UPDATE statement
            try {
                $this->db->execute();

            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Update the slug of a single record.
         * @param $category_id_unfiltered - the category id
         * @param $slug_unfiltered - the text that will be converted into a slug
         * @return bool
         * @throws Exception
         */
        public function updateSlug($category_id_unfiltered, $slug_unfiltered): bool
        {
            $category_id = filter_var($category_id_unfiltered, FILTER_SANITIZE_NUMBER_INT);
            $slug = generate_slug($slug_unfiltered);

            if ($this->isSlugExist($slug)) {
                $slug .= $category_id;
            }

            $this->db->query("UPDATE categories SET slug = :slug WHERE category_id = :category_id");
            $this->db->bindValue(':slug', $slug);
            $this->db->bindValue(':category_id', $category_id, PDO::PARAM_INT);

            if ($this->db->execute()) {
                $isSet = true;
            } else {
                $isSet = false;
            }

            return $isSet;
        }

        /**
         * Delete a single record. Check if it has any products in it and throw an error if it does. Otherwise, delete.
         * @param $id
         * @throws Exception
         */
        public function delete($id)
        {
            $category_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $category = $this->get($category_id);
            $count = (int)$category['product_count'];

            if ($count !== 0) {
                throw new Error('This category cannot be deleted because it is not empty. Move the products out first.');
            }

            $query = "DELETE FROM categories WHERE category_id = :category_id LIMIT 1";

            $this->db->query("SET foreign_key_checks = 1");
            $this->db->query($query);
            $this->db->bindValue(':category_id', $category_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot delete category: ' . $e->getMessage());
            }
        }

        /**
         * Clear all slugs.
         * @return void
         * @throws Exception
         */
        public function clearAllSlugs(): void
        {
            $this->db->query("UPDATE categories SET slug = NULL");

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot clear category slugs: ' . $e->getMessage());
            }
        }

        /**
         * Get a single record by id, as well as a product count.
         * @throws Exception
         */
        public function get($id)
        {
            $category_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $this->db->query("SELECT c.*, count(p.category_id) as product_count
                            FROM categories c
                            LEFT JOIN products p ON c.category_id = p.category_id
                            WHERE c.category_id = :category_id LIMIT 1");

            $this->db->bindValue(':category_id', $category_id, PDO::PARAM_INT);

            return $this->db->single();
        }

        /**
         * Get all records, as well as a product count.
         * Show empty categories is set to true by default.
         * By default objects will returned, but a 2d list can be returned fpr ise in menus and dropdowns.
         * @param bool $show_empty_categories
         * @param bool $as_list_array
         * @return mixed
         * @throws Exception
         */
        public function getAll(bool $show_empty_categories = true, bool $as_list_array = false)
        {
            $this->db->query($this->getQuery($show_empty_categories));

            if ($as_list_array) {
                return $this->db->resultsAs2DListArray('slug', 'title', 'icon');
            }

            return $this->db->results();
        }

        /**
         * Get a single record by slug, as well as a product count.
         * @throws Exception
         */
        public function getBySlug($slug)
        {
            $slug = filter_var($slug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $this->db->query("SELECT c.*, count(p.category_id) as product_count
                            FROM categories c
                            LEFT JOIN products p ON c.category_id = p.category_id
                            WHERE c.slug = :slug LIMIT 1");

            $this->db->bindValue(':slug', $slug);

            return $this->db->single();
        }

        /**
         * Return the query string for the categories table to be used by other functions in this model.
         * @param bool $show_empty_categories
         * @return string
         */
        public function getQuery(bool $show_empty_categories = true): string
        {
            if ($show_empty_categories) {
                $query = "SELECT c.*, count(p.category_id) as product_count
                            FROM categories c
                            LEFT JOIN products p ON c.category_id = p.category_id
                            GROUP BY c.title ORDER BY c.title";
            } else {
                $query = "SELECT c.*, count(p.category_id) as product_count
                            FROM categories c
                            JOIN products p ON c.category_id = p.category_id
                            GROUP BY c.title ORDER BY c.title";
            }

            return $query;
        }

        /**
         * Provide data validation for a single record. Throw errors if invalid. Optionally, generate a slug.
         * @param $category_data
         * @param bool $generate_slug
         * @param null $category_id
         * @return mixed
         * @throws Exception
         */
        public function validate($category_data, bool $generate_slug = false, $category_id = null): mixed
        {
            if ($category_id) {
                $category = $this->get($category_id);
            }

            foreach ($category_data as $key => $value) {
                $category_data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }

            // Generate slug - it must be unqiue.
            if ($generate_slug || !isset($category_data['slug'])) {
                $slug = generate_slug($category_data['title']);
                if ($this->isSlugExist($slug)) {
                    throw new Error('Slug must be unique.');
                }
                $category_data['slug'] = $slug;
            }

            // Unique Slugs
            if ($category_id && $category['slug'] !== $category_data['slug']) {
                if ($this->isSlugExist($category_data['slug'])) {
                    throw new Error('Slug must be unique.');
                }
            }

            // Validation - required fields
            $required = ['title', 'slug'];
            $error = [];
            foreach ($required as $item) {
                if (!$category_data[$item]) {
                    $error[] = ucwords($item);
                }
            }

            if (!empty($error)) {
                throw new Error('Missing required fields: ' . implode(", ", $error));
            }

            // Unique Title
            if (!$category_id || (isset($category) && $category['title'] !== $category_data['title'])) {
                if ($this->isTitleExist($category_data['title'])) {
                    throw new Error('Title must be unique.');
                }
            }

            // Truncate
            $truncate_values = [
                'title' => 255,
                'slug' => 255
            ];

            foreach ($truncate_values as $key => $value) {
                if (isset($category_data[$key])) {
                    $category_data[$key] = substr($category_data[$key], 0, $value);
                }
            }

            return $category_data;
        }

        /**
         * Bind all fields in a record.
         * @param $category_data
         * @return void
         * @throws Exception
         */
        public function bindAll($category_data): void
        {
            $this->db->bindValue(':title', $category_data['title']);
            $this->db->bindValue(':description', $category_data['description']);
            $this->db->bindValue(':slug', $category_data['slug']);
        }

        /**
         * Returns true if a record exists with a provided slug.
         * @param $slug
         * @return bool
         * @throws Exception
         */
        public function isSlugExist($slug): bool
        {
            $query = "SELECT slug FROM categories WHERE slug = :slug LIMIT 1";
            $this->db->query($query);
            $this->db->bindValue(':slug', $slug);

            $this->db->single();

            return $this->db->rowCount() > 0;
        }

        /**
         * Returns true if a record exists with a provided title.
         * @param $title
         * @return bool
         * @throws Exception
         */
        public function isTitleExist($title): bool
        {
            $query = "SELECT title FROM categories WHERE title = :title LIMIT 1";
            $this->db->query($query);
            $this->db->bindValue(':title', $title);

            $this->db->single();

            return $this->db->rowCount() > 0;
        }
    }