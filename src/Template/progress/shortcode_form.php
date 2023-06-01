<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Entity\Progress_Item;
use WCS4\Helper\Admin;

?>
<details class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <summary id="wcs4-management-form-title"><?= _x('Add New Progress', 'page title', 'wcs4') ?></summary>
    <form id="wcs4-progress-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_progress_subject"><?= __('Subject', 'wcs4') ?></label>
            <?php
            if (empty($subject)): ?>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_progress_subject',
                    'subject',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($subject)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_subject" name="subject" value="<?= $subject ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_progress_teacher"><?= __('Teacher', 'wcs4') ?></label>
            <?php
            if (empty($teacher)): ?>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_progress_teacher',
                    'teacher',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($teacher)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_teacher" name="teacher[]" value="<?= $teacher ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_progress_student"><?= __('Student', 'wcs4') ?></label>
            <?php
            if (empty($student)): ?>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'wcs4_progress_student',
                    'student',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($student)->post_title ?>"/>
                <input type="hidden" id="wcs4_progress_student" name="student" value="<?= $student ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <input type="hidden" id="wcs4_progress_type" value="<?= Progress_Item::TYPE_PARTIAL; ?>"/>
        <fieldset class="form-field form-required form-field-indications-wrap">
            <label for="wcs4_progress_indications"><?= __('Indications', 'wcs4') ?></label>
            <textarea rows="5" id="wcs4_progress_indications" name="indications"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-improvements-wrap">
            <label for="wcs4_progress_improvements"><?= __('Improvements', 'wcs4') ?></label>
            <textarea rows="5" id="wcs4_progress_improvements" name="improvements"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4_progress_buttons-wrapper">
            <button id="wcs4-submit-form" type="submit" class="button button-primary wcs4-submit-progress-form"
                    name="wcs4-submit">
                <span class="dashicons dashicons-plus-alt"></span>
                <?= _x('Add Progress', 'button text', 'wcs4') ?>
            </button>
            <button id="wcs4-reset-form" type="reset" class="button button-link wcs4-reset-progress-form"
                    style="display: none;">
                <?= _x('Reset form', 'button text', 'wcs4') ?>
            </button>
            <span class="spinner"></span>
            <p id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></p>
        </fieldset>
    </form>
</details> <!-- /#progress-management-form-wrapper -->