/**
 * Javascript for WCS4 journal.
 */

(function ($) {

    let SCOPE = 'journal';
    let FILTER_ID = '#wcs4-journals-filter';
    let LIST_ID = '#wcs4-journals-list-wrapper';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);
        WCS4_ADMIN.bind_sort_handler(LIST_ID, FILTER_ID, reload_html_view);
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
        $(document).on('change.wcs4_journal_type', '#wcs4-journal-form [name="type"]', function () {
            bind_form_handler();
        });
        bind_form_handler();
    });

    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        let $form = $('#wcs4-journal-form');
        $form.find('[data-wcs4="submit-form"]').click(function (e) {
            e.preventDefault();
            let entry = {
                action: 'wcs_add_or_update_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.get_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.get_field_value($form, 'teacher'),
                student_id: WCS4_LIB.get_field_value($form, 'student'),
                date: WCS4_LIB.get_field_value($form, 'date'),
                start_time: WCS4_LIB.get_field_value($form, 'start_time'),
                end_time: WCS4_LIB.get_field_value($form, 'end_time'),
                topic: WCS4_LIB.get_field_value($form, 'topic'),
                type: WCS4_LIB.get_field_value($form, 'type'),
            };
            console.log(entry)
            WCS4_LIB.submit_entry(entry, function (data, status) {
                if (200 <= status && status < 300) {
                    let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID));
                    let $sortable = $('.sortable.sorted');
                    reload_html_view(search_form_data, 'fade',
                        $sortable.data('order-current-field'),
                        $sortable.data('order-current-direction'));
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
            type: search_form_data.type,
            created_at_from: search_form_data.created_at_from,
            created_at_upto: search_form_data.created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        let $parent = $('#wcs4-journals-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        let $form = $('#wcs4-journal-form');
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            WCS4_LIB.set_radio_value($form, 'type', entry.type, true);
            WCS4_LIB.set_select_value($form, 'subject', entry.subject_id);
            WCS4_LIB.set_select_value($form, 'teacher', entry.teacher_id);
            WCS4_LIB.set_select_value($form, 'student', entry.student_id);
            WCS4_LIB.set_input_value($form, 'date', entry.date);
            WCS4_LIB.set_input_value($form, 'start_time', entry.start_time);
            WCS4_LIB.set_input_value($form, 'end_time', entry.end_time);
            WCS4_LIB.set_input_value($form, 'topic', entry.topic);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

    let bind_form_handler = function () {
        let $form = $('#wcs4-journal-form');
        let type = $form.find('[name="type"]:checked').val();
        if (typeof type === 'undefined' || '' === type) {
            return;
        }
        let $student = $('#wcs4_journal_student').closest('fieldset');
        if (type.startsWith('type.absent_teacher.') || type === 'type.absent_teacher' || type === 'type.teacher_office_works') {
            $student.hide();
        } else {
            $student.show();
        }
    }

})(jQuery);