/**
 * Javascript for WCS4 admin.
 */

(function ($) {

    // WCS4_AJAX_OBJECT available

    $(document).ready(function () {
        wcs4_bind_schedule_show_hide_handler();
        wcs4_bind_schedule_submit_handler();
        wcs4_bind_schedule_delete_handler();
        wcs4_bind_schedule_edit_handler();
        wcs4_bind_schedule_copy_handler();
        wcs4_bind_colorpickers();
        wcs4_bind_reset_settings();
    });

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
                    // Let's refresh the day
                    for (var day_to_update in data.days_to_update) {
                        update_day_schedule(day_to_update, 'add');
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
        $('.wcs4-delete-button').each(function () {
            // Check if element is already bound.
            if (is_elem_unbound($(this))) {
                // Bound, continue.
                return true;
            }

            // Re-bind new elements
            $(this).click(function (e) {
                var row_id,
                    src,
                    entry,
                    confirm = true;

                if (typeof (e.target) != 'undefined') {
                    src = e.target;
                } else {
                    src = e.srcElement;
                }
                row_id = src.id.replace('delete-entry-', '')

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
                    day = get_day_from_element(elem);

                    if (day !== false) {
                        // Let's refresh the day
                        update_day_schedule(day, 'remove');
                    }

                }).fail(function (err) {
                    // Failed
                    console.error(err);
                    schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');

                }).always(function () {
                    $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
                });
            });
        });
    }

    /**
     * Handles the edit button click event.
     */
    var wcs4_bind_schedule_edit_handler = function () {
        $('.wcs4-edit-button').each(function () {
            if (is_elem_unbound($(this))) {
                // Bound, continue.
                return true;
            }
            // Re-bind new elements
            $(this).click(function (e) {
                var src_elem,
                    row_id,
                    entry;
                if (typeof (e.target) != 'undefined') {
                    src_elem = e.target;
                } else {
                    src_elem = e.srcElement;
                }
                row_id = src_elem.id.replace('edit-entry-', '');
                entry = {
                    action: 'edit_schedule_entry',
                    security: WCS4_AJAX_OBJECT.ajax_nonce,
                    row_id: row_id
                };
                $('#wcs4-schedule-management-form-wrapper .spinner').addClass('is-active');
                jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                    enter_edit_mode(data.response);
                }).fail(function (err) {
                    // Failed
                    console.error(err);
                    schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
                }).always(function () {
                    $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
                });
            });
        });
    }

    /**
     * Handles the copy button click event.
     */
    var wcs4_bind_schedule_copy_handler = function () {
        $('.wcs4-copy-button').each(function () {
            if (is_elem_unbound($(this))) {
                // Bound, continue.
                return true;
            }
            // Re-bind new elements
            $(this).click(function (e) {
                var src_elem,
                    row_id,
                    entry;
                if (typeof (e.target) != 'undefined') {
                    src_elem = e.target;
                } else {
                    src_elem = e.srcElement;
                }
                row_id = src_elem.id.replace('copy-entry-', '');
                entry = {
                    action: 'edit_schedule_entry',
                    security: WCS4_AJAX_OBJECT.ajax_nonce,
                    row_id: row_id
                };
                $('#wcs4-schedule-management-form-wrapper .spinner').addClass('is-active');
                jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
                    // Get row data
                    enter_copy_mode(data.response);
                }).fail(function (err) {
                    // Failed
                    console.error(err);
                    schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
                }).always(function () {
                    $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
                });
            });
        });

    }

    /**
     * Updates dynamically a specific day schedule.
     */
    var update_day_schedule = function (day, action) {
        entry = {
            action: 'get_day_schedule',
            security: WCS4_AJAX_OBJECT.ajax_nonce,
            day: day
        };
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            // Rebuild table
            var html = data.html,
                parent = $('#wcs4-schedule-day-' + day),
                to_remove;
            if (html.length > 0) {
                to_remove = $('.wcs4-day-content-wrapper', parent);
                if (action === 'add') {
                    to_remove.remove();
                    parent.append(html).hide().fadeIn('slow');
                } else if (action === 'remove') {
                    to_remove.remove();
                    parent.append(html);
                }
            }
        }).fail(function (err) {
            // Failed
            console.error(err);
            schedule_item_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            // Re-bind handlers
            wcs4_bind_schedule_delete_handler();
            wcs4_bind_schedule_edit_handler();
            wcs4_bind_schedule_copy_handler();
            $('#wcs4-schedule-management-form-wrapper .spinner').removeClass('is-active');
        });
    }

    /**
     * Enter edit mode.
     */
    var enter_edit_mode = function (entry) {
        var start_array,
            end_array,
            start_hour,
            start_min,
            end_hour,
            end_min,
            visibility,
            row_hidden_field;

        if (entry.hasOwnProperty('subject_id')) {
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

            // Let's add the row id and the save button.
            $('#wcs4-submit-item').attr('value', WCS4_AJAX_OBJECT.save_item);

            // Add editing mode message
            $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.edit_mode)
            $('#wcs4-reset-form').hide();
            $('#wcs4-schedule-management-form-wrapper').addClass('is-open');

            // Add hidden row field
            if ($('#wcs4-row-id').length > 0) {
                // Field already exists, let's update.
                $('#wcs4-row-id').attr('value', entry.id);
            } else {
                // Field does not exist.
                row_hidden_field = '<input type="hidden" id="wcs4-row-id" name="wcs4-row-id" value="' + entry.id + '">';
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
    }

    /**
     * Enter copy mode.
     */
    var enter_copy_mode = function (entry) {
        var start_array,
            end_array,
            start_hour,
            start_min,
            end_hour,
            end_min,
            visibility;

        if (entry.hasOwnProperty('subject_id')) {
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

            // Let's add the row id and the save button.
            $('#wcs4-submit-item').attr('value', WCS4_AJAX_OBJECT.add_item);

            /* ------------ Change to copy mode --------- */
            // Add copying mode message
            $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.copy_mode)
            $('#wcs4-reset-form').hide();
            $('#wcs4-schedule-management-form-wrapper').addClass('is-open');

            // Add cancel copying button
            if ($('#wcs4-cancel-copying').length == 0) {
                var cancel_button = '<span id="wcs4-cancel-copying-wrapper"><a href="#" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT.cancel_copying + '</a></span>';
                $('#wcs4-reset-form').after(cancel_button);
                $('#wcs4-cancel-copying').click(function () {
                    reset_to_add_mode();
                })
            }
        }
    }

    /**
     * Exit edit mode.
     */
    var reset_to_add_mode = function () {
        $('#wcs4-schedule-management-form-wrapper form')[0].reset()
        $('#wcs4-schedule-management-form-title').text(WCS4_AJAX_OBJECT.add_mode)
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
     * Extracts the day ID from an element (delete or edit).
     */
    var get_day_from_element = function (elem) {
        var cls = elem.className,
            m,
            day;

        m = cls.match(/wcs4-action-button-day-(\d)+/g);
        if (m.length > 0) {
            m = m[0];
            day = m.replace('wcs4-action-button-day-', '');
            return parseInt(day);
        } else {
            return false;
        }
    }

    /**
     * Checks if a jQuery element is already bound to the 'click' event.
     *
     * @return bool: true if bound, false if not.
     */
    var is_elem_unbound = function (elem) {
        // Check if element is already bound.
        var t = elem.data('events');

        if (typeof (t) != 'undefined') {
            if (t.hasOwnProperty('click')) {
                if (t['click'].length > 0) {
                    // Element is already bound.
                    // Continue to next iternation, no need to re-bind.
                    return true;
                }
            }
        }
    }

    /**
     * Binds the colorpicker plugin to the selectors
     */
    var wcs4_bind_colorpickers = function () {
        $('.wcs_colorpicker').each(function (index) {
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