/**
 * The Weekly Class Schedule 4 common JavaScript library.
 */

var WCS4_LIB = (function ($) {
    /**
     * Applies hover and qtip to table layouts.
     */
    var apply_qtip = function () {
        jQuery('.wcs4-qtip-box').each(function () {
            var html = jQuery('.wcs4-qtip-data', this).html();

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
     * Handles the submit form click event.
     */
    var submit_entry = function (entry, callback) {
        WCS4_LIB.remove_message();
        if ($('#wcs4-row-id').length > 0) {
            // We've got a hidden row field, that means this is an update
            // request and not a regular insert request.
            entry.row_id = $('#wcs4-row-id').val();
        }

        $('#wcs4-management-form-wrapper .spinner').addClass('is-active');

        // We can also pass the url value separately from ajaxurl for
        // front end AJAX implementations
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            WCS4_LIB.show_message(data.response, data.result, data.errors);
            callback(data);
        }).fail(function (err) {
            // Failed
            console.error(err);
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
        });
    }

    var modify_entry = function (scope, entry, callback, confirm_message) {
        if (scope !== 'lesson' && scope !== 'journal' && scope !== 'progress') {
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            return;
        }
        // Confirm delete operation.
        if ('undefined' !== typeof confirm_message && '' !== confirm_message) {
            var confirm = window.confirm(confirm_message);
            if (!confirm) {
                reset_to_add_mode(scope)
                return;
            }
        }

        $('#wcs4-management-form-wrapper .spinner').addClass('is-active');

        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            callback(data);
        }).fail(function (err) {
            // Failed
            console.error(err);
            WCS4_LIB.show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('#wcs4-management-form-wrapper .spinner').removeClass('is-active');
        });
    }
    /**
     * Fetch entry data for form
     */
    var fetch_entry_data_to_form = function (scope, row_id, set_entry_data_to_form, reset_callback) {
        if (scope !== 'lesson' && scope !== 'journal' && scope !== 'progress') {
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            return;
        }
        reset_to_add_mode(scope)
        var get_query;
        if (Array.isArray(row_id)) {
            row_id.forEach(function (id) {
                $('tr#' + scope + '-' + id).addClass('is-active');
            });
            get_query = {
                action: 'wcs_get_' + scope,
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                row_id: row_id
            };
        } else {
            $('tr#' + scope + '-' + row_id).addClass('is-active');
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

    var form_field_value = function (id) {
        let $input = $('input#' + id);
        if ($input.length) {
            if ($input.attr('name').indexOf('[]') > 0) {
                return [$('input#' + id).val()];
            }
            return $('input#' + id).val();
        }
        let $selected = $('select#' + id + ' option:selected');
        return $('select#' + id + '[multiple]').length
            ? $selected.toArray().map(item => item.value)
            : $selected.val();
    };
    var update_view = function ($parent, entry, action) {
        $parent.find('.spinner').addClass('is-active');
        $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            // Rebuild table
            var html = data.html;
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
    var reset_to_edit_mode = function (scope, entry) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add editing mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].edit_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').attr('value', WCS4_AJAX_OBJECT[scope].save_item);
        // Add hidden row field
        if ($('#wcs4-row-id').length > 0) {
            // Field already exists, let's update.
            $('#wcs4-row-id').attr('value', entry.id);
        } else {
            // Field does not exist.
            var row_hidden_field = '<input type="hidden" id="wcs4-row-id" name="wcs4-row-id" value="' + entry.id + '">';
            $('#wcs4-management-form-wrapper').append(row_hidden_field);
        }
        // Add cancel editing button
        if ($('#wcs4-cancel-editing-wrapper').length == 0) {
            var cancel_button = '<span id="wcs4-cancel-editing-wrapper"><a href="#" id="wcs4-cancel-editing">' + WCS4_AJAX_OBJECT[scope].cancel_editing + '</a></span>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-editing').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }

    var reset_to_copy_mode = function (scope) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add copying mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].copy_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').attr('value', WCS4_AJAX_OBJECT[scope].add_item);
        // Add cancel copying button
        if ($('#wcs4-cancel-copying-wrapper').length == 0) {
            var cancel_button = '<span id="wcs4-cancel-copying-wrapper"><a href="#" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a></span>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-copying').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }
    var reset_to_create_mode = function (scope) {
        $('#wcs4-management-form-wrapper form').unbind('change.reset');
        // Add copying mode message
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].add_mode)
        $('#wcs4-reset-form').hide();
        $('#wcs4-management-form-wrapper').addClass('is-open');
        // Let's add the row id and the save button.
        $('#wcs4-submit-form').attr('value', WCS4_AJAX_OBJECT[scope].add_item);
        // Add cancel copying button
        if ($('#wcs4-cancel-copying-wrapper').length == 0) {
            var cancel_button = '<span id="wcs4-cancel-copying-wrapper"><a href="#" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a></span>';
            $('#wcs4-reset-form').after(cancel_button);
            $('#wcs4-cancel-copying').click(function () {
                reset_to_add_mode(scope);
            })
        }
    }

    var reset_to_add_mode = function (scope) {
        $('#wcs4-management-form-wrapper form')
            .one('change.reset', function () {
                $('#wcs4-reset-form').show();
            })
            .get(0).reset();
        $('#wcs4-management-form-wrapper form select').scrollTop(0);
        $('#wcs4-management-form-wrapper form').find('input,select').change();
        $('#wcs4-management-form-title').text(WCS4_AJAX_OBJECT[scope].add_mode);
        $('#wcs4-row-id').remove();
        $('#wcs4-cancel-copying-wrapper').remove();
        $('#wcs4-cancel-editing-wrapper').remove();
        $('#wcs4-submit-form').val(WCS4_AJAX_OBJECT[scope].add_item);
        $('#wcs4-management-form-wrapper').removeClass('is-open');
        $('tr.is-active').removeClass('is-active');
    }

    /**
     * Handles the Ajax UI messaging.
     */
    var show_message = function (message, status, errors) {
        remove_message();
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
            $('.form-field-' + field_id + '-wrap').addClass('form-invalid');
            for (var error_id in errors[field_id]) {
                var msg = $('<div class="error">').html(errors[field_id][error_id]);
                $('.form-field-' + field_id + '-wrap').append(msg);
            }
        }
    };
    var remove_message = function () {
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
    }
})(jQuery);