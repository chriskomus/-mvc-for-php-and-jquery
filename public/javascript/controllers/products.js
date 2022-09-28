

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

/**
 * Handles the load event.
 */
function load() {
    $('#product-detail').on('show.bs.modal', productModal);
    $(document).on('click', '.delete-button', deleteModal);
    $(document).on('click', '.image-upload-button', uploadImageModal);
    $(document).on('click', '.image-delete-button', deleteImageModal);
    $(document).on('click', '.archive-button', archiveModal);
    $('#modal-template').on('hide.bs.modal', modalHide);
    $(document).on('click', '#upload-image', submitValidationCheck);
}

/**
 * Generate items on the page from a data source.
 */
function generateItems(data) {
    $('.sortable-item').remove();
    container.empty();

    data.forEach(item => {
        // Get id
        const id = item.product_id;

        // Set muted text for archived items
        let archived = '';
        if (item.enabled === 0) {
            archived = 'text-muted';
        }

        // Generate row
        const newItems = (
            $('<tr/>', {'class': 'sortable-item', 'id': 'product-' + id})
        );

        // Add td for each visible column
        jsonColumns.forEach(function (column, index) {
            let tdAttributes = {'class': archived};

            let newTdText = item[column];
            if (newTdText) {
                if (newTdText.length > 75) {
                    newTdText = newTdText.toString().substring(0, 75) + '...';
                }
            }
            if (index === 0) {
                newItems.append($('<td/>', tdAttributes)
                    .append($('<a/>', {'href': CONTROLLER + '/' + 'product' + '/' + id})
                        .text(newTdText)
                    )
                );
            } else if (column === 'category') {
                newItems.append($('<td/>', tdAttributes)
                    .append($('<a/>', {'href': CONTROLLER + '/' + item.category_slug})
                        .text(newTdText)
                    )
                );
            } else if (column === 'sale_price' || column === 'purchase_price') {
                newItems.append($('<td/>', tdAttributes)
                    .text(getPrice(newTdText))
                );

            } else if (column === 'quantity') {
                if (!archived) {
                    if (item.quantity === 0) {
                        tdAttributes = {'class': 'text-danger'};
                    } else if (item.quantity < item.reorder) {
                        tdAttributes = {'class': 'text-warning'};
                    }
                }
                newItems.append($('<td/>', tdAttributes)
                    .text(newTdText)
                );
            } else if (column === 'image') {
                newItems.append($('<td/>', tdAttributes)
                    .append($('<div/>', {'class': 'w-50', 'id': 'image-column-' + id})
                    )
                );
            } else {
                newItems.append($('<td/>', tdAttributes)
                    .text(newTdText)
                );
            }
        });

        // display thumbnail image. Append _thumb to end of filename, before the file extension
        if (item.image) {
            const dotIndex = item.image.lastIndexOf(".");
            const imageSrc = ROOT_URL + '/public/images/products/' + item.image.substring(0, dotIndex) + '_thumb' + item.image.substring(dotIndex);
            $.get(imageSrc)
                .done(function () {
                    let archivedImage = '';
                    if (item.enabled === 0) {
                        archivedImage = ' greyscale-img';
                    }

                    $('#image-column-' + id)
                        .append($('<img/>', {'class': 'rounded img-fluid' + archivedImage, 'src': imageSrc, 'alt': item.title})
                    );
                }).fail(function () {
            });
        }

        // Disable delete products accounts
        let disabledButtonDelete = false;
        let toolTipTextDelete = 'Delete';
        let buttonDisabledDelete = 'danger';

        // Unarchive button
        let toolTipTextArchive = 'Archive';
        if (item.enabled === 0) {
            toolTipTextArchive = 'Un-Archive';
        }

        // Add a td at the end for view, archive, delete buttons
        newItems.append($('<td/>', {'class': 'd-none d-lg-table-cell'})
            .append($('<div/>', {'class': 'custom-tooltip no-underline'})
                .append($('<button/>', {
                        'class': 'btn btn-link btn-sm',
                        'type': 'button',
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#product-detail',
                        'data-id': id
                    })
                        .html('<i class="fa-solid fa-eye"></i>')
                )
                .append($('<span/>', {'class': 'custom-tooltip-text'})
                    .text('Preview')
                )
            )
            .append($('<div/>', {'class': 'custom-tooltip no-underline'})
                .append($('<button/>', {
                        'class': 'btn btn-link btn-sm text-info archive-button',
                        'type': 'button',
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modal-template',
                        'data-id': id,
                        'data-enabled': item.enabled
                    })
                        .html('<i class="fa-solid fa-box-archive"></i>')
                )
                .append($('<span/>', {'class': 'custom-tooltip-text'})
                    .text(toolTipTextArchive)
                )
            )
            .append($('<div/>', {'class': 'custom-tooltip no-underline'})
                .append($('<button/>', {
                        'class': 'btn btn-link btn-sm text-' + buttonDisabledDelete + ' delete-button',
                        'type': 'button',
                        'data-bs-toggle': 'modal',
                        'data-bs-target': '#modal-template',
                        'data-id': id,
                        'disabled': disabledButtonDelete
                    })
                        .html('<i class="fa-solid fa-delete-left"></i>')
                )
                .append($('<span/>', {'class': 'custom-tooltip-text'})
                    .text(toolTipTextDelete)
                )
            )
        );

        container.append(newItems);
    });

    pagination();
}


