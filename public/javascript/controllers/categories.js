

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

/**
 * Handles the load event.
 */
function load() {
    $(document).on('click', '.delete-button', deleteModal);
    $('#modal-template').on('hide.bs.modal', modalHide);
}

/**
 * Generate items on the page from a data source.
 */
function generateItems(data) {
    $('.sortable-item').remove();
    container.empty();

    if (!data) {
        return;
    }

    data.forEach(item => {
        // Get id
        const id = item.category_id;

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
                    .append($('<a/>', {'href': CONTROLLER + '/' + 'category' + '/' + id})
                        .text(newTdText)
                    )
                );
            } else if (column === 'product_count') {
                newItems.append($('<td/>', tdAttributes)
                    .append($('<a/>', {'href': 'products/' + item.slug})
                        .text(newTdText)
                    )
                );
            } else {
                newItems.append($('<td/>', tdAttributes)
                    .text(newTdText)
                );
            }
        });

        // Disable delete products accounts
        let disabledButtonDelete = false;
        let toolTipTextDelete = 'Delete';
        let buttonDisabledDelete = 'danger';
        if (item.product_count !== 0) {
            disabledButtonDelete = true
            toolTipTextDelete = 'Cannot delete categories with products in it.';
            buttonDisabledDelete = 'secondary';
        }

        // Add a td at the end for view, archive, delete buttons
        newItems.append($('<td/>', {'class': 'd-none d-lg-table-cell'})
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
    inputId.attr("id","id");
    inputId.attr("name","id");
    inputId.val(id);

    $('#modal-template-title').text('Are you sure you want to delete this category?');
    $('#modal-template-body').text('Categories can only be deleted if they are empty. Move the products out into a different category first');

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
    const inputId = $('.modal-hidden-id');
    inputId.attr("id","inactive-id");
    inputId.attr("name","inactive-id");
    inputId.val('');

    const buttonContainer = $('#modal-template-footer');
    buttonContainer.empty();
}

document.addEventListener('DOMContentLoaded', load);