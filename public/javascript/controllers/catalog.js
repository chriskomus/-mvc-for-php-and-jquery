

/*
 * Copyright (c) 2022 Chris Komus - GNU GPLv3
 * https://github.com/chriskomus/
 */

/**
 * Handles the load event.
 */
function load() {
    $('#product-detail').on('show.bs.modal', productModal);
}

/**
 * Generate items on the page from a data source.
 */
function generateItems(data) {
    container.empty();
    currentPage = 1;

    if (!data) {
        return;
    }

    data.forEach(item => {
        // Generate items on the page
        const newItems = (
            $('<div/>', {'class': 'col-lg-4 col-sm-6 mb-3 sortable-item'})
                .append($('<div/>', {'class': 'card h-100'})
                    .append($('<div/>', {'class': 'card-body'})
                        .append($('<div/>', {'class': 'row', 'id': 'top-container-' + item.product_id})
                            .append($('<div/>', {
                                    'class': 'col-sm-12',
                                    'id': 'top-contents-container-' + item.product_id
                                })
                                    .append($('<h5/>', {'class': 'card-title'})
                                        .text(item.title)
                                    )
                                    .append($('<h6/>', {'class': 'card-subtitle text-muted'})
                                        .append($('<a/>', {'href': 'catalog/' + item.category_slug})
                                            .text(item.category)
                                        )
                                    )
                            )
                        )
                    )
                    .append($('<div/>', {'class': 'card-body'})
                        .append($('<button/>', {
                                'class': 'btn btn-primary',
                                'type': 'button',
                                'data-bs-toggle': 'modal',
                                'data-bs-target': '#product-detail',
                                'data-id': item.product_id
                            })
                                .text('Quick View...')
                        )
                    )
                    .append($('<div/>', {'class': 'card-footer'})
                        .append($('<h5/>', {'class': 'text-warning'})
                            .text(getPrice(item.sale_price, true))
                        )
                    )
                )
        );


        // display thumbnail image. Append _thumb to end of filename, before the file extension
        if (item.image) {
            const dotIndex = item.image.lastIndexOf(".");
            const imageSrc = ROOT_URL + '/public/images/products/' + item.image.substring(0, dotIndex) + '_thumb' + item.image.substring(dotIndex);
            $.get(imageSrc)
                .done(function () {
                    const contentContainer = $('#top-contents-container-' + item.product_id);
                    $('#top-container-' + item.product_id).prepend(
                        $('<div/>', {'class': 'col-sm-4'})
                            .append($('<img/>', {'class': 'rounded img-fluid', 'src': imageSrc, 'alt': item.title})
                            )
                    );
                    contentContainer.removeClass('col-sm-12');
                    contentContainer.addClass('col-sm-8');
                }).fail(function () {
            });
        }

        container.append(newItems);
    });
    pagination();
}

document.addEventListener('DOMContentLoaded', load);