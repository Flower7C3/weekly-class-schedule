<?php
/**
 * @var $subject
 * @var $teacher
 * @var $student
 */

use WCS4\Entity\WorkPlan_Item;
use WCS4\Helper\Admin;

?>
<details class="wcs4-form-wrap" id="wcs4-management-form-wrapper">
    <summary id="wcs4-management-form-title"><?php
        _ex('Add New Work Plan', 'page title', 'wcs4'); ?></summary>
    <form id="wcs4-work-plan-form" class="czr-form" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
        <fieldset class="form-field form-required form-field-subject_id-wrap">
            <label for="wcs4_work_plan_subject"><?php
                _e('Subject', 'wcs4'); ?></label>
            <?php
            if (empty($subject)): ?>
                <?= Admin::generate_admin_select_list(
                    'subject',
                    'wcs4_work_plan_subject',
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
                <input type="hidden" id="wcs4_work_plan_subject" name="subject"
                       value="<?= $subject ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-teacher_id-wrap">
            <label for="wcs4_work_plan_teacher"><?php
                _e('Teacher', 'wcs4'); ?></label>
            <?php
            if (empty($teacher)): ?>
                <?= Admin::generate_admin_select_list(
                    'teacher',
                    'wcs4_work_plan_teacher',
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
                <input type="hidden" id="wcs4_work_plan_teacher" name="teacher[]"
                       value="<?= $teacher ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <fieldset class="form-field form-required form-field-student_id-wrap">
            <label for="wcs4_work_plan_student"><?php
                _e('Student', 'wcs4'); ?></label>
            <?php
            if (empty($student)): ?>
                <?= Admin::generate_admin_select_list(
                    'student',
                    'wcs4_work_plan_student',
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
                <input type="hidden" id="wcs4_work_plan_student" name="student" value="<?= $student ?>"/>
            <?php
            endif; ?>
        </fieldset>
        <input type="hidden" id="wcs4_work_plan_type" name="type"
               value="<?= WorkPlan_Item::TYPE_PARTIAL ?>"/>
        <fieldset class="form-field form-required form-field-start_date-wrap">
            <label for="wcs4_progress_start_date"><?= __('Start date', 'wcs4') ?></label>
            <input type="date" id="wcs4_progress_start_date" name="start_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-end_date-wrap">
            <label for="wcs4_progress_end_date"><?= __('End date', 'wcs4') ?></label>
            <input type="date" id="wcs4_progress_end_date" name="end_date"/>
        </fieldset>
        <fieldset class="form-field form-required form-field-diagnosis-wrap">
            <label for="wcs4_work_plan_diagnosis"><?php
                _e('Diagnosis', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_work_plan_diagnosis" name="diagnosis"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-strengths-wrap">
            <label for="wcs4_work_plan_strengths"><?php
                _e('Strengths', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_work_plan_strengths" name="strengths"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-goals-wrap">
            <label for="wcs4_work_plan_goals"><?php
                _e('Goals', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_work_plan_goals" name="goals"></textarea>
        </fieldset>
        <fieldset class="form-field form-required form-field-methods-wrap">
            <label for="wcs4_work_plan_methods"><?php
                _e('Methods', 'wcs4'); ?></label>
            <textarea rows="5" id="wcs4_work_plan_methods" name="methods"></textarea>
        </fieldset>
        <fieldset class="submit" id="wcs4_work_plan_buttons-wrapper">
            <button id="wcs4-submit-form" type="submit" class="button button-primary wcs4-submit-work-plan-form"
                    name="wcs4-submit">
                <span class="dashicons dashicons-plus-alt"></span>
                <?= _x('Add Work Plan', 'button text', 'wcs4') ?>
            </button>
            <button id="wcs4-reset-form" type="reset" class="button button-link wcs4-reset-work-plan-form"
                    style="display: none;">
                <?php
                _ex('Reset form', 'button text', 'wcs4') ?>
            </button>
            <span class="spinner"></span>
            <p id="wcs4-ajax-text-wrapper" class="wcs4-ajax-text"></p>
        </fieldset>
    </form>
</details> <!-- /#work-plan-management-form-wrapper -->