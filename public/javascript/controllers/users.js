

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

// Store Address div in variable to delete elements and regenerate, in order to override validation when changing password.
let addressSection = null;

// JSON data and API URLs
let countriesStatesData = [];
let extraUrls = [['countriesStates', encodeURI('https://countriesnow.space/api/v0.1/countries/states')]];

/**
 * Controller specific API data. This is called from main.js after Promise.all has been fulfilled.
 */
function loadSiteWithExtraData() {
    countriesStatesData = jsonAllData.find(x => x.name === 'countriesStates').results;
    populateCountryDropdown();
    pageLoadTime();
}


/**
 * Handles the load event.
 */
function load() {
    $('.delete-button').on('click', deleteModal);
    $('.accordion-button').on('click', hideAddress);
    $('.change-password-button').on('click', changePassword);
    $('#modal-template').on('hide.bs.modal', modalHide);
    $(document).on('change', '#country', changeRegions);

    $(document).on('click', '#delete', overRideSubmit);
    $(document).on('click', '.delete-button', deleteModal);
}

/**
 * Generate items on the page from a data source.
 */
function generateItems(data) {
    $('.sortable-item').remove();
    container.empty();

    data.forEach(item => {
        // Get id
        const id = item.user_id;

        // Generate row
        const newItems = (
            $('<tr/>', {'class': 'sortable-item'})
        );

        // Add td for each visible column
        jsonColumns.forEach(function (column, index) {
            let tdAttributes = {'class': ''};

            let newTdText = item[column];
            if (newTdText) {
                if (newTdText.length > 75) {
                    newTdText = newTdText.toString().substring(0, 75) + '...';
                }
            }
            if (index === 0) {
                newItems.append($('<td/>', tdAttributes)
                    .append($('<a/>', {'href': CONTROLLER + '/' + 'user' + '/' + id})
                        .text(newTdText)
                    )
                );
            } else if (column === 'type') {
                newItems.append($('<td/>', tdAttributes)
                        // .append($('<a/>', {'href': 'kits/' + id})
                        .text(newTdText)
                    // )
                );
            } else {
                newItems.append($('<td/>', tdAttributes)
                    .text(newTdText)
                );
            }
        });

        // Disable delete admin accounts
        let disabledButton = false;
        let toolTipText = 'Delete';
        let buttonDisabled = 'danger';
        if (item.type.includes('a')) {
            disabledButton = true
            toolTipText = 'Cannot delete Admin accounts.';
            buttonDisabled = 'secondary';
        }

        // Add a td at the end for view, archive, delete buttons
        newItems.append($('<td/>', {'class': 'd-none d-lg-table-cell'})
            .append($('<div/>', {'class': 'custom-tooltip no-underline'})
                .append($('<button/>', {
                        'class': 'btn btn-link btn-sm text-' + buttonDisabled + ' delete-button',
                        'type': 'button',
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modal-template',
                        'data-id': id,
                        'disabled': disabledButton
                    })
                        .html('<i class="fa-solid fa-delete-left"></i>')
                )
                .append($('<span/>', {'class': 'custom-tooltip-text'})
                    .text(toolTipText)
                )
            )
        );

        container.append(newItems);
    });

    pagination();
}

/**
 * Populate the country dropdown using CountriesNow api.
 */
function populateCountryDropdown() {
    const countryDropdown = $('#country');
    const savedCountry = $('#country').data('saved');

    countryDropdown.empty();
    countryDropdown.append($('<option/>', {
        text: 'Choose a country...',
        value: ''
    }));

    $.each(countriesStatesData.data, function (key, country) {
        countryDropdown.append($('<option/>', {
            value: country.name,
            text: country.name,
        }));
    });

    if (savedCountry) {
        countryDropdown.val(savedCountry);
    } else {
        countryDropdown.val('Canada');
    }

    changeRegions(savedCountry);

}

/**
 * Update the province/state based on the selected country using countriesnow api.
 */
function changeRegions(savedCountry = null) {
    const countryDropdown = $('#country');
    const selectedCountry = countryDropdown.val();

    const provinceDropdown = $('#province');
    const savedProvince = $('#province').data('saved');

    provinceDropdown.empty();
    provinceDropdown.append($('<option/>', {
        text: 'Choose a province/state...',
        value: ''
    }));

    if (selectedCountry) {
        const states = countriesStatesData.data.find(x => x.name === selectedCountry).states;

        $.each(states, function (key, state) {
            provinceDropdown.append($('<option/>', {
                value: state.state_code,
                text: state.name
            }));
        });

        if (savedProvince) {
            provinceDropdown.val(savedProvince);
        }
    }

    const postalCodeInput = $('#postal-code');
    const postalCodeValue = postalCodeInput.val();
    if (countryDropdown.val() === 'Canada') {
        postalCodeInput.data('validate-me', 'postalCode');
    } else if (countryDropdown.val() === 'United States') {
        postalCodeInput.data('validate-me', 'zipCode');
    } else {
        postalCodeInput.data('validate-me', '');
    }

    // the postal code field needs to be blank to start a new validation, but
    // this prevents the form from automatically erasing the postal code on page load.
    if (selectedCountry !== savedCountry) {
        postalCodeInput.val('');
    }
}

