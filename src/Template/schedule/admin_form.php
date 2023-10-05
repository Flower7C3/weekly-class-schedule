<?php

use WCS4\Helper\Admin;

?>
<div class="wcs4-form-wrap wcs4-management-form-wrapper" id="wcs4-schedule-form-wrapper">
    <h2 data-wcs4="management-form-title"><?= _x('Add New Lesson', 'page title', 'wcs4') ?></h2>
    <form id="wcs4_schedule_management-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_schedule_subject_id"><?= __('Subject', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'subject',
                'wcs4_schedule_subject',
                'subject',
                null,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_schedule_teacher_id"><?= __('Teacher', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'teacher',
                'wcs4_schedule_teacher',
                'teacher',
                null,
                true,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_schedule_student_id"><?= __('Student', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'student',
                'wcs4_schedule_student',
                'student',
                null,
                true,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-classroom_id-wrap">
            <label for="wcs4_schedule_classroom_id"><?= __('Classroom', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'classroom',
                'wcs4_schedule_classroom',
                'classroom',
                null,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field row">
            <div class="form-field form-required form-field-weekday-wrap col-6">
                <label for="wcs4_schedule_weekday"><?= __('Weekday', 'wcs4') ?></label>
                <?= Admin::generate_weekday_select_list('wcs4_schedule_weekday', 'weekday', ['required' => true]) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                <label for="wcs4_schedule_start_time"><?= __('Start Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_schedule_start_time',
                    'start_time',
                    ['default' => '09:00', 'required' => true, 'step' => 300]
                ) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                <label for="wcs4_schedule_end_time"><?= __('End Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_schedule_end_time',
                    'end_time',
                    ['default' => '10:00', 'required' => true, 'step' => 300]
                ) ?>
            </div>
        </fieldset>
        <fieldset class="form-field form-required form-field-visibility-wrap" id="wcs4_schedule_visibility">
            <label for="wcs4_schedule_visibility"><?= __('Visibility', 'wcs4') ?></label>
            <?= Admin::generate_admin_radio_options(
                'schedule_visibility',
                'wcs4_schedule_visibility',
                'visibility',
                $_GET['type'] ?? 'visible',
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-collision_detection-wrap"
                  id="wcs4_schedule_collision_detection">
            <label for="wcs4_schedule_collision_detection"><?= __('Collision detection', 'wcs4') ?></label>
            <?= Admin::generate_admin_radio_options(
                'schedule_collision_detection',
                'wcs4_schedule_collision_detection',
                'collision_detection',
                $_GET['type'] ?? 'yes',
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-notes-wrap">
            <label for="wcs4_schedule_notes"><?= __('Notes', 'wcs4') ?></label>
            <textarea rows="3" id="wcs4_schedule_notes" name="notes"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4_schedule_buttons-wrapper">
            <span class="spinner"></span>
            <button data-wcs4="submit-form" type="submit" class="button button-primary wcs4-submit-schedule-form"
                    name="wcs4-submit">
                <span class="dashicons dashicons-plus-alt"></span>
                <?= _x('Add Lesson', 'button text', 'wcs4') ?>
            </button>
            <button data-wcs4="reset-form" type="reset" class="button button-link wcs4-reset-schedule-form"
                    style="display: none;">
                <?= _x('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</div> <!-- /#schedule-management-form-wrapper -->
