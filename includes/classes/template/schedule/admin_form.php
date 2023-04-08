<div class="form-wrap" id="wcs4-management-form-wrapper">
    <h2 id="wcs4-management-form-title"><?php
        _ex('Add New Lesson', 'page title', 'wcs4'); ?></h2>
    <form id="wcs4-schedule-management-form" action="<?php
    echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_lesson_subject_id"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'subject',
                'wcs4_lesson_subject',
                'wcs4_lesson_subject',
                null,
                true
            ); ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_lesson_teacher_id"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'teacher',
                'wcs4_lesson_teacher',
                'wcs4_lesson_teacher',
                null,
                true,
                true
            ); ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_lesson_student_id"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'student',
                'wcs4_lesson_student',
                'wcs4_lesson_student',
                null,
                true,
                true
            ); ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-classroom_id-wrap">
            <label for="wcs4_lesson_classroom_id"><?php
                _e('Classroom', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_admin_select_list(
                'classroom',
                'wcs4_lesson_classroom',
                'wcs4_lesson_classroom',
                null,
                true
            ); ?>
        </fieldset>
        <fieldset class="form-field row">
            <div class="form-field form-required form-field-weekday-wrap col-6">
                <label for="wcs4_lesson_weekday"><?php
                    _e('Weekday', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_weekday_select_list('wcs4_lesson_weekday', ['required' => true]); ?>
            </div>
            <div class="form-field form-time-field form-required form-field-start_time-wrap col-3">
                <label for="wcs4_lesson_start_time"><?php
                    _e('Start Time', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_time_select_list(
                    'wcs4_lesson_start_time',
                    'wcs4_lesson_start_time',
                    ['default' => '09:00', 'required' => true, 'step' => 300]
                ); ?>
            </div>
            <div class="form-field form-time-field form-required form-field-end_time-wrap col-3">
                <label for="wcs4_lesson_end_time"><?php
                    _e('End Time', 'wcs4'); ?></label>
                <?php
                echo WCS_Admin::generate_time_select_list(
                    'wcs4_lesson_end_time',
                    'wcs4_lesson_end_time',
                    ['default' => '10:00', 'required' => true, 'step' => 300]
                ); ?>
            </div>
        </fieldset>
        <fieldset class="form-field form-required form-field-visibility-wrap" id="wcs4_lesson_visibility">
            <label for="wcs4_lesson_visibility"><?php
                _e('Visibility', 'wcs4'); ?></label>
            <?php
            echo WCS_Admin::generate_visibility_fields('wcs4_lesson_visibility', 'visible', true); ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-notes-wrap">
            <label for="wcs4_lesson_notes"><?php
                _e('Notes', 'wcs4'); ?></label>
            <textarea rows="3" id="wcs4_lesson_notes" name="wcs4_lesson_notes"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4-schedule-buttons-wrapper">
            <span class="spinner"></span>
            <input id="wcs4-submit-form" type="submit" class="button-primary wcs4-submit-lesson-form"
                   value="<?php
                   _ex('Add Lesson', 'button text', 'wcs4') ?>" name="wcs4-submit"/>
            <button id="wcs4-reset-form" type="reset" class="button-link wcs4-reset-lesson-form"
                    style="display: none;">
                <?php
                _ex('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</div> <!-- /#schedule-management-form-wrapper -->
