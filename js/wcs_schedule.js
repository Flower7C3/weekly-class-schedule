/**
 * Javascript for WCS4 admin.
 */

(function ($) {

    let SCOPE = 'schedule';
    let FILTER_ID = '#wcs4_schedule_filter';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);

        WCS4_ADMIN.bind_edit_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_copy_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_delete_handler(SCOPE, function (data) {
            const day = $('[data-scope="' + data.scope + '"][data-id="' + data.id + '"]').data('day');
            if (day !== false) {
                let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
                reload_html_view(search_form_data, 'remove', [day]);
            }
        });
        bind_submit_handler();
        bind_visibility_handler();
    });


    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_handler = function () {
        let $form = $('#wcs4_schedule_management-form');
        $form.find('#wcs4-submit-form').click(function (e) {
            e.preventDefault();
            let entry = {
                action: 'wcs_add_or_update_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                classroom_id: WCS4_LIB.form_field_value($form, 'classroom'),
                weekday: WCS4_LIB.form_field_value($form, 'weekday'),
                start_time: WCS4_LIB.form_field_value($form, 'start_time'),
                end_time: WCS4_LIB.form_field_value($form, 'end_time'),
                visible: WCS4_LIB.form_field_value($form, 'visibility'),
                collision_detection: WCS4_LIB.form_field_value($form, 'collision_detection'),
                notes: WCS4_LIB.form_field_value($form, 'notes'),
            };

            WCS4_LIB.submit_entry(entry, function (data, status) {
                if (200 <= status && status < 300) {
                    // Let's refresh the day
                    let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID))
                    reload_html_view(search_form_data, 'fade', data.days_to_update);

                    // Clear notes.
                    WCS4_LIB.reset_to_add_mode('schedule');
                }
            });
        });
    }

    /**
     * Updates dynamically a specific day schedule.
     */
    let reload_html_view = function (search_form_data, action, days) {
        if (typeof days === 'undefined') {
            days = [0, 1, 2, 3, 4, 5, 6, 7];
        }
        days.forEach(function (day) {
            let entry = {
                action: 'wcs_get_day_schedules_html',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                classroom: search_form_data.classroom ? '#' + search_form_data.classroom : null,
                teacher: search_form_data.teacher ? '#' + search_form_data.teacher : null,
                student: search_form_data.student ? '#' + search_form_data.student : null,
                subject: search_form_data.subject ? '#' + search_form_data.subject : null,
                weekday: day,
                visibility: search_form_data.visibility,
                collision_detection: search_form_data.collision_detection
            };
            let $parent = $('#wcs4_schedule_day-' + day);
            WCS4_LIB.update_view($parent, entry, action);
        });
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        // prepare form data
        let $form = $('#wcs4_schedule_management-form');
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $form.find('[name="subject"]').val(entry.subject_id);
            $form.find('[name="teacher"]').val(entry.teacher_id);
            $form.find('[name="student"]').val(entry.student_id);
            $form.find('[name="classroom"]').val(entry.classroom_id);
            $form.find('[name="weekday"]').val(entry.weekday);
            $form.find('[name="start_time"]').val(entry.start_time);
            $form.find('[name="end_time"]').val(entry.end_time);
            $form.find('[name="visibility"][value="' + ((entry.visible === '1') ? 'visible' : 'hidden') + '"]').prop('checked', true);
            $form.find('[name="collision_detection"][value="' + ((entry.collision_detection === '1') ? 'yes' : 'no') + '"]').prop('checked', true);
            $form.find('[name="notes"]').val(entry.notes);
            $form.find('[name="type"][value="' + entry.type + '"]').prop('checked', true).change();

        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

    /**
     * Handles the edit button click event.
     */
    let bind_visibility_handler = function () {
        $(document).on('click.wcs4-visibility-schedule-button', 'tr[data-scope="schedule"] .wcs4-visibility-button', function (e) {
            let entry = {
                action: 'wcs_toggle_visibility_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                visible: 'true' === $(this).attr('data-visible') ? '0' : '1',
                row_id: $(this).closest('tr').data('id')
            };
            const day = $(this).closest('tr').data('day')
            WCS4_LIB.modify_entry('schedule', entry, function (data) {
                let search_form_data = WCS4_ADMIN.search_form_process_and_push_history_state($(FILTER_ID));
                reload_html_view(search_form_data, 'fade', [day]);
            });
        });
    }
})(jQuery);