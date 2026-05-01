/**
 * Scripts for the Weekly Class Schedule 4.0 front-end.
 */
(function ($) {
    $(document).ready(function () {
        WCS4_LIB.apply_qtip();

        $(document).on('change.form-invalid', '.form-invalid input,.form-invalid textarea,.form-invalid select', function () {
            $(this).closest('.form-invalid').find('.error').remove();
        });

        $(document).on('click.wcs4-schedule-fullscreen', '.wcs4_schedule_wrapper .toggle', function (e) {
            e.preventDefault();
            const gridEl = $(this).closest('.wcs4_schedule_wrapper').find('.wcs4_schedule_grid').get(0);
            if (!gridEl) {
                return;
            }
            if (gridEl.requestFullscreen) {
                gridEl.requestFullscreen();
            } else if (gridEl.webkitRequestFullscreen) {
                gridEl.webkitRequestFullscreen();
            }
        });

        $(document).on('click.wcs4-schedule-png', '.wcs4_schedule_wrapper a.download', function (e) {
            e.preventDefault();
            const gridEl = $(this).closest('.wcs4_schedule_wrapper').find('.wcs4_schedule_grid').get(0);
            if (!gridEl || typeof html2canvas !== 'function') {
                return;
            }
            const titleBits = [];
            const $wrapHeading = $('#wcs_schedule-shortcode-wrapper h2');
            if ($wrapHeading.length) {
                titleBits.push($wrapHeading.text());
            }
            const $entry = $('.wp-block-post-title').first();
            if ($entry.length) {
                titleBits.push($entry.text());
            }
            const baseName = titleBits.join(' ').trim() || 'schedule';
            html2canvas(gridEl).then(function (canvas) {
                const dataUrl = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.href = dataUrl;
                link.download = baseName.replace(/[/\\?%*:|"<>]/g, '-') + '.png';
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    });
})(jQuery);

let WCS4_FRONT = (function ($) {

    let bind_edit_handler = function (scope, set_entry_data_to_form) {
        $('#wcs4-' + scope + '-modal').on('hidden.bs.modal', function () {
            let $form = $('.wcs4-management-form-wrapper form');
            $form.trigger("reset");
            $form.find('input,textarea,select').trigger("change");
            $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].add_mode);
            $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].add_item);
            $('[data-wcs4="cancel-form"]').hide();
        })
        $(document).on('click.wcs4-edit-' + scope + '-button', '[data-scope="' + scope + '"] .wcs4-edit-button', function (e) {
            WCS4_LIB.fetch_entry_data_to_form(scope, $(this).data('id'), set_entry_data_to_form, function () {
                let $modal = $('#wcs4-' + scope + '-modal');
                let el = $modal.get(0);
                if (el && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(el).show();
                } else if (typeof $modal.modal === 'function') {
                    $modal.modal('show');
                }
                let $form = $('.wcs4-management-form-wrapper form');
                $form.find('input,textarea,select').focus();
                $form.find('[data-readonly]').prop('readonly', true);
                $form.unbind('change.reset');
                $('[data-wcs4="management-form-title"]').text(WCS4_AJAX_OBJECT[scope].edit_mode)
                $('[data-wcs4="submit-form"]').html(WCS4_AJAX_OBJECT[scope].save_item);
                $('[data-wcs4="cancel-form"]').show();
            });
            e.preventDefault();
        });
    };

    return {
        bind_edit_handler,
    }
})(jQuery);