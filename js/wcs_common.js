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
        let $rowId = $('#wcs4-row-id');
        if ($rowId.length > 0) {
            entry.row_id = $rowId.val();
        }

        let $spinner = $('.wcs4-management-form-wrapper .spinner');
        $spinner.addClass('is-active');
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data, state, xhr) {
            WCS4_LIB.show_message(data.response, xhr.status);
            callback(data, xhr.status);
        }).fail(function (err) {
            let json = err.responseJSON || {};
            WCS4_LIB.show_message(
                json.response || WCS4_AJAX_OBJECT.ajax_error,
                err.status || 'error',
                json.errors || []
            );
        }).always(function () {
            $spinner.removeClass('is-active');
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

        $('#wcs4-' + scope + '-form-wrapper .spinner').addClass('is-active');

        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data, state, xhr) {
            callback(data, xhr.status);
        }).fail(function (err) {
            let json = err.responseJSON || {};
            WCS4_LIB.show_message(
                json.response || WCS4_AJAX_OBJECT.ajax_error,
                err.status || 'error',
                json.errors || []
            );
        }).always(function () {
            $('#wcs4-' + scope + '-form-wrapper .spinner').removeClass('is-active');
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
        $('html,body').css({'cursor': 'wait'});
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
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $fields = $wrapper.find('input,select,textarea');
        $wrapper.find('.spinner').addClass('is-active');
        $fields.prop('readonly', true);
        jQuery.post(WCS4_AJAX_OBJECT.ajax_url, get_query)
            .always(function () {
                $('html,body').css({'cursor': 'auto'});
                $wrapper.find('.spinner').removeClass('is-active');
                $fields.prop('readonly', false);
            })
            .done(function (data) {
                set_entry_data_to_form(data.response)
                reset_callback(scope, data.response)
            })
            .fail(function (err) {
                console.error(err);
                show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
            })
        ;
    };

    /**
     * Ustawia wartość grupy radio w formularzu (zaznacza input o danej value).
     * @param {jQuery} $form - formularz
     * @param {string} name - atrybut name grupy radio
     * @param {string|number|null|undefined} value - wartość do zaznaczenia
     * @param {boolean} [triggerChange=false] - czy wywołać change() na zaznaczonym
     */
    let set_radio_value = function ($form, name, value, triggerChange) {
        let val = value != null ? String(value) : '';
        let $radios = $form.find('input[type="radio"][name="' + name + '"]');
        $radios.each(function () {
            $(this).prop('checked', $(this).val() === val);
        });
        if (triggerChange) {
            $form.find('input[type="radio"][name="' + name + '"]:checked').first().trigger('change');
        }
    };

    /**
     * Ustawia wartość pola input (text, date, time, number itd.) lub textarea.
     * @param {jQuery} $form - formularz
     * @param {string} name - atrybut name pola
     * @param {string|number|null|undefined} value - wartość do ustawienia
     */
    let set_input_value = function ($form, name, value) {
        let val = (value != null && value !== '') ? String(value) : '';
        let $el = $form.find('input[name="' + name + '"]').not('[type="radio"], [type="checkbox"]')
            .add($form.find('textarea[name="' + name + '"]'));
        $el.val(val);
    };

    /**
     * Ustawia wartość selecta (pojedynczy lub wielokrotny wybór).
     * @param {jQuery} $form - formularz
     * @param {string} name - atrybut name selecta
     * @param {string|number|Array|null|undefined} value - wartość; dla multi: tablica lub string po przecinku
     */
    let set_select_value = function ($form, name, value) {
        let $sel = $form.find('select[name="' + name + '"]');
        if (!$sel.length) {
            return;
        }
        if ($sel.prop('multiple')) {
            let arr = Array.isArray(value)
                ? value.map(function (v) {
                    return String(v);
                })
                : (value != null ? String(value).split(',').map(function (s) {
                    return s.trim();
                }) : []);
            $sel.val(arr);
        } else {
            $sel.val(value != null ? String(value) : '');
        }
    };

    /**
     * Odczytuje wartość pola formularza (checkbox, radio, input, textarea, select).
     * @param {jQuery} $form - formularz
     * @param {string} name - atrybut name pola (dla multi: 'name' lub 'name[]')
     * @returns {string|string[]|null} - wartość lub tablica (checkbox/multi select) lub null
     */
    let get_field_value = function ($form, name) {
        let $checkedCb = $form.find('input[type="checkbox"][name="' + name + '"]:checked');
        if ($checkedCb.length) {
            return $checkedCb.toArray().map(function (item) {
                return item.value;
            });
        }
        let $checkedRadio = $form.find('input[type="radio"][name="' + name + '"]:checked');
        if ($checkedRadio.length) {
            return $checkedRadio.val();
        }
        let $input = $form.find('input[name="' + name + '"]').not('[type="radio"], [type="checkbox"]');
        if ($input.length) {
            return $input.val();
        }
        let $inputArr = $form.find('input[name="' + name + '[]"]');
        if ($inputArr.length) {
            return $inputArr.toArray().map(function (item) {
                return item.value;
            });
        }
        let $textarea = $form.find('textarea[name="' + name + '"]');
        if ($textarea.length) {
            return $textarea.val();
        }
        let $select = $form.find('select[name="' + name + '"]');
        if ($select.length) {
            return $select.val();
        }
        return null;
    };
    let update_view = function ($parent, entry, action) {
        let $spinner = $parent.find('.spinner');
        $spinner.addClass('is-active');
        $.post(WCS4_AJAX_OBJECT.ajax_url, entry, function (data) {
            let html = data.html;
            let $dayContent = $parent.find('.wcs4-day-content-wrapper');
            if (html.length > 0 && $dayContent.data('hash') !== $(html).data('hash')) {
                $dayContent.fadeOut(300, function () {
                    $(this).remove();
                    $parent.append(html);
                    if (action === 'fade') {
                        let $newContent = $parent.find('.wcs4-day-content-wrapper');
                        $newContent.hide().fadeIn(300, function () {
                            $(this).removeAttr('style');
                        });
                    }
                });
            }
        }).fail(function (err) {
            console.error(err);
            show_message(WCS4_AJAX_OBJECT.ajax_error, 'error');
        }).always(function () {
            $('.wcs4-management-form-wrapper .spinner').removeClass('is-active');
            $spinner.removeClass('is-active');
        });
    };


    /**
     * Enter edit mode
     */
    let reset_to_edit_mode = function (scope, entry) {
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $form = $wrapper.find('form');
        let $resetForm = $('[data-wcs4="reset-form"]');
        let $rowId = $('#wcs4-row-id');
        $form.unbind('change.reset');
        $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].edit_mode);
        $resetForm.hide();
        $wrapper.addClass('is-open');
        $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].save_item);
        if ($rowId.length > 0) {
            $rowId.val(entry.id);
        } else {
            let row_hidden_field = $('<input>', {
                type: 'hidden',
                id: 'wcs4-row-id',
                name: 'wcs4-row-id',
                value: entry.id
            });
            $wrapper.append(row_hidden_field);
        }
        let $cancelEditing = $('#wcs4-cancel-editing');
        if ($cancelEditing.length === 0) {
            let cancel_button = '<span class="wp-block-button is-style-outline"><a href="#" class="wp-block-button__link wp-element-button" id="wcs4-cancel-editing">' + WCS4_AJAX_OBJECT[scope].cancel_editing + '</a></span>';
            $resetForm.after(cancel_button);
            $cancelEditing = $('#wcs4-cancel-editing');
            $cancelEditing.on('click', function () {
                reset_to_add_mode(scope);
            });
        }
    }

    let reset_to_copy_mode = function (scope) {
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $resetForm = $('[data-wcs4="reset-form"]');
        $wrapper.find('form').unbind('change.reset');
        $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].copy_mode);
        $resetForm.hide();
        $wrapper.addClass('is-open');
        $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].add_item);
        let $cancelCopying = $('#wcs4-cancel-copying');
        if ($cancelCopying.length === 0) {
            let cancel_button = '<span class="wp-block-button is-style-outline"><a href="#" class="wp-block-button__link wp-element-button" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a></span>';
            $resetForm.after(cancel_button);
            $cancelCopying = $('#wcs4-cancel-copying');
            $cancelCopying.on('click', function () {
                reset_to_add_mode(scope);
            });
        }
    }
    let reset_to_create_mode = function (scope) {
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $resetForm = $('[data-wcs4="reset-form"]');
        $wrapper.find('form').unbind('change.reset');
        $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].add_mode);
        $resetForm.hide();
        $wrapper.addClass('is-open');
        $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].add_item);
        let $cancelCopying = $('#wcs4-cancel-copying');
        if ($cancelCopying.length === 0) {
            let cancel_button = '<span class="wp-block-button is-style-outline"><a href="#" class="wp-block-button__link wp-element-button" id="wcs4-cancel-copying">' + WCS4_AJAX_OBJECT[scope].cancel_copying + '</a></span>';
            $resetForm.after(cancel_button);
            $cancelCopying = $('#wcs4-cancel-copying');
            $cancelCopying.on('click', function () {
                reset_to_add_mode(scope);
            });
        }
    }

    let reset_to_add_mode = function (scope) {
        let $wrapper = $('.wcs4-management-form-wrapper');
        let $form = $wrapper.find('form');
        let $dataTitle = $('[data-wcs4="management-form-title"]');
        let $dataSubmit = $('[data-wcs4="submit-form"]');
        $form.one('change.reset', function () {
            $('[data-wcs4="reset-form"]').show();
        }).get(0).reset();
        $form.trigger("reset");
        $form.find('input,textarea,select').trigger("change");
        $dataTitle.text(WCS4_AJAX_OBJECT[scope].add_mode);
        $('#wcs4-row-id').remove();
        $('#wcs4-cancel-copying').remove();
        $('#wcs4-cancel-editing').remove();
        $dataSubmit.html(WCS4_AJAX_OBJECT[scope].add_item);
        $wrapper.removeClass('is-open');
        $('tr.is-active').removeClass('is-active');
    }

    /**
     * Where inline AJAX status (success/error) is rendered. Kept separate from
     * #wcs4-ajax-text-wrapper so code can replace details without wiping the banner.
     */
    let resolveAjaxMessageTarget = function () {
        let $visibleBanner = $('.wcs4-ajax-banner').filter(':visible').first();
        if ($visibleBanner.length) {
            return $visibleBanner;
        }
        let $banner = $('.wcs4-ajax-banner').first();
        if ($banner.length) {
            return $banner;
        }
        let $legacy = $('#wcs4-ajax-text-wrapper.wcs4-ajax-text');
        if ($legacy.length) {
            return $legacy.first();
        }
        return $('.wcs4-ajax-text').first();
    };

    /**
     * Handles the Ajax UI messaging.
     */
    let show_message = function (message, status, errors) {
        remove_message();
        if (message !== undefined && message !== null && String(message) !== '') {
            let $text = resolveAjaxMessageTarget();
            if (!$text.length) {
                return;
            }
            $text.html('');
            // Close button (messages should not disappear automatically).
            let $close = $('<button type="button" class="notice-dismiss"></button>');
            $close.attr('aria-label', (WCS4_AJAX_OBJECT && WCS4_AJAX_OBJECT.close_aria_label) ? WCS4_AJAX_OBJECT.close_aria_label : 'Close');
            $close.on('click', function () {
                remove_message();
            });
            $text.append($close);
            if ('created' === status || 'updated' === status || (typeof status === 'number' && 200 <= status && status < 300)) {
                $text.addClass('updated');
                $text.append('<span class="dashicons dashicons-cloud-saved"></span>');
            } else if (
                'error' === status
                || (typeof status === 'number' && (400 <= status || status === 0))
            ) {
                $text.addClass('error');
                $text.append('<span class="dashicons dashicons-warning"></span>');
            }
            $text.append($('<span>').text(String(message))).show();
        }
        if (!errors || typeof errors !== 'object' || Array.isArray(errors)) {
            return;
        }
        for (let field_id in errors) {
            if (!Object.prototype.hasOwnProperty.call(errors, field_id)) {
                continue;
            }
            let $wrap = $('.form-field-' + field_id + '-wrap');
            $wrap.addClass('form-invalid');
            for (let error_id in errors[field_id]) {
                if (!Object.prototype.hasOwnProperty.call(errors[field_id], error_id)) {
                    continue;
                }
                let msg = $('<div class="error">').text(errors[field_id][error_id]);
                $wrap.append(msg);
            }
        }
    };
    let remove_message = function () {
        $('.wcs4-ajax-banner').each(function () {
            let $b = $(this);
            $b.html('').hide().removeClass('updated').removeClass('error');
        });
        let $legacy = $('#wcs4-ajax-text-wrapper.wcs4-ajax-text');
        if ($legacy.length) {
            $legacy.html('').hide().removeClass('updated').removeClass('error');
        } else {
            $('#wcs4-ajax-text-wrapper').html('');
        }
        let $formFields = $('.form-field');
        $formFields.removeClass('form-invalid');
        $formFields.find('.error').remove();
    };

    return {
        // tools
        apply_qtip,
        find_get_parameter,
        // form
        submit_entry,
        modify_entry,
        fetch_entry_data_to_form,
        get_field_value,
        set_radio_value,
        set_input_value,
        set_select_value,
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