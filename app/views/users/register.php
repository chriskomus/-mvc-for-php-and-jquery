<?php
/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */


    /**
     * @var $dark
     * @var $data
     * @var $settings
     */

    // Page specific variable requirements
    $row = $data['user'];

    if (isset($row['user_id'])) {
        $page = 'Edit User';
        $action = 'edit';
        $user_id = $row['user_id'];
        $address_id = $row['address_id'];
    } else {
        $page = 'Register';
        $action = 'register';
        $user_id = false;
    }

    // Begin assembling and displaying view components
    require APP_DIRECTORY . '/views/includes/head.php'; ?>
<body>
<?php require APP_DIRECTORY . '/views/includes/nav.php'; ?>
<main class="container mt-4 main">
    <?php require APP_DIRECTORY . '/views/includes/breadcrumb.php'; ?>
    <?php require APP_DIRECTORY . '/views/includes/indicator.php'; ?>

    <!-- START PAGE CONTENT -->

    <form id="main-form" action="users/<?= $user_id ? 'update/update_address' : 'create' ?>" method="post" novalidate
          class="needs-validation">
        <div class="card mt-4 bg-<?= $dark ? 'dark text-white' : 'light' ?> mb-3">
            <div
                class="card-header"><?= $page ?>
            </div>
            <div class="card-body">
                <?php if ($user_id): ?><input type="hidden" name="id" id="id"
                                              value="<?= $user_id ?>"><?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"> <?= $user_id ? 'User Info' : '' ?></h5>
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control validate-me" id="email" name="email"
                                           data-validate-me="email" required
                                           value="<?= $user_id ? $row['email'] : '' ?>">
                                    <div class="invalid-feedback">Enter a valid email address.</div>
                                    <div class="valid-feedback">Looks good!</div>
                                </div>
                                <?php if ($user_id && isset($data['user_types'])): ?>
                                    <div class="form-group mb-3">
                                        <label for="type" class="form-label">User Permission Type (Enter as
                                            single string ie: ps)<br><small>(Options are: [g]uest, [a]dmin, [u]ser)</small></label>
                                        <input type="text" class="form-control validate-me" id="type"
                                               name="type" value="<?= $row['type'] ?>" required>
                                        <div class="invalid-feedback">Enter user permission types as single characters
                                            (ie: psi).
                                        </div>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                <?php elseif (!$user_id): ?>
                                    <div class="form-group mb-3">
                                        <label for="new-password" class="form-label">Password</label>
                                        <input type="password" class="form-control validate-me" id="new-password"
                                               name="new-password"
                                               data-validate-me="password" required>
                                        <div class="invalid-feedback">Enter a password. It must be alphanumeric and
                                            between 8 and 32 characters.
                                        </div>
                                        <div class="valid-feedback">Your password is secure!</div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control validate-me" id="match-password"
                                               name="match-password"
                                               data-validate-me="password" required>
                                        <div class="invalid-feedback">Your new password does not match.</div>
                                        <div class="valid-feedback">Your new password is a match.</div>
                                    </div>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?php if ($user_id): ?>
                            <div class="card mb-3">
                                    <div class="accordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-address">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#collapse-address" data-address="<?= $address_id ? 'true' : 'false' ?>" aria-expanded="true"
                                                        aria-controls="collapse-address">
                                                    Address
                                                </button>
                                            </h2>
                                            <div id="collapse-address" class="accordion-collapse collapse show"
                                                 aria-labelledby="heading-address" data-bs-parent="#accordion">
                                                <div class="accordion-body" id="address-section">
                                                    <?= !$address_id ? '<p>Hide the address section to update user without also updating the address.</p>' : '' ?>

                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="name">Name:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['name'] : '' ?>"
                                                               id="name" name="name" required>
                                                        <div class="invalid-feedback">A full name is required.</div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="company-name">Company
                                                            Name:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['company_name'] : '' ?>"
                                                               id="company-name" name="company-name">
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="address">Address:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['address'] : '' ?>"
                                                               id="address" name="address" required>
                                                        <div class="invalid-feedback">An address is required.</div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="address-two">Address
                                                            2:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['address_two'] : '' ?>"
                                                               id="address-two" name="address-two">
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="city">City:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['city'] : '' ?>"
                                                               id="city" name="city" required>
                                                        <div class="invalid-feedback">A city is required.</div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label"
                                                               for="province">Province/State:</label>
                                                        <select class="form-select validate-me" id="province"
                                                                name="province"
                                                                data-saved="<?= $address_id ? $row['province'] : '' ?>"
                                                                required>
                                                            <option value="">Choose a province/state...</option>
                                                        </select>
                                                        <div class="invalid-feedback">A province/state is required.
                                                        </div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="postal-code">Postal Code/ZIP
                                                            Code:</label>
                                                        <input type="text" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['postal_code'] : '' ?>"
                                                               id="postal-code" name="postal-code" required>
                                                        <div class="invalid-feedback">A postal code/zip code is
                                                            required.
                                                        </div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="country">Country:</label>
                                                        <select class="form-select validate-me" id="country"
                                                                name="country"
                                                                data-saved="<?= $address_id ? $row['country'] : '' ?>"
                                                                required>
                                                            <option value="">Choose a country...</option>
                                                        </select>
                                                        <div class="invalid-feedback">A country is required.</div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="phone">Phone Number:</label>
                                                        <input type="tel" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['phone'] : '' ?>"
                                                               id="phone" name="phone" data-validate-me="phone"
                                                               required>
                                                        <div class="invalid-feedback">A valid phone number is required.
                                                            ie: 1-222-555-5555
                                                        </div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="fax">Fax Number:</label>
                                                        <input type="tel" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['fax'] : '' ?>"
                                                               id="fax" name="fax" data-validate-me="phone">
                                                        <div class="invalid-feedback">Must be a valid fax ie:
                                                            1-222-222-2222.
                                                        </div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                    <hr>
                                                    <div class="form-group mb-3">
                                                        <label class="col-form-label" for="website">Website:</label>
                                                        <input type="tel" class="form-control validate-me"
                                                               value="<?= $address_id ? $row['website'] : '' ?>"
                                                               id="website" name="website">
                                                        <div class="invalid-feedback">The website must be a valid
                                                            format.
                                                        </div>
                                                        <div class="valid-feedback">Looks good!</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($user_id): ?>
                    <div class="form-group mb-3">
                        <label class="col-form-label" for="type">Notes:</label>
                        <textarea class="form-control" name="notes"
                                  rows="5"><?= $address_id ? $row['notes'] : '' ?></textarea>
                    </div>
                <?php endif; ?>
                <hr>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <?php if ($user_id): ?>
                        <button type="button" class="btn btn-danger delete-button" data-bs-toggle="modal"
                                data-bs-target="#modal-template" data-id="<?= $user_id ?>">Delete Account
                        </button>
                        <button type="button" class="btn btn-secondary change-password-button" data-bs-toggle="modal"
                                data-bs-target="#modal-template" data-id="<?= $user_id ?>"
                                data-address="<?= $address_id ? 'true' : 'false' ?>">Change Password
                        </button>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary" name="submit"
                            id="submit"><?= $user_id ? 'Modify' : 'Create' ?> Account
                    </button>
                </div>

            </div>
        </div>

        <!-- MODALS -->
        <?php require APP_DIRECTORY . '/views/includes/modal.php'; ?>

    </form>
    <!-- END PAGE CONTENT -->

</main>
<?php require APP_DIRECTORY . '/views/includes/modal-form.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/footer.php'; ?>
<?php require APP_DIRECTORY . '/views/includes/scripts.php'; ?>
</body>
</html>