/**
 * Show a modal popup to delete a product
 */
function deleteModal(e) {
    let id = $(this).attr('data-id');
    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "id");
    inputId.attr("name", "id");
    inputId.val(id);

    $('#modal-template-title').text('Are you sure you want to delete this product?');
    $('#modal-template-body').text('Products can only be deleted if they are not attached to any sales orders, purchase orders, or\n' +
        'general adjustments. Archive the product to hide it from view, but keep it in the database.');

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
 * Show a modal popup to archive a product
 */
function archiveModal(e) {
    let id = $(this).attr('data-id');
    let enabled = $(this).attr('data-enabled');
    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "id");
    inputId.attr("name", "id");
    inputId.val(id);

    let archive = 'archive';
    if (enabled === '0') {
        archive = 'un-archive';
    }

    $('#modal-template-title').text('Are you sure you want to ' + archive + ' this product?');

    if (enabled === '1') {
        $('#modal-template-body').text('Products that are archived are still available, but will not be displayed in the catalog view.');
    } else {
        $('#modal-template-body').text('Enabled products will be visible in the catalog view.');
    }

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
                'name': 'archive',
                'id': 'archive'
            }).text('Yes')
        );
}

/**
 * Upload an image
 */
function uploadImageModal(e) {
    e.preventDefault();

    let id = $(this).attr('data-id');
    const modalBody = $('#modal-form-template-body');
    modalBody.empty();

    // Set the forms action and reset to needs-validation
    const form = $('#modal-form');
    form.prop('enctype', 'multipart/form-data');
    form.attr('action', 'products/upload_image');
    form.addClass('needs-validation');
    form.removeClass('was-validated');

    // Create contents
    $('#modal-form-template-title').text('Upload an Image');
    modalBody
        .append($('<input/>', {
                'class': 'form-control validate-me',
                'type': 'hidden',
                'value': id,
                'name': 'id'
            })
        )
        .append($('<div/>', {'class': 'input-group'})
            .append($('<input/>', {
                    'class': 'form-control validate-me',
                    'type': 'file',
                    'id': 'image-file',
                    'name': 'image-file',
                    'data-validate-me': 'email'
                })
            )
            .append($('<button/>', {
                    'class': 'btn btn-secondary',
                    'type': 'submit',
                    'name': 'upload-image',
                    'id': 'upload-image'
                }).text('Upload')
            )
        );

    // Make these required inputs and add validation requirements
    $('#image-file').prop('required', true);

    const buttonContainer = $('#modal-form-template-footer');
    buttonContainer.empty();

    // Add buttons to the footer
    buttonContainer
        .append($('<button/>', {
                'class': 'btn btn-secondary',
                'type': 'button',
                'data-bs-dismiss': 'modal'
            }).text('Close')
        );
}

/**
 * Show a modal popup to delete a product
 */
function deleteImageModal(e) {
    let id = $(this).attr('data-id');
    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "id");
    inputId.attr("name", "id");
    inputId.val(id);

    $('#modal-template-title').text('Delete product image?');
    $('#modal-template-body').text('Are you sure you want to delete the image for this product?');

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
                'name': 'delete-image',
                'id': 'delete-image'
            }).text('Delete')
        );
}

/**
 * Close a modal popup
 */
function modalHide(e) {
    const inputId = $('.modal-hidden-id');
    inputId.attr("id", "inactive-id");
    inputId.attr("name", "inactive-id");
    inputId.val('');

    const buttonContainer = $('#modal-template-footer');
    buttonContainer.empty();
}

/**
 * Remove all required fields so that an image.
 * This prevents validation errors that prevent the form from submitting.
 */
function overRideSubmit(e) {
    e.preventDefault();
    $('input').prop('required', false);
    $('select').prop('required', false);
    // $('#main-form').prop('enctype', 'multipart/form-data');
    $("#main-form").submit();
}

document.addEventListener('DOMContentLoaded', load);