/**
 * Javascript for WCS4 admin.
 */

(function ($) {

    let SCOPE = 'schedule';
    let FILTER_ID = '#wcs4-schedule-filter';

    $(document).ready(function () {
        WCS4_ADMIN.bind_search_handler(FILTER_ID, reload_html_view);

        WCS4_ADMIN.bind_edit_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_copy_handler(SCOPE, set_entry_data_to_form);
        WCS4_ADMIN.bind_delete_handler(SCOPE, function (data) {
            const elem = '#' + data.scope + '-' + data.id;
            const day = $(elem).data('day');
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
        $('.wcs4-submit-schedule-form').click(function (e) {
            e.preventDefault();
            let entry = {
                action: 'wcs_add_or_update_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value('wcs4_schedule_subject'),
                teacher_id: WCS4_LIB.form_field_value('wcs4_schedule_teacher'),
                student_id: WCS4_LIB.form_field_value('wcs4_schedule_student'),
                classroom_id: $('#wcs4_schedule_classroom[multiple]').length ? $('#wcs4_schedule_classroom option:selected').toArray().map(item => item.value) : $('#wcs4_schedule_classroom option:selected').val(),
                weekday: $('#wcs4_schedule_weekday option:selected').val(),
                start_time: $('#wcs4_schedule_start_time').val(),
                end_time: $('#wcs4_schedule_end_time').val(),
                visible: $('#wcs4_schedule_visibility :checked').val(),
                collision_detection: $('#wcs4_schedule_collision_detection :checked').val(),
                notes: $('#wcs4_schedule_notes').val()
            };

            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
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
            let $parent = $('#wcs4-schedule-day-' + day);
            WCS4_LIB.update_view($parent, entry, action);
        });
    }

    /**
     * Fill up form with entry data
     */
    let set_entry_data_to_form = function (entry) {
        // prepare form data
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $('#wcs4_schedule_subject').val(entry.subject_id);
            $('#wcs4_schedule_teacher').val(entry.teacher_id);
            $('#wcs4_schedule_student').val(entry.student_id);
            $('#wcs4_schedule_classroom').val(entry.classroom_id);
            $('#wcs4_schedule_weekday').val(entry.weekday);
            $('#wcs4_schedule_start_time').val(entry.start_time);
            $('#wcs4_schedule_end_time').val(entry.end_time);
            $('#wcs4_schedule_visibility-' + ((entry.visible === '1') ? 'visible' : 'hidden')).prop('checked', true);
            $('#wcs4_schedule_collision_detection-' + ((entry.collision_detection === '1') ? 'yes' : 'no')).prop('checked', true);
            $('#wcs4_schedule_notes').val(entry.notes);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

    /**
     * Handles the edit button click event.
     */
    let bind_visibility_handler = function () {
        $(document).on('click.wcs4-visibility-schedule-button', 'tr[data-type="schedule"] .wcs4-visibility-button', function (e) {
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