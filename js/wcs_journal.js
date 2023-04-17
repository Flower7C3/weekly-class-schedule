/**
 * Javascript for WCS4 journal.
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
        // $(document).on('submit.wcs4-journals-filter', '#wcs4-journals-filter', function (e) {
        //     e.preventDefault();
        //     $('#wcs4-journals-search').click();
        // });
        $(document).on('click.wcs4-journals-search', '#wcs4-journals-search', function (e) {
            e.preventDefault();
            reload_html_view(
                $('#search_wcs4_journal_teacher_id').val(),
                $('#search_wcs4_journal_student_id').val(),
                $('#search_wcs4_journal_subject_id').val(),
                $('#search_wcs4_journal_date_from').val(),
                $('#search_wcs4_journal_date_upto').val(),
                $('#search_wcs4_journal_created_at_from').val(),
                $('#search_wcs4_journal_created_at_upto').val(),
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
        $(document).on('click.wcs4-journal-events-list-sort', '#wcs4-journal-events-list-wrapper [data-order-field][data-order-direction]', function (e) {
            reload_html_view(
                $('#search_wcs4_journal_teacher_id').val(),
                $('#search_wcs4_journal_student_id').val(),
                $('#search_wcs4_journal_subject_id').val(),
                $('#search_wcs4_journal_date_from').val(),
                $('#search_wcs4_journal_date_upto').val(),
                $('#search_wcs4_journal_created_at_from').val(),
                $('#search_wcs4_journal_created_at_upto').val(),
                $(this).data('order-field'),
                $(this).data('order-direction'),
                'fade')
            ;
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var bind_submit_handler = function () {
        $('.wcs4-submit-journal-form').click(function (e) {
            var entry;
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
                    reload_html_view(
                        $('#search_wcs4_journal_teacher_id').val(),
                        $('#search_wcs4_journal_student_id').val(),
                        $('#search_wcs4_journal_subject_id').val(),
                        $('#search_wcs4_journal_date_from').val(),
                        $('#search_wcs4_journal_date_upto').val(),
                        $('#search_wcs4_journal_created_at_from').val(),
                        $('#search_wcs4_journal_created_at_upto').val(),
                        $('.sortable.sorted').data('order-current-field'),
                        $('.sortable.sorted').data('order-current-direction'),
                        'fade'
                    );
                    // Clear topic.
                    $('#wcs4_journal_topic').val('');
                    WCS4_LIB.reset_to_add_mode('journal');
                }
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var bind_edit_handler = function () {
        $(document).on('click.wcs4-edit-journal-button', 'tr[data-type="journal"] .wcs4-edit-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('journal', $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_edit_mode);
        });
    }

    /**
     * Handles the copy button click event.
     */
    var bind_copy_handler = function () {
        $(document).on('click.wcs4-copy-journal-button', 'tr[data-type="journal"] .wcs4-copy-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form('journal', $(this).closest('tr').data('id'), set_entry_data_to_form, WCS4_LIB.reset_to_copy_mode)
        });
    }

    /**
     * Handles the delete button click event.
     */
    var bind_delete_handler = function () {
        $(document).on('click.wcs4-delete-journal-button', 'tr[data-type="journal"] .wcs4-delete-button', function (e) {
            var entry = {
                action: 'wcs_delete_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: $(this).closest('tr').data('id')
            };
            WCS4_LIB.modify_entry('journal', entry, function (data) {
                // Let's refresh the date
                reload_html_view(
                    $('#search_wcs4_journal_teacher_id').val(),
                    $('#search_wcs4_journal_student_id').val(),
                    $('#search_wcs4_journal_subject_id').val(),
                    $('#search_wcs4_journal_date_from').val(),
                    $('#search_wcs4_journal_date_upto').val(),
                    $('#search_wcs4_journal_created_at_from').val(),
                    $('#search_wcs4_journal_created_at_upto').val(),
                    $('.sortable.sorted').data('order-current-field'),
                    $('.sortable.sorted').data('order-current-direction'),
                    'remove'
                );
            }, WCS4_AJAX_OBJECT['journal'].delete_warning);
        });
    }

    /**
     * Updates dynamically a specific journal vi.
     */
    var reload_html_view = function (teacher, student, subject, date_from, date_upto, created_at_from, created_at_upto, order_field, order_direction, action) {
        var page = $('#search_wcs4_page').val();
        var state = {
            'page': page,
            'teacher': teacher,
            'student': student,
            'subject': subject,
            'date_from': date_from,
            'date_upto': date_upto,
            'created_at_from': created_at_from,
            'created_at_upto': created_at_upto,
            'order_field': order_field,
            'order_direction': order_direction,
        };
        var url = $('#wcs4-journals-filter').attr('action')
            + '?page=' + page
            + '&teacher=' + teacher
            + '&student=' + student
            + '&subject=' + subject
            + '&date_from=' + date_from
            + '&date_upto=' + date_upto
            + '&created_at_from=' + created_at_from
            + '&created_at_upto=' + created_at_upto
            + '&order_field=' + order_field
            + '&order_direction=' + order_direction
        ;
        history.pushState(state, $('title').text(), url);
        entry = {
            action: 'wcs_get_journals_html',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            teacher: teacher ? '#' + teacher : null,
            student: student ? '#' + student : null,
            subject: subject ? '#' + subject : null,
            date_from: date_from,
            date_upto: date_upto,
            created_at_from: created_at_from,
            created_at_upto: created_at_upto,
            order_field: order_field,
            order_direction: order_direction,
        };
        var $parent = $('#wcs4-journal-events-list-wrapper');
        WCS4_LIB.update_view($parent, entry, action)
    }

    /**
     * Fill up form with entry data
     */
    var set_entry_data_to_form = function (entry) {
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