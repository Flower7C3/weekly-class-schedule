/**
 * Javascript for WCS4 snapshot.
 */

(function ($) {

    let SCOPE = 'snapshot';
    let FILTER_ID = '#wcs4-snapshots-filter';
    let LIST_ID = '#wcs4-snapshot-list-wrapper';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_sort_handler(LIST_ID, FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_delete_handler(SCOPE, function (data) {
            let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
            let $sortable = $('.sortable.sorted');
            reload_html_view(search_form_data, 'remove',
                $sortable.data('order-current-field'),
                $sortable.data('order-current-direction'));
        });
    });

    /**
     * Updates dynamically a specific snapshot vi.
     */
    let reload_html_view = function (search_form_data, action, order_field, order_direction) {
        let entry = {
            action: 'wcs_get_snapshots_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            log_action: search_form_data.log_action,
            log_title: search_form_data.log_title,
            log_location: search_form_data.log_location,
            created_at_from: search_form_data.created_at_from,
            created_at_upto: search_form_data.created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        let $parent = $('#wcs4-snapshot-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }
})(jQuery);