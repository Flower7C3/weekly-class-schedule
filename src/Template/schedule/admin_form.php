<?php

use WCS4\Helper\Admin;

?>
<div class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <h2 id="wcs4-management-form-title"><?= _x('Add New Lesson', 'page title', 'wcs4') ?></h2>
    <form id="wcs4-schedule-management-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_schedule_subject_id"><?= __('Subject', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'subject',
                'wcs4_schedule_subject',
                'wcs4_schedule_subject',
                null,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_schedule_teacher_id"><?= __('Teacher', 'wcs4') ?></label>
            <?= Admin::generate_admin_select_list(
                'teacher',
                'wcs4_schedule_teacher',
                'wcs4_schedule_teacher',
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
                'wcs4_schedule_student',
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
                'wcs4_schedule_classroom',
                null,
                true
            ) ?>
        </fieldset>
        <fieldset class="form-field row">
            <div class="form-field form-required form-field-weekday-wrap col-6">
                <label for="wcs4_schedule_weekday"><?= __('Weekday', 'wcs4') ?></label>
                <?= Admin::generate_weekday_select_list('wcs4_schedule_weekday', ['required' => true]) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                <label for="wcs4_schedule_start_time"><?= __('Start Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_schedule_start_time',
                    'wcs4_schedule_start_time',
                    ['default' => '09:00', 'required' => true, 'step' => 300]
                ) ?>
            </div>
            <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                <label for="wcs4_schedule_end_time"><?= __('End Time', 'wcs4') ?></label>
                <?= Admin::generate_time_select_list(
                    'wcs4_schedule_end_time',
                    'wcs4_schedule_end_time',
                    ['default' => '10:00', 'required' => true, 'step' => 300]
                ) ?>
            </div>
        </fieldset>
        <fieldset class="form-field form-required form-field-visibility-wrap" id="wcs4_schedule_visibility">
            <label for="wcs4_schedule_visibility"><?= __('Visibility', 'wcs4') ?></label>
            <?= wcs4_select_radio(array(
                'visible' => _x('Visible', 'visibility', 'wcs4'),
                'hidden' => _x('Hidden', 'visibility', 'wcs4'),
            ), 'wcs4_schedule_visibility', 'wcs4_schedule_visibility', 'visible', true) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-collision_detection-wrap" id="wcs4_schedule_collision_detection">
            <label for="wcs4_schedule_collision_detection"><?= __('Collision detection', 'wcs4') ?></label>
            <?= wcs4_select_radio(array(
                'yes' => __('Yes', 'wcs4'),
                'no' => __('No', 'wcs4'),
            ), 'wcs4_schedule_collision_detection', 'wcs4_schedule_collision_detection', 'yes', true) ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-notes-wrap">
            <label for="wcs4_schedule_notes"><?= __('Notes', 'wcs4') ?></label>
            <textarea rows="3" id="wcs4_schedule_notes" name="wcs4_schedule_notes"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4-schedule-buttons-wrapper">
            <span class="spinner"></span>
            <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-schedule-form"
                   value="<?= _x('Add Lesson', 'button text', 'wcs4') ?>" name="wcs4-submit"/>
            <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-schedule-form"
                    style="display: none;">
                <?= _x('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</div> <!-- /#schedule-management-form-wrapper -->
