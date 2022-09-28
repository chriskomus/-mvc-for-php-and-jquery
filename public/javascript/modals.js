

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

/*
-------------------------------------- MODAL POPUPS -------------------------------------------
 */

/**
 * Show a modal popup of an individual product.
 */
function productModal(e) {
    // Get individual item
    const id = parseInt(e.relatedTarget.getAttribute('data-id'));
    let product = jsonControllerData.filter(function (e) {
        if (e['product_id'] === id) {
            return e;
        }
    });
    let item = product[0];

    // Clear image from modal and fix column width
    const contentContainer = $('#top-contents-container');
    contentContainer.addClass('col-sm-12');
    contentContainer.removeClass('col-sm-9');

    $('#top-image-container').remove();

    // apply data to modal
    $(this).find('.modal-name').text(item.title);
    $(this).find('.modal-title').text(item.title);
    $(this).find('.modal-category').html('<a href="catalog/' + item.category_slug + '">' + item.category + '</a>');
    $(this).find('.modal-sku').text('SKU: ' + item.sku);
    $(this).find('.modal-desc').html(decodeToHTML(item.detailed_description));
    $(this).find('#view-product').attr("href", 'catalog/product/' + item.slug)

    // display price
    $(this).find('.modal-price').text(getPrice(item.sale_price, true));

    // display image
    if (item.image) {
        const image_src = ROOT_URL + '/public/images/products/' + item.image;
        $.get(image_src)
            .done(function () {
                const contentContainer = $('#top-contents-container');
                $('#top-container').prepend(
                    $('<div/>', {'class': 'col-sm-3', 'id': 'top-image-container'})
                        .append($('<img/>', {'class': 'img-thumbnail rounded-3', 'src': image_src, 'alt': item.title})
                        )
                );
                contentContainer.removeClass('col-sm-12');
                contentContainer.addClass('col-sm-9');
            }).fail(function () {
        });
    }
}

/**
 * Show a login modal popup
 */
function loginModal(e) {
    e.preventDefault();

    const modalBody = $('#modal-form-template-body');
    modalBody.empty();

    // Set the forms action and reset to needs-validation
    const form = $('#modal-form');
    form.attr('action', 'users/login');
    form.addClass('needs-validation');
    form.removeClass('was-validated');

    // Create contents
    $('#modal-form-template-title').text('Log In');
    modalBody
        .append($('<div/>', {'class': 'form-group mb-3'})
            .append($('<label/>', {'class': 'form-label', 'for': 'email'})
                .text('Email Address')
            )
            .append($('<input/>', {
                    'class': 'form-control validate-me',
                    'type': 'email',
                    'id': 'modal-email',
                    'name': 'email',
                    'data-validate-me': 'email'
                })
            )
            .append($('<div/>', {'class': 'invalid-feedback'})
                .text('Enter a valid email address.')
            )
            .append($('<div/>', {'class': 'valid-feedback'})
                .text('Looks good!')
            )
        )
        .append($('<div/>', {'class': 'form-group mb-3'})
            .append($('<label/>', {'class': 'form-label', 'for': 'password'})
                .text('Password')
            )
            .append($('<input/>', {
                    'class': 'form-control validate-me',
                    'type': 'password',
                    'id': 'modal-password',
                    'name': 'password',
                    'data-validate-me': 'password'
                })
            )
            .append($('<div/>', {'class': 'invalid-feedback'})
                .text('Enter a password. It must be alphanumeric and between 8 and 32 characters.')
            )
            .append($('<div/>', {'class': 'valid-feedback'})
                .text('Your password is secure!')
            )
        );

    // Make these required inputs and add validation requirements
    $('#modal-email').prop('required', true);
    $('#modal-password').prop('required', true);

    const buttonContainer = $('#modal-form-template-footer');
    buttonContainer.empty();

    // Add buttons to the footer
    buttonContainer
        .append($('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'data-bs-dismiss': 'modal'
            }).text('Close')
        )
        .append($('<button/>', {
                'class': 'btn btn-primary login-button',
                'type': 'submit',
                'name': 'login',
                'id': 'login'
            }).text('Log In')
        );
}

/**
 * Close a modal popup and clear its contents.
 */
function modalFormHide(e) {
    $('#modal-form-template-footer').empty();
}