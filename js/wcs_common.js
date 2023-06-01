/**
 * The Weekly Class Schedule 4 common JavaScript library.
 */

let WCS4_LIB = (function ($) {
    /**
     * Applies hover and qtip to table layouts.
     */
    let apply_qtip = function () {
        jQuery('.wcs4-qtip-box').each(function () {
            let html = jQuery('.wcs4-qtip-data', this).html();

            jQuery('a.wcs4-qtip', this).qtip({
                content: {
                    text: html
                },
                show: {
                    event: 'click',
                },
                style: {lessons: 'wcs4-qtip-tip'}
            })
        });
    }

    let find_get_parameter = function (parameterName) {
        let result = null,
            tmp = [];
        let items = location.search.substr(1).split("&");
        for (let index = 0; index < items.length; index++) {
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
     * Handles the submit form click event.
     */
    let submit_entry = function (entry, callback) {
        WCS4_LIB.remove_message();
        if ($('#wcs4-row-id').length > 0) {
            // We've got a hidden row field, that means this is an update
            // request and not a regular insert request.
            entry.row_id = $('#wcs4-row-id').val();
        }

        $('#wcs4-management-form-wrapper .spinner').addClass('is-active');

        // We can also pass the url value separately from ajaxurl for
        // front end AJAX implementations
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data, state, xhr) {
            WCS4_LIB.show_message(data.response, xhr.status);
            callback(data, xhr.status);
        }).fail(function (err) {
            WCS4_LIB.show_message(err.responseJSON.response, err.status, err.responseJSON.errors ?? []);
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
        });
    }

    let modify_entry = function (scope, entry, callback, confirm_message) {
        if (scope !== 'schedule' && scope !== 'journal' && scope !== 'work-plan' && scope !== 'progress' && scope !== 'snapshot') {
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            return;
        }
        // Confirm delete operation.
        if ('undefined' !== typeof confirm_message && '' !== confirm_message) {
            let confirm = window.confirm(confirm_message);
            if (!confirm) {
                reset_to_add_mode(scope)
                return;
            }
        }

        $('#wcs4-management-form-wrapper .spinner').addClass('is-active');

        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data, state, xhr) {
            callback(data, xhr.status);
        }).fail(function (err) {
            WCS4_LIB.show_message(err.responseJSON.response, err.status, err.responseJSON.errors ?? []);
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
        });
    }

    let lock_tr = function (scope, row_id) {
        console.log('lock-tr', scope, row_id)
        if (Array.isArray(row_id)) {
            row_id.forEach(function (id) {
                $('[data-scope="' + scope + '"][data-id="' + id + '"]').addClass('is-active');
            });
        } else {
            $('[data-scope="' + scope + '"][data-id="' + row_id + '"]').addClass('is-active');
        }
    }
    /**
     * Fetch entry data for form
     */
    let fetch_entry_data_to_form = function (scope, row_id, set_entry_data_to_form, reset_callback) {
        if (scope !== 'schedule' && scope !== 'journal' && scope !== 'work-plan' && scope !== 'progress' && scope !== 'snapshot') {
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            return;
        }
        reset_to_add_mode(scope)
        lock_tr(scope, row_id)
        let get_query;
        if (Array.isArray(row_id)) {
            get_query = {
                action: 'wcs_get_' + scope,
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: row_id
            };
        } else {
            get_query = {
                action: 'wcs_get_' + scope,
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: row_id
            };
        }
        $('#wcs4-management-form-wrapper .spinner').addClass('is-active');
        $('#wcs4-management-form-wrapper input,#wcs4-management-form-wrapper select,#wcs4-management-form-wrapper textarea').attr('readonly', true);
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, get_query, function (data) {
            set_entry_data_to_form(data.response)
            reset_callback(scope, data.response)
        }).fail(function (err) {
            console.error(err);
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
            $('#wcs4-management-form-wrapper input,#wcs4-management-form-wrapper select,#wcs4-management-form-wrapper textarea').attr('readonly', null);
        });
    };

    let form_field_value = function ($form, name) {
        let $checkbox = $form.find('input[type="checkbox"][name="' + name + '"]');
        if ($checkbox.length) {
            let $checked = $form.find('input[type="checkbox"][name="' + name + '"]:checked');
            return $checked.toArray().map(item => item.value);
        }
        let $radio = $form.find('input[type="radio"][name="' + name + '"]');
        if ($radio.length) {
            let $checked = $form.find('input[type="radio"][name="' + name + '"]:checked');
            return $checked.val();
        }
        let $inputSingle = $form.find('input[name="' + name + '"]');
        if ($inputSingle.length) {
            return $inputSingle.val();
        }
        let $inputMultiple = $form.find('input[name="' + name + '[]"]');
        if ($inputMultiple.length) {
            return $inputMultiple.toArray().map(item => item.value);
        }
        let $textarea = $form.find('textarea[name="' + name + '"]');
        if ($textarea.length) {
            return $textarea.val();
        }
        let $select = $form.find('select[name="' + name + '"]');
        if ($select.length) {
            let $selected = $select.find('option:selected');
            return 'multiple' === $select.attr('multiple')
                ? $selected.toArray().map(item => item.value)
                : $selected.val();
        }
        return null;
    };
    let update_view = function ($parent, entry, action) {
        $parent.find('.spinner').addClass('is-active');
        $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            // Rebuild table
            let html = data.html;
            if (html.length > 0 && $('.wcs4-day-content-wrapper', $parent).data('hash') !== $(html).data('hash')) {
                $('.wcs4-day-content-wrapper', $parent).fadeOut(300, function () {
                    $(this).remove();
                    $parent.append(html);
                    if (action === 'fade') {
                        $('.wcs4-day-content-wrapper', $parent).hide().fadeIn(300, function () {
                            $(this).attr('style', null);
                        });
                    }
                });
            }
        }).fail(function (err) {
            // Failed
            console.error(err);
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
            $parent.find('.spinner').removeClass('is-active');
        });
    };


    /**
     * Enter edit mode
     */
    let reset_to_edit_mode = function (scope, entry) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add editing mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].edit_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').html(WCS4_AJAX_OBJECT[scope].save_item);
        // Add hidden row field
        if ($('#wcs4-row-id').length > 0) {
            // Field already exists, let's update.
            $('#wcs4-row-id').attr('value', entry.id);
        } else {
            // Field does not exist.
            let row_hidden_field = '<input type="hidden" id="wcs4-row-id" name="wcs4-row-id" value="' + entry.id + '">';
            $('#wcs4-management-form-wrapper').append(row_hidden_field);
        }
        // Add cancel editing button
        if ($('#wcs4-cancel-editing').length == 0) {
            let cancel_button = '<a href="#" class="button button-link" id="wcs4-cancel-editing">' + WCS4_AJAX_OBJECT[scope].cancel_editing + '</a>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-editing').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }

    let reset_to_copy_mode = function (scope) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add copying mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].copy_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').html(WCS4_AJAX_OBJECT[scope].add_item);
        // Add cancel copying button
        if ($('#wcs4-cancel-copying').length == 0) {
            let cancel_button = '<a href="#" class="button button-link" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-copying').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }
    let reset_to_create_mode = function (scope) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add copying mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].add_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').html(WCS4_AJAX_OBJECT[scope].add_item);
        // Add cancel copying button
        if ($('#wcs4-cancel-copying').length == 0) {
            let cancel_button = '<a href="#" class="button button-link" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-copying').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }

    let reset_to_add_mode = function (scope) {
        $('#wcs4-management-form-wrapper form')
            .one('change.reset', function () {
                $('#wcs4-reset-form').show();
            })
            .get(0).reset();
        $('#wcs4-management-form-wrapper form').trigger("reset");
        $('#wcs4-management-form-wrapper form').find('input,textarea,select').trigger("change");
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].add_mode);
        $('#wcs4-row-id').remove();
        $('#wcs4-cancel-copying').remove();
        $('#wcs4-cancel-editing').remove();
        $('#wcs4-submit-form').html(WCS4_AJAX_OBJECT[scope].add_item);
        $('#wcs4-management-form-wrapper').removeClass('is-open');
        $('tr.is-active').removeClass('is-active');
    }

    /**
     * Handles the Ajax UI messaging.
     */
    let show_message = function (message, status, errors) {
        remove_message();
        if ('' !== message) {
            let $text = $('.wcs4-ajax-text');
            $text.html('');
            if ('created' === status || 'updated' === status || (200 <= status && status < 300)) {
                $text.addClass('updated');
                $text.append('<span class="dashicons dashicons-cloud-saved"></span>');
            } else if ('error' === status || (400 <= status)) {
                $text.addClass('error');
                $text.append('<span class="dashicons dashicons-warning"></span>');
            }
            $text.append(message).show();
            setTimeout(function () {
                $('.wcs4-ajax-text').fadeOut('slow');
            }, 3000);
        }
        for (let field_id in errors) {
            let $wrap = $('.form-field-' + field_id + '-wrap');
            $wrap.addClass('form-invalid');
            for (let error_id in errors[field_id]) {
                let msg = $('<div class="error">').html(errors[field_id][error_id]);
                $wrap.append(msg);
            }
        }
    };
    let remove_message = function () {
        $('.wcs4-ajax-text').html('').hide();
        $('.wcs4-ajax-text').removeClass('updated').removeClass('error')
        $('.form-field').removeClass('form-invalid');
        $('.form-field .error').remove();
    };

    return {
        // tools
        apply_qtip,
        find_get_parameter,
        // form
        submit_entry,
        modify_entry,
        fetch_entry_data_to_form,
        form_field_value,
        // view
        reset_to_add_mode,
        reset_to_create_mode,
        reset_to_edit_mode,
        reset_to_copy_mode,
        update_view,
        // messages
        show_message,
        remove_message,
        lock_tr,
    }
})(jQuery);