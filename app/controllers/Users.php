<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

    /**
     * This class represents the Users controller.
     */
    class Users extends Controller
    {
        private mixed $userModel;

        public function __construct()
        {
            // Required for providing settings, users info, and category lists (for menu items)
            parent::__construct();

            // Admin user type is required to access this controller.
            $this->authenticated = in_array('a', $this->data['user_type'], true);

            $this->userModel = $this->model('User');
        }

        /**
         * Default view for the controller.
         * @return void
         */
        public function index(): void
        {
            if ($this->authenticated) {
                $this->users();
            } else {
                $this->login();
            }
        }

        /**
         * Administrative settings for users.
         */
        public function users()
        {
            if ($this->authenticated) {
                $this->displayResults('email');

                $this->data += [
                    'user_types' => USER_TYPES
                ];

                $this->view('users/users', $this->data);
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * For admin users. Register a new user, or edit a user if an id is provided.
         * @param $user_id - user id
         * @return void
         */
        public function user($user_id = null): void
        {
            if ($this->authenticated) {
                $user = [];

                if (!is_null($user_id)) {
                    $user = $this->userModel->get(id: $user_id);
                }

                $this->data += [
                    'user' => $user,
                    'user_types' => USER_TYPES,
                ];

                $this->view('users/register', $this->data);
            } else {
                $this->login();
            }
        }

        /**
         * Create a user. First server side validation of the provided _POST fields.
         * Try to create a user, if it fails, return the error and provide it to display in the view.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function create()
        {
            if ($_POST && ($this->authenticated || boolean($this->data['settings']['guests_can_register']))) {
                // Sanitize user data
                $user_data = sanitize_post();

                try {
                    // Create a new user
                    $this->userModel->create($user_data['email'], $user_data['new-password'], $user_data['match-password']);
                    $user = $this->userModel->getByEmail($user_data['email']);
                    $user_id = $user['user_id'];
                    $user_type = $this->userModel->getType($user_id);

                    $error_type = 'success';
                    $error_message = 'Account created successfully.';
					
                    if (!$this->authenticated) {
                        // set session
                        if (!isset($_SESSION)) {
                            session_start();
                        }
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_type'] = $user_type;

                        $error_message .= ' You can add an address to your account if you want.';
                    }

                } catch (Error $e) {
                    // There was an error creating a new user
                    $user = [];
                    $error_type = 'warning';
                    $error_message = 'There was an error creating your account.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'user' => $user,
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                if ($this->authenticated) {
                    $this->users();
                } else {
                    $this->register();
                }

            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Update a user. Check to see if there is an address in the database, if so update it. If not, create a new one.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * Admin accounts are able to update any user id that is submitted in the form. Logged-in users without
         * admin type will only be able to edit their own id, even if they try to change the hidden input id value.
         * Similarly, only admins can change a user type, and if a user were to use dev tools to modify the form
         * and add a type input textbox, it won't allow them if they are not an admin.
         * @param null $update_address
         * @return void
         */
        public function update($update_address = null): void
        {
            if ($this->logged_in && !empty($_POST['id']) && isset($_POST['submit'])) {

                // Sanitize data
                $user_data = sanitize_post();

                // Only allow authenticated (admin) users to edit other users, otherwise always default to the session user id
                // This prevents a user from changing the hidden input id value and changing another user.
                if ($this->authenticated) {
                    $user_id = filter_var($user_data['id'], FILTER_SANITIZE_NUMBER_INT);
                }
                else {
                    $user_id = filter_var(get_session_value('user_id'), FILTER_SANITIZE_NUMBER_INT);
                }

                $user = $this->userModel->get($user_id);

                try {
                    $error_type = 'success';
                    $error_message = '';

                    if ($update_address === 'update_address') {
                        // Get address model
                        $addressModel = $this->model('Address');

                        if ($this->userModel->isAddressIdSet(id: $user_id)) {
                            // Update existing address
                            $address_id = $user['address_id'];
                            $addressModel->update(id: $address_id, address_data: $user_data);
                            $error_message = 'Address has been successfully updated. ';
                        } else {
                            // Add new address
                            $address_id = $addressModel->create(address_data: $user_data);
                            $this->userModel->updateAddressId(id: $user_id, address_id_to_add: $address_id);
                            $error_message = 'Address has been added to user account. ';
                        }
                    }

                    // update email
                    if ($user['email'] !== $user_data['email']) {
                        $this->userModel->updateEmail(id: $user_id, new_email: $user_data['email']);
                        $error_message .= 'Your email address has been updated. ';
                    }

                    // update type - only authenticated admin users can update type. This prevents a user from
                    // using development tools to create an input with name/id of 'type' on the page and submitting a type value.
                    if ($this->authenticated && isset($user_data['type']) && $user['type'] !== $user_data['type']) {
                        $this->userModel->updateType(id: $user_id, new_type: $user_data['type']);
                        $error_message .= 'User type has been successfully updated. ';
                    }

                    // Get user from the database again after it has been updated.
                    $user = $this->userModel->get($user_id);
                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'There was an error updating the user.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'user' => $user,
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];

                if ($this->authenticated) {
                    $this->users();
                } else {
                    $this->view('users/register', $this->data);
                }

            } else if ($this->logged_in && !empty($_POST['id']) && isset($_POST['delete'])) {
                $this->delete();
            } else if ($this->logged_in && !empty($_POST['id']) && isset($_POST['change-password'])) {
                $this->change_password();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Delete a user. Admin accounts are able to delete any user id that is submitted in the form.
         * Logged-in users without admin type will only be able to delete their own id, even if they try to change
         * the hidden input id value on the form.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function delete(): void
        {
            if ($_POST && $this->logged_in) {
                $addressModel = $this->model('Address');

                // Sanitize data
                $user_data = sanitize_post();

                // Only allow authenticated (admin) users to edit other users, otherwise default to the session user id
                if ($this->authenticated) {
                    $user_id = filter_var($user_data['id'], FILTER_SANITIZE_NUMBER_INT);
                }
                else {
                    $user_id = filter_var(get_session_value('user_id'), FILTER_SANITIZE_NUMBER_INT);
                }

                $user = $this->userModel->get($user_id);

                try {
                    $this->userModel->delete($user_id);
                    if ($user['address_id']) {
                        $addressModel->delete($user['address_id']);
                    }

                    $error_type = 'success';
                    $error_message = 'User account belonging to ' . $user['email'] . ' has been deleted.';
                    $user = [];

                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'Unable to delete user:' . $user['email'];
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'user' => $user,
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];
                if ($this->authenticated) {
                    $this->users();
                } else {
                    $this->logout();
                }
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Add or remove a column from the user view. The column name will be checked against the userdetails view
         * @param $column_name
         * @return void
         */
        public function add_remove_columns($column_name = null)
        {
            if ($this->authenticated) {
                $this->addRemoveColumns($column_name);
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Reset headings to default.
         * @return void
         */
        public function reset_columns()
        {
            if ($this->authenticated) {
                $this->resetColumns();
            } else {
                $this->view('catalog/404', $this->data);
            }
        }

        /**
         * Log in a user. Check if there is a matching email in the user table, then validate the password.
         * If the user wants to stay logged in, set a cookie. Start a session with user id and user type.
         * @return void
         */
        public function login(): void
        {
            if ($_POST) {
                // Sanitize user data
                $user_data = sanitize_post();

                try {
                    $user = [];
                    if ($this->userModel->isEmailExist($user_data['email'])) {
                        // A matching email is found
                        $user = $this->userModel->getByEmail($user_data['email']);
                        $user_id = $user['user_id'];
                        $user_type = $this->userModel->getType($user_id);
                        if ($this->userModel->validatePassword($user_id, $user_data['password'])) {
                            // Password is valid, set session and cookie
                            if (!isset($_SESSION)) {
                                session_start();
                            }
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['user_type'] = $user_type;

                            $error_type = 'success';
                            $error_message = 'You have successfully logged in.';
                        } else {
                            $error_type = 'warning';
                            $error_message = 'The password is incorrect.';
                        }
                    } else {
                        // No matching email
                        $error_type = 'warning';
                        $error_message = 'The account was not found.';
                    }

                    $this->data += [
                        'user' => $user,
                        'error_type' => $error_type,
                        'error_message' => $error_message,
                    ];

                    if ($error_type === 'success') {
                        header("Location: " . ROOT_URL . "/catalog");
                    } else {
                        $this->view('users/login', $this->data);
                    }
                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'There was an error logging in.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }

                    $this->data += [
                        'error_type' => $error_type,
                        'error_message' => $error_message,
                    ];
                    $this->view('users/register', $this->data);
                }
            } else {
                if ($this->logged_in) {
                    $this->register();
                } else {
                    $this->view('users/login', $this->data);
                }
            }

        }

        /**
         * Clear session, delete cookies and log user out.
         * @return void
         */
        public function logout()
        {
            session_start();
            $_SESSION = [];

            header("Location: " . ROOT_URL . "/catalog");
        }

        /**
         * Register a new user, or edit a user if logged in. If guests can't register an account, redirect to login.
         * @return void
         */
        public function register(): void
        {
            if ($this->logged_in || boolean($this->data['settings']['guests_can_register'])) {
                $user = [];
                if ($this->logged_in) {
                    $user = $this->userModel->get(id: $this->data['user_id']);
                }

                $this->data += [
                    'user' => $user,
                ];

                $this->view('users/register', $this->data);
            }
            else {
                $this->login();
            }
        }

        /**
         * Change a user's password. Admin accounts are able to update any user id that is submitted in the form.
         * Logged-in users without admin type will only be able to edit their own id, even if they try to change
         * the hidden input id value on the form.
         * Only accessible by POST. If controller is accessed by url it will redirect to 404.
         * @return void
         */
        public function change_password(): void
        {
            if ($_POST && $this->logged_in) {
                // Sanitize data
                $user_data = sanitize_post();

                // Only allow authenticated (admin) users to edit other users, otherwise default to the session user id
                if ($this->authenticated) {
                    $user_id = filter_var($user_data['id'], FILTER_SANITIZE_NUMBER_INT);
                }
                else {
                    $user_id = filter_var(get_session_value('user_id'), FILTER_SANITIZE_NUMBER_INT);
                }

                $user = $this->userModel->get($user_id);

                try {
                    // Verify password
                    $this->userModel->updatePassword(id: $user_id,
                        current_password: $user_data['current-password'],
                        new_password: $user_data['new-password'],
                        match_password: $user_data['match-password']);

                    $error_type = 'success';
                    $error_message = 'Password successfully changed.';

                } catch (Error $e) {
                    // There was an error updating the address
                    $error_type = 'warning';
                    $error_message = 'Unable to update password. Check your current password and try again.';
                    if (DETAILED_ERROR_MSG) {
                        $error_message .= '</br>' . $e->getMessage();
                    }
                }

                // Send data to the view.
                $this->data += [
                    'user' => $user,
                    'error_type' => $error_type,
                    'error_message' => $error_message
                ];
                if ($this->authenticated) {
                    $this->users();
                } else {
                    $this->view('users/register', $this->data);
                }
            } else {
                $this->view('catalog/404', $this->data);
            }
        }
    }