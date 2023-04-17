<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Helper\Admin;

?>
<div class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <h2 id="wcs4-management-form-title"><?= _x('Add New Progress', 'page title', 'wcs4') ?></h2>
    <form id="wcs4-progress-management-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-type-wrap">
            <label for="wcs4_progress_type"><?= __('Type', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list_options(
                'progress_type',
                'wcs4_progress_type',
                'type'
            ) ?>
        </fieldset>
        <?php
        if (empty($subject)): ?>
            <fieldset class="form-field form-required form-field-subject_id-wrap">
                <label for="wcs4_progress_subject_id"><?= __('Subject', 'wcs4') ?></label>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_progress_subject',
                    'wcs4_progress_subject',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ) ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_progress_subject" name="wcs4_progress_subject" value="<?= $subject ?>"/>
        <?php
        endif; ?>
        <?php
        if (empty($teacher)): ?>
            <fieldset class="form-field form-required form-field-teacher_id-wrap">
                <label for="wcs4_progress_teacher_id"><?= __('Teacher', 'wcs4') ?></label>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_progress_teacher',
                    'wcs4_progress_teacher',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ) ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_progress_teacher" name="wcs4_progress_teacher[]" value="<?= $teacher ?>"/>
        <?php
        endif; ?>
        <?php
        if (empty($student)): ?>
            <fieldset class="form-field form-required form-field-student_id-wrap">
                <label for="wcs4_progress_student_id"><?= __('Student', 'wcs4') ?></label>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'wcs4_progress_student',
                    'wcs4_progress_student',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ) ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_progress_student" name="wcs4_progress_student" value="<?= $student ?>"/>
        <?php
        endif; ?>
        <fieldset class="form-field form-required form-field-start_date-wrap">
            <label for="wcs4_progress_start_date"><?= __('Start date', 'wcs4') ?></label>
            <input type="date" id="wcs4_progress_start_date" name="wcs4_progress_start_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-end_date-wrap">
            <label for="wcs4_progress_end_date"><?= __('End date', 'wcs4') ?></label>
            <input type="date" id="wcs4_progress_end_date" name="wcs4_progress_end_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-indications-wrap">
            <label for="wcs4_progress_indications"><?= __('Indications', 'wcs4') ?></label>
            <textarea rows="6" id="wcs4_progress_indications" name="wcs4_progress_indications"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-improvements-wrap">
            <label for="wcs4_progress_improvements"><?= __('Improvements', 'wcs4') ?></label>
            <textarea rows="6" id="wcs4_progress_improvements" name="wcs4_progress_improvements"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4-progress-buttons-wrapper">
            <span class="spinner"></span>
            <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-progress-form"
                   value="<?= _x('Add Progress', 'button text', 'wcs4') ?>" name="wcs4-submit"/>
            <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-progress-form"
                    style="display: none;">
                <?= _x('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</div> <!-- /#progress-management-form-wrapper -->
