<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * This class represents an Address.
     */
    class Address
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
        public function create($address_data)
        {
            $validated_address_data = $this->validate($address_data);

            // Insert into database
            $query = "INSERT INTO addresses (name, contact_email, website, phone, fax, company_name, 
                       address, address_two, city, province, postal_code, country, notes)
            VALUES (:name, :contact_email, :website, :phone, :fax, :company_name, :address, 
                    :address_two, :city, :province, :postal_code, :country, :notes)";
            $this->db->query($query);
            $this->bindAll($validated_address_data);

            // execute INSERT statement and return the new user.
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
        public function update($id, $address_data)
        {
            $validated_address_data = $this->validate($address_data);
            $address_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            // Update row in database
            $query = "UPDATE addresses SET name = :name, contact_email = :contact_email , website = :website, 
                     phone = :phone, fax = :fax, company_name = :company_name, address = :address,
                     address_two = :address_two, city = :city, province = :province, postal_code = :postal_code,
                     country = :country, notes = :notes WHERE address_id = :address_id";

            $this->db->query($query);
            $this->bindAll($validated_address_data);
            $this->db->bindValue(':address_id', $address_id, PDO::PARAM_INT);

            // execute UPDATE statement
            try {
                $this->db->execute();

            } catch (Exception $e) {
                throw new Error('Database error: ' . $e->getMessage());
            }
        }

        /**
         * Delete a single record.
         */
        public function delete($id)
        {
            $address_id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

            $query = "DELETE FROM addresses WHERE address_id = :address_id LIMIT 1";

            $this->db->query("SET foreign_key_checks = 1");
            $this->db->query($query);
            $this->db->bindValue(':address_id', $address_id, PDO::PARAM_INT);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                throw new Error('Cannot delete address: ' . $e->getMessage());
            }
        }

        /**
         * Provide data validation for a single record.
         * @param $address_data
         * @return mixed
         */
        public function validate($address_data): mixed
        {
            foreach ($address_data as $key => $value) {
                $address_data[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            }

            // Validation - required fields
            $required = ['name', 'address', 'city', 'province', 'postal-code', 'country'];
            $error = [];
            foreach ($required as $item) {
                if (!$address_data[$item]) {
                    $error[] = ucwords($item);
                }
            }

            if (!empty($error)) {
                throw new Error('Missing required fields: ' . implode(", ", $error));
            }

            // Validation - postal code
            if ($address_data['country'] === 'Canada') {
                if (!regex_match('postalCode', $address_data['postal-code'])) {
                    throw new Error('Invalid postal code. Check formatting and try again.');
                }
            } elseif ($address_data['country'] === 'United States') {
                if (!regex_match('zipCode', $address_data['postal-code'])) {
                    throw new Error('Invalid zip code. Check formatting and try again.');
                }
            }

            // Validation - phone and fax
            if ($address_data['phone'] && !regex_match('phone', $address_data['phone'])) {
                throw new Error('Invalid phone number. Check formatting and try again.');
            }
            if ($address_data['fax'] && !regex_match('phone', $address_data['fax'])) {
                throw new Error('Invalid fax number. Check formatting and try again.');
            }

            // Validation - email
            if (!regex_match('email', $address_data['email'])) {
                throw new Error('Invalid email. Check formatting and try again.');
            }

            // Truncate
            $truncate_values = [
                'name' => 255,
                'company-name' => 255,
                'address' => 255,
                'address-two' => 255,
                'city' => 100,
                'province' => 45,
                'postal-code' => 16,
                'country' => 64,
                'contact-email' => 255,
                'website' => 255,
                'phone' => 45,
                'fax' => 45,
            ];
            foreach ($truncate_values as $key => $value) {
                if (isset($address_data[$key])) {
                    $address_data[$key] = substr($address_data[$key], 0, $value);
                }
            }

            return $address_data;
        }

        /**
         * Bind all fields in a record
         * @param $address_data
         * @return void
         * @throws Exception
         */
        public function bindAll($address_data): void
        {
            $this->db->bindValue(':name', $address_data['name']);
            $this->db->bindValue(':contact_email', $address_data['email']);
            $this->db->bindValue(':website', $address_data['website']);
            $this->db->bindValue(':phone', $address_data['phone']);
            $this->db->bindValue(':fax', $address_data['fax']);
            $this->db->bindValue(':company_name', $address_data['company-name']);
            $this->db->bindValue(':address', $address_data['address']);
            $this->db->bindValue(':address_two', $address_data['address-two']);
            $this->db->bindValue(':city', $address_data['city']);
            $this->db->bindValue(':province', $address_data['province']);
            $this->db->bindValue(':postal_code', $address_data['postal-code']);
            $this->db->bindValue(':country', $address_data['country']);
            $this->db->bindValue(':notes', $address_data['notes']);
        }

    }