/**
 * Javascript for WCS4 journal.
 */

(function ($) {

    let SCOPE = 'journal';
    let FILTER_ID = '#wcs4-journals-filter';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_sort_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_edit_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_copy_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_delete_handler(SCOPE, function (data) {
            let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
            let $sortable = $('.sortable.sorted');
            reload_html_view(search_form_data, 'remove',
                $sortable.data('order-current-field'),
                $sortable.data('order-current-direction'));
        });
        bind_submit_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        $('.wcs4-submit-journal-form').click(function (e) {
            let entry;
            e.preventDefault();
            entry = {
                action: 'wcs_add_or_update_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value('wcs4_journal_subject'),
                teacher_id: WCS4_LIB.form_field_value('wcs4_journal_teacher'),
                student_id: WCS4_LIB.form_field_value('wcs4_journal_student'),
                date: $('#wcs4_journal_date').val(),
                start_time: $('#wcs4_journal_start_time').val(),
                end_time: $('#wcs4_journal_end_time').val(),
                topic: $('#wcs4_journal_topic').val()
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    // Let's refresh the day
                    let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
                    reload_html_view(search_form_data, 'fade',
                        $('.sortable.sorted').data('order-current-field'),
                        $('.sortable.sorted').data('order-current-direction'));
                    // Clear topic.
                    WCS4_LIB.reset_to_add_mode('journal');
                }
            });
        });
    }

    /**
     * Updates dynamically a specific journal vi.
     */
    let reload_html_view = function (search_form_data, action, order_field, order_direction) {
        let entry = {
            action: 'wcs_get_journals_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            teacher: search_form_data.teacher ? '#' + search_form_data.teacher : null,
            student: search_form_data.student ? '#' + search_form_data.student : null,
            subject: search_form_data.subject ? '#' + search_form_data.subject : null,
            date_from: search_form_data.date_from,
            date_upto: search_form_data.date_upto,
            created_at_from: search_form_data.created_at_from,
            created_at_upto: search_form_data.created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        let $parent = $('#wcs4-journal-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        // prepare form data
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $('#wcs4_journal_subject').val(entry.subject_id);
            $('#wcs4_journal_teacher').val(entry.teacher_id);
            $('#wcs4_journal_student').val(entry.student_id);
            $('#wcs4_journal_date').val(entry.date);
            $('#wcs4_journal_start_time').val(entry.start_time);
            $('#wcs4_journal_end_time').val(entry.end_time);
            $('#wcs4_journal_topic').val(entry.topic);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

})(jQuery);