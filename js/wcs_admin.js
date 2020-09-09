/**
 * Javascript for WCS4 admin.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        wcs4_bind_schedule_show_hide_handler();
        wcs4_bind_schedule_search_handler();
        wcs4_bind_schedule_submit_handler();
        wcs4_bind_schedule_delete_handler();
        wcs4_bind_schedule_edit_handler();
        wcs4_bind_schedule_copy_handler();
        wcs4_bind_colorpickers();
        wcs4_bind_reset_settings();
    });

    var find_get_parameter = function (parameterName) {
        var result = null,
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === parameterName) {
                result = decodeURIComponent(tmp[1]);
                if ('undefined' === result) {
                    result = null;
                }
            }
        }
        return result;
    }

    /**
     * Handles the Show form button click event.
     */
    var wcs4_bind_schedule_show_hide_handler = function () {
        $('#wcs4-show-form').click(function () {
            $('#wcs4-schedule-management-form-wrapper').toggleClass('is-open');
        });
        $('#wcs4-reset-form').click(function () {
            $('#wcs4-schedule-management-form-wrapper').removeClass('is-open');
            remove_item_message();
        });
    };

    /**
     * Handles the search button click event.
     */
    var wcs4_bind_schedule_search_handler = function () {
        $('#wcs-posts-filter').submit(function (e) {
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
                update_day_schedule(classroom, teacher, student, subject, day, 'fade');
            }
        });
    };

    /**
     * Handles the Add Item button click event.
     */
    var wcs4_bind_schedule_submit_handler = function () {
        $('#wcs4-submit-item').click(function (e) {
            var entry;

            e.preventDefault();
            remove_item_message();

            entry = {
                action: 'add_or_update_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: $('#wcs4_lesson_subject[multiple]').length ? $('#wcs4_lesson_subject option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_subject option:selected').val(),
                teacher_id: $('#wcs4_lesson_teacher[multiple]').length ? $('#wcs4_lesson_teacher option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_teacher option:selected').val(),
                student_id: $('#wcs4_lesson_student[multiple]').length ? $('#wcs4_lesson_student option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_student option:selected').val(),
                classroom_id: $('#wcs4_lesson_classroom[multiple]').length ? $('#wcs4_lesson_classroom option:selected').toArray().map(item => item.value) : $('#wcs4_lesson_classroom option:selected').val(),
                weekday: $('#wcs4_lesson_weekday option:selected').val(),
                start_hour: $('#wcs4_lesson_start_time_hours option:selected').val(),
                start_minute: $('#wcs4_lesson_start_time_minutes option:selected').val(),
                end_hour: $('#wcs4_lesson_end_time_hours option:selected').val(),
                end_minute: $('#wcs4_lesson_end_time_minutes option:selected').val(),
                visible: $('#wcs4_lesson_visibility option:selected').val(),
                notes: $('#wcs4_lesson_notes').val()
            };

            if ($('#wcs4-row-id').length > 0) {
                // We've got a hidden row field, that means this is an update
                // request and not a regular insert request.
                entry.row_id = $('#wcs4-row-id').val();
            }

            $('#wcs4-schedule-management-form-wrapper .spinner').addClass('is-active');

            // We can also pass the url value separately from ajaxurl for
            // front end AJAX implementations
            jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                schedule_item_message(data.response, data.result, data.errors);

                if (data.result === 'updated') {
                    var classroom = find_get_parameter('classroom');
                    var teacher = find_get_parameter('teacher');
                    var student = find_get_parameter('student');
                    var subject = find_get_parameter('subject');
                    // Let's refresh the day
                    for (var day_to_update in data.days_to_update) {
                        update_day_schedule(classroom, teacher, student, subject, day_to_update, 'fade');
                    }

                    // Clear notes.
                    $('#wcs4_lesson_notes').val('');
                    reset_to_add_mode();
                }

            }).fail(function (err) {
                // Failed
                console.error(err);
                schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');

            }).always(function () {
                $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
            });
        });
    }

    /**
     * Handles the delete button click event.
     */
    var wcs4_bind_schedule_delete_handler = function () {
        $(document).on('click.wcs4-delete-button', '.wcs4-delete-button', function (e) {
            var row_id,
                entry,
                confirm = true;
            row_id = $(this).attr('data-lesson-id');

            // Confirm delete operation.
            confirm = window.confirm(WCS4_AJAX_OBJECT.delete_warning);
            if (!confirm) {
                return;
            }

            entry = {
                action: 'delete_schedule_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: row_id
            };

            $('#wcs4-schedule-management-form-wrapper .spinner').addClass('is-active');

            jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                var day,
                    elem;

                if (typeof (e.target) != 'undefined') {
                    elem = e.target;
                } else {
                    elem = e.srcElement;
                }
                day = $(elem).data('day');

                if (day !== false) {
                    var classroom = find_get_parameter('classroom');
                    var teacher = find_get_parameter('teacher');
                    var student = find_get_parameter('student');
                    var subject = find_get_parameter('subject');
                    // Let's refresh the day
                    update_day_schedule(classroom, teacher, student, subject, day, 'remove');
                }

            }).fail(function (err) {
                // Failed
                console.error(err);
                schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            }).always(function () {
                $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var wcs4_bind_schedule_edit_handler = function () {
        $(document).on('click.wcs4-edit-button', '.wcs4-edit-button', function (e) {
            fetch_entry_data_to_form($(this).attr('data-lesson-id'), enter_edit_mode);
        });
    }

    /**
     * Handles the copy button click event.
     */
    var wcs4_bind_schedule_copy_handler = function () {
        $(document).on('click.wcs4-copy-button', '.wcs4-copy-button', function (e) {
            fetch_entry_data_to_form($(this).attr('data-lesson-id'), enter_copy_mode)
        });
    }

    /**
     * Updates dynamically a specific day schedule.
     */
    var update_day_schedule = function (classroom, teacher, student, subject, day, action) {
        entry = {
            action: 'get_day_schedule',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            classroom: classroom ? '#' + classroom : null,
            teacher: teacher ? '#' + teacher : null,
            student: student ? '#' + student : null,
            subject: subject ? '#' + subject : null,
            weekday: day
        };
        var parent = $('#wcs4-schedule-day-' + day);
        parent.find('.spinner').addClass('is-active');
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            // Rebuild table
            var html = data.html;
            if (html.length > 0 && $('.wcs4-day-content-wrapper', parent).data('hash') !== $(html).data('hash')) {
                $('.wcs4-day-content-wrapper', parent).fadeOut(300, function () {
                    jQuery(this).remove();
                    parent.append(html);
                    if (action === 'fade') {
                        $('.wcs4-day-content-wrapper', parent).hide().fadeIn(300, function () {
                            $(this).attr('style', null);
                        });
                    }
                });
            }
        }).fail(function (err) {
            // Failed
            console.error(err);
            schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
            $(parent).find('.spinner').removeClass('is-active');
        });
    }

    /**
     * Fetch entry data for form
     */
    var fetch_entry_data_to_form = function (row_id, callback) {
        var get_lesson_query;
        get_lesson_query = {
            action: 'get_lesson',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            row_id: row_id
        };
        $('#wcs4-schedule-management-form-wrapper .spinner').addClass('is-active');
        $('#wcs4-schedule-management-form input,#wcs4-schedule-management-form select,#wcs4-schedule-management-form textarea').attr('readonly', true);
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, get_lesson_query, function (data) {
            set_entry_data_to_form(data.response, callback)
        }).fail(function (err) {
            console.error(err);
            schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
            $('#wcs4-schedule-management-form input,#wcs4-schedule-management-form select,#wcs4-schedule-management-form textarea').attr('readonly', null);
        });
    };

    /**
     * Fill up form with entry data
     */
    var set_entry_data_to_form = function (entry, callback) {
        reset_to_add_mode();
        var start_array,
            end_array,
            start_hour,
            start_min,
            end_hour,
            end_min,
            visibility;
        // prepare form data
        if (entry.hasOwnProperty('id')) {
            // We got an entry.
            $('#wcs4_lesson_subject').val(entry.subject_id);
            $('#wcs4_lesson_teacher').val(entry.teacher_id);
            $('#wcs4_lesson_student').val(entry.student_id);
            $('#wcs4_lesson_classroom').val(entry.classroom_id);
            $('#wcs4_lesson_weekday').val(entry.weekday);

            // Update time fields.
            start_array = entry.start_hour.split(':');
            start_hour = start_array[0].replace(/^[0]/g, "");
            start_min = start_array[1].replace(/^[0]/g, "");

            end_array = entry.end_hour.split(':');
            end_hour = end_array[0].replace(/^[0]/g, "");
            end_min = end_array[1].replace(/^[0]/g, "");

            $('#wcs4_lesson_start_time_hours').val(start_hour);
            $('#wcs4_lesson_start_time_minutes').val(start_min);

            $('#wcs4_lesson_end_time_hours').val(end_hour);
            $('#wcs4_lesson_end_time_minutes').val(end_min);

            if (entry.visible === '1') {
                visibility = 'visible';
            } else {
                visibility = 'hidden';
            }

            $('#wcs4_lesson_visibility').val(visibility);
            $('#wcs4_lesson_notes').val(entry.notes);

            callback(entry);
        } else {
            schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }
    };

    /**
     * Enter edit mode
     */
    var enter_edit_mode = function (entry) {
        // Add editing mode message
        $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.edit_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-schedule-management-form-wrapper').addClass('is-open');

        // Let's add the row id and the save button.
        $('#wcs4-submit-item').attr('value', WCS4_AJAX_OBJECT.save_item);

        // Add hidden row field
        if ($('#wcs4-row-id').length > 0) {
            // Field already exists, let's update.
            $('#wcs4-row-id').attr('value', entry.id);
        } else {
            // Field does not exist.
            var row_hidden_field = '<input type="hidden" id="wcs4-row-id" name="wcs4-row-id" value="' + entry.id + '">';
            $('#wcs4-schedule-management-form-wrapper').append(row_hidden_field);
        }

        // Add cancel editing button
        if ($('#wcs4-cancel-editing').length == 0) {
            var cancel_button = '<span id="wcs4-cancel-editing-wrapper"><a href="#" id="wcs4-cancel-editing">' + WCS4_AJAX_OBJECT.cancel_editing + '</a></span>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-editing').click(function () {
                reset_to_add_mode();
            })
        }
    }

    /**
     * Enter copy mode.
     */
    var enter_copy_mode = function (entry) {
        // Add copying mode message
        $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.copy_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-schedule-management-form-wrapper').addClass('is-open');

        // Let's add the row id and the save button.
        $('#wcs4-submit-item').attr('value', WCS4_AJAX_OBJECT.add_item);

        // Add cancel copying button
        if ($('#wcs4-cancel-copying').length == 0) {
            var cancel_button = '<span id="wcs4-cancel-copying-wrapper"><a href="#" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT.cancel_copying + '</a></span>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-copying').click(function () {
                reset_to_add_mode();
            })
        }
    }

    /**
     * Exit edit mode.
     */
    var reset_to_add_mode = function () {
        remove_item_message();
        $('#wcs4-schedule-management-form-wrapper form')[0].reset();
        $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.add_mode);
        $('#wcs4-row-id').remove();
        $('#wcs4-cancel-copying-wrapper').remove();
        $('#wcs4-cancel-editing-wrapper').remove();
        $('#wcs4-submit-item').val(WCS4_AJAX_OBJECT.add_item);
        $('#wcs4-reset-form').show();
        $('#wcs4-schedule-management-form-wrapper').removeClass('is-open');
    }

    /**
     * Handles the Ajax UI messaging.
     */
    var schedule_item_message = function (message, status, errors) {
        remove_item_message();
        if ('' !== message) {
            if (status == 'updated') {
                $('.wcs4-ajax-text').addClass('updated');
            } else if (status == 'error') {
                $('.wcs4-ajax-text').addClass('error');
            }
            $('.wcs4-ajax-text').html(message).show();
            setTimeout(function () {
                $('.wcs4-ajax-text').fadeOut('slow');
            }, 2000);
        }
        for (var field_id in errors) {
            $('.term-wcs4_lesson_' + field_id + '-wrap').addClass('form-invalid');
            for (var error_id in errors[field_id]) {
                var message = $('<div class="error">').html(errors[field_id][error_id]);
                $('.term-wcs4_lesson_' + field_id + '-wrap').append(message);
            }
        }
    }

    var remove_item_message = function () {
        $('.wcs4-ajax-text').html('').hide();
        $('.wcs4-ajax-text').removeClass('updated').removeClass('error')
        $('.form-field').removeClass('form-invalid');
        $('.form-field .error').remove();
    }

    /**
     * Binds the colorpicker plugin to the selectors
     */
    var wcs4_bind_colorpickers = function () {
        $(document).on('click.wcs_colorpicker', '.wcs_colorpicker', function (index) {
            var elementName = $(this).attr('id');
            $(this).ColorPicker({
                onChange: function (hsb, hex, rgb) {
                    $('#' + elementName).val(hex);
                    $('.' + elementName).css('background', '#' + hex);
                },
                onBeforeShow: function (hsb, hex, rgb) {
                    $(this).ColorPickerSetColor(this.value);
                }
            });
        });
    };

    var wcs4_bind_reset_settings = function () {
        $('#wcs4-reset-database input').click(function (e) {
            e.preventDefault();
            entry = {
                action: $(this).attr('name'),
                security: WCS4_AJAX_OBJECT.ajax_nonce,
            };
            var confirm = window.confirm(WCS4_AJAX_OBJECT.reset_warning);
            if (!confirm) {
                return;
            }
            $('#wcs4-reset-database .spinner').addClass('is-active');
            jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                schedule_item_message(data.response, data.result);
            }).fail(function (err) {
                console.error(err);
            }).always(function () {
                $('#wcs4-reset-database .spinner').removeClass('is-active');
            });
        });
    };
})(jQuery);