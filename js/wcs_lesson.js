/**
 * Javascript for WCS4 admin.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_search_handler();
        bind_submit_handler();
        bind_edit_handler();
        bind_copy_handler();
        bind_delete_handler();
    });

    /**
     * Handles the search button click event.
     */
    var bind_search_handler = function () {
        $('#wcs-lessons-filter').submit(function (e) {
            e.preventDefault();
            var page = $('#search_wcs4_page').val();
            var classroom = $('#search_wcs4_lesson_classroom_id').val();
            var teacher = $('#search_wcs4_lesson_teacher_id').val();
            var student = $('#search_wcs4_lesson_student_id').val();
            var subject = $('#search_wcs4_lesson_subject_id').val();
            var state = {
                'page': page,
                'classroom': classroom,
                'teacher': teacher,
                'student': student,
                'subject': subject,
            };
            var url = $(this).attr('action');
            url += '?page=' + page;
            url += '&classroom=' + classroom;
            url += '&teacher=' + teacher;
            url += '&student=' + student;
            url += '&subject=' + subject;
            history.pushState(state, $('title').text(), url);
            for (var day = 0; day < 7; day++) {
                reload_html_view(classroom, teacher, student, subject, day, 'fade');
            }
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_handler = function () {
        $('.wcs4-submit-lesson-form').click(function (e) {
            var entry;

            e.preventDefault();

            entry = {
                action: 'add_or_update_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: $('#wcs4_lesson_subject[multiple]').length ? $('#wcs4_lesson_subject option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_subject option:selected').val(),
                teacher_id: $('#wcs4_lesson_teacher[multiple]').length ? $('#wcs4_lesson_teacher option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_teacher option:selected').val(),
                student_id: $('#wcs4_lesson_student[multiple]').length ? $('#wcs4_lesson_student option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_student option:selected').val(),
                classroom_id: $('#wcs4_lesson_classroom[multiple]').length ? $('#wcs4_lesson_classroom option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_classroom option:selected').val(),
                weekday: $('#wcs4_lesson_weekday option:selected').val(),
                start_time: $('#wcs4_lesson_start_time').val(),
                end_time: $('#wcs4_lesson_end_time').val(),
                visible: $('#wcs4_lesson_visibility :checked').val(),
                notes: $('#wcs4_lesson_notes').val()
            };

            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    var classroom = $('#search_wcs4_lesson_classroom_id').val();
                    var teacher = $('#search_wcs4_lesson_teacher_id').val();
                    var student = $('#search_wcs4_lesson_student_id').val();
                    var subject = $('#search_wcs4_lesson_subject_id').val();
                    // Let's refresh the day
                    for (var day_to_update in data.days_to_update) {
                        reload_html_view(classroom, teacher, student, subject, day_to_update, 'fade');
                    }

                    // Clear notes.
                    $('#wcs4_lesson_notes').val('');
                    WCS4_LIB.reset_to_add_mode('lesson');
                }
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var bind_edit_handler = function () {
        $(document).on('click.wcs4-visibility-lesson-button', '.wcs4-visibility-lesson-button', function (e) {
            var entry = {
                action: 'toggle_visibility_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                visible: 'true' === $(this).attr('data-visible') ? '0' : '1',
                row_id: $(this).attr('data-lesson-id')
            };
            WCS4_LIB.modify_entry('lesson', entry, function (data) {
                const elem = '#' + data.scope + '-' + data.id;
                const day = $(elem).data('day');
                if (day !== false) {
                    const classroom = $('#search_wcs4_lesson_classroom_id').val();
                    const teacher = $('#search_wcs4_lesson_teacher_id').val();
                    const student = $('#search_wcs4_lesson_student_id').val();
                    const subject = $('#search_wcs4_lesson_subject_id').val();
                    // Let's refresh the day
                    reload_html_view(classroom, teacher, student, subject, day, 'fade');
                }
            });
        });
        $(document).on('click.wcs4-edit-lesson-button', '.wcs4-edit-lesson-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('lesson', $(this).attr('data-lesson-id'), set_entry_data_to_form, WCS4_LIB.reset_to_edit_mode);
        });
    }

    /**
     * Handles the copy button click event.
     */
    var bind_copy_handler = function () {
        $(document).on('click.wcs4-copy-lesson-button', '.wcs4-copy-lesson-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('lesson', $(this).attr('data-lesson-id'), set_entry_data_to_form, WCS4_LIB.reset_to_copy_mode)
        });
    }

    /**
     * Handles the delete button click event.
     */
    var bind_delete_handler = function () {
        $(document).on('click.wcs4-delete-lesson-button', '.wcs4-delete-lesson-button', function (e) {
            let entry = {
                action: 'delete_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: $(this).attr('data-lesson-id')
            };
            WCS4_LIB.modify_entry('lesson', entry, function (data) {
                const elem = '#' + data.scope + '-' + data.id;
                const day = $(elem).data('day');
                if (day !== false) {
                    const classroom = $('#search_wcs4_lesson_classroom_id').val();
                    const teacher = $('#search_wcs4_lesson_teacher_id').val();
                    const student = $('#search_wcs4_lesson_student_id').val();
                    const subject = $('#search_wcs4_lesson_subject_id').val();
                    // Let's refresh the day
                    reload_html_view(classroom, teacher, student, subject, day, 'remove');
                }
            }, WCS4_AJAX_OBJECT['lesson'].delete_warning);
        });
    }

    /**
     * Updates dynamically a specific day schedule.
     */
    var reload_html_view = function (classroom, teacher, student, subject, day, action) {
        entry = {
            action: 'get_day_schedules_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            classroom: classroom ? '#' + classroom : null,
            teacher: teacher ? '#' + teacher : null,
            student: student ? '#' + student : null,
            subject: subject ? '#' + subject : null,
            weekday: day
        };
        var $parent = $('#wcs4-schedule-day-' + day);
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    var set_entry_data_to_form = function (entry) {
        // prepare form data
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $('#wcs4_lesson_subject').val(entry.subject_id);
            $('#wcs4_lesson_teacher').val(entry.teacher_id);
            $('#wcs4_lesson_student').val(entry.student_id);
            $('#wcs4_lesson_classroom').val(entry.classroom_id);
            $('#wcs4_lesson_weekday').val(entry.weekday);
            $('#wcs4_lesson_start_time').val(entry.start_time);
            $('#wcs4_lesson_end_time').val(entry.end_time);

            var visibility;
            if (entry.visible === '1') {
                visibility = 'visible';
            } else {
                visibility = 'hidden';
            }

            $('#wcs4_lesson_visibility-' + visibility).prop('checked', true);
            $('#wcs4_lesson_notes').val(entry.notes);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };
})(jQuery);