/**
 * Show a modal popup to delete a user
 */
function deleteModal(e) {
    let id = $(this).attr('data-id');
    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "id");
    inputId.attr("name", "id");
    inputId.val(id);

    $('#modal-template-title').text('Delete User Account');
    $('#modal-template-body').text('Are you sure you want to delete this user account?');

    const buttonContainer = $('#modal-template-footer');
    buttonContainer.empty();

    buttonContainer
        .append($('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'data-bs-dismiss': 'modal'
            }).text('Close')
        )
        .append($('<button/>', {
                'class': 'btn btn-danger',
                'type': 'submit',
                'name': 'delete',
                'id': 'delete'
            }).text('Yes, delete permanently')
        );
}


/**
 * Close a modal popup
 */
function modalHide(e) {
    $('#address-section').append(addressSection);

    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "inactive-id");
    inputId.attr("name", "inactive-id");
    inputId.val('');

    const modalBody = $('#modal-template-body');
    modalBody.empty();

    const buttonContainer = $('#modal-template-footer');
    buttonContainer.empty();
}

function changePassword(e) {
    // Store Address div in variable to delete elements and regenerate, in order to override validation when changing password.
    if ($(this).attr('data-address') === 'false') {
        addressSection = $('#address-section').html();
        $('#address-section').empty();
    }

    const modalBody = $('#modal-template-body');
    modalBody.empty();

    $('#modal-template-title').text('Change Password');
    modalBody
        .append($('<div/>', {'class': 'form-group mb-3'})
            .append($('<label/>', {'class': 'form-label', 'for': 'current-password'})
                .text('Current Password')
            )
            .append($('<input/>', {
                    'class': 'form-control validate-me',
                    'type': 'password',
                    'id': 'current-password',
                    'name': 'current-password',
                    'data-validate-me': 'password'
                })
            )
            .append($('<div/>', {'class': 'invalid-feedback'})
                .text('Enter a password. It must be alphanumeric and between 8 and 32 characters.')
            )
            .append($('<div/>', {'class': 'valid-feedback'})
                .text('Your password is secure')
            )
        )
        .append($('<div/>', {'class': 'form-group mb-3'})
            .append($('<label/>', {'class': 'form-label', 'for': 'new-password'})
                .text('New Password')
            )
            .append($('<input/>', {
                    'class': 'form-control validate-me new-password',
                    'type': 'password',
                    'id': 'new-password',
                    'name': 'new-password',
                    'data-validate-me': 'password'
                })
            )
            .append($('<div/>', {'class': 'invalid-feedback'})
                .text('Enter a password. It must be alphanumeric and between 8 and 32 characters.')
            )
            .append($('<div/>', {'class': 'valid-feedback'})
                .text('Your password is secure')
            )
        )
        .append($('<div/>', {'class': 'form-group mb-3'})
            .append($('<label/>', {'class': 'form-label', 'for': 'match-password'})
                .text('Confirm Password')
            )
            .append($('<input/>', {
                    'class': 'form-control validate-me',
                    'type': 'password',
                    'id': 'match-password',
                    'name': 'match-password',
                    'data-validate-me': 'password'
                })
            )
            .append($('<div/>', {'class': 'invalid-feedback'})
                .text('Your new password does not match.')
            )
            .append($('<div/>', {'class': 'valid-feedback'})
                .text('Your new password is a match.')
            )
        );

    // Make these required inputs and add validation requirements
    $('#current-password').prop('required', true);
    $('.new-password').prop('required', true);
    $('#match-password').prop('required', true);

    const buttonContainer = $('#modal-template-footer');
    buttonContainer.empty();

    buttonContainer
        .append($('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'data-bs-dismiss': 'modal'
            }).text('Close')
        )
        .append($('<button/>', {
                'class': 'btn btn-primary',
                'type': 'submit',
                'name': 'change-password',
                'id': 'change-password'
            }).text('Change Password')
        );
}

/**
 * Show and hide address content and validation requirements when clicking the accordion.
 */
function hideAddress(e) {
    if ($(this).hasClass('collapsed')) {

        // Store Address div in variable to delete elements and regenerate, in order to override validation when changing password.
        if ($(this).attr('data-address') === 'false') {
            addressSection = $('#address-section').html();
            $('#address-section').empty();
        }

        // Change form action to only update user info and not address
        $('#main-form').attr('action', 'users/update');

    } else {

        $('#address-section').append(addressSection);
    }

}

/**
 * Remove all required fields so that user accounts without addresses can be deleted.
 * This prevents validation errors that prevent a deletion.
 */
function overRideSubmit(e) {
    $('input').prop('required', false);
    $('select').prop('required', false);
    $("#main-form").submit();
}


document.addEventListener('DOMContentLoaded', load);