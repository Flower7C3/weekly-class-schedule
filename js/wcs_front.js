/**
 * Scripts for the Weekly Class Schedule 4.0 front-end.
 */
(function ($) {
    $(document).ready(function () {
        WCS4_LIB.apply_qtip();
        bind_submit_journal_handler();
        bind_submit_work_plan_handler();
        bind_submit_progress_handler();
        $(document).on('change.wcs4_journal_type', '#wcs4-journal-form [name="type"]', function () {
            bind_journal_form_handler();
        });
        bind_journal_form_handler();
        $(document).on('change.form-invalid', '.form-invalid input,.form-invalid textarea,.form-invalid select', function () {
            $(this).closest('.form-invalid').find('.error').remove();
        });
        $('.wcs4_schedule_wrapper .toggle').on('click', function () {
            if (document.fullscreenElement) {
                $('.toggle').removeClass('fa-minimize').addClass('fa-maximize')
                document.exitFullscreen();
            } else {
                $('.wcs4_schedule_grid').get(0).requestFullscreen();
                $('.toggle').removeClass('fa-maximize').addClass('fa-minimize')
            }
        });

        html2canvas(document.querySelector(".wcs4_schedule_grid")).then((canvas) => {
            console.log('canvas')
            const data = canvas.toDataURL("image/png;base64");
            const downloadLink = document.querySelector(".wcs4_schedule_wrapper .download");
            downloadLink.download = $('#wcs_schedule-shortcode-wrapper h2').text() + ' ' + $('.entry-title').text();
            downloadLink.href = data;
            console.log(downloadLink)
        });
    });
    /**
     * Handles the Add Item button click event.
     */
    let bind_submit_journal_handler = function () {
        let $form = $('#wcs4-journal-form');
        $form.find('#wcs4-submit-form').click(function (e) {
            e.preventDefault();
            let $spinner = $(this).closest('form').find('.spinner');
            $spinner.addClass('is-active');
            let entry = {
                action: 'wcs_add_journal_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                date: WCS4_LIB.form_field_value($form, 'date'),
                start_time: WCS4_LIB.form_field_value($form, 'start_time'),
                end_time: WCS4_LIB.form_field_value($form, 'end_time'),
                topic: WCS4_LIB.form_field_value($form, 'topic'),
                type: WCS4_LIB.form_field_value($form, 'type'),
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                $spinner.removeClass('is-active');
                // if (200 <= status && status < 300) {
                //     jQuery('#wcs4-journal-form').get(0).reset()
                // }
            });
        });
    }


    let bind_journal_form_handler = function () {
        let $form = $('#wcs4-journal-form');
        let type = $form.find('[name="type"]:checked').val();
        if (typeof type === 'undefined' || '' === type) {
            return;
        }
        if (type.startsWith('type.absent_teacher.') || type === 'type.teacher_office_works') {
            $('#wcs4_journal_student').closest('fieldset').hide();
        } else {
            $('#wcs4_journal_student').closest('fieldset').show();
        }
    }
    let bind_submit_work_plan_handler = function () {
        let $form = $('#wcs4-work-plan-form');
        $form.find('#wcs4-submit-form').click(function (e) {
            e.preventDefault();
            let $spinner = $(this).closest('form').find('.spinner');
            $spinner.addClass('is-active');
            let entry = {
                action: 'wcs_add_work_plan_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                diagnosis: WCS4_LIB.form_field_value($form, 'diagnosis'),
                strengths: WCS4_LIB.form_field_value($form, 'strengths'),
                goals: WCS4_LIB.form_field_value($form, 'goals'),
                methods: WCS4_LIB.form_field_value($form, 'methods'),
                type: 'type.partial',
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                $spinner.removeClass('is-active');
                // if (200 <= status && status < 300) {
                //     jQuery('#wcs4-work-plan-form').get(0).reset()
                // }
            });
        });
    }
    let bind_submit_progress_handler = function () {
        let $form = $('#wcs4-progress-form');
        $form.find('#wcs4-submit-form').click(function (e) {
            e.preventDefault();
            let $spinner = $(this).closest('form').find('.spinner');
            $spinner.addClass('is-active');
            let entry = {
                action: 'wcs_add_progress_entry',
                security: WCS4_AJAX_OBJECT.ajax_nonce,
                subject_id: WCS4_LIB.form_field_value($form, 'subject'),
                teacher_id: WCS4_LIB.form_field_value($form, 'teacher'),
                student_id: WCS4_LIB.form_field_value($form, 'student'),
                improvements: WCS4_LIB.form_field_value($form, 'improvements'),
                indications: WCS4_LIB.form_field_value($form, 'indications'),
                type: 'type.partial',
            };
            WCS4_LIB.submit_entry(entry, function (data, status) {
                $spinner.removeClass('is-active');
                // if (200 <= status && status < 300) {
                //     jQuery('#wcs4-progress-form').get(0).reset()
                // }
            });
        });
    }

})(jQuery);
