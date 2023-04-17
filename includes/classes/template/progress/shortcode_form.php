<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

?>
<details class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <summary id="wcs4-management-form-title"><?php
        _ex('Add New Progress', 'page title', 'wcs4'); ?></summary>
    <form id="wcs4-progress-management-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_progress_subject_id"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            if (empty($subject)): ?>
                <?= WCS_Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_progress_subject',
                    'wcs4_progress_subject',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($subject)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_subject" name="wcs4_progress_subject" value="<?= $subject ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_progress_teacher_id"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            if (empty($teacher)): ?>
                <?= WCS_Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_progress_teacher',
                    'wcs4_progress_teacher',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($teacher)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_teacher" name="wcs4_progress_teacher[]" value="<?= $teacher ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_progress_student_id"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            if (empty($student)): ?>
                <?= WCS_Admin::generate_admin_select_list(
                    'student',
                    'wcs4_progress_student',
                    'wcs4_progress_student',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($student)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_student" name="wcs4_progress_student" value="<?= $student ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <input type="hidden" id="wcs4_progress_type= WCS_DB_Progress_Item::TYPE_PARTIAL; ?>"/>
        <fieldset class="form-field form-required form-field-indications-wrap">
            <label for="wcs4_progress_indications"><?php
                _e('Indications', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_progress_indications" name="wcs4_progress_indications"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-improvements-wrap">
            <label for="wcs4_progress_improvements"><?php
                _e('Improvements', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_progress_improvements" name="wcs4_progress_improvements"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4-progress-buttons-wrapper">
            <span class="spinner"></span>
            <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-progress-form"
                   value="<?php
                   _ex('Add Progress', 'button text', 'wcs4') ?>" name="wcs4-submit"/>
            <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-progress-form"
                    style="display: none;">
                <?php
                _ex('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</details> <!-- /#progress-management-form-wrapper -->