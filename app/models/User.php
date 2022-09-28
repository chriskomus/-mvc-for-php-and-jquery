<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents a User.
     */
    class User
    {
        private $db;

        public function __construct()
        {
            $this->db = new Database;
        }

        /**
         * Create a single record.
         * Validate the email and password. Truncate the values and insert into users table.
         * @throws Exception
         */
        public function create($email, $password, $confirm_password)
        {
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            // Validation - email
            if (!regex_match('email', $email)) {
                throw new Error('Invalid email. Check formatting and try again.');
            }

            if ($this->isEmailExist($email)) {
                throw new Error('The provided email address already exists in the database.');
            }

            // Validation - password
            if (($password !== $confirm_password) ||
                !regex_match('password', $password) ||
                !regex_match('password', $confirm_password)) {
                throw new Error('Invalid password. Must be 8-32 alphanumeric characters.');
            }

            $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

            // Validation - type. For security reasons, admins cannot be created even if set as a default new user type.
            $type = str_replace('a', '', DEFAULT_USER_TYPE);

            // Truncate
            $email = substr($email, 0, 255);
            $type = substr($type, 0, 8);

            // Insert into database
            $query = "INSERT INTO users (email, password, type) VALUES (:email, :password, :type)";
            $this->db->query($query);
            $this->db->bindValue(':email', $email);
            $this->db->bindValue(':password', $hash);
            $this->db->bindValue(':type', $type);

            // execute INSERT statement and return the new user.
            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Update a user's email. First validate it then check if it already exists in the database.
         * @param $id
         * @param $new_email
         * @return void
         * @throws Exception
         */
        public function updateEmail($id, $new_email)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $email = filter_var($new_email, FILTER_SANITIZE_EMAIL);

            // Validation - email
            if (!regex_match('email', $email)) {
                throw new Error('Invalid email. Check formatting and try again.');
            }

            if ($this->isEmailExist($email)) {
                throw new Error('The provided email address already exists in the database.');
            }

            $query = "UPDATE users SET email = :email WHERE user_id = :user_id";

            $this->db->query($query);
            $this->db->bindValue(':email', $email);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot update email address: ' . $e->getMessage());
            }
        }

        /**
         * Update a user's permission type. Prevent an admin from removing the last admin account if there is only
         * one left. Since only admins can grant admin access, removing all admins would make it so that there is no
         * way of adding a new admin (apart from manually changing the value in phpmyadmin).
         * @param $id
         * @param $new_type
         * @return void
         * @throws Exception
         */
        public function updateType($id, $new_type)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            // Validation - type
            $user_types_unfiltered = str_split(strtolower(filter_var($new_type, FILTER_SANITIZE_FULL_SPECIAL_CHARS)));

            $user_types_filtered = [];
            foreach (USER_TYPES as $key => $value) {
                if (in_array($key, $user_types_unfiltered, true)) {
                    $user_types_filtered[] = $key;
                }
            }

            $type = implode($user_types_filtered);

            $query = "UPDATE users SET type = :type WHERE user_id = :user_id";

            $this->db->query($query);
            $this->db->bindValue(':type', $type);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot update user permission type: ' . $e->getMessage());
            }
        }

        /**
         * Update a user password. First verify the current password, the rehash if needed, then validate the new
         * password, confirm the new passwords match, then update the password in the users table. Rehash if needed.
         * @param $id
         * @param $current_password
         * @param $new_password
         * @param $match_password
         * @return void
         * @throws Exception
         */
        public function updatePassword($id, $current_password, $new_password, $match_password)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $user = $this->get($id);

            $hashed_password = $user['password'];

            // Check current password
            if (password_verify($current_password, $hashed_password)) {
                $this->passwordRehash($id, $current_password, $hashed_password);

                // Validation - password
                if ($new_password !== $match_password) {
                    throw new Error('New passwords fields do not match.');
                } else if (!regex_match('password', $new_password) ||
                    !regex_match('password', $match_password)) {
                    throw new Error('Invalid password. Must be 8-32 alphanumeric characters.');
                }

                $hash = password_hash($new_password, PASSWORD_DEFAULT, ['cost' => 12]);

                // Update into database
                $query = "UPDATE users SET password = :password WHERE user_id = :user_id";

                $this->db->query($query);
                $this->db->bindValue(':password', $hash);
                $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

                try {
                    $this->db->execute();
                } catch (Exception $e) {
                    throw new Error('Cannot update password: ' . $e->getMessage());
                }
            } else {
                throw new Error('Current password is incorrect.');
            }
        }

        /**
         * Add an address foreign key id to a user id
         * @param $id
         * @param $address_id_to_add
         * @return void
         * @throws Exception
         */
        public function updateAddressId($id, $address_id_to_add)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            $address_id = filter_var($address_id_to_add, FILTER_SANITIZE_NUMBER_INT);

            $query = "UPDATE users SET address_id = :address_id WHERE user_id = :user_id";

            $this->db->query($query);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $this->db->bindValue(':address_id', $address_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot add address to user: ' . $e->getMessage());
            }
        }

        /**
         * Delete a user. Prevent admin accounts from being deleted. Prevent an admin from deleting the last account
         * if there is only one left. Since only admins can grant admin access, removing all users would make it so
         * that there is no way of adding a new admin or setting user permissions
         * (apart from manually changing the value in phpmyadmin).
         * @param $id
         * @throws Exception
         */
        public function delete($id)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $user_type = $this->getType($user_id);

            if (in_array('a', $user_type, true)) {
                throw new Error('Admin accounts cannot be deleted. Remove admin permissions first.');
            }

            if ($this->count() <= 1) {
                throw new Error('This is the only user account remaining. Create another account and then delete this one.');
            }

            $query = "DELETE FROM users WHERE user_id = :user_id LIMIT 1";

            $this->db->query("SET foreign_key_checks = 1");
            $this->db->query($query);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot delete user account: ' . $e->getMessage());
            }
        }

        /**
         * Get a single record. Get their associated address if it exists.
         * @param $id
         * @return mixed
         * @throws Exception
         */
        public function get($id)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            if ($this->isAddressIdSet(id: $user_id)) {
                $query = "SELECT u.*, a.*
                      FROM users u
                      JOIN addresses a ON a.address_id = u.address_id
                      WHERE u.user_id = :user_id LIMIT 1";
            } else {
                $query = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
            }

            $this->db->query($query);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            return $this->db->single();
        }

        /**
         * Get all users from the database, along with address details, if an address is connected to a user.
         * @param string $sort - the column in which to sort the products by.
         * @param $descending - asc or desc
         * @return mixed
         * @throws Exception
         */
        public function getAll(string $sort = 'email', $descending = null): mixed
        {
            $sort = filter_var($sort, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $asc_desc = ascending_or_descending_sort($descending);

            $query = "SELECT u.*, a.*
                      FROM users u
                      LEFT JOIN addresses a ON a.address_id = u.address_id
                      ORDER BY " . $sort . " " . $asc_desc;

            try {
                $this->db->query($query);
                return $this->db->results();
            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }

        }

        /**
         * Get a single record by email.
         * @throws Exception
         */
        public function getByEmail($user_email)
        {
            $email = filter_var($user_email, FILTER_SANITIZE_EMAIL);

            if ($this->isAddressIdSet(user_email: $email)) {
                $query = "SELECT u.*, a.*
                      FROM users u
                      JOIN addresses a ON a.address_id = u.address_id
                      WHERE u.email = :email LIMIT 1";
            } else {
                $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            }

            $this->db->query($query);
            $this->db->bindValue(':email', $email);

            return $this->db->single();
        }

        /**
         * Get user permission type from database and check against valid user permission type and return an array of valid types.
         * @return array
         * @throws Exception
         */
        public function getType($id)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $query = "SELECT type FROM users WHERE user_id = :user_id LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

            $user = $this->db->single();
            $user_types_unfiltered = str_split($user['type']);

            $user_types_filtered = [];
            foreach (USER_TYPES as $key => $value) {
                if (in_array($key, $user_types_unfiltered, true)) {
                    $user_types_filtered[] = $key;
                }
            }

            return $user_types_filtered;
        }

        /**
         * Validate a user's password. Rehash if needed.
         * @param $id
         * @param $password
         * @return bool
         * @throws Exception
         */
        public function validatePassword($id, $password)
        {
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $query = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':user_id', $user_id);

            $user = $this->db->single();

            $hashed_password = $user['password'];

            if (password_verify($password, $hashed_password)) {
                $this->passwordRehash($id, $password, $hashed_password);
                $valid_password = true;
            } else {
                $valid_password = false;
            }

            return $valid_password;
        }

        /**
         * Check if an email exists in the user table
         * @throws Exception
         */
        public function isEmailExist($email): bool
        {
            $query = "SELECT email FROM users WHERE email = :email LIMIT 1";

            $this->db->query($query);
            $this->db->bindValue(':email', $email);

            $this->db->single();

            return $this->db->rowCount() > 0;
        }

        /**
         * Check if an address row is connected to a user
         * @throws Exception
         */
        public function isAddressIdSet($user_email = null, $id = null): bool
        {
            $email = filter_var($user_email, FILTER_SANITIZE_EMAIL);
            $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            if ($email) {
                $query = "SELECT user_id, address_id, email FROM users WHERE email = :email LIMIT 1";
                $this->db->query($query);
                $this->db->bindValue(':email', $email);
            } elseif ($user_id) {
                $query = "SELECT user_id, address_id FROM users WHERE user_id = :user_id LIMIT 1";
                $this->db->query($query);
                $this->db->bindValue(':user_id', $user_id);
            }

            $user = $this->db->single();

            if (isset($user['address_id'])) {
                $has_address = true;
            } else {
                $has_address = false;
            }

            return $has_address;
        }

        /**
         * Count the number of users
         * @return void
         * @throws Exception
         */
        public function count()
        {
            $query = "SELECT user_id FROM users";
            $this->db->query($query);
            $this->db->results();

            return $this->db->rowCount();
        }

        /**
         * Rehash the password if needed.
         * @param $id
         * @param $password
         * @param $hashed_password
         * @return void
         * @throws Exception
         */
        public function passwordRehash($id, $password, $hashed_password): void
        {
            /* Check if the hash needs to be created again. */
            if (password_needs_rehash($hashed_password, PASSWORD_DEFAULT, ['cost' => 12])) {
                $user_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
                $hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

                // Update into database
                $query = "UPDATE users SET password = :password WHERE user_id = :user_id";

                $this->db->query($query);
                $this->db->bindValue(':password', $hash);
                $this->db->bindValue(':user_id', $user_id, PDO::PARAM_INT);

                try {
                    $this->db->execute();
                } catch (Exception $e) {
                    throw new Error($e->getMessage());
                }
            }
        }
    }