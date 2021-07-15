/**
 * Javascript for WCS4 report.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        bind_search_handler();
        bind_sort_handler();
        bind_submit_handler();
        bind_edit_handler();
        bind_copy_handler();
        bind_delete_handler();
    });

    /**
     * Handles the search button click event.
     */
    var bind_search_handler = function () {
        $(document).on('click.wcs-reports-download', '#wcs-search-download', function (e) {
            e.preventDefault();
            window.location = WCS4_AJAX_OBJECT.ajax_url
                + '?action=download_report_csv'
                + '&teacher=' + $('#search_wcs4_report_teacher_id').val()
                + '&student=' + $('#search_wcs4_report_student_id').val()
                + '&subject=' + $('#search_wcs4_report_subject_id').val()
                + '&date_from=' + $('#search_wcs4_report_date_from').val()
                + '&date_upto=' + $('#search_wcs4_report_date_upto').val();
        });
        $(document).on('submit.wcs-reports-filter', '#wcs-reports-filter', function (e) {
            e.preventDefault();
            reload_html_view(
                $('#search_wcs4_report_teacher_id').val(),
                $('#search_wcs4_report_student_id').val(),
                $('#search_wcs4_report_subject_id').val(),
                $('#search_wcs4_report_date_from').val(),
                $('#search_wcs4_report_date_upto').val(),
                null, null, 'fade');
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_sort_handler = function () {
        $(document).on('click.wcs4-events-list-sort', '#wcs4-report-events-list-wrapper [data-orderby][data-order]', function (e) {
            reload_html_view(
                $('#search_wcs4_report_teacher_id').val(),
                $('#search_wcs4_report_student_id').val(),
                $('#search_wcs4_report_subject_id').val(),
                $('#search_wcs4_report_date_from').val(),
                $('#search_wcs4_report_date_upto').val(),
                $(this).data('orderby'), $(this).data('order'), 'fade');
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_handler = function () {
        $('.wcs4-submit-report-form').click(function (e) {
            var entry;
            e.preventDefault();
            entry = {
                action: 'add_or_update_report_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: $('input#wcs4_report_subject').length ? [$('input#wcs4_report_subject').val()] : ($('select#wcs4_report_subject[multiple]').length ? $('select#wcs4_report_subject option:selected').toArray().map(item => item.value) : $('select#wcs4_report_subject option:selected').val()),
                teacher_id: $('input#wcs4_report_teacher').length ? [$('input#wcs4_report_teacher').val()] : ($('select#wcs4_report_teacher[multiple]').length ? $('select#wcs4_report_teacher option:selected').toArray().map(item => item.value) : $('select#wcs4_report_teacher option:selected').val()),
                student_id: $('input#wcs4_report_student').length ? [$('input#wcs4_report_student').val()] : ($('select#wcs4_report_student[multiple]').length ? $('select#wcs4_report_student option:selected').toArray().map(item => item.value) : $('select#wcs4_report_student option:selected').val()),
                date: $('#wcs4_report_date').val(),
                start_time: $('#wcs4_report_start_time').val(),
                end_time: $('#wcs4_report_end_time').val(),
                topic: $('#wcs4_report_topic').val()
            };
            WCS4_LIB.submit_entry(entry, function (data) {
                if (data.result === 'updated') {
                    // Let's refresh the day
                    reload_html_view(
                        $('#search_wcs4_report_teacher_id').val(),
                        $('#search_wcs4_report_student_id').val(),
                        $('#search_wcs4_report_subject_id').val(),
                        $('#search_wcs4_report_date_from').val(),
                        $('#search_wcs4_report_date_upto').val(),
                        null, null, 'fade');
                    // Clear topic.
                    $('#wcs4_report_topic').val('');
                    WCS4_LIB.reset_to_add_mode('report');
                }
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var bind_edit_handler = function () {
        $(document).on('click.wcs4-edit-report-button', '.wcs4-edit-report-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('report', $(this).attr('data-report-id'), set_entry_data_to_form, WCS4_LIB.reset_to_edit_mode);
        });
    }

    /**
     * Handles the copy button click event.
     */
    var bind_copy_handler = function () {
        $(document).on('click.wcs4-copy-report-button', '.wcs4-copy-report-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('report', $(this).attr('data-report-id'), set_entry_data_to_form, WCS4_LIB.reset_to_copy_mode)
        });
    }

    /**
     * Handles the delete button click event.
     */
    var bind_delete_handler = function () {
        $(document).on('click.wcs4-delete-report-button', '.wcs4-delete-report-button', function (e) {
            var entry = {
                action: 'delete_report_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: $(this).attr('data-report-id')
            };
            WCS4_LIB.delete_entry('report', entry, function (data) {
                var date,
                    elem;
                if (typeof (e.target) != 'undefined') {
                    elem = e.target;
                } else {
                    elem = e.srcElement;
                }
                // Let's refresh the date
                reload_html_view(
                    $('#search_wcs4_report_teacher_id').val(),
                    $('#search_wcs4_report_student_id').val(),
                    $('#search_wcs4_report_subject_id').val(),
                    $('#search_wcs4_report_date_from').val(),
                    $('#search_wcs4_report_date_upto').val(),
                    null, null, 'remove');
            });
        });
    }

    /**
     * Updates dynamically a specific report vi.
     */
    var reload_html_view = function (teacher, student, subject, date_from, date_upto, orderby, order, action) {
        var page = $('#search_wcs4_page').val();
        var state = {
            'page': page,
            'teacher': teacher,
            'student': student,
            'subject': subject,
            'date_from': date_from,
            'date_upto': date_upto,
        };
        var url = $('#wcs-reports-filter').attr('action')
            + '?page=' + page
            + '&teacher=' + teacher
            + '&student=' + student
            + '&subject=' + subject
            + '&date_from=' + date_from
            + '&date_upto=' + date_upto;
        history.pushState(state, $('title').text(), url);
        entry = {
            action: 'get_reports_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            teacher: teacher ? '#' + teacher : null,
            student: student ? '#' + student : null,
            subject: subject ? '#' + subject : null,
            date_from: date_from,
            date_upto: date_upto,
            orderby: orderby,
            order: order,
        };
        var $parent = $('#wcs4-report-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    var set_entry_data_to_form = function (entry) {
        // prepare form data
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $('#wcs4_report_subject').val(entry.subject_id);
            $('#wcs4_report_teacher').val(entry.teacher_id);
            $('#wcs4_report_student').val(entry.student_id);
            $('#wcs4_report_date').val(entry.date);
            $('#wcs4_report_start_time').val(entry.start_time);
            $('#wcs4_report_end_time').val(entry.end_time);
            $('#wcs4_report_topic').val(entry.topic);
        } else {
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

})(jQuery);