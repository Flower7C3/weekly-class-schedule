<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Helper\Admin;

?>
<div class="wcs4-form-wrap modal modal-lg"
     id="wcs4-journal-modal">
    <div class="modal-dialog modal-dialog-centered wcs4-management-form-wrapper" id="wcs4-journal-form-wrapper">
        <form id="wcs4-journal-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <input type="hidden" name="row_id" value="">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title" data-wcs4="management-form-title">
                        <?= _x('Add New Journal', 'page title', 'wcs4') ?>
                    </strong>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <fieldset class="form-field form-required form-field-type-wrap">
                        <label for="wcs4_journal_type"><?= __('Type', 'wcs4') ?></label>
                        <?= Admin::generate_admin_select_list_options(
                            'journal_type',
                            'wcs4_journal_type',
                            'type'
                        ) ?>
                    </fieldset>
                    <fieldset class="form-field form-required form-field-subject_id-wrap">
                        <label for="wcs4_journal_subject"><?= __('Subject', 'wcs4') ?></label>
                        <?= Admin::generate_admin_select_list(
                            'subject',
                            'wcs4_journal_subject',
                            'subject',
                            null,
                            true,
                            false,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student],
                            true
                        ) ?>
                    </fieldset>
                    <fieldset class="form-field form-required form-field-teacher_id-wrap">
                        <label for="wcs4_journal_teacher"><?= __('Teacher', 'wcs4') ?></label>
                        <?= Admin::generate_admin_select_list(
                            'teacher',
                            'wcs4_journal_teacher',
                            'teacher',
                            null,
                            true,
                            true,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student],
                            true
                        ) ?>
                    </fieldset>
                    <fieldset class="form-field form-required form-field-student_id-wrap">
                        <label for="wcs4_journal_student"><?= __('Student', 'wcs4') ?></label>
                        <?= Admin::generate_admin_select_list(
                            'student',
                            'wcs4_journal_student',
                            'student',
                            null,
                            true,
                            true,
                            null,
                            ['subject' => $subject, 'teacher' => $teacher, 'student' => $student],
                            true
                        ) ?>
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
                </div>
                <div class="modal-footer">
                    <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
                    <span class="spinner"></span>
                    <button data-wcs4="submit-form" type="submit"
                            class="button button-primary wcs4-submit-journal-form"
                            name="wcs4-submit">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <?= _x('Add Journal', 'button text', 'wcs4') ?>
                    </button>
                    <button data-wcs4="cancel-form" type="reset"
                            data-bs-dismiss="modal"
                            class="button button-link wcs4-reset-journal-form"
                            style="display: none;">
                        <?= _x('Exit edit journal mode', 'button text', 'wcs4') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> <!-- /#journal-management-form-wrapper -->
