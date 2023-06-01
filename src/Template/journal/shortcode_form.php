<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Helper\Admin;

?>
<details class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <summary id="wcs4-management-form-title"><?= _x('Add New Journal', 'page title', 'wcs4') ?></summary>
    <form id="wcs4-journal-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-type-wrap">
            <label for="wcs4_journal_type"><?php
                _e('Type', 'wcs4'); ?></label>
            <?php
            echo Admin::generate_admin_select_list_options(
                'journal_type',
                'wcs4_journal_type',
                'type'
            ); ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_journal_subject"><?= __('Subject', 'wcs4') ?></label>
            <?php
            if (empty($subject)): ?>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_journal_subject',
                    'subject',
                    null,
                    true,
                    false,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($subject)->post_title ?>"/>
                <input type="hidden" id="wcs4_journal_subject" name="subject" value="<?= $subject ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_journal_teacher"><?= __('Teacher', 'wcs4') ?></label>
            <?php
            if (empty($teacher)): ?>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_journal_teacher',
                    'teacher',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($teacher)->post_title ?>"/>
                <input type="hidden" id="wcs4_journal_teacher" name="teacher[]" value="<?= $teacher ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_journal_student"><?= __('Student', 'wcs4') ?></label>
            <?php
            if (empty($student)): ?>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'wcs4_journal_student',
                    'student',
                    null,
                    true,
                    true,
                    null,
                    ['subject' => $subject, 'teacher' => $teacher, 'student' => $student]
                ) ?>
            <?php
            else: ?>
                <input readonly value="<?= get_post($student)->post_title ?>"/>
                <input type="hidden" id="wcs4_journal_student" name="student[]" value="<?= $student ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field row">
            <div class="form-field form-required form-field-date-wrap col-6">
                <label for="wcs4_journal_date"><?= __('Date', 'wcs4') ?></label>
                <?= Admin::generate_date_select_list(
                    'wcs4_journal_date',
                    'date',
                    ['default' => date('Y-m-d'), 'required' => true]
                ) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                <label for="wcs4_journal_start_time"><?= __('Start Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_journal_start_time',
                    'start_time',
                    ['default' => date('H:00', strtotime('-1 hour')), 'required' => true, 'step' => 300]
                ) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                <label for="wcs4_journal_end_time"><?= __('End Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_journal_end_time',
                    'end_time',
                    ['default' => date('H:00'), 'required' => true, 'step' => 300]
                ) ?>
            </div>
        </fieldset>
        <fieldset class="form-field form-required form-field-topic-wrap">
            <label for="wcs4_journal_topic"><?= __('Topic', 'wcs4') ?></label>
            <textarea rows="3" id="wcs4_journal_topic" name="topic"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4_journal_buttons-wrapper">
            <button id="wcs4-submit-form" type="submit" class="button button-primary wcs4-submit-journal-form"
                    name="wcs4-submit">
                <span class="dashicons dashicons-plus-alt"></span>
                <?= _x('Add Journal', 'button text', 'wcs4') ?>
            </button>
            <button id="wcs4-reset-form" type="reset" class="button button-link wcs4-reset-journal-form"
                    style="display: none;">
                <?= _x('Reset form', 'button text', 'wcs4') ?>
            </button>
            <span class="spinner"></span>
            <p id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></p>
        </fieldset>
    </form>
</details> <!-- /#journal-management-form-wrapper -->
