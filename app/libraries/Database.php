<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * Create a connection to the database with prepared statements and log in information.
     * Generate an error message if the connection is not successful.
     * Using the query method, send queries to the database. Use methods to return an array of results or a single row,
     * as well as returning the number of rows.
     */
    class Database
    {
        private $statement;
        private $db;

        public function __construct()
        {
            try {
                $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
                $options = array(
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );

                $this->db = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                print "Error: " . $e->getMessage();
                die(); // Force execution to stop on errors.
            }
        }

        /**
         * Send a query to the database.
         * @param $query
         * @throws Exception
         */
        public function query($query): void
        {
            try {
                $this->statement = $this->db->prepare($query);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Bind parameter with the value.
         * Check what type the value is (integer, bool, string, or null).
         * @param $parameter
         * @param $value
         * @param $type
         * @throws Exception
         */
        public function bindValue($parameter, $value, $type = null): void
        {
            switch (is_null($type)) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }

            try {
                $this->statement->bindValue($parameter, $value, $type);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Execute the prepared statement
         * @throws Exception
         */
        public function execute()
        {
            try {
                return $this->statement->execute();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Get last insert id
         * @throws Exception
         */
        public function lastInsert()
        {
            try {
                return $this->db->lastInsertId();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }


        /**
         * Return an array of results (as arrays) from the database.
         * @throws Exception
         */
        public function results()
        {
            try {
                $this->execute();
                return $this->statement->fetchAll();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Return a hash array of key value pairs from the database.
         * @throws Exception
         */
        public function resultsAsKeyValuePairs($key, $value): array
        {
            try {
                $data = array();

                $this->execute();
                while ($row = $this->statement->fetch()) {
                    $data[$row[$key]] = $row[$value];
                }
                return $data;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Return a two-dimensional list array from the database based on the provided array of columns.
         * The function will accept any number of arguments in the form of an array of column names.
         * @return array A two-dimensional array from the database.
         * @throws Exception
         */
        public function resultsAs2DListArray()
        {
            try {
                $args = func_get_args();

                $data = array();

                $this->execute();
                while ($row = $this->statement->fetch()) {
                    $data_row = [];
                    foreach ($args as $arg) {
                        if (isset($row[$arg])) {
                            $data_row[] = $row[$arg];
                        } else {
                            $data_row[] = '';
                        }
                    }
                    $data[] = $data_row;
                }
                return $data;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Return a one dimensional list array from a given column name from the database.
         * @param $column_name - a column name from the database
         * @return array A one-dimensional list array from the database.
         * @throws Exception
         */
        public function resultsAsList($column_name): array
        {
            try {
                $data = array();

                $this->execute();
                while ($row = $this->statement->fetch()) {
                    $data[] = $row[$column_name];
                }
                return $data;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Return a specific row from the database.
         * @throws Exception
         */
        public function single()
        {
            try {
                $this->execute();
                return $this->statement->fetch();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        /**
         * Return the number of rows.
         * @throws Exception
         */
        public function rowCount()
        {
            try {
                return $this->statement->rowCount();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

    }