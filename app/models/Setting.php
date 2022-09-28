<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents a site-wide setting. These general settings are stored in the database.
     * Critical settings (such as database connections, menu items, etc.) are set in /app/config/config.php.
     */
    class Setting
    {
        private Database $db;

        public function __construct()
        {
            $this->db = new Database;
        }

        /**
         * Update a single record.
         * @param $id
         * @param $value
         * @return bool
         * @throws Exception
         */
        public function update($id, $value): bool
        {
            $setting_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $setting_value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "UPDATE settings SET value = :value WHERE setting_id = :setting_id";

            $this->db->query($query);
            $this->db->bindValue(':value', $setting_value);
            $this->db->bindValue(':setting_id', $setting_id, PDO::PARAM_INT);

            if ($this->db->execute()) {
                $isSet = true;
            } else {
                $isSet = false;
            }

            return $isSet;
        }

        /**
         * Get a single record by id.
         * @param $id - a setting id
         * @return mixed
         * @throws Exception
         */
        public function get($id)
        {
            $setting_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $query = "SELECT * FROM settings WHERE setting_id = :setting_id LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':setting_id', $setting_id, PDO::PARAM_INT);
            return $this->db->single();
        }

        /**
         * Get all records.
         * @throws Exception
         */
        public function getAll()
        {
            $this->db->query("SELECT * FROM settings");

            return $this->db->results();
        }

        /**
         * Get all records as key value pairs.
         * @throws Exception
         */
        public function getAllAsKeyValuePairs()
        {
            $this->db->query("SELECT * FROM settings");

            return $this->db->resultsAsKeyValuePairs('setting', 'value');
        }

        /**
         * Get all column names from the provided sql view.
         * @param $table_name -  The name of the sql view
         * @return array
         * @throws Exception
         */
        public function getAllColumns($table_name): array
        {
            $column_names = [];

            $query = ("SELECT DISTINCT column_name
                                      FROM information_schema.views v
                                      JOIN information_schema.columns c on c.table_schema = v.table_schema
                                      AND c.table_name = v.table_name
                                      WHERE v.table_name = :table_name");

            $this->db->query($query);
            $this->db->bindValue(':table_name', $table_name);

            $column_data = $this->db->results();

            foreach ($column_data as $outer_key => $array) {
                foreach ($array as $inner_key => $value)
                {
                    if (!(int)$inner_key) {
                        $column_names[] = $value;
                    }
                }
            }

            return array_unique($column_names);
        }

        /**
         * Get a single record by setting.
         * @param $setting - a setting name
         * @return mixed
         * @throws Exception
         */
        public function getBySetting($setting)
        {
            $setting = filter_var($setting, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $query = "SELECT * FROM settings WHERE setting = :setting LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':setting', $setting);
            return $this->db->single();
        }

        /**
         * Get a single id by setting.
         * @param $setting
         * @return mixed|null
         * @throws Exception
         */
        public function getIdBySetting($setting)
        {
            $id = null;
            $settings = $this->getAll();

            foreach ($settings as $item) {
                if ($setting === $item['setting']) {
                    $id = $item['setting_id'];
                }
            }

            return $id;
        }

        /**
         * Take a string of column names, explode on comma character, then check it against the provided view name
         * to ensure that those column names actually exist. If so, add to the returned array of verified column names.
         * Views are used instead of tables as it allows more flexibility in using columns from other tables.
         * @param $columns_as_string
         * @param $view_name
         * @return array
         * @throws Exception
         */
        public function validateColumns($columns_as_string, $view_name)
        {
            $unverified_columns = explode(',', $columns_as_string);
            $verified_columns = [];
            $column_names = $this->getAllColumns($view_name);

            foreach ($unverified_columns as $unverified_column_name) {
                if (in_array($unverified_column_name, $column_names, true) || in_array($unverified_column_name . "_id", $column_names, true)) {
                    $verified_columns[] = $unverified_column_name;
                }
            }

            return $verified_columns;
        }
    }