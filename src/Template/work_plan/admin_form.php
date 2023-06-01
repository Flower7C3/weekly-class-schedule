<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Helper\Admin;

?>
<div class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <h2 id="wcs4-management-form-title"><?php
        _ex('Add New Work Plan', 'page title', 'wcs4'); ?></h2>
    <form id="wcs4-work-plan-form" class="czr-form" action="<?php
    echo $_SERVER['PHP_SELF']; ?>" method="post">
        <fieldset class="form-field form-required form-field-type-wrap">
            <label for="wcs4_work_plan_type"><?php
                _e('Type', 'wcs4'); ?></label>
            <?php
            echo Admin::generate_admin_radio_options(
                'work_plan_type',
                'wcs4_work_plan_type',
                'type'
            ); ?>
        </fieldset>
        <?php
        if (empty($subject)): ?>
            <fieldset class="form-field form-required form-field-subject_id-wrap" style="display: none;">
                <label for="wcs4_work_plan_subject_id"><?php
                    _e('Subject', 'wcs4'); ?></label>
                <?php
                echo Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_work_plan_subject',
                    'subject',
                    $subject ?? null,
                    true,
                    true,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ); ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_work_plan_subject" name="subject" value="<?php
            echo $subject; ?>"/>
        <?php
        endif; ?>
        <?php
        if (empty($teacher)): ?>
            <fieldset class="form-field form-required form-field-teacher_id-wrap" style="display: none;">
                <label for="wcs4_work_plan_teacher_id"><?php
                    _e('Teacher', 'wcs4'); ?></label>
                <?php
                echo Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_work_plan_teacher',
                    'teacher',
                    $teacher ?? null,
                    true,
                    true,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ); ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_work_plan_teacher" name="teacher[]" value="<?php
            echo $teacher; ?>"/>
        <?php
        endif; ?>
        <?php
        if (empty($student)): ?>
            <fieldset class="form-field form-required form-field-student_id-wrap" style="display: none;">
                <label for="wcs4_work_plan_student_id"><?php
                    _e('Student', 'wcs4'); ?></label>
                <?php
                echo Admin::generate_admin_select_list(
                    'student',
                    'wcs4_work_plan_student',
                    'student',
                    $student ?? null,
                    true,
                    false,
                    null,
                    ['subject' => $subject ?? null, 'teacher' => $teacher ?? null, 'student' => $student ?? null]
                ); ?>
            </fieldset>
        <?php
        else: ?>
            <input type="hidden" id="wcs4_work_plan_student" name="student" value="<?php
            echo $student; ?>"/>
        <?php
        endif; ?>
        <fieldset class="form-field form-required form-field-start_date-wrap" style="display: none;">
            <label for="wcs4_work_plan_start_date"><?php
                _e('Start date', 'wcs4'); ?></label>
            <input type="date" id="wcs4_work_plan_start_date" name="start_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-end_date-wrap" style="display: none;">
            <label for="wcs4_work_plan_end_date"><?php
                _e('End date', 'wcs4'); ?></label>
            <input type="date" id="wcs4_work_plan_end_date" name="end_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-diagnosis-wrap" style="display: none;">
            <label for="wcs4_work_plan_diagnosis"><?php
                _e('Diagnosis', 'wcs4'); ?></label>
            <textarea rows="6" id="wcs4_work_plan_diagnosis" name="diagnosis"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-strengths-wrap" style="display: none;">
            <label for="wcs4_work_plan_strengths"><?php
                _e('Strengths', 'wcs4'); ?></label>
            <textarea rows="6" id="wcs4_work_plan_strengths" name="strengths"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-goals-wrap" style="display: none;">
            <label for="wcs4_work_plan_goals"><?php
                _e('Goals', 'wcs4'); ?></label>
            <textarea rows="6" id="wcs4_work_plan_goals" name="goals"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-methods-wrap" style="display: none;">
            <label for="wcs4_work_plan_methods"><?php
                _e('Methods', 'wcs4'); ?></label>
            <textarea rows="6" id="wcs4_work_plan_methods" name="methods"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4_work_plan_buttons-wrapper">
            <span class="spinner"></span>
            <button id="wcs4-submit-form" type="submit" disabled class="button button-primary wcs4-submit-work-plan-form"
                    name="wcs4-submit">
                <span class="dashicons dashicons-plus-alt"></span>
                <?= _x('Add Work Plan', 'button text', 'wcs4') ?>
            </button>
            <button id="wcs4-reset-form" type="reset" class="button button-link wcs4-reset-work-plan-form"
                    style="display: none;">
                <?php
                _ex('Reset form', 'button text', 'wcs4') ?>
            </button>
            <div id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></div>
        </fieldset>
    </form>
</div> <!-- /#work-plan-management-form-wrapper -->
