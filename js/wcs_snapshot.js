/**
 * Javascript for WCS4 snapshot.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_search_handler();
        bind_sort_handler();
        bind_delete_handler();
    });

    /**
     * Handles the search button click event.
     */
    var bind_search_handler = function () {
        $(document).on('click.wcs4-snapshots-search', '#wcs4-snapshots-search', function (e) {
            e.preventDefault();
            reload_html_view(
                $('#search_wcs4_snapshot_title').val(),
                $('#search_wcs4_snapshot_location').val(),
                $('#search_wcs4_snapshot_created_at_from').val(),
                $('#search_wcs4_snapshot_created_at_upto').val(),
                $('.sortable.sorted').data('order-current-field'),
                $('.sortable.sorted').data('order-current-direction'),
                'fade'
            );
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_sort_handler = function () {
        $(document).on('click.wcs4-snapshot-events-list-sort', '#wcs4-snapshot-events-list-wrapper [data-order-field][data-order-direction]', function (e) {
            reload_html_view(
                $('#search_wcs4_snapshot_title').val(),
                $('#search_wcs4_snapshot_location').val(),
                $('#search_wcs4_snapshot_created_at_from').val(),
                $('#search_wcs4_snapshot_created_at_upto').val(),
                $(this).data('order-field'),
                $(this).data('order-direction'),
                'fade')
            ;
        });
    };

    /**
     * Handles the delete button click event.
     */
    var bind_delete_handler = function () {
        $(document).on('click.wcs4-delete-snapshot-button', 'tr[data-type="snapshot"] .wcs4-delete-button', function (e) {
            var entry = {
                action: 'wcs_delete_snapshot_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: $(this).closest('tr').data('id')
            };
            WCS4_LIB.modify_entry('snapshot', entry, function (data) {
                // Let's refresh the date
                reload_html_view(
                    $('#search_wcs4_snapshot_title').val(),
                    $('#search_wcs4_snapshot_location').val(),
                    $('#search_wcs4_snapshot_created_at_from').val(),
                    $('#search_wcs4_snapshot_created_at_upto').val(),
                    $('.sortable.sorted').data('order-current-field'),
                    $('.sortable.sorted').data('order-current-direction'),
                    'remove'
                );
            }, WCS4_AJAX_OBJECT['snapshot'].delete_warning);
        });
    }

    /**
     * Updates dynamically a specific snapshot vi.
     */
    var reload_html_view = function (title, location, created_at_from, created_at_upto, order_field, order_direction, action) {
        var page = $('#search_wcs4_page').val();
        var state = {
            'page': page,
            'title': title,
            'location': location,
            'created_at_from': created_at_from,
            'created_at_upto': created_at_upto,
            'order_field': order_field,
            'order_direction': order_direction,
        };
        var url = $('#wcs4-snapshots-filter').attr('action')
            + '?page=' + page
            + '&title=' + title
            + '&location=' + location
            + '&created_at_from=' + created_at_from
            + '&created_at_upto=' + created_at_upto
            + '&order_field=' + order_field
            + '&order_direction=' + order_direction
        ;
        history.pushState(state, $('title').text(), url);
        entry = {
            action: 'wcs_get_snapshots_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            title: title,
            location: location,
            created_at_from: created_at_from,
            created_at_upto: created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        var $parent = $('#wcs4-snapshot-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }
})(jQuery);